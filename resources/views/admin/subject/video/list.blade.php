@extends('admin.layout.nav')
@section('css')
    <style>
        .input-form {
            display: inline;
            vertical-align: bottom;
            float: left;
            margin-right: 5px;
        }

        .combo-select-div{
            line-height: 30px;
            border-bottom: 1px solid #ccc;
            height:30px;
            width:100%;
            cursor:pointer;
            text-align: center;
            border-buttom:solid 1px gray;
        }
        .combo-select-div:hover{
            background-color: #ccc;
        }
        .td-combo-div
        {
            display:none;
            width:400px;
            height:200px;
            background-color:white;
            overflow:auto;
            overflow-y:scroll;
            position:absolute;
            z-index:1000;
            box-shadow:0 6px 12px rgba(0,0,0,.175);
        }
        .td-combo-div > div {
            text-align: left;
            padding-left: 10px;
        }
        .btn_comb {
            margin:1px;
            float:left;
            height: 33px;
            vertical-align: bottom;
        }
        .highlight {
            padding: 9px 14px;
            margin-bottom: 14px;
            background-color: #f7f7f9;
            border: 1px solid #e1e1e8;
            border-radius: 4px;
        }
        .tagBtn {
            cursor: pointer;color: white;background-color: rgb(92, 184, 92);
        }
    </style>
