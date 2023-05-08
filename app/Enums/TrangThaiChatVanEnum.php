<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static ChoDuyet()
 * @method static static ChoChatVan()
 * @method static static DangChatVan()
 * @method static static DaChatVan()
 */
final class TrangThaiChatVanEnum extends Enum
{
    const ChoDuyet =  0;
    const ChoChatVan =  1;
    const DangChatVan =  2;
    const DaChatVan = 3;
}
