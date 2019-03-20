@extends('mobile.subject.v2.base_detail')

@section('content')
    @include('mobile.subject.v2.cell.basket.basket_info_cell', ['sl'=>$sl, 'season'=>$season, 'seasons'=>$seasons, 'hasPlayoff'=>isset($playoff)])
    @include('mobile.subject.v2.cell.basket.basket_playoff_cell', ['playoff'=>isset($playoff)?$playoff:null])
    @include('mobile.subject.v2.cell.basket.basket_rank_cell', ['ranks'=>isset($ranks)?$ranks:null])
    @include('mobile.subject.v2.cell.match_cell', ['lives'=>isset($lives)?$lives:null])
    @include('mobile.subject.v2.cell.basket.basket_player_cell', ['players'=>$players])
    @include('mobile.subject.v2.cell.news_cell', ['articles'=>isset($articles)?$articles:null])
    @include('mobile.subject.v2.cell.video_cell', ['videos'=>isset($videos)?$videos:null])
@stop