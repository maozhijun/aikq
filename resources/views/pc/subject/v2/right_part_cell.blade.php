<div id="Right_part">
    <div id="League_info">
        <div class="info_con">
            <img src="{{$sl["icon"]}}">
            <h1>{{$sl["name"]}}（{{$sl["name_long"]}}）</h1>
        </div>
        <div class="season_con">
            <p>{{$season["name"]}} 赛季</p>
            @if(isset($seasons))
            <dl>
                <dt>切换赛季</dt>
                <dd style="display: none;">
                @foreach($seasons as $sea)
                    <a @if($sea == $season["name"]) href="#"  class="on" @else href="/{{$sl["name_en"] . "/"  . $sea}}/" @endif >{{$sea}}赛季</a>
                @endforeach
                </dd>
            </dl>
            @endif
        </div>
    </div>
    @if(isset($articles) && count($articles) > 0 )
        <div class="con_box">
            <div class="header_con">
                <h4>{{$sl["name"]}}资讯</h4>
                <a href="/{{$sl["name_en"]}}/news/">全部{{$sl["name"]}}资讯</a>
            </div>
            <div class="news">
                @foreach($articles as $index=>$article)
                    @if($index < 2)
                        <a target="_blank" href="{{$article["link"]}}" class="img_news">
                            <p class="img_box"><img src="{{$article["cover"]}}"></p>
                            <h3>{{$article["title"]}}</h3>
                        </a>
                    @else
                        <a target="_blank" href="{{$article["link"]}}" class="text_new"><h4>{{$article["title"]}}</h4></a>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
    @if(isset($videos) && count($videos) > 0)
        <div class="con_box">
            <div class="header_con">
                <h4>{{$sl["name"]}}视频</h4>
                <a href="/{{$sl["name_en"]}}/video">{{$sl["name"]}}视频集锦</a>
                <a href="/{{$sl["name_en"]}}/record">{{$sl["name"]}}比赛录像</a>
            </div>
            <div class="video">
                @foreach($videos as $video)
                    <div class="video_item">
                        <a target="_blank" href="{{$video["link"]}}">
                            <p class="img_box"><img src="{{$video["image"]}}"></p>
                            <p class="text_box">{{$video["title"]}}</p>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    @if(isset($data))
        @if($sl["sport"] == 1)
        <div class="con_box">
            <div class="header_con">
                <h4>球员数据</h4>
                <a href="/{{$sl["name_en"]}}/data/">{{$sl["name"]}}详细数据</a>
            </div>
            <div class="player_rank">
                <div class="rank_tab_box">
                    <p class="on" foritem="goal">进球</p>
                    <p foritem="assist">助攻</p>
                    <p foritem="yellow">黄牌</p>
                    <p foritem="red">红牌</p>
                </div>
                @if(isset($data["goal"]))
                    <table class="goal">
                        <colgroup><col width="15%"><col><col width="30%"></colgroup>
                        <tbody>
                        @foreach($data["goal"] as $index=>$goal)
                            <tr>
                                <td class="num">{{$index + 1}}</td>
                                <td>
                                    <p class="name">{{$goal["pname"]}}</p>
                                </td>
                                <td class="score">{{$goal["value"]}}@if($goal["penalty"] > 0)<span>(点球：{{$goal["penalty"]}})</span>@endif</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
                @if(isset($data["assist"]))
                    <table class="assist" style="display: none;">
                        <colgroup><col width="15%"><col><col width="24%"></colgroup>
                        <tbody>
                        @foreach($data["assist"] as $index=>$assist)
                            <tr>
                                <td class="num">{{$index + 1}}</td>
                                <td>
                                    <p class="name">{{$assist["pname"]}}</p>
                                </td>
                                <td class="score">{{$assist["value"]}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
                @if(isset($data["yellow"]))
                    <table class="yellow" style="display: none;">
                        <colgroup><col width="15%"><col><col width="24%"></colgroup>
                        <tbody>
                        @foreach($data["yellow"] as $index=>$yellow)
                            <tr>
                                <td class="num">{{$index + 1}}</td>
                                <td>
                                    <p class="name">{{$yellow["pname"]}}</p>
                                </td>
                                <td class="score">{{$yellow["value"]}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
                @if(isset($data["red"]))
                    <table class="red" style="display: none;">
                        <colgroup><col width="15%"><col><col width="24%"></colgroup>
                        <tbody>
                        @foreach($data["red"] as $index=>$red)
                            <tr>
                                <td class="num">{{$index + 1}}</td>
                                <td>
                                    <p class="name">{{$red["pname"]}}</p>
                                </td>
                                <td class="score">{{$red["value"]}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
        @else
            <div class="con_box">
                <div class="header_con">
                    <h4>球员数据</h4>
                    <a href="/{{$sl["name_en"]}}/data/">{{$sl["name"]}}详细数据</a>
                </div>
                <div class="player_rank">
                    <div class="rank_tab_box">
                        <p class="on" foritem="score">得分</p><p foritem="rebound">篮板</p><p foritem="assist">助攻</p><p foritem="steal">抢断</p><p foritem="cap">盖帽</p>
                    </div>
                    <table class="score">
                        <colgroup><col width="15%"><col><col width="24%"></colgroup>
                        <tbody>
                        @foreach($data["ppg"] as $index=>$ppg)
                        <tr>
                            <td class="num">{{$index+1}}</td>
                            <td>
                                <p class="name">{{$ppg["name"]}}</p>
                                <p class="info">{{$ppg["tname"]}}</p>
                            </td>
                            <td class="score">{{$ppg["ppg"]}}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <table class="rebound" style="display: none;">
                        <colgroup><col width="15%"><col><col width="24%"></colgroup>
                        <tbody>
                        @foreach($data["rpg"] as $index=>$rpg)
                        <tr>
                            <td class="num">{{$index+1}}</td>
                            <td>
                                <p class="name">{{$rpg["name"]}}</p>
                                <p class="info">{{$rpg["tname"]}}</p>
                            </td>
                            <td class="score">{{$rpg["rpg"]}}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <table class="assist" style="display: none;">
                        <colgroup><col width="15%"><col><col width="24%"></colgroup>
                        <tbody>
                        @foreach($data["apg"] as $index=>$apg)
                        <tr>
                            <td class="num">{{$index+1}}</td>
                            <td>
                                <p class="name">{{$apg["name"]}}</p>
                                <p class="info">{{$apg["tname"]}}</p>
                            </td>
                            <td class="score">{{$apg["apg"]}}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <table class="steal" style="display: none;">
                        <colgroup><col width="15%"><col><col width="24%"></colgroup>
                        <tbody>
                        @foreach($data["spg"] as $index=>$spg)
                        <tr>
                            <td class="num">{{$index+1}}</td>
                            <td>
                                <p class="name">{{$spg["name"]}}</p>
                                <p class="info">{{$spg["tname"]}}</p>
                            </td>
                            <td class="score">{{$spg["spg"]}}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <table class="cap" style="display: none;">
                        <colgroup><col width="15%"><col><col width="24%"></colgroup>
                        <tbody>
                        @foreach($data["bpg"] as $index=>$bpg)
                        <tr>
                            <td class="num">{{$index+1}}</td>
                            <td>
                                <p class="name">{{$bpg["name"]}}</p>
                                <p class="info">{{$bpg["tname"]}}</p>
                            </td>
                            <td class="score">{{$bpg["bpg"]}}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endif
</div>