@extends('mobile.fifa.base')
@section('content')
    <div id="Topbar">
        <a href="https://www.aikq.cc/m/">直播</a>
        <a href="https://www.aikq.cc/m/live/subject/videos/all/1.html">录像</a>
        <a href="https://shop.liaogou168.com/article/recommends">推荐</a>
        <a class="on">世界杯</a>
    </div>
    <div id="roundTab">
        @foreach($stages as $stage)
            <?php
            if(isset($stage['groupMatch'])){
                $id = 'group';
            }
            else{
                $id = $stage['id'];
            }
            ?>
            <p @if($stage['status'] == 1)class="on"@endif for="{{$id}}">{{$stage['name']}}</p>
        @endforeach
    </div>
    @foreach($stages as $stage)
        <?php
        if(isset($stage['groupMatch'])){
            $id = 'group';
        }
        else{
            $id = $stage['id'];
        }
        ?>
        @if(isset($stage['groupMatch']))
            <?php
            $groups = $stage['groupMatch'];
            ?>
            <div id="{{$id}}" class="matchList" @if($stage['status'] == 0)style="display: none" @endif>
                <div class="groupBox">
                    @foreach($groups as $key=>$group)
                        <p for="{{$key}}" @if($key == 'A')class="on"@endif><span>{{$key}}组</span></p>
                    @endforeach
                </div>
                @foreach($groups as $key=>$group)
                    <div class="matchList" id="{{$key}}" @if($key != 'A')style="display: none;"@endif>
                        <?php
                        //比赛状态分类
                        $notStart = array();
                        $ing = array();
                        $end = array();
                        foreach ($group['matches'] as $match){
                            if ($match['status'] == 0){
                                $notStart[] = $match;
                            }
                            elseif ($match['status'] == -1){
                                $end[] = $match;
                            }
                            else{
                                $ing[] = $match;
                            }
                        }
                        ?>
                        <table>
                            <thead>
                            <tr>
                                <th>排名</th>
                                <th>球队</th>
                                <th>赛</th>
                                <th>胜/平/负</th>
                                <th>进/失</th>
                                <th>净</th>
                                <th>积分</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $scores = $group['scores'];
                            ?>
                            @for($i = 0 ; $i < count($scores) ; $i++)
                                <?php
                                $score = $scores[$i];
                                ?>
                                <tr>
                                    <td>{{$i+1}}</td>
                                    <td>{{$score['tname']}}</td>
                                    <td>{{$score['count']}}</td>
                                    <td>{{$score['win']}}/{{$score['draw']}}/{{$score['lose']}}</td>
                                    <td>{{$score['goal']}}/{{$score['fumble']}}</td>
                                    <td>{{$score['goal'] - $score['fumble']}}</td>
                                    <td>{{$score['score']}}</td>
                                </tr>
                            @endfor
                            </tbody>
                        </table>
                        <div class="matches_div">
                            <div class="separated live" style="display: none;">比赛中</div>
                            @foreach($ing as $match)
                                @component('mobile.fifa.match_cell',['match'=>$match])
                                @endcomponent
                            @endforeach
                        </div>
                        <div class="matches_div">
                            <div class="separated noStart" style="display: none;">未开始</div>
                            @foreach($notStart as $match)
                                @component('mobile.fifa.match_cell',['match'=>$match])
                                @endcomponent
                            @endforeach
                        </div>
                        <div class="matches_div">
                            <div class="separated end" style="display: none;">已结束</div>
                            @foreach($end as $match)
                                @component('mobile.fifa.match_cell',['match'=>$match])
                                @endcomponent
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="matchList" id="{{$id}}" @if($stage['status'] == 0)style="display: none" @endif>
                <?php
                $matches = $stage['matches'];
                //比赛状态分类
                $notStart = array();
                $ing = array();
                $end = array();
                foreach ($matches as $match){
                    if ($match['status'] == 0){
                        $notStart[] = $match;
                    }
                    elseif ($match['status'] == -1){
                        $end[] = $match;
                    }
                    else{
                        $ing[] = $match;
                    }
                }
                ?>
                <div class="matches_div">
                    <div class="separated live" style="display: none;">比赛中</div>
                    @foreach($ing as $match)
                        @component('mobile.fifa.match_cell',['match'=>$match])
                        @endcomponent
                    @endforeach
                </div>
                <div class="matches_div">
                    <div class="separated noStart" style="display: none;">未开始</div>
                    @foreach($notStart as $match)
                        @component('mobile.fifa.match_cell',['match'=>$match])
                        @endcomponent
                    @endforeach
                </div>
                <div class="matches_div">
                    <div class="separated end" style="display: none;">已结束</div>
                    @foreach($end as $match)
                        @component('mobile.fifa.match_cell',['match'=>$match])
                        @endcomponent
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach
    @component('mobile.fifa.base_bottom',['index'=>0])
    @endcomponent
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/fifa/home.css">
    <link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/mobile/fifa/matchList.css">
    <style>
        #roundTab{
            background: url({{env('CDN_URL')}}/img/mobile/fifa/image_bg_nav_n.jpg) no-repeat center; background-size: cover;!important;
        }
        #group table{
            background: url({{env('CDN_URL')}}/img/mobile/fifa/image_bg_nav_n_copy.jpg) no-repeat center; background-size: cover;!important;
        }
        #group table tr:nth-child(1) td:first-child, #group table tr:nth-child(2) td:first-child{
            background: url({{env('CDN_URL')}}/img/mobile/fifa/icon_number_n.png) no-repeat center;background-size: 50px!important;
        }
        .separated.live:before, .separated.live:after{
            background: url({{env('CDN_URL')}}/img/mobile/fifa/icon_live_n.png) no-repeat center;background-size: 40px !important;
        }
        .separated.noStart:before, .separated.noStart:after{
            background: url({{env('CDN_URL')}}/img/mobile/fifa/icon_future_n.png) no-repeat center;background-size: 40px !important;
        }
        .separated.end:before, .separated.end:after{
            background: url({{env('CDN_URL')}}/img/mobile/fifa/icon_end_n.png) no-repeat center; background-size: 40px!important;
        }
    </style>
@endsection
@section('js')
    <script src="{{env('CDN_URL')}}/js/public/mobile/fifa/home.js?time=201803030002"></script>
    <script type="text/javascript">
        var divs = $('div.matches_div');
        for (var i = 0 ; i < divs.length ; i++){
            if (divs[i].getElementsByTagName('a').length > 0){
                $(divs[i]).find('div.separated')[0].style['display'] = '';
            }
            else{
                $(divs[i]).find('div.separated')[0].style['display'] = 'none';
            }
        }
    </script>
@endsection