<?php

namespace App\Helpers;

use App\Constants\Constants;
use App\Enums\TrangThaiChatVanEnum;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;

class FireBaseHelper
{
    // event đại biểu điểm danh theo buổi họp
    public static function SendEventDiemDanh($tenantId, $buoihopId, $userId, $trang_thai) {
        $reference = sprintf(Constants::REF_DIEMDANH, $tenantId,  $buoihopId, $userId);
        $data = [
            'userid' => $userId,
            'trangthai' => $trang_thai
        ];
        FireBaseHelper::SendDataFireBase($reference, $data);
    }

    // event firbase biểu quyết
    public static function SendEventBatDauBieuQuyet($tenantId, $kyhopid, $bieuquyetid, $tenbieuquyet, $thoigian_batdau, $thoigian_bieuquyet) {
        $reference = sprintf(Constants::REF_BIEUQUYETDANGDIENRA, $tenantId, $kyhopid);//$tenantId."/bieuquyets/".$kyhopid."/dangdienra";
        $data = [
            'bieuquyet' => [
                'bieuquyetid' => $bieuquyetid,
                'tenbieuquyet' => $tenbieuquyet,
                'thoigian_batdau' => $thoigian_batdau,
                'thoigian_bieuquyet' => $thoigian_bieuquyet
            ]
        ];
        FireBaseHelper::SendDataFireBase($reference, $data);
    }
    public static function SendEventKetThucBieuQuyet($tenantId, $kyhopid) {
        $reference = sprintf(Constants::REF_BIEUQUYETDANGDIENRA, $tenantId, $kyhopid);//$tenantId."/bieuquyets/".$kyhopid."/dangdienra";
        FireBaseHelper::RemoveDataFireBase($reference);
    }

    public static function SendEventBinhChonBieuQuyet($tenantId, $kyhopid, $bieuquyetid, $userid, $giatri) {
        $reference = sprintf(Constants::REF_KQBIEUQUYET, $tenantId, $kyhopid, $bieuquyetid, $userid);//$tenantId."/bieuquyets/".$kyhopid."/".$bieuquyetid."/".$userid;
        $data = [
            'bieuquyetid' => $bieuquyetid,
            'userid' => $userid,
            'giatri' => $giatri
        ];
        FireBaseHelper::SendDataFireBase($reference, $data);
    }

    // event firebase chất vấn
    public static function SendEventBatDauPhienChatVan($tenantId, $kyhopid, $phienchatvanid, $nguoitraloi, $thoigian_batdau, $thoigian_traloi) {
        $reference = sprintf(Constants::REF_PHIENCHATVANDANGDIENRA, $tenantId, $kyhopid);//$tenantId."/chatvans/".$kyhopid."/dangdienra";
        $data = [
            'phienchatvan' => [
                'phienchatvanid' => $phienchatvanid,
                'nguoitraloi' => $nguoitraloi,
                'thoigian_batdau' => $thoigian_batdau,
                'thoigian_traloi' => $thoigian_traloi
            ]
        ];
        FireBaseHelper::SendDataFireBase($reference, $data);
    }
    public static function SendEventKetThucPhienChatVan($tenantId, $kyhopid) {
        $reference = sprintf(Constants::REF_PHIENCHATVANDANGDIENRA, $tenantId, $kyhopid);//$tenantId."/chatvans/".$kyhopid."/dangdienra";
        FireBaseHelper::RemoveDataFireBase($reference);
    }
    // event đại biểu chất vấn
    public static function SendEventBatDauDaiBieuChatVan($tenantId, $kyhopid, $phienchatvanid, $chatvanid,
        $userid, $user_name, $thoigian_batdau, $thoigian_chatvan) {
        $reference = sprintf(Constants::REF_DAIBIEUCHATVAN, $tenantId, $kyhopid, $phienchatvanid, $userid);//$tenantId."/chatvans/".$kyhopid."/".$phienchatvanid."/".$userid;
        $data = [
            'phienchatvanid' => $phienchatvanid,
            'chatvanid' => $chatvanid,
            'userid' => $userid,
            'user_name' => $user_name,
            'trang_thai' => TrangThaiChatVanEnum::DangChatVan,
            'thoigian_batdau' => $thoigian_batdau,
            'thoigian_chatvan' => $thoigian_chatvan,
        ];
        FireBaseHelper::SendDataFireBase($reference, $data);
    }

    public static function SendEventKetThucDaiBieuChatVan($tenantId, $kyhopid, $phienchatvanid
    , $userid) {
        $reference = sprintf(Constants::REF_DAIBIEUCHATVAN, $tenantId, $kyhopid, $phienchatvanid, $userid);//$tenantId."/chatvans/".$kyhopid."/".$phienchatvanid."/".$userid;
        $data = [
            'phienchatvanid' => $phienchatvanid,
            'userid' => $userid,
            'trang_thai' => TrangThaiChatVanEnum::DaChatVan
        ];
        FireBaseHelper::SendDataFireBase($reference, $data);
    }

    public static function SendEventXoaDaiBieuChatVan($tenantId, $kyhopid, $phienchatvanid, $userid) {
        $reference = sprintf(Constants::REF_DAIBIEUCHATVAN, $tenantId, $kyhopid, $phienchatvanid, $userid);//$tenantId."/chatvans/".$kyhopid."/".$phienchatvanid."/".$userid;
        FireBaseHelper::RemoveDataFireBase($reference);
    }
    // event add or update node firebase
    private static function SendDataFireBase($reference, $data) {
        try {
            $url = config('firebase.projects.app.database.url');
            $credentials = config('firebase.projects.app.credentials.file');
            $factory = (new Factory)
            ->withServiceAccount($credentials)
            ->withDatabaseUri($url);
            $database = $factory->createDatabase();
            $database->getReference($reference)
                ->set($data);
        } catch (\Exception $e) {
            Log::error("Send Data Firebase error: ".json_encode($e));
        }
    }

    // event remove node firebase
    private static function RemoveDataFireBase($reference) {
        try {
            $url = config('firebase.projects.app.database.url');
            $credentials = config('firebase.projects.app.credentials.file');
            $factory = (new Factory)
            ->withServiceAccount($credentials)
            ->withDatabaseUri($url);
            $database = $factory->createDatabase();
            $database->getReference($reference)
                ->remove();
        } catch (\Exception $e) {
            Log::error("Remove Data Firebase error: ".json_encode($e));
        }
    }
}
