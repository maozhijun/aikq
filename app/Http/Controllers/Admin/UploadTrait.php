<?php
/**
 * Created by PhpStorm.
 * User: maozhijun
 * Date: 17/5/22
 * Time: 13:09
 */

namespace App\Http\Controllers\Admin;


use App\Models\Upload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait UploadTrait
{

    public function saveUploadedFile(UploadedFile $file, $disks)
    {
        $env = env('APP_URL');
        $length = $file->getClientSize();
        $md5 = md5_file($file->getRealPath());
        $extension = $file->guessClientExtension();
        $mime = $file->getMimeType();
        $suffix = last(explode('/', $mime));
        $upload = Upload::query()->where(['md5' => $md5, 'length' => $length, 'env' => $env])->first();
        if (isset($upload)) {
            return $upload;
        }
        $upload = new Upload();
        $upload->md5 = $md5;
        $upload->length = $length;
        $upload->suffix = $suffix;
        $upload->extension = $extension;
        $upload->mime = $mime;
        $path = $file->storeAs(date('Ymd'), str_random() . '.' . $extension, $disks);
        $upload->path = $path;
        $upload->disks = $disks;
        $upload->env = $env;
        $upload->save();
        return $upload;
    }

    public function saveUrlFile($url, $disks)
    {
        $env = env('APP_URL');

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
//        echo $response;
        $md5 = md5($response);
        $mime = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        if (strpos($mime, ';') !== false) {
            $mime = explode(';', $mime)[0];
        }
        $length = curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD);
        $suffix = last(explode('/', $mime));
        $upload = Upload::query()->where(['md5' => $md5, 'length' => $length, 'env' => $env])->first();
        if (isset($upload)) {
//            echo $upload->getUrl();
            return $upload;
        }
        $upload = new Upload();
        $upload->md5 = $md5;
        $upload->length = $length;
        $upload->suffix = $suffix;
        $upload->extension = $suffix;
        $upload->mime = $mime;
        $path = date('Ymd') . '/' . str_random() . '.' . $suffix;
        Storage::disk($disks)->put($path, $response);
        $upload->env = $env;
        $upload->path = $path;
        $upload->disks = $disks;
//        echo $upload->getUrl();
        $upload->save();
        return $upload;
    }

}