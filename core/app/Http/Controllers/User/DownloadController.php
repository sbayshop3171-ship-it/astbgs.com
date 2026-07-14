<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Lib\FileUploader;
use App\Models\AuthorLevel;
use App\Models\Product;
use App\Models\Earning;
use App\Models\DownloadLog;
use App\Models\Transaction;

class DownloadController extends Controller
{
    public function downloadProduct($slug)
    {
        $user = auth()->user();
        $product = Product::where('slug', $slug)->firstOrFail();

        // Free product
        if ($product->is_free) {
            $this->earningTrack($user, $product, 0, 0);
            return $this->proceedDownload($product);
        }

        if(userActivePlan() == null) {
            $notify[] = ['error', 'You need to subscribe to a plan to download this product'];
            return back()->withNotify($notify);
        }

        $isDownloaded = DownloadLog::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->exists();

        // Check download limit
        if (!hasDownloadProduct('daily')) {
            $notify[] = ['error', "You've reached your daily download limit"];
            return back()->withNotify($notify);
        }

        if (!hasDownloadProduct('weekly')) {
            $notify[] = ['error', "You've reached your weekly download limit"];
            return back()->withNotify($notify);
        }

        if (!hasDownloadProduct('monthly')) {
            $notify[] = ['error', "You've reached your monthly download limit"];
            return back()->withNotify($notify);
        }

        $categoryCommission = 0;
        $levelCommission    = 0;
        $totalCommisison    = 0;

        // Only process commission if this is the first download
        if (!$isDownloaded) {
            $categoryCommission = $product->category->author_commission;
            $author = $product->author;
            $authorLevel = $author->currentAuthorLevel()->first();

            $userPlan = userActivePlan();
            if ($authorLevel) {
                $levelCommission = ($categoryCommission * $authorLevel->increase_commission) / 100;
                if ($levelCommission > 0) {
                    createPlanHistory($userPlan->plan_id, $levelCommission, '-', 'level_commission');
                }
            }


            $totalCommisison = $categoryCommission + $levelCommission;

            // Update author's stats
            $author->balance += $totalCommisison;
            $author->total_download_amount += $totalCommisison;
            $author->total_download += 1;
            $author->save();

            // Level upgrade
            $newLevel = AuthorLevel::active()
                ->where('minimum_earning', '<=', $author->total_download_amount)
                ->orderByDesc('minimum_earning')
                ->first();

            if ($newLevel && (!$authorLevel || $authorLevel->id != $newLevel->id)) {
                $author->authorLevels()->sync([$newLevel->id]);
            }

            // Add transaction
            $trx = new Transaction();
            $trx->user_id = $author->id;
            $trx->amount = $totalCommisison;
            $trx->post_balance = $author->balance;
            $trx->trx_type = "+";
            $trx->trx = getTrx();
            $trx->remark = "download_commission";
            $trx->details = "Earned " . gs('cur_sym') . $totalCommisison . " from $product->title download";
            $trx->balance_type = Status::BALANCE_TYPE_EARNING;
            $trx->save();

            $product->total_download += 1;
            $product->save();
            if ($categoryCommission > 0) {
                createPlanHistory($userPlan->plan_id,  $categoryCommission, '-', 'commission');
            }
        }

        // Log the download
        $downloadLog = new DownloadLog();
        $downloadLog->user_id = $user->id;
        $downloadLog->product_id = $product->id;
        $downloadLog->save();

        // Update download count
        $this->earningTrack($user, $product, $categoryCommission, $levelCommission);

        return $this->proceedDownload($product);
    }

    private function proceedDownload($product)
    {
        $fileUploader = new FileUploader();
        $fileUploader->path = getFilePath('productFile') . '/' . $product->slug;
        $fileUploader->file = $product->file;

        return $fileUploader->downloadFile($product);
    }

    private function earningTrack($user, $product, $categoryCommission, $levelCommission)
    {
        $download = Earning::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if (!$download) {
            $download = new Earning();
            $download->user_id = $user->id;
            $download->author_id = $product->user_id;
            $download->product_id = $product->id;
            $download->category_commission = $categoryCommission;
            $download->level_commission = $levelCommission;
            $download->total_earning = $categoryCommission + $levelCommission;
            $download->download_count = 1;
        } else {
            $download->category_commission += $categoryCommission;
            $download->level_commission += $levelCommission;
            $download->total_earning += ($categoryCommission + $levelCommission);
            $download->download_count += 1;
        }

        $download->save();
    }
}
