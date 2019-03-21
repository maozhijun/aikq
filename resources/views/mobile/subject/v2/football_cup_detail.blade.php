@extends('mobile.subject.v2.base_detail')

@section('content')
    @include('mobile.subject.v2.cell.foot.foot_info_cell', ['sl'=>$sl, 'seasons'=>$seasons, 'hasKnockout'=>isset($knockout)])
    @include('mobile.subject.v2.cell.foot.foot_knockout_cell', ['knockout'=>isset($knockout)?$knockout:null])
    @include('mobile.subject.v2.cell.foot.foot_rank_cell', ['ranks'=>isset($ranks)?$ranks:null, 'display'=>isset($knockout)?'none':''])
    @include('mobile.subject.v2.cell.foot.foot_cup_match_cell', ['schedules'=>isset($schedules)?$schedules:null])
    @include('mobile.subject.v2.cell.foot.foot_player_cell', ['players'=>isset($players)?$players:null])
    @include('mobile.subject.v2.cell.news_cell', ['articles'=>isset($articles)?$articles:null])
    @include('mobile.subject.v2.cell.video_cell', ['videos'=>isset($videos)?$videos:null])
@stop