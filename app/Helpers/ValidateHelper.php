<?php

namespace App\Helpers;

use App\Enums\BuoiHopEnum;

class ValidateHelper
{
    public static function CheckThoiGianDiemDanh($ngaydienra, $buoihop) {
        $isValid = false;
        if ($ngaydienra == date("Y-m-d")) {
            if (($buoihop == BuoiHopEnum::BuoiSang && (date('H') >= 6 && date('H') <= 12))
                || ($buoihop == BuoiHopEnum::BuoiChieu && (date('H') >= 12 && date('H') <= 18))
                )
            {
                $isValid = true;
            }
        }
        return $isValid;
    }
}
