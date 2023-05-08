<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static VangMat()
 * @method static static DiemDanhBangApp()
 * @method static static DiemDanhThuCong()
 */
final class TrangThaiDiemDanhEnum extends Enum
{
    const VangMat =   1;
    const DiemDanhBangApp =   2;
    const DiemDanhThuCong = 3;
}
