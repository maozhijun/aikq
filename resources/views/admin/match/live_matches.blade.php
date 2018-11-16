@extends('admin.layout.nav')
@section("css")
    <link href="/css/admin/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
    <style>
        select {
            height: 26px;
        }

        .table tr td div {
            margin-bottom: 3px;
            padding-top: 8px;
            padding-bottom: 5px;
        }

        .table tr td div:nth-child(odd) {
            /*background: #C0C0C0;*/
        }

    </style>
@endsection
@section('content')
    <h1 class="page-header">{{$sport == 1 ? '足球' : '篮球'}}比赛直播设置</h1>

    <div class="row">
        <div class="table-responsive">
            <div style="text-align: left">
                <form action="/admin/live/matches{{$sport == 2 ? '/basketball' : ''}}">
                    <label>赛事名称：</label>
                    <input type="text" name="l_name" value="{{ request('l_name', '') }}">
                    &nbsp;
                    <label>球队名称：</label>
                    <input type="text" name="t_name" value="{{ request('t_name', '') }}">
                    &nbsp;
                    <label>是否有直播：</label>
                    <select name="has_live" style="height: 26px;">
                        <option value="">全部</option>
                        <option value="1" @if(request('has_live') == 1) selected @endif>有</option>
                        <option value="2" @if(request('has_live') == 2) selected @endif>无</option>
                    </select>
                    &nbsp;
                    <label>赛事类型：</label>
                    <select name="type" style="height: 26px;">
                        <option value="">全部</option>
                        <option value="1" @if(request('type') == 1) selected @endif>竞彩</option>
                        @if($sport == 1) <option value="2" @if(request('type') == 2) selected @endif>精简</option> @endif
                    </select>
                    &nbsp;
                    <label>比赛状态：</label>
                    <select name="status" style="height: 26px;">
                        <option value="">全部</option>
                        <option value="1" @if(request('status') == 1) selected @endif>未开始</option>
                        <option value="2" @if(request('status') == 2) selected @endif>进行中</option>
                        <option value="3" @if(request('status') == 3) selected @endif>已结束</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary">搜索</button>
                    @if($sport == 1)
                        {{--<a class="btn btn-sm btn-warning" target="_blank" href="http://match.liaogou168.com/api/spider/ballbar/spiderLiveData">抓取BallBar</a>--}}
                        {{--<a class="btn btn-sm btn-warning" target="_blank" href="http://match.liaogou168.com/api/spider/ss365/spider365">抓取ss365</a>--}}
                        <a class="btn btn-sm btn-warning" target="_blank" href="/api/spider/ttzb/spiderLiveData">抓取天天直播</a>
                    @elseif($sport == 2)
                        {{--<a class="btn btn-sm btn-warning" target="_blank" href="http://match.liaogou168.com/api/spider/ballbar/spiderBasketLiveData">抓取BallBar</a>--}}
                        {{--<a class="btn btn-sm btn-warning" target="_blank" href="http://match.liaogou168.com/api/spider/ss365/spider365BasketBall">抓取ss365</a>--}}
                        <a class="btn btn-sm btn-warning" target="_blank" href="/api/spider/ttzb/spiderBasketLiveData">抓取天天直播</a>
                    @endif
                    {{--<a class="btn btn-sm btn-success" onclick="randomCode(this);">高清验证码</a><input class="form-control" style="width:99px;height: 30px;display: inline;margin-left: 5px;vertical-align: bottom;" readonly value="{{$ch_code or '暂无验证码'}}">--}}
                </form>
            </div>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>比赛信息</th>
                    <th width="85%">直播地址</th>
                    {{--<th width="100px;">操作</th>--}}
                </tr>
                </thead>
                <tbody>
                @foreach($matches as $match)
                    <?php
                        $impt = false;
                        if (isset($match->live_id)) {
                            $live = \App\Models\Match\MatchLive::query()->find($match->live_id);
                            if (isset($live)) {
                                $impt = $live->impt == 2;
                            }
                        }
                        $channels = \App\Models\Match\MatchLive::liveChannels($match->live_id);
                    ?>
                    <tr>
                        <td>
                            <p style="color: red;">设置重点：<input id="impt_{{$match->id}}" onchange="changeImp('{{$match->id}}');" @if($impt) checked @endif type="checkbox"  /></p>
                            <p>ID：{{$match->id}}</p>
                            <p>比赛状态：{{$match->getStatusText($match->id)}}</p>
                            <p>赛事：{{$match->getLeagueName($match->id)}}</p>
                            <p>对阵：{{$match->hname}} VS {{$match->aname}}</p>
                            <p>时间：{{$match->time}}</p>
                            <p><button class="btn btn-sm btn-primary" onclick="addChannel('{{$match->id}}');">添加直播地址</button></p>
                            <p><input placeholder="房间号" value=""/><button onclick="saveLHChannel(this, '{{$match->id}}');">乐虎房间填写</button></p>
                        </td>
                        <td id="td_{{$match->id}}" match_id="{{$match->id}}" lid="{{$match->lid}}" isPri="{{in_array($match->lid, $private_arr)}}">
                            @foreach($channels as $channel)
                                @continue(!isset($channel))
                                @component('admin.match.live_matches_channel_cell', ['channel'=>$channel, 'types'=>$types, 'sport'=>$sport]) @endcomponent
                            @endforeach
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$matches or ''}}
        </div>
    </div>

    <div style="display: none;" id="channel_cell">
        @component('admin.match.live_matches_channel_cell', ['types'=>$types, 'sport'=>$sport]) @endcomponent
    </div>
