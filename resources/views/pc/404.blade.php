<?php $title = "404错误页"; $noMeta = true; $noSubmitBD = true; ?>
@extends('pc.layout.base')
@section('css')
    <style type="text/css">
        body{ background: url({{env('CDN_URL')}}/img/pc/image_fzf_n.png) no-repeat center 160px #f2f2f2; background-size: 320px;}
        #Content{ padding-top: 410px; font-size: 20px; line-height: 30px; text-align: center; color: #9794bc;}
        a.back{ width: 100px; height: 44px; margin: 30px auto 0; border-radius: 2px; background: #4492fd; font-size: 16px; line-height: 44px; text-align: center; color: #fff; display: block;}
    </style>
@endsection
@section('content')
    <div id="Content">
        <p>找不到您想要的页面哦</p>
    </div>
@endsection