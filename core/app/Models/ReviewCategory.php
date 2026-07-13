<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class ReviewCategory extends Model
{
    use GlobalStatus;
}