@endsection

@section('js')
    <script type="text/javascript" src="/js/admin/bootstrap-datetimepicker.min.js" charset="UTF-8"></script>
    <script type="text/javascript" src="/js/admin/locales/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>
    <script type="text/javascript">
        $(".form_datetime").datetimepicker({
            language: 'zh-CN',
            weekStart: 1,
            todayBtn: 1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            minView: 2,
            forceParse: 0
        });

        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        });

        /**
         * 保存直播线路
         * @param thisObj   保存按钮
         * @param channelId 频道id
         * @param matchId   比赛id
         * @param sport     运动类型
         */
        function saveChannel(thisObj, channelId, sport) {
            var dataDiv = $(thisObj).parent().parent();
            var matchId = dataDiv.parent().attr('match_id');
            var name = dataDiv.find("input[name=name]").val();
            var content = dataDiv.find("input[name=content]").val();
            var akq_url = dataDiv.find("input[name=akq_url]").val();
            var h_content = dataDiv.find("input[name=h_content]").val();
            var od = dataDiv.find("input[name=od]").val();
            var use = dataDiv.find("select[name=use]").val();
            var impt = dataDiv.find('input[name=impt]:checked');
            var type = dataDiv.find("select[name=type]").val();
            var ad = dataDiv.find("select[name=ad]").val();
            var room_num = dataDiv.find("input[name=room_num]").val();

            if ($.trim(name) == "") {
//                alert("线路名称不能为空。");
//                return;
            }
            if ($.trim(content) == "") {
                alert("线路内容不能为空。");
                return;
            }
            if (type == 9) {//高清验证类型
                if ($.trim(h_content) == "") {
                    alert("高清线路内容不能为空。");
                    return;
                }
            }
            if (od.length > 0 && !/^\d+$/.test(od)) {
                alert("排序必须为正整数。");
                return;
            }

            var data = {};
            data['channel_id'] = channelId;
            data['match_id'] = matchId;
            data['sport'] = sport;

            data['type'] = type;
            data['show'] = dataDiv.find("select[name=show]").val();
            data['platform'] = dataDiv.find("select[name=platform]").val();
            data['isPrivate'] = dataDiv.find("select[name=isPrivate]").val();
            data['player'] = dataDiv.find("select[name=player]").val();
            data['name'] = name;
            data['content'] = content;
            data['akq_url'] = akq_url;
            data['h_content'] = h_content;
            data['od'] = od;
            data['use'] = use;
            data['impt'] = impt.length == 0 ? 1 : 2;{{-- 是否重点线路 --}}
            data['ad'] = ad;
            data['room_num'] = room_num;
            thisObj.setAttribute('disabled', 'disabled');
            $.ajax({
                "url": "/admin/live/matches/channel/save",
                "type": "POST",
                "data": data,
                "dataType": "json",
                "success": function (json) {
                    if (json && json.code == 200) {
                        alert(json.msg);
                        location.reload();
                    } else if (json) {
                        alert(json.msg);
                    } else {
                        alert("保存线路失败");
                    }
                    thisObj.removeAttribute('disabled');
                },
                "error": function () {
                    alert('保存线路失败。');
                    thisObj.removeAttribute('disabled');
                }
            });
        }

        /**
         * 删除直播线路
         * @param channelId
         */
        function delChannel(thisObj, channelId) {
            if (channelId == '') {//新增的线路
                if (!confirm('该线路尚未保存，是否删除该线路？')) {
                    return;
                }
                $(thisObj).parent().parent().remove();
                return;
            }
            if (!confirm('是否确认删除该直播线路？')) {
                return;
            }
            $.ajax({
                "url": "/admin/live/matches/channel/del",
                "type": "POST",
                "data": {'id': channelId},
                "dataType": "json",
                "success": function (json) {
                    if (json && json.code == 200) {
                        alert(json.msg);
                        location.reload();
                    } else if (json) {
                        alert(json.msg);
                    } else {
                        alert("删除线路失败");
                    }
                },
                "error": function () {
                    alert('删除线路失败。');
                }
            });
        }
        
        function addChannel(match_id) {
            var html = $("#channel_cell").html();
            var pri = $("#td_" + match_id).attr('ispri');
            $("#td_" + match_id).append(html);

            //var group = $("#td_" + match_id).find('.input-group:last');
            var group = $("#td_" + match_id).find('div:last');

            var isPrivate = group.find('select[name=isPrivate]');
            var use = group.find('select[name=use]');
            var type = group.find('select[name=type]');
            if (pri == 1) {
                isPrivate.val(2);
                use.val(2);
            }
            useSetStyle(use[0]);
            privateSetStyle(isPrivate[0]);
            setTypeStyle(type[0]);

            use.change(function () {
                useSetStyle(this);
            });
            isPrivate.change(function () {
                privateSetStyle(this);
            });
            type.change(function () {
                setTypeStyle(this);
            });
        }

        /**
         * 修改是否重点比赛。
         * @param mid
         */
        function changeImp(mid) {
            var imtpObj = $("#impt_" + mid)[0];
            var impt = imtpObj.checked ? 2 : 1;
            imtpObj.setAttribute("disabled", "disabled");
            $.ajax({
                "url": "/admin/live/matches/live/save-impt",
                "type": "POST",
                "data": {'mid': mid, 'impt': impt, 'sport': '{{$sport or 1}}'},
                "dataType": "json",
                "success": function (json) {
                    if (json && json.code == 200) {
                        alert(json.msg);
                    } else if (json) {
                        imtpObj.checked = false;
                        alert(json.msg);
                    } else {
                        imtpObj.checked = false;
                        alert("设置重点比赛失败");
                    }
                    imtpObj.removeAttribute("disabled");
                },
                "error": function () {
                    alert('设置重点比赛失败。');
                    imtpObj.checked = false;
                    imtpObj.removeAttribute("disabled");
                }
            });
        }

        /**
         * 生成高清验证码
         * @param thisObj
         * @returns {boolean}
         */
        function randomCode(thisObj) {
            thisObj.setAttribute("disabled", "disabled");
            $.ajax({
                "url": "/admin/live/matches/channel/random-code",
                "type": "POST",
                "data": {},
                "dataType": "json",
                "success": function (json) {
                    if (json && json.code == 200) {
                        $(thisObj).next().val(json.random_code);
                        alert(json.msg);
                    } else if (json) {
                        alert(json.msg);
                    } else {
                        alert("生成高清验证码失败");
                    }
                    thisObj.removeAttribute("disabled");
                },
                "error": function () {
                    alert('生成高清验证码失败。');
                    thisObj.removeAttribute("disabled");
                }
            });
            return false;
        }
    </script>
    <script type="text/javascript">
        function changePlatform(thisObj) {
            //m3u8 13, flv 14, rtmp 15
            var player = thisObj.value;
            var $nextPlatForm = $(thisObj).parent().find("select[name=platform]");
            if (player == 13) {
                $nextPlatForm.val(3);
            } else if (player == 14 || player == 15) {
                $nextPlatForm.val(2);
            }
        }

        function useSetStyle(obj) {
            var val = obj.value;
            if (val == 2) {//爱看球
                obj.style.backgroundColor = "#00DFE1";
                obj.style.color = "white";
            } else if (val == 3) {//黑土
                obj.style.backgroundColor = "#5c5c6a";
                obj.style.color = "white";
            } else {//通用
                obj.style.backgroundColor = "";
                obj.style.color = "";
            }
        }
        function privateSetStyle(obj) {
            var val = obj.value;
            if (val == 2) {//有版权
                obj.style.backgroundColor = "red";
                obj.style.color = "white";
            } else {//无版权
                obj.style.backgroundColor = "";
                obj.style.color = "";
            }
        }

        function setTypeStyle(obj) {
            var val = parseInt(obj.value);
            var color = 'white';
            switch (val){
                case 1:
                    color = 'pink';
                    break;
                case 2:
                    color = 'lightskyblue';
                    break;
                case 3:
                    color = 'lightcoral';
                    break;
                case 4:
                    color = 'lightgreen';
                    break;
                case 5:
                    color = 'lightyellow';
                    break;
                case 6:
                    color = 'lightsteelblue';
                    break;
                case 7:
                    color = 'deeppink';
                    break;
                case 9:
                    color = 'yellow';
                    break;
                case 10:
                    color = 'lightblue';
                    break;
                case 99:
                    color = 'burlywood';
                    break;
            }
            if (color == 'white') {
                obj.style.backgroundColor = "";
                obj.style.color = "";
            } else {
                obj.style.backgroundColor = color;
                obj.style.color = "black";
            }
        }

        function setSelectStyle() {
            //color: #00DFE1;蓝底 color: #5c5c6a 灰底
            var uses = $('select[name=use]');
            uses.each(function (index, obj) {
                useSetStyle(obj);
            });
            var privates = $('select[name=isPrivate]');
            privates.each(function (index, obj) {
                privateSetStyle(obj);
            });
            var types = $('select[name=type]');
            types.each(function (index, obj) {
                setTypeStyle(obj);
            });
        }
        $('select[name=use]').change(function () {
            useSetStyle(this);
        });
        $('select[name=isPrivate]').change(function () {
            privateSetStyle(this);
        });
        setSelectStyle();
        
        function selectType(thisObj) {
            var val = thisObj.value;
            if (val == 9) {//高清验证类型
                var div = $(thisObj).parent().next('p').show();
            } else {
                var div = $(thisObj).parent().next('p').hide();
            }
            setTypeStyle(thisObj);
        }

        function saveLHChannel(thisBtn, mid) {
            var $thisBtn = $(thisBtn);
            var sport = "{{$sport}}";
            var roomNum = $thisBtn.prev().val();
            if ($.trim(roomNum) == "") {
                alert("请填写乐虎房间号");
                return;
            }
            if (!confirm("是否确认自动填写乐虎房间？")) {
                return;
            }
            $thisBtn.button("loading");
            $.ajax({
                "url": "/admin/live/matches/channel/save_lehu",
                "type": "post",
                "dataType": "json",
                "data": {"sport": sport, "match_id": mid, "room_num": roomNum},
                "success": function (data) {
                    if (data.code == 200) {
                        alert(data.msg);
                        location.reload();
                    } else {
                        alert(data.msg);
                    }
                    $thisBtn.button("reset");
                },
                "error": function () {
                    alert("自动填写乐虎房间失败");
                    $thisBtn.button("reset");
                }
            });
        }
    </script>
@endsection