@endsection
@section('content')
    <h1 class="page-header" style="margin-bottom: 5px;">专题录像列表</h1>
    <div style="margin: 15px 0 15px;">
        <div>
            <form action="/admin/subject/videos" method="get">
                <select name="s_lid" style="width: 130px;" class="form-control input-form">
                    <option value="">联赛专题</option>
                    @foreach($leagues as $league)
                        <option value="{{$league->id}}" @if(request('s_lid', '') == $league->id) selected @endif >{{$league->name}}</option>
                    @endforeach
                    <option value="999" @if(request('s_lid', '') == '999') selected @endif >其他</option>
                </select>
                <input name="hname" style="width: 160px; margin-left: 10px;" value="{{request('hname', '')}}" class="form-control input-form" placeholder="主队名称">
                <input name="aname" style="width: 160px; margin-left: 10px;" value="{{request('aname', '')}}" class="form-control input-form" placeholder="客队名称">
                <button style="margin-left: 10px;" class="btn btn-primary">搜索</button>
            </form>
            <div style="clear: both"></div>
        </div>
    </div>
    <hr>
    <div style="margin-bottom: 15px;">
        <form action="/admin/subject/videos/save" id="save_form" method="post" onsubmit="return checkForm(this);">
            <input type="hidden" name="_token" value="{{csrf_token()}}">
            <input type="hidden" name="mid" value="">
            <input type="hidden" name="cover" value="">
            <input type="hidden" name="tags" value="">
            {{--<button onmouseover="showCover(this);" onmouseout="hideCover();" style="float: left;margin-right: 5px;" type="button" class="btn btn-default" onclick="uploadCover(this)">--}}
                {{--<span class="glyphicon glyphicon-upload"></span>封面图--}}
            {{--</button>--}}
            <select id="s_lid" name="s_lid" style="width: 130px;" class="form-control input-form">
                <option value="">联赛专题</option>
                @foreach($leagues as $league)
                    <option value="{{$league->id}}" sport="{{$league->sport}}">{{$league->name}}</option>
                @endforeach
                <option value="999-1" sport="1">其他(足球)</option>
                <option value="999-2" sport="2">其他(篮球)</option>
            </select>
            <input id="name" style="width: 400px; margin-left: 10px;" class="form-control input-form" placeholder="球队名称">
            <button type="button" style="margin-left: 10px;" class="btn btn-default" onclick="findMatches('');">获取比赛</button>
            <button style="margin-left: 10px;" class="btn btn-warning">保存</button>
            <div style="margin-top: 10px;">
                @include("admin.tag.add_tag_cell")
            </div>
        </form>
        <div style="clear: both"></div>
    </div>

    <div class="row placeholders">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    {{--<th>封面</th>--}}
                    <th>专题</th>
                    <th>比赛信息</th>
                    <th width="200px;">操作</th>
                </tr>
                </thead>
                <tbody style="text-align: left;">
                @foreach($page as $index=>$video)
                    <?php
                        $channels = $video->getChannels();
                        $tr_color = ($index + 1) % 3 == 0 ? 'background-color: #FFFFFF;' : 'background-color: #f9f9f9;';
                    ?>
                    <form action="/admin/subject/videos/save" id="save_form{{$video->id}}" method="post" onsubmit="return checkForm(this, '{{$video->id}}');">
                        <input type="hidden" name="_token" value="{{csrf_token()}}">
                        <input type="hidden" name="id" value="{{$video->id}}">
                        <input type="hidden" name="mid" value="{{$video->mid}}">
                        <input type="hidden" name="tags" value="">
                        <tr style="{!! $tr_color !!}">
                            <td><h5>{{$video->id}}</h5></td>
                            {{--<td>--}}
                                {{--<input type="hidden" name="cover" value="{{$video->cover}}">--}}
                                {{--<button onmouseover="showCover(this);" onmouseout="hideCover();" style="float: left;margin-right: 5px;" type="button" class="btn btn-default" onclick="uploadCover(this)">--}}
                                    {{--<span class="glyphicon glyphicon-upload"></span>封面图--}}
                                {{--</button>--}}
                            {{--</td>--}}
                            <td>
                                <select id="s_lid{{$video->id}}" name="s_lid" style="width: 130px;" class="form-control input-form">
                                    <option value="">联赛专题</option>
                                    @foreach($leagues as $league)
                                        <option value="{{$league->id}}" @if($video->s_lid == $league->id) selected @endif >{{$league->name}}</option>
                                    @endforeach
                                    <option value="999-1" @if($video->s_lid == 999 && $video->sport == 1) selected @endif >其他(足球)</option>
                                    <option value="999-2" @if($video->s_lid == 999 && $video->sport == 2) selected @endif >其他(篮球)</option>
                                </select>
                            </td>
                            <td>
                                <input id="name{{$video->id}}" style="width: 400px; margin-left: 10px;"
                                       class="form-control input-form"
                                       placeholder="球队名称"
                                       value="{{'（' . substr($video->time, 0, 16) . '）' . $video->hname . ' ' . $video->hscore . ' - ' . $video->ascore . ' ' . $video->aname}}">
                                <button type="button" style="margin-left: 10px;" class="btn btn-default" onclick="findMatches('{{$video->id}}');">获取比赛</button>
                            </td>
                            <td>
                                <button class="btn btn-primary btn-sm">保存</button>
                                <a type="button" class="btn btn-danger btn-sm" onclick="delSv(this, '{{$video->id}}');">删除</a>
                                <a type="button" class="btn btn-info btn-sm" onclick="addVideoChannel(this, '{{$video->id}}');">加录像</a>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5">
                                @component("admin.tag.add_tag_cell", ["mul_id"=>$video->id, "sport"=>["tag_id"=>$video->sport], "tags"=>$video->tagRelations() ]) @endcomponent
                            </td>
                        </tr>
                    </form>
                    <tr sv_id="{{$video->id}}" style="{!! $tr_color !!} @if(count($channels) == 0) display:none; @endif" >
                        <td colspan="5">
                        @foreach($channels as $channel)
                            @component('admin.subject.video.channel_cell', ['channel'=>$channel, 'players'=>$players]) @endcomponent
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
@section('extra_content')
    <div  class="td-combo-div" id="modalLabelCombo"></div>
    <div id="channel_div" style="display: none;">
        @component('admin.subject.video.channel_cell', ['players'=>$players]) @endcomponent
    </div>
    <form id="imageUploadForm" enctype="multipart/form-data" action="/admin/upload/cover" method="post">
        {{ csrf_field() }}
        <input type="file" id="ImageBrowse" name="cover" onchange="changeCoverImage()" style="position:absolute;clip:rect(0 0 0 0);"/>
    </form>
    <div id="cover_show" style="display: none; position:absolute;z-index:1000;"><img src="" style="max-width: 300px;max-height: 300px;" ></div>
