@extends('pc.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/recording.css">
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
                        <?php
                            $cover = $video['cover'];
                            $cover = str_replace('https://www.liaogou168.com', '', $cover);
                            $cover = str_replace('http://www.liaogou168.com', '', $cover);
                            $cover = env('CDN_URL') . '/live/subject/videos' . $cover;
                        ?>
                        <a class="big" href="{{\App\Http\Controllers\PC\MatchTool::subjectLink($video['sv_id'], 'video')}}?cid={{$video['id']}}" target="_blank">
                            <img src="{{$cover}}"><p>{{$video['title']}}</p>
                        </a>
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