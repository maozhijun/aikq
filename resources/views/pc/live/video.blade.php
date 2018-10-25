@extends('pc.layout.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/video.css">
@endsection
@section("content")
<div id="Content">
    <div class="inner">
        @if(isset($zhuanti))
            <div id="Crumb"><a href="/">爱看球</a>&nbsp;&nbsp;>  <a href="/{{$zhuanti['name_en']}}/">{{$zhuanti['name']}}</a>  >&nbsp;&nbsp;<span class="on">{{$match['hname']}}@if(!empty($match['aname']))&nbsp;&nbsp;VS&nbsp;&nbsp;{{$match['aname']}}@endif</span></div>
        @else
            <div id="Crumb"><a href="/">爱看球</a>&nbsp;&nbsp;>&nbsp;&nbsp;<span class="on">{{$match['hname']}}@if(!empty($match['aname']))&nbsp;&nbsp;VS&nbsp;&nbsp;{{$match['aname']}}@endif</span></div>
        @endif
        <div class="right_part">
            @if(isset($leagueLives) && count($leagueLives) > 0)
            <div id="League">
                <p class="title">{{$match['win_lname']}}赛事直播</p>
                <ul>
                    @foreach($leagueLives as $leagueLive)
                    <li>
                        <p class="time">{{date('m/d H:i', strtotime($leagueLive['time']))}}</p>
                        <p class="status">
                            @if(\App\Models\Match\MatchLive::isLive($leagueLive['id'], $sport, \App\Models\Match\MatchLiveChannel::kPlatformPC))
                                <a class="live" target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getLiveDetailUrl($sport, $lid, $leagueLive['id'])}}">直播中</a>
                            @endif
                        </p>
                        <p class="team">{{$leagueLive['hname']}} VS {{$leagueLive['aname']}}</p>
                    </li>
                    @endforeach
                    {{--<li>--}}
                        {{--<p class="time">10/20 22:15</p>--}}
                        {{--<p class="status"><a class="live" href="">直播中</a></p>--}}
                        {{--<p class="team"><a href="">瓦伦西亚 vs 莱加内安徽发</a></p>--}}
                    {{--</li>--}}
                    {{--<li>--}}
                        {{--<p class="time">10/20 22:15</p>--}}
                        {{--<p class="status"><a class="record" href="">录像</a></p>--}}
                        {{--<p class="team"><a href="">瓦伦西亚 vs 莱加内安徽发</a></p>--}}
                    {{--</li>--}}
                </ul>
            </div>
            @endif
            @if(isset($articles) && count($articles) > 0)
            <div id="News">
                <p class="title">相关新闻</p>
                @foreach($articles as $index=>$article)
                    @if($index < 3)
                        <a class="big" target="_blank" href="{{$article->url}}">
                            @if(!empty($article->cover))<p class="imgbox" style="background: url({{$article->cover}}); background-size: cover;"></p>@endif
                            <p class="con">{{$article->title}}</p>
                        </a>
                    @else
                        <a target="_blank" class="small" href="{{$article->url}}">{{$article->title}}</a>
                    @endif
                @endforeach
            </div>
            @endif
            <div id="Record">
                <p class="title">相关录像</p>
                <a href="">
                    <p class="imgbox" style="background: url(https://ss0.bdstatic.com/6ONWsjip0QIZ8tyhnq/it/u=1175366969,3493604330&fm=77&w_h=121_75&cs=2759057500,2022424845); background-size: cover;"></p>
                    <p class="name">C罗运球被对手</p>
                </a>
                <a href="">
                    <p class="imgbox" style="background: url(https://ss0.bdstatic.com/6ONWsjip0QIZ8tyhnq/it/u=1175366969,3493604330&fm=77&w_h=121_75&cs=2759057500,2022424845); background-size: cover;"></p>
                    <p class="name">C罗运球被对手</p>
                </a>
                <a href="">
                    <p class="imgbox" style="background: url(https://ss0.bdstatic.com/6ONWsjip0QIZ8tyhnq/it/u=1175366969,3493604330&fm=77&w_h=121_75&cs=2759057500,2022424845); background-size: cover;"></p>
                    <p class="name">C罗运球被对手</p>
                </a>
                <a href="">
                    <p class="imgbox" style="background: url(https://ss0.bdstatic.com/6ONWsjip0QIZ8tyhnq/it/u=1175366969,3493604330&fm=77&w_h=121_75&cs=2759057500,2022424845); background-size: cover;"></p>
                    <p class="name">C罗运球被对手</p>
                </a>
                <a href="">
                    <p class="imgbox" style="background: url(https://ss0.bdstatic.com/6ONWsjip0QIZ8tyhnq/it/u=1175366969,3493604330&fm=77&w_h=121_75&cs=2759057500,2022424845); background-size: cover;"></p>
                    <p class="name">C罗运球被对手</p>
                </a>
                <a href="">
                    <p class="imgbox" style="background: url(https://ss0.bdstatic.com/6ONWsjip0QIZ8tyhnq/it/u=1175366969,3493604330&fm=77&w_h=121_75&cs=2759057500,2022424845); background-size: cover;"></p>
                    <p class="name">C罗运球被对手</p>
                </a>
                <a href="">
                    <p class="imgbox" style="background: url(https://ss0.bdstatic.com/6ONWsjip0QIZ8tyhnq/it/u=1175366969,3493604330&fm=77&w_h=121_75&cs=2759057500,2022424845); background-size: cover;"></p>
                    <p class="name">C罗运球被对手</p>
                </a>
                <a href="">
                    <p class="imgbox" style="background: url(https://ss0.bdstatic.com/6ONWsjip0QIZ8tyhnq/it/u=1175366969,3493604330&fm=77&w_h=121_75&cs=2759057500,2022424845); background-size: cover;"></p>
                    <p class="name">C罗运球被对手</p>
                </a>
            </div>
        </div>
        <div class="left_part">
            <!-- <div class="adbanner inner"><a href="https://www.liaogou168.com/merchant/detail/10008" target="_blank"><img src="img/ad_1.jpg"><button class="close"></button></a></div> -->
            <div id="Info">
                <h1 class="name">{{$match['lname']}}直播：{{$match['hname']}}@if(!empty($match['aname']))　VS　{{$match['aname']}}@endif</h1>
                <p class="line">
                    <?php $channels = $live['channels']; ?>
                    @if(isset($channels))
                        @foreach($channels as $index=>$channel)
                            <?php
                            $player = $channel['player'];
                            if ($player == 11) {
                                $link = '/live/iframe/player-'.$channel['id'].'-'.$channel['type'].'.html';
                            } else {
                                $link = '/live/player/player-'.$channel['id'].'-'.$channel['type'].'.html';
                            }
                            ?>
                            <button id="{{$channel['channelId']}}"onclick="ChangeChannel('{{$link}}', this)">{{$channel['name']}}</button>
                        @endforeach
                    @endif
                </p>
            </div>
            <div class="iframe" id="Video">
                <!-- <iframe id="Frame" src="player.html?id=123"></iframe> -->
            </div>
            <div class="share" id="Share">
                {{--复制此地址分享：<input type="text" name="share" value="" onclick="Copy()"><span></span>--}}
            </div>
            <div id="Data">
                <div class="column">
                    <a href="javascript:void(0)" class="on" value="Analysis">数据分析</a>
                    <a href="javascript:void(0)" value="Lineup">球队阵容</a>
                    <a href="javascript:void(0)" value="Technology">技术统计</a>
                </div>
                <div id="Analysis" style="display: ;">
                    @if(isset($passVSMatches) && count($passVSMatches) > 0)
                    <p class="title">对往赛事</p>
                    <table>
                        <thead>
                        <tr>
                            <th>赛事</th>
                            <th>时间</th>
                            <th>主队</th>
                            <th>比分</th>
                            <th>客队</th>
                            <th>录像</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($passVSMatches as $pMatch)
                        <tr>
                            <td>{{$pMatch->getLeagueName()}}</td>
                            <td>{{substr($pMatch['time'], 2, 14)}}</td>
                            <td>{{$pMatch['hname']}}</td>
                            <td>{{$pMatch['hscore']}}-{{$pMatch['ascore']}}</td>
                            <td>{{$pMatch['aname']}}</td>
                            <td><a href="{{$pMatch['id']}}">全场录像</a></td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @endif
                    @if(isset($hNearMatches) && count($hNearMatches) > 0)
                    <p class="title">{{$match['hname']}}近期战绩</p>
                    <table>
                        <thead>
                        <tr>
                            <th>赛事</th>
                            <th>时间</th>
                            <th>主队</th>
                            <th>比分</th>
                            <th>客队</th>
                            <th>录像</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($hNearMatches as $hMatch)
                        <tr>
                            <td>{{$hMatch->getLeagueName()}}</td>
                            <td>{{substr($hMatch['time'], 2, 14)}}</td>
                            <td>{{$hMatch['hname']}}</td>
                            <td>{{$hMatch['hscore']}}-{{$hMatch['ascore']}}</td>
                            <td>{{$hMatch['aname']}}</td>
                            <td><a href="{{$hMatch['id']}}">全场录像</a></td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @endif
                    @if(isset($aNearMatches) && count($aNearMatches) > 0)
                    <p class="title">{{$match['aname']}}近期战绩</p>
                    <table>
                        <thead>
                        <tr>
                            <th>赛事</th>
                            <th>时间</th>
                            <th>主队</th>
                            <th>比分</th>
                            <th>客队</th>
                            <th>录像</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($aNearMatches as $aMatch)
                        <tr>
                            <td>{{$aMatch->getLeagueName()}}</td>
                            <td>{{substr($aMatch['time'], 2, 14)}}</td>
                            <td>{{$aMatch['hname']}}</td>
                            <td>{{$aMatch['hscore']}}-{{$aMatch['ascore']}}</td>
                            <td>{{$aMatch['aname']}}</td>
                            <td><a href="{{$aMatch['id']}}">全场录像</a></td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
                <div id="Lineup" style="display: none;">
                    <div class="team">
                        <p class="title">英格兰</p>
                        <table>
                            <thead>
                            <tr>
                                <th>号码</th>
                                <th>姓名</th>
                                <th>首发</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td>是</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td>是</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td>是</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td>是</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td>是</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td>是</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td>是</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td>是</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td>是</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td>是</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td>是</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="team">
                        <p class="title">西班牙</p>
                        <table>
                            <thead>
                            <tr>
                                <th>号码</th>
                                <th>姓名</th>
                                <th>首发</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td>是</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td>是</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td>是</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td>是</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td>是</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td>是</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td>是</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td>是</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td>是</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td>是</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td>是</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>瑞恩</td>
                                <td></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div id="Technology" style="display: none;">
                    <p class="title">本次技术统计</p>
                    <table>
                        <colgroup>
                            <col>
                            <col width="45">
                            <col width="15%">
                            <col width="45">
                            <col>
                        </colgroup>
                        <tr>
                            <td>
                                <p><span style="width: 60%;"></span></p>
                            </td>
                            <td>2</td>
                            <td>角球角球角球角球角球</td>
                            <td>9</td>
                            <td>
                                <p><span style="width: 40%;"></span></p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p><span style="width: 60%;"></span></p>
                            </td>
                            <td>2</td>
                            <td>角球</td>
                            <td>9</td>
                            <td>
                                <p><span style="width: 40%;"></span></p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p><span style="width: 60%;"></span></p>
                            </td>
                            <td>2</td>
                            <td>角球</td>
                            <td>9</td>
                            <td>
                                <p><span style="width: 40%;"></span></p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p><span style="width: 60%;"></span></p>
                            </td>
                            <td>2</td>
                            <td>角球</td>
                            <td>9</td>
                            <td>
                                <p><span style="width: 40%;"></span></p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p><span style="width: 60%;"></span></p>
                            </td>
                            <td>2</td>
                            <td>角球</td>
                            <td>9</td>
                            <td>
                                <p><span style="width: 40%;"></span></p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p><span style="width: 60%;"></span></p>
                            </td>
                            <td>2</td>
                            <td>角球</td>
                            <td>9</td>
                            <td>
                                <p><span style="width: 40%;"></span></p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p><span style="width: 60%;"></span></p>
                            </td>
                            <td>2</td>
                            <td>角球</td>
                            <td>9</td>
                            <td>
                                <p><span style="width: 40%;"></span></p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p><span style="width: 60%;"></span></p>
                            </td>
                            <td>2</td>
                            <td>角球</td>
                            <td>9</td>
                            <td>
                                <p><span style="width: 40%;"></span></p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p><span style="width: 60%;"></span></p>
                            </td>
                            <td>2</td>
                            <td>角球</td>
                            <td>9</td>
                            <td>
                                <p><span style="width: 40%;"></span></p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p><span style="width: 60%;"></span></p>
                            </td>
                            <td>2</td>
                            <td>角球</td>
                            <td>9</td>
                            <td>
                                <p><span style="width: 40%;"></span></p>
                            </td>
                        </tr>
                    </table>
                    <p class="title">详细事件</p>
                    <table>
                        <colgroup>
                            <col>
                            <col width="16%">
                            <col width="11%">
                            <col width="16%">
                            <col>
                        </colgroup>
                        <thead>
                        <tr>
                            <th>英格兰</th>
                            <th>事件</th>
                            <th>时间</th>
                            <th>事件</th>
                            <th>西班牙</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>莫离（助攻:皮亚尔）</td>
                            <td>进球</td>
                            <td>25‘</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td>57‘</td>
                            <td>黄牌</td>
                            <td>阿诺托维奇</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td>57‘</td>
                            <td>换人</td>
                            <td>安东尼奥（换上）佩里克（换下）</td>
                        </tr>
                        <tr>
                            <td>安东尼奥（换上）佩里克（换下）</td>
                            <td>换人</td>
                            <td>57‘</td>
                            <td></td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @if(count($moreLives) > 0)
            <div id="Other">
                <p class="title">更多直播</p>
                <table>
                    <colgroup>
                        <col width="120">
                        <col width="100">
                        <col width="30%">
                        <col>
                        <col width="100">
                    </colgroup>
                    @foreach($moreLives as $more)
                    <tr>
                        <td>{{$more['league_name']}}</td>
                        <td>{{substr($more['time'], 5, 11)}}</td>
                        <td>{{$more['hname']}} VS {{$more['aname']}}</td>
                        <td>
                            <?php
                            $mChannels = $more['channels'];
                            $url = \App\Http\Controllers\PC\CommonTool::getLiveDetailUrl($more['sport'], $more['lid'], $more['mid']);
                            ?>
                            @foreach($mChannels as $mch)
                                @if(isset($mch['player']) && $mch['player'] == 16){{-- 外链 --}}
                                <a target="_blank" href="/live/ex-link/{{$mch['id']}}">{{$mch['name']}}</a>
                                @else
                                    <?php
                                    if(isset($mch['akq_url']) && strlen($mch['akq_url']) > 0){
                                        $tmp_url = $mch['akq_url'];
                                    }
                                    else{
                                        $tmp_url = $url;
                                    }
                                    ?>
                                    <a target="_blank" href="{{$tmp_url . '?btn=' . $index}}">{{$mch['name']}}</a>
                                @endif
                            @endforeach
                        </td>
                        <td>@if($more['isMatching'])<a target="_blank" href="{{$url}}">直播中</a>@endif</td>
                    </tr>
                    @endforeach
                </table>
            </div>
            @endif
        </div>
    </div>
    <!-- <div class="adbanner inner"><img src="img/banner_pc_n@1x.jpg"><img class="show" src="img/wechat.jpeg"></div> -->
    <div class="clear"></div>
</div>
<!-- <div class="adflag left">
    <a href="http://91889188.87.cn" target="_blank"><img src="img/ad.jpg"><button class="close"></button></a>
</div>
<div class="adflag right">
    <a href="http://91889188.87.cn" target="_blank"><img src="img/ad.jpg"><button class="close"></button></a>
</div> -->
@endsection
@section("js")
<script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/jquery.js"></script>
<!--[if lte IE 8]>
<script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/jquery_191.js"></script>
<![endif]-->
<script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/video.js"></script>
<script type="text/javascript">
    window.onload = function () { //需要添加的监控放在这里
        setADClose();
        setPage();
    }
</script>
@endsection