<?php

namespace App\Services;

use App\Models\MarketingType;
use App\Models\Position;
use App\Models\Subscription_plan;

class GeneralSettingService{
    public static function optionsMarketingType($user){
        return MarketingType::all('name', 'id');
    }
    public static function optionsPosition($user){
        return Position::all('name', 'id');
    }
    public static function optionsPlanType($user){
        return Subscription_plan::all('name', 'id');
    }

}
