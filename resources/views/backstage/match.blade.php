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
						<col width="11%">
						<col width="15%">
						<col width="17%">
						<col width="12%">
						<col width="17%">
						<col>
					</colgroup>
					@if(isset($tags))
						@foreach($tags as $tag)
                            <?php $match = $tag->match; ?>
							<tr>
								<td><span class="team">{{$match->hname}}</span><span> VS </span><span class="team">{{$match->aname}}</span></td>
								<td>{{$match->win_lname}}</td>
								<td>{{date('Y/m/d H:i', strtotime($tag->match_time))}}</td>
								<td>
									<div class="color">
										<div class="choose">
											<div class="in" tid="{{$tag->id}}" home="1" @if(!empty($tag->h_color)) style="background: {{$tag->h_color}}; border: 1px solid {{$tag->h_color}};" @endif ></div>
											<div class="box"><!--加个“show”类就能显示-->
												<p>{{$match->hname}}球衣颜色</p>
												<span><i style="background: #c5001a;"></i></span>
												<span><i style="background: #e37b12;"></i></span>
												<span><i style="background: #f6dc00;"></i></span>
												<span><i style="background: #18b602;"></i></span>
												<span><i style="background: #0600e1;"></i></span>
												<span><i style="background: #AC00CB;"></i></span>
												<span><i style="background: #262626;"></i></span>
												<span><i style="background: #ffffff;"></i></span>
											</div>
										</div>
										<p>主队球衣</p>
									</div>
									<div class="color">
										<div class="choose">
											<div class="in" tid="{{$tag->id}}" home="2" @if(!empty($tag->h_color)) style="background: {{$tag->a_color}}; border: 1px solid {{$tag->a_color}};" @endif ></div>
											<div class="box">
												<p>{{$match->aname}}球衣颜色</p>
												<span><i style="background: #c5001a;"></i></span>
												<span><i style="background: #e37b12;"></i></span>
												<span><i style="background: #f6dc00;"></i></span>
												<span><i style="background: #18b602;"></i></span>
												<span><i style="background: #0600e1;"></i></span>
												<span><i style="background: #AC00CB;"></i></span>
												<span><i style="background: #262626;"></i></span>
												<span><i style="background: #ffffff;"></i></span>
											</div>
										</div>
										<p>客队球衣</p>
									</div>
								</td>
								<td>
									<p class="sh_hi">
										<button tid="{{$tag->id}}" class="show @if($tag->show_score == 1) on @endif ">显示</button>
										<button tid="{{$tag->id}}" class="hide @if($tag->show_score == 0) on @endif ">隐藏</button>
									</p>
									<p>对阵</p>
								</td>
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
        $(".color .box").removeClass("show");
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


    //设置球衣颜色事件
	$(".color .in").click(function () {
        stopPro(this);

		var $this = $(this);
		$(".color .box").removeClass("show");
		$this.next().addClass("show");
    });

	//设置颜色选择事件
	$(".color .box span").click(function () {
        stopPro(this);

		var $this = $(this);
		var $in = $this.parent().prev();
		var id = $in.attr("tid");
		var home = $in.attr("home");

		var color = $this.find('i')[0].style.background;
        $.ajax({
            "url": "/backstage/matches/team/color",
            "dataType": "json",
            "data": {"id": id, "color": color, "home": home},
            "success": function (json) {
                if (json.code == 200) {
                    $(".color .box").removeClass("show");
                    var style = $in[0].style;
                    style.background = color;
                    style.border = "1px solid " + color;
				}
                alert(json.message);
            },
            "error": function () {
                $(".color .box").removeClass("show");
                alert("设置球衣颜色失败");
            }
        });
    });

	/**
	 * 隐藏显示对阵
	 */
	$(".sh_hi button").click(function () {
		var $this = $(this);
		var className = $this.attr("class");
		if (className.indexOf("on") > -1) {
		    return;
		}
		var id = $this.attr("tid");
		var type = className.replace("on", "");
        type = $.trim(type);
        var msg = type == "show" ? "显示" : "隐藏";

		$.ajax({
			"url": "/backstage/matches/score/set",
			"dataType": "json",
			"data": {"id": id, "type": type},
			"success": function (json) {
				alert(json.message);
				if (json.code == 200) {
                    $this.parent().find("button").removeClass("on");
                    $this.addClass("on");
				}
            },
			"error": function () {
				alert(msg + "失败");
            }
		});
    });

    /**
	 * 取消事件冒泡
     * @param obj
     */
	function stopPro(obj) {
        if(obj.stopPropagation){
            obj.stopPropagation();//W3C取消冒泡事件
        }else{
            //IE取消冒泡事件
            window.event.cancelBubble = true;
        }
    }
</script>
@endsection













