<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static DongY()
 * @method static static KhongDongY()
 * @method static static KhongBieuQuyet()
 */
final class GiaTriBieuQuyetEnum extends Enum
{
    const DongY =   1;
    const KhongDongY =   2;
    const KhongBieuQuyet = 3;
}
