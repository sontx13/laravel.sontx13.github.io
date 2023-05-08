<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static KhongSuDung()
 * @method static static DangDienRa()
 * @method static static DaDienRa()
 */
final class TrangThaiKyHopEnum extends Enum
{
    const KhongSuDung =  0;
    const DangDienRa =  1;
    const DaDienRa = 2;
}
