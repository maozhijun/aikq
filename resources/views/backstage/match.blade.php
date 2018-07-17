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
				{{--<div class="item" id="Match">--}}
					{{--<p class="name">热门比赛：</p>--}}
					{{--<div class="hot on">--}}
						{{--<p class="team">法国</p>--}}
						{{--<p class="vs">vs</p>--}}
						{{--<p class="team">乌拉圭</p>--}}
					{{--</div>--}}
					{{--<div class="hot">--}}
						{{--<p class="team">法国</p>--}}
						{{--<p class="vs">vs</p>--}}
						{{--<p class="team">乌拉圭</p>--}}
					{{--</div>--}}
					{{--<div class="hot">--}}
						{{--<p class="team">法国</p>--}}
						{{--<p class="vs">vs</p>--}}
						{{--<p class="team">乌拉圭</p>--}}
					{{--</div>--}}
					{{--<div class="hot">--}}
						{{--<p class="team">法国</p>--}}
						{{--<p class="vs">vs</p>--}}
						{{--<p class="team">乌拉圭</p>--}}
					{{--</div>--}}
					{{--<div class="hot">--}}
						{{--<p class="team">法国</p>--}}
						{{--<p class="vs">vs</p>--}}
						{{--<p class="team">乌拉圭</p>--}}
					{{--</div>--}}
					{{--<div class="hot">--}}
						{{--<p class="team">法国</p>--}}
						{{--<p class="vs">vs</p>--}}
						{{--<p class="team">乌拉圭</p>--}}
					{{--</div>--}}
					{{--<div class="hot">--}}
						{{--<p class="team">法国</p>--}}
						{{--<p class="vs">vs</p>--}}
						{{--<p class="team">乌拉圭</p>--}}
					{{--</div>--}}
					{{--<div class="hot">--}}
						{{--<p class="team">法国</p>--}}
						{{--<p class="vs">vs</p>--}}
						{{--<p class="team">乌拉圭</p>--}}
					{{--</div>--}}
				{{--</div>--}}
				<div class="item" id="Sreach">
					<p class="name">搜索比赛：</p>
					<div class="choose">
						<span class="on" sport="1">足球</span>/<span sport="2">篮球</span>
					</div>
					<input type="text" name="search">
					<div id="select" class="hot" style="display: none;" mid="">
						<p class="team">-</p>
						<p class="vs">vs</p>
						<p class="team">-</p>
					</div>
					<ul class="list" style="display: none;"></ul>
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
					@if(isset($tags))
						@foreach($tags as $tag)
                            <?php $match = $tag->match; ?>
							<tr>
								<td><span class="team">{{$match->hname}}</span><span> VS </span><span class="team">{{$match->aname}}</span></td>
								<td>{{$match->win_lname}}</td>
								<td>{{date('Y/m/d H:i', strtotime($tag->match_time))}}</td>
								@if($match->status == -1)
									<td><span>已结束</span></td>
								@elseif($match->status == 0)
									<td><button onclick="cancelBook('{{$tag->id}}', this);">取消预约</button></td>
								@elseif($match->status > 0)
									<td><span class="live">比赛中</span></td>
								@endif

							</tr>
						@endforeach
					@endif
				</table>
			</div>
		</div>
	</div>
@endsection
@section("js")
<script type="text/javascript">
	window.searching = false;
    /**
     * 异步表单验证
     */
    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
    });

	window.onload = function () { //需要添加的监控放在这里
		$(".choose span").click(function () {
            $(".choose span").removeClass("on");
            this.className = "on";
        });
	}

	$(".comfirm").click(function () {//确定预约
		var $select = $("#select");
        var sport = $(".choose span.on").attr("sport");
		var mid = $select.attr("mid");
		if (mid == "") {
		    alert("请先选择赛事");
		    return;
		}
		if (!confirm("是否确认预约比赛？")) {
		    return;
		}
		$.ajax({
			"url": "/backstage/matches/book",
			"type": "post",
			"data": {"mid": mid, "sport": sport},
			"dataType": "json",
			"success": function (json) {
				alert(json.message);
				if (json.code == 200) {
				    location.reload();
				}
            },
			"error": function () {
				alert("预约比赛失败");
            }
		});
		//var thisBtn = this;
    });

	function findMatches(input) {
		var sport = $(".choose span.on").attr("sport");
		var search = input.value;
		if (!window.searching) {
            window.searching = true;
            $.ajax({
                "url": "/backstage/matches/find",
                "type": "post",
                "dataType": "json",
                "data": {"sport": sport, "search": search},
                "success": function (json) {
                    var html = "";
                    if (json.code == 200) {
                        var matches = json.matches;
                        $.each(matches, function (index, match) {
                            var mid = match.mid;
                            var hname = match.hname;
                            var aname = match.aname;
                            var time = match.time;
                            var time = time.substr(5, 11);
                            html += "<li onclick='selectMatch(" + mid + ", \"" + hname + "\", \""+ aname +"\")' mid='" + mid + "'>" + hname + " VS " + aname + "(" + time+ ")" + "</li>";
                        });
                        $("ul.list").html(html).show();
                    } else {
                        $("ul.list").html(html);
                        alert(json.message);
                    }
                    window.searching = false;
                },
                "error": function () {
                    window.searching = false;
                }
            });
		}
    }

    function selectMatch(mid, hname, aname) {
        $("#select p.team:first").html(hname);
        $("#select p.team:last").html(aname);
        $("#select").attr("mid", mid).show();
        $("ul.list").hide();
    }

    $("body").click(function () {
        $("ul.list").hide();
    });

	$("input[name=search]").bind("click", function (event) {
		if ($("ul.list li").length > 0) {
            $("ul.list").show();
		}
        if(this.stopPropagation){
            //W3C取消冒泡事件
            this.stopPropagation();
        }else{
            //IE取消冒泡事件
            window.event.cancelBubble = true;
        }
    }).keyup(function () {
        var search = this.value;
        //var reg = /[\u4E00-\u9FA5]+/;|| reg.test(search)
        if (/^[\u4E00-\u9FA5|A-Z]+$/.test(search)) {
            findMatches(this);
        }
    });

    /**
	 * 取消比赛预约
     * @param id
     * @param btn
     */
	function cancelBook(id, btn) {
	    var $parent = $(btn).parent().parent();
	    var hname = $parent.find(".team:first").html();
        var aname = $parent.find(".team:last").html();
        var time = $parent.find("td:eq(2)").html();
        var msg = "《" + hname + " VS " + aname + " " + time + "》";
		if (!confirm("是否确定取消" + msg + "的比赛预约？")) {
		    return;
		}
		$.ajax({
			"url": "/backstage/matches/book/cancel",
			"type": "post",
			"dataType": "json",
			"data": {"id": id},
			"success": function (json) {
				alert(json.message);
				if (json.code == 200) {
				    location.reload();
				}
            },
			"error": function () {
				alert("取消预约失败");
            }
		});
    }
</script>
@endsection