@endsection
@section('js')
    <script type="text/javascript" src="/js/admin/articleTag.js"></script>
    <script type="text/javascript">
        var upBtn;
        /**
         * 上传图片
         */
        function changeCoverImage() {
            $(upBtn).button('loading');
            var formData = new FormData($('#imageUploadForm')[0]);
            console.log(formData);
            $.ajax({
                type: 'POST',
                url: '/admin/upload/cover',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    var type = typeof data;
                    if (type == 'object') {
                        alert(data.msg);
                    } else if (type == 'string') {
                        var cover = $(upBtn).parent().find('input[name="cover"]');
                        cover.val(data);
                    } else {
                        alert("上传封面图失败");
                    }
                    $(upBtn).button('reset');
                },
                error: function (data) {
                    $(upBtn).button('reset');
                }
            });
        }

        /**
         * 选择文件
         */
        function uploadCover(btnObj) {
            $('#ImageBrowse').click();
            upBtn = btnObj;
        }

    </script>
    <script type="text/javascript">
        function submit() {
            console.info('submit...');
        }
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        });

        /**
         * 添加线路
         **/
        function addVideoChannel(thisObj, vid) {
            var channelTr = $(thisObj).parent().parent().next();
            var channelHtml = $("#channel_div").html();
            channelTr.find('td').append(channelHtml);
            channelTr.show();
        }

        /**
         * 删除录像线路
         */
        function delVideoChannel(thisObj, ch_id) {
            if (ch_id == '') {
                var channelTr = $(thisObj).parent().parent().parent();
                $(thisObj).parent().remove();
                if (channelTr.find('div').length == 0) {
                    channelTr.hide();
                }
            } else {
                if (!confirm('是否确认删除录像线路？')) {
                    return;
                }
                thisObj.setAttribute('disabled', 'disabled');
                location.href = '/admin/subject/videos/del-channel?id=' + ch_id;
            }
        }

        /**
         * 保存录像线路
         */
        function saveVideoChannel(thisObj, ch_id) {
            var channelDiv = $(thisObj).parent();

            var cover = channelDiv.find('input[name=cover]').val();
            var title = channelDiv.find('input[name=title]').val();
            var platform = channelDiv.find('select[name=platform]').val();
            var type = channelDiv.find('select[name=type]').val();
            var player = channelDiv.find('select[name=player]').val();
            var content = channelDiv.find('input[name=content]').val();
            var od = channelDiv.find('input[name=od]').val();
            var sv_id = channelDiv.parent().parent().attr('sv_id');

            title = $.trim(title);
            content = $.trim(content);
            od = $.trim(od);

            if (cover == "") {
                //alert("请先上传封面图片");
                //return;
            }
            if (title == "" || title.length > 32) {
                alert("录像线路标题不能为空或者标题不大于32字符");
                return;
            }
            if (content == "") {
                alert("录像线路链接不能为空");
                return;
            }
            if (od != "" && !/^\d+$/.test(od)) {
                alert("录像线路排序只能填写正整数");
                return;
            }
            var data = {"cover": cover, "title": title, "platform": platform, "type": type, "player": player
                , "content": content, "od": od, "sv_id": sv_id, 'id': ch_id};

            $(thisObj).button('loading');
            $.ajax({
                "url": "/admin/subject/videos/save-channel",
                "type": "post",
                "data": data,
                "dataType": "json",
                "success": function (json) {
                    if (json) {
                        alert(json.msg);
                        if (json.code == 200) {
                            location.reload();
                        }
                    } else {
                        alert("保存录像线路失败");
                    }
                    $(thisObj).button('reset');
                },
                "error": function () {
                    $(thisObj).button('reset');
                }
            });
        }

        /**
         * 检查录像参数
         **/
        function checkForm(thisObj, mul_id) {
            var mid = thisObj.mid.value;
            var s_lid = thisObj.s_lid.value;
            if (s_lid == "") {
                alert("请选择赛事专题");
                return false;
            }
            if (mid == "") {
                alert("请选择比赛");
                return false;
            }
            if (!mul_id) mul_id = "";
            var tags = formatTags("match_tag" + mul_id, "team_tag" + mul_id, "player_tag" + mul_id);
            thisObj.tags.value = tags;
            return true;
        }

        /**
         * 删除专题录像
         * @param thisObj
         * @param id
         */
        function delSv(thisObj, id) {
            if (!confirm('是否确认删除此录像？')) {
                return;
            }
            thisObj.setAttribute('disabled', 'disabled');
            location.href = "/admin/subject/videos/del?id=" + id;
        }
    </script>

    <script>
        $(function () {
           $("#s_lid").change(function () {
               var sport = $(this).find("option:selected").attr("sport");
               var sportObj = $("#save_form").find("#sport");
               if (sport != sportObj.val()) {
                   sportObj.val(sport).trigger("change");
               }
           });
        });

        function showCover(thisObj) {
            var $btn = $(thisObj);
            var cover = $(thisObj).prev().val();
            var $cover_show = $("#cover_show");
            var display = $cover_show.css("display");

            if (cover != "" && display != "block") {
                var offset = $btn.offset();

                var index = offset.top;
                var left = offset.left;
                var input_height = + 32;//$btn.outerHeight(true);
                $cover_show.find("img").attr("src", cover);
                $cover_show.css({"left":left + "px","top":index + input_height + "px","display":"block"});
            }
        }
        
        function hideCover() {
            $("#cover_show").hide();
        }


        var onceSearch = false;
        $("input[id^=name]").click(function () {
            if (onceSearch) showCombo(this);
        });

        function selectCombo(thisObj, id) {
            var mid = thisObj.getAttribute('mid');
            var hname = thisObj.getAttribute('hname');
            var aname = thisObj.getAttribute('aname');
            var hscore = thisObj.getAttribute('hscore');
            var ascore = thisObj.getAttribute('ascore');
            var time = thisObj.getAttribute('time');

            var save_form = $("#save_form" + id)[0];
            save_form.mid.value = mid;
            // save_form.hname.value = hname;
            // save_form.aname.value = aname;
            // save_form.hscore.value = hscore;
            // save_form.ascore.value = ascore;
            // save_form.time.value = time;

            var match_msg = "(" + time + ") " + hname + " " + hscore + " - " + ascore + " " + aname;
            $("#name" + id).val(match_msg);
        }

        $("body").click(function () {
            $('#modalLabelCombo').hide();
        });

        /**
         * 修改类型时，清空lid
         */
        $("select[name=sport]").change(function () {
            $("input[name=lid]").val("");
        });

        /**
         * 显示下拉框
         * @param thisObj
         */
        function showCombo(thisObj) {
            $('#modalLabelCombo').show();
            if(thisObj && thisObj.stopPropagation){
                //W3C取消冒泡事件
                thisObj.stopPropagation();
            }else{
                //IE取消冒泡事件
                window.event.cancelBubble = true;
            }
        }

        /**
         * 查找赛事
         */
        function findMatches(id) {
            var name = $("#name" + id);
            var s_lid = $("#s_lid" + id).val();
            if (s_lid == "") {
                alert("请先选择联赛专题");
                return;
            }
            if (name.val() == "") {
                alert("请填写球队名称");
                return;
            }
            $.ajax({
                "url": "/admin/subject/videos/find-matches",
                "dataType": "json",
                "data": {"name": name.val(), "s_lid": s_lid},
                "success": function (json) {
                    var comboObj = $('#modalLabelCombo');
                    var offset = name.offset();

                    var index = offset.top;
                    var left = offset.left;
                    var input_height = name.outerHeight(true);
                    comboObj.css({"left":left,"top":index + input_height,"display":"block"});
                    if (json && json.matches) {
                        var str = "";
                        var matches = json.matches;
                        for(var i = 0; i < matches.length; i++) {
                            var match = matches[i];
                            var attr = 'mid="' + match.mid + '"';
                            attr += 'hname="' + match.hname + '"';
                            attr += 'aname="' + match.aname + '"';
                            attr += 'hscore="' + match.hscore + '"';
                            attr += 'ascore="' + match.ascore + '"';
                            attr += 'time="' + match.time + '"';
                            var time = match.time.substr(0, 16);
                            str += '<div '+ attr +' onclick="selectCombo(this, \'' + id + '\');" class="combo-select-div">('+ time +') '+ match.hname + ' VS ' + match.aname +'</div>';
                        }
                        comboObj.html(str);
                        onceSearch = true;
                    }
                },
                "error": function () {
                    alert("查找赛事失败");
                }
            });
        }
    </script>
@endsection