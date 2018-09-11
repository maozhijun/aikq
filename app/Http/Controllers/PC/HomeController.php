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
                    'weixin'=>'kanqiu868',
                    'ios_version'=>'1.2.0',
                    'android_version'=>'1.2.0',
                    'update_url'=>'https://www.aikq.cc/downloadPhone2.html#browser',
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
                'host'=>'http://cms.aikanqiu.com',
                'ws_host'=>'http://ws.aikq.cc',
                'icon'=>'http://mp.dlfyb.com/img/pc/image_qr_868.jpg',
                'weixin'=>'kanqiu868',
                'ios_version'=>'1.2.0',
                'android_version'=>'1.2.0',
                'update_url'=>'http://mp.dlfyb.com/downloadPhone.html#browser',
                'android_upgrade_url'=>'http://mp.dlfyb.com/downloadPhone.html',
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
                'host'=>'http://cms.aikanqiu.com',
                'ws_host'=>'http://ws.aikanqiu.com',
                'icon'=>'http://mp.dlfyb.com/img/pc/image_qr_868.jpg',
                'weixin'=>'kanqiu868',
                'ios_version'=>'1.2.0',
                'android_version'=>'1.2.0',
                'update_url'=>'http://mp.dlfyb.com/downloadPhone.html#browser',
                'android_upgrade_url'=>'http://mp.dlfyb.com/downloadPhone.html',
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
