@extends('pc.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/recording.css?time=20180000003">
@endsection
@section('content')
    <div id="Content">
        <div class="inner">
            <div class="moreVideo default">
                <div class="labelbox">
                    @foreach($leagues as $lid=>$league)
                        @if(isset($league['count']) && isset($league['name']) && $league['count'] > 0)
                        <button id="{{$lid}}" class="{{$lid == $type ? 'on' : ''}}">{{$league['name']}}</button>
                        @endif
                    @endforeach
                </div>
                @if(isset($videos) && count($videos) > 0)
                <div class="list">
                    @foreach($videos as $video)
                        <p class="match">
                            <span class="time">{{date('Y-m-d H:i', $video['time'])}}</span>
                            <span class="host">{{$video['hname']}}</span>
                            <span class="score">{{$video['hscore']}} - {{$video['ascore']}}</span>
                            <span class="away">{{$video['aname']}}</span>
                        </p>
                        <?php $channels = $video['channels']; ?>
                        @foreach($channels as $channel)
                            <?php
                                $cover = $channel['cover'];
                                $cover = str_replace('https://www.liaogou168.com', '', $cover);
                                $cover = str_replace('http://www.liaogou168.com', '', $cover);
                                $cover = env('CDN_URL') . '/live/subject/videos' . $cover;
                            ?>
                            <a class="big" href="{{\App\Http\Controllers\PC\MatchTool::subjectLink($video['id'], 'video')}}?cid={{$channel['id']}}" target="_blank">
                            <img style="height: 110px;" src="{{$cover}}" onerror="this.src='{{env('CDN_URL')}}/img/pc/image_video_bg.jpg'"><p>{{$channel['title']}}</p>
                            </a>
                        @endforeach
                    @endforeach
                    <div class="clear"></div>
                </div>
                @endif
                <div id="Page" curPage="{{$page['curPage']}}" lastPage="{{$page['lastPage']}}" style="display: none;"></div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/home.js"></script>
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/subject_recording.js"></script>
    <script type="text/javascript">
        window.onload = function () { //需要添加的监控放在这里
            setADClose();
            createPageHtml('Page');
            bindPageA("Page");
            bindType();
        }
    </script>
@endsection