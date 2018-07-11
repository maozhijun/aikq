@extends('admin.layout.nav')
@section('css')
    <style>
        .input-form {
            display: inline;
            vertical-align: bottom;
            float: left;
        }
        select {
            height: 27px;
        }
    </style>
@endsection
@section('content')
    <h1 class="page-header" style="margin-bottom: 5px;">自建赛事直播设置</h1>
    <div style="margin-bottom: 15px;">
        <div style="margin-top: 15px;">
            <form action="/admin/other/matches" method="get">
                <select name="type" style="width: 100px;" class="form-control input-form">
                    <option value="">类型</option>
                    <option value="1" @if(request('type', '') == "1") selected @endif >节目</option>
                    <option value="2" @if(request('type', '') == "2") selected @endif >比赛</option>
                </select>
                <input name="lname" style="width: 140px; margin-left: 10px;" value="{{request('lname', '')}}" class="form-control input-form" placeholder="赛事名称">
                <input name="name" style="width: 160px; margin-left: 10px;" value="{{request('name', '')}}" class="form-control input-form" placeholder="节目/主队/客队名称">
                <input type="text" style="width: 140px; margin-left: 10px;"
                       placeholder="开始时间"
                       class="form-control input-form form_datetime"
                       name="s_time"
                       data-date-format="yyyy-mm-dd hh:ii"
                       value="{{request('s_time', '')}}">
                <input type="text" style="width: 140px; margin-left: 10px;"
                       placeholder="结束时间"
                       class="form-control input-form form_datetime"
                       name="e_time"
                       data-date-format="yyyy-mm-dd hh:ii"
                       value="{{request('e_time', '')}}">
                <button style="margin-left: 10px;" class="btn btn-primary">搜索</button>
            </form>
            <div style="clear: both"></div>
        </div>
    </div>
    <div class="row placeholders">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <form method="post" action="" onsubmit="return saveOther(this);">
                    <tr>
                        <th>
                            <input type="hidden" name="id" value="">
                            <input type="hidden" name="hname" value="">
                        </th>
                        <th>
                            <input style="width: 60px;margin-right: 5px;" class="form-control input-form" name="project" value="" placeholder="项目">
                        </th>
                        <th>
                            <select mid="" style="width: 80px;" name="m_type" class="form-control">
                                <option value="">类型</option>
                                <option value="1">节目</option>
                                <option value="2">比赛</option>
                            </select>
                        </th>
                        <th>
                            <div data="match">
                                <input style="width: 100px;margin-right: 5px;" class="form-control input-form" name="lname" value="" placeholder="赛事">
                                <input style="width: 110px;" class="form-control input-form" id="hname" value="" placeholder="主队">
                                <h5 style="float: left;margin-left: 5px;margin-right: 5px;"> VS</h5>
                                <input style="width: 110px;" class="form-control input-form" name="aname" value="" placeholder="客队">
                            </div>
                            <div style="display: none;" data="menu">
                                <input style="width: 388px;" class="form-control input-form" id="mname" value="" placeholder="节目名称">
                            </div>
                            <div style="clear: both"></div>
                        </th>
                        <th>
                            <input type="text" style="width: 140px;" class="form-control input-form form_datetime" name="time" data-date-format="yyyy-mm-dd hh:ii" value="">
                        </th>
                        <th>
                            <input type="text" style="width: 140px;" class="form-control input-form form_datetime" name="end_time" data-date-format="yyyy-mm-dd hh:ii" value="">
                        </th>
                        <th>
                            <button id="save_btn" type="submit" class="btn btn-success btn-xs">保存</button>
                            {{--<a class="btn btn-primary btn-xs" onclick="addChannel('', this);">加线路</a>--}}
                        </th>
                    </tr>
                </form>

                <tr>
                    <th width="60px;">#</th>
                    <th>项目</th>
                    <th width="100px;">类型</th>
                    <th>赛事/对阵/节目</th>
                    <th width="120px;">开始时间</th>
                    <th width="120px;">结束时间</th>
                    <th width="120px;">操作</th>
                </tr>
                </thead>
                <tbody style="text-align: left;">
                @foreach($page as $index=>$match)
                    <?php
                        $live = $match->live;
                        $channels = [];
                        if (isset($live)) {
                            $channels = $live->liveChannels($live->id);
                        }
                        $tr_color = ($index + 1) % 2 == 0 ? 'style="background-color: #FFFFFF"' : 'style="background-color: #f9f9f9"';
                    ?>
                    <form onsubmit="return saveOther(this);">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{ $match->id }}">
                        <tr {!! $tr_color !!} >
                            <td><h5>{{ $match->id }}</h5></td>
                            <td>
                                <input style="width: 60px;margin-right: 5px;" class="form-control input-form" name="project" value="{{$match->project}}">
                            </td>
                            <td>
                                <select mid="{{$match->id}}" style="width: 80px;" name="m_type" class="form-control">
                                    <option value="">类型</option>
                                    <option @if($match->type == 1) selected @endif value="1">节目</option>
                                    <option @if($match->type == 2) selected @endif value="2">比赛</option>
                                </select>
                            </td>
                            <td>
                                <div data="match{{$match->id}}" @if($match->type == 1) style="display: none;" @endif >
                                    <input style="width: 100px;margin-right: 5px;" class="form-control input-form" name="lname" value="{{$match->lname}}">
                                    <input style="width: 110px;" class="form-control input-form" id="hname{{$match->id}}" value="{{$match->hname}}">
                                    <h5 style="float: left;margin-left: 5px;margin-right: 5px;"> VS</h5>
                                    <input style="width: 110px;" class="form-control input-form" name="aname" value="{{$match->aname}}">
                                </div>
                                <div @if($match->type == 2) style="display: none;" @endif data="menu{{$match->id}}">
                                    <input style="width: 388px;" class="form-control input-form" id="mname{{$match->id}}" value="{{$match->hname}}">
                                </div>
                                <div style="clear: both"></div>
                            </td>
                            <td>
                                <input type="text" style="width: 140px;" class="form-control input-form form_datetime" name="time" data-date-format="yyyy-mm-dd hh:ii" value="{{date('Y-m-d H:i', strtotime($match->time))}}">
                             </td>
                            <td>
                                <input type="text" style="width: 140px;" class="form-control input-form form_datetime" name="end_time" data-date-format="yyyy-mm-dd hh:ii" value="{{empty($match->end_time) ? '' : date('Y-m-d H:i', strtotime($match->end_time))}}">
                            </td>
                            <td>
                                <button id="save_btn{{$match->id}}" class="btn btn-success btn-xs">保存</button>
                                <a class="btn btn-primary btn-xs" onclick="addChannel('{{$match->id}}', this);">加线路</a>
                            </td>
                        </tr>
                    </form>
                    <tr {!! $tr_color !!} >
                        <td></td>
                        <td id="td_{{$match->id}}" match_id="{{$match->id}}" lid="{{$match->lid}}" colspan="7" style="text-align: left;vertical-align: bottom">
                            @foreach($channels as $channel)
                                @continue(!isset($channel))
                                @component('admin.other.match.list_channel_cell', ['channel'=>$channel, 'types'=>$types]) @endcomponent
                            @endforeach
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$page}}
        </div>
    </div>
