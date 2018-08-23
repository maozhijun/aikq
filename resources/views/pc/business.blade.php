@extends('pc.layout.base')
@section("meta")
	<meta name="robots"content="nofollow">
@endsection
@section('css')
	<link rel="stylesheet" type="text/css" href="{{env('CDN_URL')}}/css/pc/home.css?time=20180203030006">
	<style type="text/css">
		table.list thead th{height: 30px; font-size: 12px; line-height: 30px;}
		table.list tbody th{height: 20px; font-size: 12px; line-height: 20px;}
		table.list tbody td{height: 30px; font-size: 12px; line-height: 30px;}
		table.list tbody td:nth-child(1) p{height: 20px; font-size: 12px; line-height: 20px;}
		table.list tbody tr:nth-child(even) td{background: #f2f8fd;}
		table.list tbody tr:nth-child(odd) td{background: #f9efed;}
	</style>
@endsection
@section('content')
	<div id="Content">
		<div class="inner">
			<table class="list" id="TableHead">
				<col number="1" width="50px">
				<col number="2" width="80px">
				<col number="3" width="70px">
				<col number="4" width="16%">
				<col number="5" width="12px">
				<col number="6" width="26px">
				<col number="7" width="12px">
				<col number="8" width="16%">
				<col number="9" width="300px">
				<col number="10" width="60px">
				<thead>
				<tr>
					<th>项目</th>
					<th>赛事</th>
					<th>时间</th>
					<th colspan="5">对阵</th>
					<th>接入地址</th>
					<th>状态</th>
				</tr>
				</thead>
				<tbody>
				<tr><th colspan="10"></th></tr>
				</tbody>
			</table>
			<table class="list" id="Show">
				<col number="1" width="50px">
				<col number="2" width="80px">
				<col number="3" width="70px">
				<col number="4" width="16%">
				<col number="5" width="12px">
				<col number="6" width="26px">
				<col number="7" width="12px">
				<col number="8" width="16%">
				<col number="9" width="300px">
				<col number="10" width="60px">
				<thead>
				<tr>
					<th>项目</th>
					<th>赛事</th>
					<th>时间</th>
					<th colspan="5">对阵</th>
					<th>接入地址</th>
					<th>状态</th>
				</tr>
				</thead>
				<tbody>
                <?php $bj = 0;
                ?>
				@foreach($matches as $time=>$match_array)
                    <?php
                    $week = date('w', strtotime($time));
                    ?>
					<tr class="date">
						<th colspan="10">{{date_format(date_create($time),'Y年m月d日')}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$week_array[$week]}}</th>
					</tr>
					@foreach($match_array as $match)
						@component('pc.cell.business_match_cell',['match'=>$match])
						@endcomponent
					@endforeach
				@endforeach
				</tbody>
			</table>
		</div>
	</div>
	{{--<div class="adflag left">--}}
	{{--<button class="close" onclick="document.body.removeChild(this.parentNode)"></button>--}}
	{{--<a><img src="/img/pc/ad/double.jpg"></a>--}}
	{{--<br>--}}
	{{--<a href="http://91889188.87.cn" target="_blank"><img src="/img/pc/ad_zhong_double.jpg"></a>--}}
	{{--</div>--}}
	{{--<div class="adflag right">--}}
	{{--<button class="close" onclick="document.body.removeChild(this.parentNode)"></button>--}}
	{{--<a href="http://91889188.87.cn" target="_blank"><img src="/img/pc/ad_zhong_double.jpg"></a>--}}
	{{--<br>--}}
	{{--<a><img src="/img/pc/ad/double.jpg"></a>--}}
	{{--</div>--}}
@endsection
@section('js')
	<script type="text/javascript" src="{{env('CDN_URL')}}/js/public/pc/home.js?time=2018012619310003"></script>
	<script type="text/javascript">
        window.onload = function () { //需要添加的监控放在这里
            setADClose();
            setPage();
            Copy();
        }
	</script>
@endsection