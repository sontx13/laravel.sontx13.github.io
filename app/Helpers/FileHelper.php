<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FileHelper
{
    public static function Save(
        $file, $tenantid, $kyhopid, $filename)
    {
        $url = '';
        try {
            $path = 'tenant'.$tenantid.'/kyhop'.$kyhopid.'/'.$filename;
            $url = $file->storeAs(
                'public/uploads', $path
            );
            $url = '/storage/uploads/'.$path;
        }
        catch (\Exception $e) {
            $url = '';
        }
        return $url;
    }

    public static function Remove($url) {
        $success = true;
        try {
            $path = public_path($url);
            if (File::exists($path)) {
                $success = File::delete($path);
            }
        }
        catch (\Exception $e) {
            $success = false;
        }
        return $success;
    }

    public static function SaveAvatar(
        $content, $tenantid, $filename)
    {
        $url = '';
        try {
            $path = 'uploads/tenant'.$tenantid.'/'.'avatar/'.$filename;
            $url = '/'.$path;
            $storage = Storage::disk('public');
            $storage->put($path, base64_decode($content), 'public');
        }
        catch (\Exception $e) {
            $url = '';
        }
        return $url;
    }
}
