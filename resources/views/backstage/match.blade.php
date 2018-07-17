@extends('backstage.layout.nav')
@section("css")
	<link rel="stylesheet" type="text/css" href="/backstage/css/match.css">
@endsection
@section("content")
	<div id="Content">
		<div class="inner">
			<div id="Tab">
				<a href="/backstage/info">直播信息</a>
				<a class="on">赛事预约</a>
			</div>
			<div class="box">
				<p class="title">新建预约</p>
				<div class="item" id="Match">
					<p class="name">热门比赛：</p>
					<div class="hot on">
						<p class="team">法国</p>
						<p class="vs">vs</p>
						<p class="team">乌拉圭</p>
					</div>
					<div class="hot">
						<p class="team">法国</p>
						<p class="vs">vs</p>
						<p class="team">乌拉圭</p>
					</div>
					<div class="hot">
						<p class="team">法国</p>
						<p class="vs">vs</p>
						<p class="team">乌拉圭</p>
					</div>
					<div class="hot">
						<p class="team">法国</p>
						<p class="vs">vs</p>
						<p class="team">乌拉圭</p>
					</div>
					<div class="hot">
						<p class="team">法国</p>
						<p class="vs">vs</p>
						<p class="team">乌拉圭</p>
					</div>
					<div class="hot">
						<p class="team">法国</p>
						<p class="vs">vs</p>
						<p class="team">乌拉圭</p>
					</div>
					<div class="hot">
						<p class="team">法国</p>
						<p class="vs">vs</p>
						<p class="team">乌拉圭</p>
					</div>
					<div class="hot">
						<p class="team">法国</p>
						<p class="vs">vs</p>
						<p class="team">乌拉圭</p>
					</div>
				</div>
				<div class="item" id="Sreach">
					<p class="name">搜索比赛：</p>
					<div class="choose">
						<span class="on">足球</span>/<span>篮球</span>
					</div>
					<input type="text" name="sreach">
					<div class="hot" style="display: none;">
						<p class="team">法国</p>
						<p class="vs">vs</p>
						<p class="team">乌拉圭</p>
					</div>
					<ul class="list" style="display: none;">
						<li>比利时 vs 克罗地亚</li>
						<li>比利时 vs 克罗地亚</li>
						<li>比利时 vs 克罗地亚</li>
						<li>比利时 vs 克罗地亚</li>
						<li>比利时 vs 克罗地亚</li>
					</ul>
				</div>
				<div class="item">
					<button class="comfirm">确定预约</button>
				</div>
			</div>
			<div class="box">
				<p class="title">我的预约</p>
				<table>
					<colgroup>
						<col width="30%">
						<col width="15%">
						<col width="24%">
						<col>
					</colgroup>
					<tr>
						<td><span class="team">比利时</span><span> VS </span><span class="team">英格兰</span></td>
						<td>世界杯</td>
						<td>2018/10/10&nbsp;&nbsp;18:10</td>
						<td><button>取消预约</button></td>
					</tr>
					<tr>
						<td><span class="team">比利时</span><span> VS </span><span class="team">英格兰</span></td>
						<td>世界杯</td>
						<td>2018/10/10&nbsp;&nbsp;18:10</td>
						<td><button>取消预约</button></td>
					</tr>
					<tr>
						<td><span class="team">比利时</span><span> VS </span><span class="team">英格兰</span></td>
						<td>世界杯</td>
						<td>2018/10/10&nbsp;&nbsp;18:10</td>
						<td><span class="live">比赛中</span></td>
					</tr>
					<tr>
						<td><span class="team">比利时</span><span> VS </span><span class="team">英格兰</span></td>
						<td>世界杯</td>
						<td>2018/10/10&nbsp;&nbsp;18:10</td>
						<td><span>已结束</span></td>
					</tr>
					<tr>
						<td><span class="team">比利时</span><span> VS </span><span class="team">英格兰</span></td>
						<td>世界杯</td>
						<td>2018/10/10&nbsp;&nbsp;18:10</td>
						<td><span>已结束</span></td>
					</tr>
					<tr>
						<td><span class="team">比利时</span><span> VS </span><span class="team">英格兰</span></td>
						<td>世界杯</td>
						<td>2018/10/10&nbsp;&nbsp;18:10</td>
						<td><span>已结束</span></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
@endsection
@section("js")
<script type="text/javascript">
	window.onload = function () { //需要添加的监控放在这里
		
	}
</script>
@endsection













