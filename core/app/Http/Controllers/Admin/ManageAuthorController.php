<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\User;

class ManageAuthorController extends Controller
{
    public function list()
    {
        $pageTitle = 'Authors';
        $authors   = User::active()->where('is_author', Status::YES)->withCount(['products'])->searchable(['username', 'email'])->latest()->paginate(getPaginate());
        return view('admin.author.list', compact('pageTitle', 'authors'));
    }

    public function data($id)
    {
        $pageTitle = 'Author Data';
        $user      = User::findOrFail($id);
        return view('admin.users.author_data', compact('pageTitle', 'user'));
    }

    public function toggleFeature($id)
    {
        $user = User::author()->withCount(['products'])->findOrFail($id);
        if (!$user->products_count) {
            $notify[] = ['error', 'No products found of this author'];
            return back()->withNotify($notify);
        }

        $wasFeatured = $user->is_author_featured;
        $user->is_author_featured = $user->is_author_featured ? Status::NO : Status::YES;
        $user->save();

        if ($user->is_author_featured && !$wasFeatured) {
            User::author()->where('id', '!=', $user->id)->where('is_author_featured', Status::YES)->update(['is_author_featured' => Status::NO]);
            notify($user, 'CONGRATULATE_AUTHOR', []);
        }

        $message = $user->is_author_featured ? 'Featured Successfully' : 'Unfeature sucessfully';
        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }
}
