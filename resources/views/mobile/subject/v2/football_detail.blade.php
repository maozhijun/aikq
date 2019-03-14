@extends('mobile.subject.v2.base_detail')

@section('content')
    @include('mobile.subject.v2.cell.info_cell', ['sl'=>$sl, 'seasons'=>$seasons])
    @include('mobile.subject.v2.cell.knockout_cell')
    @include('mobile.subject.v2.cell.rank_cell', ['ranks'=>isset($ranks)?$ranks:null])
    @include('mobile.subject.v2.cell.match_cell', ['lives'=>isset($lives)?$lives:null])
    @include('mobile.subject.v2.cell.player_cell')
    @include('mobile.subject.v2.cell.news_cell', ['articles'=>isset($articles)?$articles:null])
    @include('mobile.subject.v2.cell.video_cell', ['videos'=>isset($videos)?$videos:null])
@stop