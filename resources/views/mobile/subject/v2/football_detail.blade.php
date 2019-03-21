@extends('mobile.subject.v2.base_detail')

@section('content')
    @include('mobile.subject.v2.cell.foot.foot_info_cell', ['sl'=>$sl, 'seasons'=>$seasons, 'hasKnockout'=>false])
    @include('mobile.subject.v2.cell.foot.foot_rank_cell', ['ranks'=>isset($ranks)?$ranks:null, 'display'=>''])
    @include('mobile.subject.v2.cell.foot.foot_match_cell', ['schedules'=>isset($schedules)?$schedules:null, 'curRound'=>$curRound])
    @include('mobile.subject.v2.cell.foot.foot_player_cell', ['players'=>isset($players)?$players:null])
    @include('mobile.subject.v2.cell.news_cell', ['articles'=>isset($articles)?$articles:null])
    @include('mobile.subject.v2.cell.video_cell', ['videos'=>isset($videos)?$videos:null])
@stop