<?php

namespace App\Http\Controllers\PC;

use App\Http\Controllers\Controller;
use App\Models\PcArticle;
use Illuminate\Http\Request;

class HomeController extends Controller
{
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
