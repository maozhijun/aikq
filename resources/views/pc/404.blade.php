@extends('pc.layout.base')
@section('ie9css')
    <style type="text/css">
        @media screen and (max-width: 1400px){
            #Navigation:before{width:1750px; height:70px; margin-left:-875px; background:#fff; border-bottom:1px solid #e6e6e6; position:absolute; top:0; left:50%; z-index:0;content:'';}
            #Bottom:after{width:1750px; height:202px; margin-left:-875px; background:#373838; position:absolute; bottom:0; left:50%; z-index:0 ;content: '';}
            html{overflow-y: scroll; overflow-x: hidden;}
            body{overflow: hidden;}
            #Navigation,#Bottom,#Content,#Path{-ms-transform: scale(0.8); -ms-transform-origin: center top;}
            #Content{padding: 0 !important;}
            #Path{padding: 6px 0 !important;}
            #Bottom{position: relative;}
            #TableHead{zoom: 0.8; margin-left: -492px !important;}
        }
    </style>
@endsection
@section('content')
    <div id="Content" type="404">
        出错了
        <div class="inner"></div>
    </div>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('css/error.css')}}">
@endsection
@section('iecss')
    <style>
        html,body{height:100%;}
        #Content{height:80%;}
    </style>
@endsection