<?php

namespace App\Http\Controllers;

use ApiResponse;
use App\Services\GeneralSettingService;
use App\Services\UserService;
use Illuminate\Http\Request;

class GeneralSettingController extends Controller
{
    public function getOptionRole()
    {
        $user = UserService::getAuthUser();
        return ApiResponse::JsonResult(GeneralSettingService::optionRole($user));
    }

    public function getOptionCategory()
    {
        $user = UserService::getAuthUser();
        return ApiResponse::JsonResult(GeneralSettingService::optionCategory($user));
    }
    public function getOptionAuthor()
    {
        $user = UserService::getAuthUser();
        return ApiResponse::JsonResult(GeneralSettingService::optionsAuthor($user));
    }
}