@endsection
@section("extra_content")
    <div style="display: none;" id="channel_cell">
        @component('admin.other.match.list_channel_cell', ['sport'=>3, 'types'=>$types]) @endcomponent
    </div>
@endsection
@section('js')
    <script type="text/javascript">
        function submit() {
            console.info('submit...');
        }

        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        });

        /**
         * 检查自建赛事参数
         * @param formObj
         * @returns {boolean}
         */
        function saveOther(thisObj) {
            var id = thisObj.id.value;
            var type = thisObj.m_type.value;
            var time = thisObj.time.value;
            var end_time = thisObj.end_time.value;
            var lName = thisObj.lname.value;//赛事名称
            var mName = $("#mname" + id).val();//节目名称
            var hName = $("#hname" + id).val();//主队名称
            var aName = thisObj.aname.value;//客队名称
            var project = thisObj.project.value;

            lName = $.trim(lName);
            mName = $.trim(mName);
            hName = $.trim(hName);
            aName = $.trim(aName);

            if (type == "") {
                alert("请选择类型");
                return false;
            }

            if (type == 2 && lName == "") {
                alert("请填写赛事");
                return false;
            }
            if (type == 1 && mName == "") {
                alert("请填写节目名称");
                return false;
            }
            if (type == 2 && hName == "") {
                alert("请填写主队名称");
                return false;
            }
            if (type ==2 && aName == "") {
                alert("请填写客队名称");
                return false;
            }
            if (time == "") {
                alert("请填写开始时间");
                return false;
            }
            if (end_time == "") {
                alert("请填写结束时间");
                return false;
            }
            var data = {"id": id, "type":type, "lname": lName, "aname": aName, "time": time, "end_time": end_time, "project": project};
            if (type == 1) {
                data.hname = mName;
            } else {
                data.hname = hName;
            }

            var btnObj = $("#save_btn")[0];
            btnObj.setAttribute("disabled", "disabled");
            $.ajax({
                "url": "/admin/other/matches/save",
                "type": "post",
                "dataType": "json",
                "data": data,
                "success": function (json) {
                    if (json) {
                        alert(json.msg);
                        if (json.code == 200) {
                            location.reload();
                        }
                    } else {
                        alert("保存失败");
                    }
                    btnObj.removeAttribute("disabled");
                },
                "error": function () {
                    alert("保存失败");
                    btnObj.removeAttribute("disabled");
                }
            });
            return false;
        }

        /**
         * 选择类型shi
         */
        $("select[name=m_type]").change(function () {
            var value = this.value;
            var mid = this.getAttribute("mid");
            if (value == 2) {
                $("div[data=match" + mid + "]").show();
                $("div[data=menu" + mid + "]").hide();
            } else if (value == 1) {
                $("div[data=menu" + mid + "]").show();
                $("div[data=match" + mid + "]").hide();
            }
        });

    </script>
    <link href="/css/admin/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
    <script type="text/javascript" src="/js/admin/bootstrap-datetimepicker.min.js" charset="UTF-8"></script>
    <script type="text/javascript" src="/js/admin/locales/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>
    <script>
        $(".form_datetime").datetimepicker({
            "language": 'zh-CN',
            "weekStart": 1,
            "todayBtn": 1,
            "autoclose": 1,
            "todayHighlight": 1,
            "startView": 2,
            "minView": 0,
            "forceParse": 0
        });
    </script>

    <script type="text/javascript">
        function addChannel(match_id, thisObj) {
            var html = $("#channel_cell").html();

            var $thisObj = $(thisObj);
            var $td = $thisObj.parent().parent().next().find("td:last");
            if (!match_id || match_id == '') {
                $td.html(html);
                $td.find("div button").hide();
            } else {
                $td.append(html);
            }

            // var pri = $("#td_" + match_id).attr('ispri');

            var group = $("#td_" + match_id).find('div:last');

            var isPrivate = group.find('select[name=isPrivate]');
            var use = group.find('select[name=use]');
            var type = group.find('select[name=type]');

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
            var h_content = dataDiv.find("input[name=h_content]").val();
            var od = dataDiv.find("input[name=od]").val();
            var use = dataDiv.find("select[name=use]").val();
            var impt = dataDiv.find('input[name=impt]:checked');
            var type = dataDiv.find("select[name=type]").val();
            var ad = dataDiv.find("select[name=ad]").val();

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
            data['h_content'] = h_content;
            data['od'] = od;
            data['use'] = use;
            data['impt'] = impt.length == 0 ? 1 : 2;{{-- 是否重点线路 --}}
                data['ad'] = ad;
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
    </script>
    <script type="text/javascript">
        function changePlayer(thisObj) {
            var player = thisObj.value;
            var $platform = $(thisObj).parent().find('select[name=platform]');
            if (player == 13) {
                $platform.val(3);
            } else if (player == 14 || player == 15) {
                $platform.val(2);
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

    </script>
@endsection