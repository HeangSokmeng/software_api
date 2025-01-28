<?php

namespace App\Services;

use App\Models\Author;
use App\Models\Category;
use App\Models\MarketingType;
use App\Models\Position;
use App\Models\Role;
use App\Models\Subscription_plan;

class GeneralSettingService{
    public static function optionRole($user){
        return Role::all('name', 'id');
    }
    public static function optionCategory($user){
        return Category::all('cate_name', 'cate_id');
    }
    public static function optionsAuthor($user){
        return Author::all('auth_name', 'auth_id');
    }

}
