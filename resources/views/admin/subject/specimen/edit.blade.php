@extends('admin.layout.nav')
@section('css')
<style>
    #coverImage {
        max-width: 200px;
        max-height: 200px;
    }
</style>
@endsection
@section('content')
    <h1 class="page-header">专题集锦</h1>
    <div class="row">
        <div class="col-lg-12">
            <form class="form" method="post" action="/admin/subject/specimens/save" onsubmit="return saveSpecimen(this);">
                {{ csrf_field() }}
                <input type="hidden" name="id" value="{{ isset($specimen) ? $specimen->id : '' }}">
                <input type="hidden" name="cover" id="coverImageInput"  value="{{ isset($specimen) ? $specimen->cover : '' }}">

                <div class="input-group form-group">
                    <span class="input-group-addon">标题</span>
                    <input name="title" class="form-control" value="{{$specimen->title or ''}}">
                    <span class="input-group-addon">{{isset($specimen) ? mb_strlen($specimen->title) : 0}}字</span>
                </div>

                <div class="input-group form-group">
                    <span class="input-group-addon">链接</span>
                    <input name="link" class="form-control" value="{{$specimen->link or ''}}">
                </div>

                <div class="input-group form-group">
                    <span class="input-group-addon">专题</span>
                    <select style="width: 200px;" name="s_lid" class="form-control" required>
                        <option value="">请选择专题</option>
                        @foreach($leagues as $league)
                            <option value="{{$league->id}}" @if(isset($specimen) && $specimen->s_lid == $league->id) selected @endif>{{$league->name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="input-group form-group">
                    <span class="input-group-addon">播放</span>
                    <select style="width: 200px;" name="player" class="form-control" required>
                        <option value="">请选择播放方式</option>
                        @foreach($players as $key=>$pName)
                            <option value="{{$key}}" @if(isset($specimen) && $specimen->player == $key) selected @endif>{{$pName}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="input-group form-group">
                    <span class="input-group-addon">显示</span>
                    <select style="width: 200px;" name="show" class="form-control" required>
                        <option value="1" @if(isset($specimen) && $specimen->show == 1) selected @endif >显示</option>
                        <option value="2" @if(isset($specimen) && $specimen->show == 2) selected @endif >隐藏</option>
                    </select>
                </div>

                <div class="input-group form-group">
                    <span class="input-group-addon">时间</span>
                    <input style="width: 200px;" name="time" placeholder="比赛时间" class="form-control form_datetime" value="{{$specimen->time or ''}}">
                </div>

                <div class="input-group form-group">
                    <span class="input-group-addon">排序</span>
                    <input style="width: 200px;" type="number" name="od" class="form-control" value="{{$specimen->od or ''}}">
                </div>

                <img @if(!isset($specimen) || empty($specimen->cover)) style="display: none;" @endif
                        class="img-thumbnail" id="coverImage" src="{{ $specimen->cover or '' }}">

                <div class="input-group form-group">
                    <span class="input-group-addon">上传封面</span>
                    <button type="button" class="btn btn-sm btn-default" onclick="uploadCover()">
                        <span class="glyphicon glyphicon-upload"></span>上传封面
                    </button>
                </div>

                <div class="form-group">
                    <div class="col-lg-offset-2 col-lg-2">
                        <button type="submit" id="save" class="btn btn-success">保存</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
    <form id="imageUploadForm" enctype="multipart/form-data" action="/admin/upload/cover" method="post">
        {{ csrf_field() }}
        <input type="file" id="ImageBrowse" name="cover" onchange="changeCoverImage()" style="position:absolute;clip:rect(0 0 0 0);"/>
    </form>
@endsection
@section('js')
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        });

        /**
         * 保存外链
         * @param formObj
         * @returns {boolean}
         */
        function saveSpecimen(thisForm) {
            //判断参数
            var id = thisForm.id.value;
            var title = thisForm.title.value;
            var link = thisForm.link.value;
            var s_lid = thisForm.s_lid.value;
            var player = thisForm.player.value;
            var cover = thisForm.cover.value;
            var time = thisForm.time.value;
            var od = thisForm.od.value;

            title = $.trim(title);
            link = $.trim(link);
            if (title.length == 0) {
                alert("请填写集锦标题");
                return false;
            }
            if (title.length > 16) {
                alert("集锦标题不能超过16字");
                return false;
            }
            if (link.length == 0) {
                alert("请填写链接");
                return false;
            }
            if (s_lid == "") {
                alert("请选择专题");
                return false;
            }
            if (player == "") {
                alert("请选择一种播放方式");
                return false;
            }
            if (time == "") {
                alert("请选择比赛时间");
                return false;
            }
            if (od != "" && !/\d+/.test(od)) {
                alert("排序只能填写正整数");
                return false;
            }
            if (cover == "") {
                alert("请先上传封面图");
                return false;
            }
            //判断参数

            var $form = $(thisForm);
            var data = $form.serialize();
            var btn = $("#save")[0];
            btn.setAttribute('disabled', 'disabled');
            $.ajax({
                "url": "/admin/subject/specimens/save",
                "data": data,
                "type": "post",
                "dataType": "json",
                "success": function (json) {
                    if (json) {
                        alert(json.msg);
                        if (json.code == 200) {
                            location.href = "/admin/subject/specimens";
                        }
                    }
                    btn.removeAttribute('disabled');
                },
                "error": function () {
                    alert("保存失败");
                    btn.removeAttribute('disabled');
                }
            });
            return false;
        }

        /**
         * 上传图片
         */
        function changeCoverImage() {
            $('.btn').button('loading');
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
                    //console.log("success");
                    var type = typeof data;
                    if (type == 'object') {
                        alert(data.msg);
                    } else if (type == 'string') {
                        $('#coverImageInput')[0].value = $('#coverImage')[0].src = data;
                        $('#coverImage').show();
                    } else {
                        alert("上传封面图失败");
                    }
                    $('.btn').button('reset');
                },
                error: function (data) {
                    //console.log("error");
                    $('.btn').button('reset');
                }
            });
        }

        /**
         * 选择文件
         */
        function uploadCover() {
            $('#ImageBrowse').click();
        }

        /**
         * 统计标题字数
         */
        $("input[name='title']").keyup(function () {
            var len = this.value.length;
            var $next = $(this).next();
            if (len > 16) {
                $next.css({"background-color": "red"});
            } else {
                $next.css({"background-color": "#eee"});
            }
            $next.html(len + '字');
        });

    </script>

    <script>
        var onceSearch = false;
        $("input[name='name']").click(function () {
            if (onceSearch) showCombo(this);
        });

        function selectCombo(thisObj) {
            var lid = thisObj.getAttribute('value');
            var name = thisObj.innerHTML;
            var type = thisObj.getAttribute('type');

            $("input[name='lid']").val(lid);
            $("input[name='name']").val(name);
            $("input[name='type']").val(type);
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
        function findLeague() {
            var nameObj = $("input[name=name]");
            var btObj = nameObj[0];
            var sport = $("select[name=sport]").val();
            var name = nameObj.val();
            if (name != "") {
                $.ajax({
                    "url": "/admin/subject/leagues/find-league",
                    "type": "post",
                    "dataType": "json",
                    "data": {"name": name, "sport": sport},
                    "success": function (json) {
                        if (json && json.leagues) {
                            var comboObj = $('#modalLabelCombo');
                            var index = btObj.offsetTop;
                            var left = btObj.offsetLeft;
                            comboObj.css({"left":left + 40,"top":index + 175,"display":"block"});
                            var str = "";
                            var leagues = json.leagues;
                            for(var i = 0; i < leagues.length; i++) {
                                var league = leagues[i];
                                str += '<div value="'+ league.id +'" type="' + league.type + '" onclick="selectCombo(this);" class="combo-select-div">'+ league.name +'</div>';
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
        }
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
@endsection