@extends('pc.layout.v2.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/left_right_2.css?time=20192191536">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/v2/record_2.css?time=20192191536">
@endsection
@section('content')
    @if(isset($zhuanti))
        <div id="Crumbs">
            <div class="def_content">
                <a href="/">爱看球</a>&nbsp;&nbsp;-&nbsp;&nbsp;<a href="/{{$zhuanti['name_en']}}/">{{$zhuanti['name']}}</a>&nbsp;&nbsp;-&nbsp;&nbsp;{{$zhuanti['name']}}录像
            </div>
        </div>
    @endif
    <?php
    if (!is_null($match) && !is_null($sv)){
        $time = date('m月d日', $match['time']);
        $time = date('m月d日', $match['time']);
        $mTitle = $sv['lname'] . ' ' . $sv['hname']. ' VS '.$sv['aname'];
        $sTitle = isset($subjects[$sv['s_lid']])? $subjects[$sv['s_lid']]['name'] : '';
//        $sTitle = $sv['lname'];
    }
    else{
        $time = '';
        $mTitle = '';
        $sTitle = '';
    }
    if (isset($zhuanti))
        {
            $zt = '/'.$zhuanti['name_en'].'/record/index.html';
        }
        else{
            $zt = '/record/index.html';
        }
    ?>
    <div class="def_content" id="Part_parent">
        <div id="Left_part">
            <div id="Video_play_box">
                <h1>{{$time}} {{$mTitle}}</h1>
                <ul>
                    @foreach($records as $record)
                        <li><a target="_blank" href="{{$record['content']}}" class="go_play">【立即播放】</a><a target="_blank" href="{{$record['content']}}">【{{$record['title']}}】{{$time}} {{$mTitle}}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="el_con">
                <div class="header">
                    <h3><p>{{$sTitle}}录像</p></h3>
                    <p class="aline">
                        <a href="{{$zt}}">全部{{$sTitle}}录像 ></a>
                    </p>
                </div>
                <table class="match">
                    <col width="25%"><col><col width="15%"><col><col width="20%">
                    @foreach($hotRecords as $hotRecord)
                        <?php
                        $time = date('Y年m月d日 H:m', date_create($hotRecord['time'])->getTimestamp());
                            $subject = isset($subjects[$hotRecord['s_lid']])? $subjects[$hotRecord['s_lid']]['name_en'] : 'other';
                            if (!is_null($hotRecord->url)){
                                $url = $hotRecord->url;
                            }
                            else{
                                $url = \App\Http\Controllers\PC\CommonTool::getRecordDetailUrl($subject,$hotRecord['mid']);
                            }
                        ?>
                        <tr>
                            <td><span>{{$time}}</span></td>
                            <td class="host">{{$hotRecord['hname']}}</td>
                            <td class="vs">{{$hotRecord['hscore']}} - {{$hotRecord['ascore']}}</td>
                            <td class="away">{{$hotRecord['aname']}}</td>
                            <td class="line"><a href="{{$url}}" class="live">观看录像</a></td>
                        </tr>
                    @endforeach
                </table>
            </div>
            <div style="display: none" class="el_con">
                <div class="header">
                    <h3><p>NBA视频</p></h3>
                    <p class="aline">
                        <a href="video_league.html">全部NBA视频 ></a>
                    </p>
                </div>
                <div class="video_list">
                    <div class="list_item">
                        <a href="#">
                            <p class="img_box"><img src="https://puui.qpic.cn/vpic/0/n08406t9mrm.png/0"></p>
                            <div class="title_text"><p><span>小伙子很激动 布莱恩特劲爆挂筐还造犯规</span></p></div>
                        </a>
                    </div>
                    <div class="list_item">
                        <a href="#">
                            <p class="img_box"><img src="https://puui.qpic.cn/vpic/0/n08406t9mrm.png/0"></p>
                            <div class="title_text"><p><span>小伙子很激动 布莱恩特劲爆挂筐还造犯规</span></p></div>
                        </a>
                    </div>
                    <div class="list_item">
                        <a href="#">
                            <p class="img_box"><img src="https://puui.qpic.cn/vpic/0/n08406t9mrm.png/0"></p>
                            <div class="title_text"><p><span>小伙子很激动 布莱恩特劲爆挂筐还造犯规</span></p></div>
                        </a>
                    </div>
                    <div class="list_item">
                        <a href="#">
                            <p class="img_box"><img src="https://puui.qpic.cn/vpic/0/n08406t9mrm.png/0"></p>
                            <div class="title_text"><p><span>小伙子很激动 布莱恩特劲爆挂筐还造犯规</span></p></div>
                        </a>
                    </div>
                    <div class="list_item">
                        <a href="#">
                            <p class="img_box"><img src="https://puui.qpic.cn/vpic/0/n08406t9mrm.png/0"></p>
                            <div class="title_text"><p><span>小伙子很激动 布莱恩特劲爆挂筐还造犯规</span></p></div>
                        </a>
                    </div>
                    <div class="list_item">
                        <a href="#">
                            <p class="img_box"><img src="https://puui.qpic.cn/vpic/0/n08406t9mrm.png/0"></p>
                            <div class="title_text"><p><span>小伙子很激动 布莱恩特劲爆挂筐还造犯规</span></p></div>
                        </a>
                    </div>
                    <div class="list_item">
                        <a href="#">
                            <p class="img_box"><img src="https://puui.qpic.cn/vpic/0/n08406t9mrm.png/0"></p>
                            <div class="title_text"><p><span>小伙子很激动 布莱恩特劲爆挂筐还造犯规</span></p></div>
                        </a>
                    </div>
                    <div class="list_item">
                        <a href="#">
                            <p class="img_box"><img src="https://puui.qpic.cn/vpic/0/n08406t9mrm.png/0"></p>
                            <div class="title_text"><p><span>小伙子很激动 布莱恩特劲爆挂筐还造犯规</span></p></div>
                        </a>
                    </div>
                    <div class="list_item">
                        <a href="#">
                            <p class="img_box"><img src="https://puui.qpic.cn/vpic/0/n08406t9mrm.png/0"></p>
                            <div class="title_text"><p><span>小伙子很激动 布莱恩特劲爆挂筐还造犯规</span></p></div>
                        </a>
                    </div>
                    <div class="list_item">
                        <a href="#">
                            <p class="img_box"><img src="https://puui.qpic.cn/vpic/0/n08406t9mrm.png/0"></p>
                            <div class="title_text"><p><span>小伙子很激动 布莱恩特劲爆挂筐还造犯规</span></p></div>
                        </a>
                    </div>
                    <div class="list_item">
                        <a href="#">
                            <p class="img_box"><img src="https://puui.qpic.cn/vpic/0/n08406t9mrm.png/0"></p>
                            <div class="title_text"><p><span>小伙子很激动 布莱恩特劲爆挂筐还造犯规</span></p></div>
                        </a>
                    </div>
                    <div class="list_item">
                        <a href="#">
                            <p class="img_box"><img src="https://puui.qpic.cn/vpic/0/n08406t9mrm.png/0"></p>
                            <div class="title_text"><p><span>小伙子很激动 布莱恩特劲爆挂筐还造犯规</span></p></div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div id="Right_part" style="display: none">
            <a class="banner_entra" href="league_nba.html">
                <img src="https://gss2.bdstatic.com/9fo3dSag_xI4khGkpoWK1HF6hhy/baike/w%3D268%3Bg%3D0/sign=c95a12f874f0f736d8fe4b07326ed424/3801213fb80e7bec36d92766232eb9389b506b31.jpg">
                <h3>美国男子职业篮球联赛</h3>
                <p>球队：<span>30支</span></p>
            </a>
            <a class="banner_entra" href="">
                <img src="http://img1.gtimg.com/sports/pics/hv1/231/116/2220/144385311.png">
                <h3>圣安东尼奥马刺</h3>
                <p>赛事：<span>NBA</span>排名：<span>东部第1名</span></p>
            </a>
            <a class="banner_entra" href="">
                <img src="http://img1.gtimg.com/sports/pics/hv1/133/21/2268/147482188.png">
                <h3>多伦多猛龙</h3>
                <p>赛事：<span>NBA</span>排名：<span>东部第1名</span></p>
            </a>
            <div class="con_box">
                <div class="header_con">
                    <h4>最近NBA直播</h4>
                    <a href="live_list.html">全部直播</a>
                </div>
                <div class="live">
                    <div class="live_item">
                        <p class="live_match_info">NBA<span>01-24 16:20</span></p>
                        <div class="live_match_team">
                            <p class="team"><span>达拉斯独行侠</span></p>
                            <p class="vs"><span>直播中</span></p>
                            <p class="team"><span>多伦多猛龙</span></p>
                        </div>
                        <div class="live_match_line">
                            <a href="live.html">高清直播</a>
                            <a href="live.html">主播剧本球童</a>
                            <a href="live.html">体育直播</a>
                        </div>
                    </div>
                    <div class="live_item">
                        <p class="live_match_info">NBA<span>01-24 16:20</span></p>
                        <div class="live_match_team">
                            <p class="team"><span>达拉斯独行侠</span></p>
                            <p class="vs"><span>直播中</span></p>
                            <p class="team"><span>多伦多猛龙</span></p>
                        </div>
                        <div class="live_match_line">
                            <a href="live.html">高清直播</a>
                            <a href="live.html">高清直播2</a>
                            <a href="live.html">主播剧本球童</a>
                            <a href="live.html">主播三少</a>
                            <a href="live.html">体育直播</a>
                        </div>
                    </div>
                    <div class="live_item">
                        <p class="live_match_info">NBA<span>01-24 16:20</span></p>
                        <div class="live_match_team">
                            <p class="team"><span>达拉斯独行侠</span></p>
                            <p class="vs"><span>直播中</span></p>
                            <p class="team"><span>多伦多猛龙</span></p>
                        </div>
                        <div class="live_match_line">
                            <a href="live.html">高清直播</a>
                            <a href="live.html">主播剧本球童</a>
                            <a href="live.html">体育直播</a>
                        </div>
                    </div>
                    <div class="live_item">
                        <p class="live_match_info">NBA<span>01-24 16:20</span></p>
                        <div class="live_match_team">
                            <p class="team"><span>达拉斯独行侠</span></p>
                            <p class="vs"><span>直播中</span></p>
                            <p class="team"><span>多伦多猛龙</span></p>
                        </div>
                        <div class="live_match_line">
                            <a href="live.html">高清直播</a>
                            <a href="live.html">主播剧本球童</a>
                            <a href="live.html">体育直播</a>
                        </div>
                    </div>
                    <div class="live_item">
                        <p class="live_match_info">NBA<span>01-24 16:20</span></p>
                        <div class="live_match_team">
                            <p class="team"><span>达拉斯独行侠</span></p>
                            <p class="vs"><span>直播中</span></p>
                            <p class="team"><span>多伦多猛龙</span></p>
                        </div>
                        <div class="live_match_line">
                            <a href="live.html">高清直播</a>
                            <a href="live.html">主播剧本球童</a>
                            <a href="live.html">体育直播</a>
                        </div>
                    </div>
                    <div class="live_item">
                        <p class="live_match_info">NBA<span>01-24 16:20</span></p>
                        <div class="live_match_team">
                            <p class="team"><span>达拉斯独行侠</span></p>
                            <p class="vs"><span>直播中</span></p>
                            <p class="team"><span>多伦多猛龙</span></p>
                        </div>
                        <div class="live_match_line">
                            <a href="live.html">高清直播</a>
                            <a href="live.html">主播剧本球童</a>
                            <a href="live.html">体育直播</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="con_box">
                <div class="header_con">
                    <h4>NBA资讯</h4>
                    <a href="news_league.html">全部NBA资讯</a>
                </div>
                <div class="news">
                    <a href="news.html" class="img_news">
                        <p class="img_box"><img src="https://puui.qpic.cn/vpic/0/c00262vdebg.png/0"></p>
                        <h3>直击-哈登赛后采访挤爆球员通道 直言能赢球就不怕累</h3>
                    </a>
                    <a href="news.html" class="img_news">
                        <p class="img_box"><img src="https://puui.qpic.cn/vpic/0/a0026rei5lt.png/0"></p>
                        <h3>直击-哈登赛后采访挤爆球员通道 直言能赢球就不怕累</h3>
                    </a>
                    <a href="news.html" class="text_new"><h4>直击-哈登赛后采访挤爆球员通道 直言能赢球就不直言能赢球就不</h4></a>
                    <a href="news.html" class="text_new"><h4>他仍然面临着这样的挑战，他决定将它作</h4></a>
                    <a href="news.html" class="text_new"><h4>机角度和慢动作重放，显然距离争球线的距</h4></a>
                    <a href="news.html" class="text_new"><h4>直击-哈登赛后采访挤爆球员通道 直言能赢球就不直言能赢球就不</h4></a>
                    <a href="news.html" class="text_new"><h4>机角度和慢动作重放，显然距离争球线的距</h4></a>
                    <a href="news.html" class="text_new"><h4>直击-哈登赛后采访挤爆球员通道 直言能赢球就不直言能</h4></a>
                    <a href="news.html" class="text_new"><h4>机角度和慢动作重放，显然距离争球线的距</h4></a>
                    <a href="news.html" class="text_new"><h4>直击-哈登赛后采访挤爆球员通道 直言能赢球就不直言能</h4></a>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript" src="{{env('CDN_URL')}}/js/pc/v2/live_2.js"></script>
    <script type="text/javascript">
        window.onload = function () { //需要添加的监控放在这里
            setPage();
        }
    </script>
@endsection