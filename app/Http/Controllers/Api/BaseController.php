<?php

namespace App\Http\Controllers\Api;

use App\ApiResponse as AppApiResponse;
use Illuminate\Routing\Controller as BaseLaravelController;

class BaseController extends BaseLaravelController
{
    use AppApiResponse;
}