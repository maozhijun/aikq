@extends('pc.team.v2.base')
@section('detail')
    <div id="Tab_con">
        <p class="on"><a href="#">综合</a></p>
        <p><a href="/{{$zhuanti['name_en']}}/team{{$tid}}_news_1.html">资讯</a></p>
        <p><a href="/{{$zhuanti['name_en']}}/team{{$tid}}_video_1.html">视频</a></p>
        <p><a href="/{{$zhuanti['name_en']}}/team{{$tid}}_record_1.html">录像</a></p>
    </div>
    <div id="News_Video">
        @if(isset($articles) && count($articles) > 0)
        <div class="news_con">
            @for($i = 0 ; $i < min(1,count($articles)) ; $i++)
                <?php
                $article = $articles[$i];
                ?>
                <a href="{{$article['link']}}" class="img_con">
                    <img src="{{$article['cover']}}">
                    <p>{{$article['title']}}</p>
                </a>
            @endfor
            <div class="list_con">
                @for($i = 1 ; $i < count($articles) ; $i++)
                    <?php
                    $article = $articles[$i];
                    ?>
                    @if($i == 1)
                        <a href="{{$article['link']}}" class="h3"><h3>{{$article['title']}}</h3></a>
                    @else
                        <a href="{{$article['link']}}">{{$article['title']}}</a>
                    @endif
                @endfor
            </div>
        </div>
        @endif
        @if(isset($videos) && count($videos) > 0)
            <div class="video_con">
                @foreach($videos as $video)
                    <div class="item_con">
                        <a href="{{\App\Models\Match\HotVideo::getVideoDetailUrl($video->id)}}">
                            <p class="img_box"><img src="{{$video['image']}}"></p>
                            <p class="title_con">{{$video['title']}}</p>
                            <p class="other_info">{{date('m-d', date_create($video['time'])->getTimestamp())}}</p>
                            <p class="tag_list">
                                <?php
                                $tags = \App\Models\Tag\TagRelation::getTagWithSids(\App\Models\Tag\TagRelation::kTypeVideo,$video['id']);
                                ?>
                                @foreach($tags as $key=>$tag)
                                    <span>{{$tag['name']}}</span>
                                @endforeach
                            </p>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    <div class="el_con">
        <div class="header">
            <h3><p>历史战绩</p></h3>
        </div>
        <div class="schedule_con">
            <table class="match">
                <col width="12%"><col width="16.6%"><col><col width="12%"><col><col width="16%">
                @if(isset($lives['recent']) && count($lives['recent']) > 0)
                    @foreach($lives['recent'] as $match)
                        <?php
                        $liveUrl = \App\Http\Controllers\PC\CommonTool::getLiveDetailUrl($match['sport'], $match['lid'], $match['mid']);
                        $fv = \App\Models\Subject\SubjectVideo::firstVideo($match['mid']);
                        ?>
                        <tr>
                            <td><span>{{$match['lname']}}</span></td>
                            <td><span>{{date('Y-m-d', $match['time'])}}</span><br/>{{date('H:i', $match['time'])}}</td>
                            @if(isset($match['hid']) && $match['hid'] != $team['id'])
                                <td class="host"><a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match['sport'], $match['lid'], $match['hid'])}}">{{$match['hname']}}</a></td>
                            @else
                                <td class="host"><a href="#">{{$match['hname']}}</a></td>
                            @endif
                            @if($match['status'] < 0)
                                <td class="vs">{{$match['hscore']}} - {{$match['ascore']}}</td>
                            @else
                                <td class="vs">vs</td>
                            @endif
                            @if(isset($match['aid']) && $match['aid'] != $team['id'])
                                <td class="away"><a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match['sport'], $match['lid'], $match['aid'])}}">{{$match['aname']}}</a></td>
                            @else
                                <td class="away"><a href="#">{{$match['aname']}}</a></td>
                            @endif
                            @if($match['status'] >= 0)
                                @foreach($match['channels'] as $c_index=>$channel)
                                    @continue($c_index)
                                    <td class="line"><a class="live" target="_blank" href="{{$liveUrl}}#btn={{$c_index}}">{{$channel['name']}}</a></td>
                                @endforeach
                            @elseif(isset($fv))
                                <td class="line"><a class="live" target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getRecordDetailUrl('nba',$match['mid'])}}">全场录像</a></td>
                            @else
                                <td class="line"></td>
                            @endif
                        </tr>
                    @endforeach
                @endif
            </table>
        </div>
    </div>
    <div class="el_con">
        <div class="header">
            <h3><p>未来赛程</p></h3>
        </div>
        <div class="schedule_con">
            <table class="match">
                <col width="10%"><col width="10%"><col><col width="11%"><col><col width="40%">
                @if(isset($lives['schedule']) && count($lives['schedule']) > 0)
                    @foreach($lives['schedule'] as $match)
                        <?php
                        $liveUrl = \App\Http\Controllers\PC\CommonTool::getLiveDetailUrl($match['sport'], $match['lid'], $match['mid']);
                        $fv = \App\Models\Subject\SubjectVideo::firstVideo($match['mid']);
                        ?>
                        <tr>
                            <td><span>{{$match['lname']}}</span></td>
                            <td><span>{{date('Y-m-d', $match['time'])}}</span><br/>{{date('H:i', $match['time'])}}</td>
                            @if(isset($match['hid']) && $match['hid'] != $team['id'])
                                <td class="host"><a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match['sport'], $match['lid'], $match['hid'])}}">{{$match['hname']}}</a></td>
                            @else
                                <td class="host"><a href="">{{$match['hname']}}</a></td>
                            @endif
                            @if($match['status'] < 0)
                                <td class="vs"><span class="living">直播中</span></td>
                            @else
                                <td class="vs">vs</td>
                            @endif
                            @if(isset($match['aid']) && $match['aid'] != $team['id'])
                                <td class="away"><a href="{{\App\Http\Controllers\PC\CommonTool::getTeamDetailUrl($match['sport'], $match['lid'], $match['aid'])}}">{{$match['aname']}}</a></td>
                            @else
                                <td class="away"><a href="">{{$match['aname']}}</a></td>
                            @endif
                            @if($match['status'] >= 0)
                                @foreach($match['channels'] as $c_index=>$channel)
                                    @continue($c_index)
                                    <td class="line"><a class="live" target="_blank" href="{{$liveUrl}}#btn={{$c_index}}">{{$channel['name']}}</a></td>
                                @endforeach
                                @if(0 == count($match['channels']))
                                    <td class="line"></td>
                                @endif
                            @elseif(isset($fv))
                                <td class="line"><a class="live" target="_blank" href="{{\App\Http\Controllers\PC\CommonTool::getVideosDetailUrlByPc($fv['s_lid'], $fv['id'], 'video')}}">全场录像</a></td>
                            @else
                                <td class="line"></td>
                            @endif
                        </tr>
                    @endforeach
                @endif
            </table>
        </div>
    </div>
@endsection
@section('right')
    <div class="con_box">
        <div class="header_con">
            <h4>球队队员</h4>
        </div>
        <table class="right_table">
            <col width="25%"><col width="25%"><col>
            <tr>
                <th>位置</th>
                <th>号码</th>
                <th class="name">姓名</th>
            </tr>
            @if(isset($team['lineup']) && count($team['lineup']) > 0)
                @foreach($team['lineup'] as $lineup)
                    <?php if (str_contains($lineup['position'], "教练")) $coach = $lineup['name']; ?>
                    <tr>
                        <td>{{$lineup['position']}}</td>
                        <td>{{strlen($lineup['num']) > 0 ? $lineup['num'] : '-'}}</td>
                        <td class="name">{{$lineup['name']}}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td>-</td>
                    <td>-</td>
                    <td><p>-</p></td>
                </tr>
            @endif
        </table>
    </div>
    @if(isset($rank) && count($rank) > 0)
        @include('pc.team.detail_rank_cell', ['ranks'=>$rank, 'subject'=>$league ])
    @endif
@endsection