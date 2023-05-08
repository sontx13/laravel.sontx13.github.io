<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static TaiLieu()
 * @method static static DuThaoNghiQuyet()
 * @method static static NghiQuyetThongQua()
 */
final class LoaiTaiLieuEnum extends Enum
{
    const TaiLieu =   0;
    const DuThaoNghiQuyet =   1;
    const NghiQuyetThongQua = 2;
}
