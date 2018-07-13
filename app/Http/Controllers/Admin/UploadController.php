<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/7/11
 * Time: 18:41
 */

namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UploadController extends Controller
{
    use UploadTrait;

    public function uploadCover(Request $request)
    {
        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $upload = $this->saveUploadedFile($file, 'cover');
            $url = $upload->getUrl();
            return response($url);
        }
    }

}