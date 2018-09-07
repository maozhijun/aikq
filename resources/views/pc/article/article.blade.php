@extends("pc.layout.base")
@section("css")
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/article.css?rd={{date('YmdHi')}}">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/jqcloud.css?rd={{date('YmdHi')}}">

@endsection
@section("content")
    <div id="Content">
        @if(isset($zhuanti))
            <div id="Crumb"><a href="/">爱看球</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;<a href="/{{$zhuanti['name_en']}}/">{{$zhuanti['name']}}</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;<span class="on">资讯详情</span></div>
        @else
            <div id="Crumb"><a href="/">爱看球</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;<a href="/news/">资讯</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;<span class="on">资讯详情</span></div>
        @endif
        <div class="inner">
            <div id="Right">
                <dl>
                    <dt>直播赛程</dt>
                </dl>
                <div id="LatinWords">
                    <?php
                    $keys = \App\Models\Admin\CloudKeyword::getKeyWord();
                    ?>
                    <p class="title">热门关键词</p>
                    <div id="LatinWords_in">
                        @for($i = 0 ; $i < count($keys) ; $i++)
                            <?php
                            $item = $keys[$i];
                            if (is_null($item['level']) || $item['level'] == 0){
                                if ($i < 1)
                                    $level = 10;
                                else if($i < 2)
                                    $level = 8;
                                else if($i < 3)
                                    $level = 7;
                                else if($i < 4)
                                    $level = 6;
                                else if($i < 5)
                                    $level = 5;
                                else if($i < 6)
                                    $level = 4;
                                else if($i < 15)
                                    $level = 3;
                                else if($i < 23)
                                    $level = 2;
                                else
                                    $level = 1;
                            }
                            else{
                                $level = $item['level'];
                            }
                            ?>
                            <a target="_blank" href="{{$item['url']}}" level="{{$level}}">{{$item['keyword']}}</a>
                        @endfor
                    </div>
                </div>
            </div>
            <div id="Left">
                <div class="con">
                    <h1>{{$article->title}}</h1>
                    <p class="info">{!! !empty($article->resource) ? "来源：" . $article->resource . "&nbsp;&nbsp;&nbsp;" : "" !!}
                        作者：{{$article->author}}&nbsp;&nbsp;&nbsp;&nbsp;{{$article->type_obj->name}}<span>发表于：{{substr($article->publish_at, 0, 16)}}</span></p>
                    <div class="detail">{!! $article->getContent() !!}</div>
                </div>
                @if(isset($res) && count($res) > 0)
                    <div class="other">
                        <div class="title">相关文章</div>
                        @foreach($res as $re)
                            <a target="_blank" href="{{$re->url}}">{{$re->title}}</a>
                        @endforeach
                        <p class="clear"></p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
@section("js")
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/jqcloud-1.0.4.js"></script>
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/article.js"></script>
    <script type="text/javascript">
        $.get("/news/lives.html", function (html) {
            $("#Right dl").html(html);
        });
        window.onload = function () { //需要添加的监控放在这里
            setPage();
        }
        var ua = navigator.userAgent;
        if (ua.indexOf('http://www.baidu.com/search/spider.html') > 0) {
            $.ajaxSetup({
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
            });
            $.ajax({
                type: 'POST',
                url: 'http://cms.aikq.cc/spider/article/'+'{{$article['id']}}',
                success: function (data) {
                    console.log(data);
                },
            });
        };
    </script>
@endsection