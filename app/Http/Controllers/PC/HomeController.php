<?php

namespace App\Http\Controllers\PC;

use App\Http\Controllers\Controller;
use App\Models\PcArticle;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function appConfig(Request $request){
        return response()->json(
            [
                'code'=>0,
                'data'=>[
                    'more'=>'https://shop.liaogou168.com',
                    'icon'=>'http://mp.dlfyb.com/img/pc/image_qr_868.jpg',
                    'weixin'=>'kanqiu8888',
                    'ios_version'=>'1.2.0',
                    'android_version'=>'1.2.0',
                    'update_url'=>'https://www.aikanqiu.com/download/#browser',
                    'anchor'=>1,
                ]
            ]
        );
    }

    public function appConfigV110(Request $request){
        $array = $this->appConfivV110p();
        return response()->json($array);
    }

    public function appConfigV120(Request $request){
        $array = $this->appConfivV120p();
        return response()->json($array);
    }

    public function appConfivV110p(){
        return array(
            'code'=>0,
            'data'=>[
                'more'=>'https://shop.liaogou168.com/',
                'host'=>'https://api.dlfyb.com',
                'ws_host'=>'http://ws.aikanqiu.com',
                'icon'=>'https://static.dlfyb.com/img/pc/image_qr_868.jpg',
                'weixin'=>'kanqiu8888',
                'ios_version'=>'1.2.0',
                'android_version'=>'1.3.2',
                'update_url'=>'https://www.aikanqiu.com/download/#browser',
                'android_upgrade_url'=>'https://www.aikanqiu.com/download/',
                'anchor'=>1,
                'showMore'=>'0',
            ]
        );
    }

    public function appConfivV120p(){
        return array(
            'code'=>0,
            'data'=>[
                'more'=>'https://shop.liaogou168.com/',
                'host'=>'https://api.dlfyb.com',
                'ws_host'=>'http://ws.aikanqiu.com',
                'icon'=>'https://static.dlfyb.com/img/pc/image_qr_868.jpg',
                'weixin'=>'kanqiu8888',
                'ios_version'=>'1.2.0',
                'android_version'=>'1.3.2',
                'update_url'=>'https://www.aikanqiu.com/download/#browser',
                'android_upgrade_url'=>'https://www.aikanqiu.com/download/',
                'android_install_url'=>'https://static.dlfyb.com/download/android.apk',
                'install_url'=>'itms-services://?action=download-manifest&url=https://static.dlfyb.com/download/test.plist',
                'anchor'=>1,
                'showMore'=>'1',
            ]
        );
    }

    public function appConfivV130p(){
        return array(
            'code'=>0,
            'data'=>[
                'more'=>'https://shop.liaogou168.com/',
                'host'=>'https://api.dlfyb.com',
                'ws_host'=>'http://ws.aikanqiu.com',
                'icon'=>'https://static.dlfyb.com/img/pc/image_qr_868.jpg',
                'weixin'=>'kanqiu8888',
                'ios_version'=>'1.2.0',
                'android_version'=>'1.3.2',
                'update_url'=>'https://www.aikanqiu.com/download/#browser',
                'android_upgrade_url'=>'https://www.aikanqiu.com/download/',
                'android_install_url'=>'https://static.dlfyb.com/download/android.apk',
                'install_url'=>'itms-services://?action=download-manifest&url=https://static.dlfyb.com/download/test.plist',
                'anchor'=>1,
                'showMore'=>'1',
            ]
        );
    }

    public function index(Request $request){
       return view('pc.home');
   }

    /**
     * 邀请
     * @param Request $request
     * @param $code
     * @return $this
     */
   public function invitation(Request $request,$code){
       if (self::isMobile($request)){
           return response()->redirectTo('/m')->withCookie(cookie('LIAOGOU_INVITATION_CODE', $code));
       }
       else{
           return response()->redirectTo('/')->withCookie(cookie('LIAOGOU_INVITATION_CODE', $code));
       }
   }
}
