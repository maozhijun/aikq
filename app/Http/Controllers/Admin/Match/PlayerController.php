<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/8/30
 * Time: 9:16
 */

namespace App\Http\Controllers\Admin\Match;


use App\Http\Controllers\Admin\UploadTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class PlayerController extends Controller
{
    use UploadTrait;

    const kTypeL = 'l', kTypeD = 'd', kTypeZ = 'z', kTypeW = 'w', kTypeCD = 'cd';//播放器广告类型，1：前置广告，2：暂停广告，3：缓冲广告， 4：倒计时广告
    const TypeCnArray = [self::kTypeL=>'前置广告', self::kTypeD=>'暂停广告', self::kTypeZ=>'缓冲广告'
        , self::kTypeCD=>'倒计时广告'];//, AkqAdImage::kTypeW=>'倒计时广告'

    /**
     * 广告图片列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function adImages(Request $request) {
        $images = self::getAdImages();
        $typeCnArray = self::TypeCnArray;
        return view('admin.player.ad_images', ['images'=>$images, 'typeCns'=>$typeCnArray]);
    }

    /**
     * 保存 播放器广告图片
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveAdImage(Request $request) {
        $user = $request->_account;//当前登录用户。

        $type = $request->input('type');
        $name = $request->input('name');//倒计时微信名称
        $text = $request->input('text');//倒计时广告语

        if (!isset(self::TypeCnArray[$type])) {
            return back()->with('error', '广告类型错误');
        }

        try {
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $upload = $this->saveUploadedFile($file, 'cover');
                $image = $upload->getUrl();
                $link = $image;
            }

            $images = [];
            if ($type == self::kTypeCD) {
                if (!empty($name)) {
                    $images['cd_name'] = $name;
                }
                if (!empty($text)) {
                    $images['cd_text'] = $text;
                }
            }
            if (!empty($link)) {
                $images[$type] = $link;
            }
            self::saveImagesJson($images);
        } catch (\Exception $exception) {
            dump($exception);
            return back()->with('error', '保存播放器广告失败');
        }
        return back()->with('success', '保存播放器广告成功');
    }

    public static function saveImagesJson(array $newImages) {
        $images = self::getAdImages();
        foreach ($newImages as $key=>$val) {
            $images[$key] = $val;
        }
        Storage::disk('public')->put('www/json/dd_image/images.json', json_encode($images));
        Storage::disk('public')->put('m/json/dd_image/images.json', json_encode($images));
    }

    public static function getAdImages() {
        try {
            $string = Storage::get("public/www/m/dd_image/images.json");
            $json = json_decode($string, true);
        } catch (\Exception $exception) {
            $json = null;
        }

        $defaultImg = "/img/pc/demo.jpg";
        if (!isset($json)) {
            $json = [self::kTypeL=>$defaultImg, self::kTypeD=>$defaultImg, self::kTypeZ=>$defaultImg];
            $json[self::kTypeW] = $defaultImg;
            $json[self::kTypeCD] = $defaultImg;
            $json['cd_name'] = '';
            $json['cd_text'] = '';
            $json['code'] = '';
        }
        return $json;
    }
}