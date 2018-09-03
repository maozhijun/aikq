@extends("backstage.layout.nav")
@section("css")
    <link rel="stylesheet" type="text/css" href="/backstage/css/info.css?2018080131820">
@endsection
@section("content")
    <div id="Content">
        <div class="inner" style="">
            <div id="Tab">
                <a class="on">直播信息</a>
                <a href="/bs/comment">评论管理</a>
            </div>
            <div id="Link">
                {{--<button class="get" style="display: {{$iLive ? 'none' : 'block'}};">开始直播，获取推流地址</button>--}}
                {{--<button class="reset live" style="display: {{$iLive ? 'block' : 'none'}};" >重置推流地址</button>--}}
                {{--<button class="end live" style="display: {{$iLive ? 'block' : 'none'}};" >结束直播</button>--}}
                <button class="reset live">重置推流地址</button>
                <div class="link live">
                    {{--<div class="link live" style="display: {{$iLive ? 'block' : 'none'}};" >--}}
                    <p class="url">
                        <input type="text" name="link" value="{{$room->url or ''}}">
                        <button class="copy">复制</button>
                    </p>
                    <p class="name">
                        <input type="text" name="link" value="{{$room->url_key or ''}}">
                        <button class="copy">复制</button>
                    </p>
                </div>
                {{--<p class="warm red live" style="display: {{$iLive ? 'block' : 'none'}};" >若OBS推流中断，请点击重置推流地址</p>--}}
                <p class="warm red live">若OBS无法推流，请点击重置</p>
            </div>
            <div class="box">
                <p class="title">主播信息</p>
                <div class="item">
                    <p class="name">主播昵称：</p>
                    <input type="text" name="nickname" readonly placeholder="请输入您的昵称" value="{{$anchor->name}}">
                </div>
                <div class="item">
                    <p class="name">主播头像：</p>
                    <img src="{{empty($anchor->icon) ? '/backstage/img/image_avatar_n.jpg' : $anchor->icon}}"
                         class="face">
                    <button id="anchor_file">上传</button>
                    <div class="prompt">
                        <p>*请上传正方形头像</p>
                        <p>*图片大小不得大于<b>1M</b></p>
                    </div>
                </div>
                <a target="_blank" href="/anchor/room/{{$room->id}}.html" class="myroom">
                    <img src="{{isset($roomImg)?('data:image/png;base64,'.$roomImg):''}}">
                    <p>我的直播间</p>
                </a>
            </div>
            <div class="box">
                <p class="title">主播间信息</p>
                <div class="item">
                    <p class="name">主播间标题：</p>
                    <input type="text" id="room_title" placeholder="请输入房间标题" value="{{$room->title or ''}}">
                </div>
                <div class="item">
                    <p class="name">主播间封面：</p>
                    <img src="{{ (!isset($room) || empty($room->cover)) ? '/backstage/img/image_picture_n.jpg' : $room->cover}}"
                         class="cover">
                    <button id="room_file">上传</button>
                    <button id="room_clean">清除</button>
                    <div class="prompt">
                        <p>*图片大小不得大于<b>1M</b></p>
                    </div>
                </div>
            </div>
            <div class="comfirm">
                <form action="/bs/info/save" method="post" onsubmit="return checkSubmit(this);"
                      enctype="multipart/form-data">
                    <input name="_token" value="{{csrf_token()}}" type="hidden">
                    <input name="room_title" value="" type="hidden">
                    <input name="anchor_icon" type="file" style="display: none;">
                    <input name="room_cover" onchange="readImg(this, 'cover');" type="file" style="display: none;">
                    <input name="clean" type="hidden" value="0">
                    <button type="submit">保存</button>
                </form>
            </div>
        </div>
    </div>
@endsection
@section("js")
    <script type="text/javascript">
        $("#anchor_file").click(function () {
            $("input[name=anchor_icon]").trigger("click");
        });
        $("#room_file").click(function () {
            $("input[name=room_cover]").trigger("click");
        });
        $("#room_clean").click(function () {
            $("img.cover").attr("src", "/backstage/img/image_picture_n.jpg");

            var $roomCover = $("input[name=room_cover]");
            var $parent = $roomCover.parent();
            $roomCover.remove();
            $parent.append("<input name=\"room_cover\" onchange=\"readImg(this, 'cover');\" type=\"file\" style=\"display: none;\">");
            $("input[name=clean]").val(1);
        });

        //在input file内容改变的时候触发事件
        // $('input[name=room_cover]').change(function(){
        //     readImg(this, 'cover');
        // });

        $('input[name=anchor_icon]').change(function () {
            readImg(this, 'face');
        });

        function readImg(fileInput, className) {
            //获取input file的files文件数组;
            //$('#filed')获取的是jQuery对象，.get(0)转为原生对象;
            //这边默认只能选一个，但是存放形式仍然是数组，所以取第一个元素使用[0];
            var file = $(fileInput).get(0).files[0];
            //创建用来读取此文件的对象
            var reader = new FileReader();
            //使用该对象读取file文件
            reader.readAsDataURL(file);
            //读取文件成功后执行的方法函数
            reader.onload = function (e) {
                //读取成功后返回的一个参数e，整个的一个进度事件
                //console.log(e);
                //选择所要显示图片的img，要赋值给img的src就是e中target下result里面
                //的base64编码格式的地址
                $('.' + className).get(0).src = e.target.result;
                if (className == 'cover') {
                    $("input[name=clean]").val(0);
                }
            }
        }

        function checkSubmit(form) {
            var tempRoomTitle = "{{$room->title or ''}}";
            var tempFace = "{{$anchor->icon or ''}}";
            var tempCover = "{{$room->cover or ''}}";

            var roomTitle = $("#room_title").val();
            // if ($.trim(roomTitle).length == 0) {
            //    alert("房间标题不能为空");
            //    return false;
            // }
            form.room_title.value = roomTitle;
            return true;
        }

        var success = "{{session("success")}}";
        var error = "{{session("error")}}";
        if (error != "") {
            alert(error);
        } else if (success) {
            alert(success);
        }

        /**
         * 异步表单验证
         */
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        });

        $(".end").click(function () {
            if (!confirm("是否确认结束直播？")) {
                return;
            }
            $.ajax({
                "url": "/bs/info/room/end",
                "type": "post",
                "dataType": "json",
                "data": {},
                "success": function (json) {
                    alert(json.message);
                    if (json.code == 200) {
                        $(".live").hide();
                        $(".get").show();
                    }
                },
                "error": function () {

                }
            });
        });

        $(".get").click(function () {
            if (!confirm("是否确认开始直播？")) {
                return;
            }
            var $btn = $(this);
            startLive($btn, false);
        });

        $(".reset").click(function () {
            if (!confirm("重置推流地址将刷新房间直播信号，是否继续？")) {
                return;
            }
            startLive(null, 1);
        });

        function startLive($btn, refulsh) {
            $.ajax({
                "url": "/bs/info/room/start",
                "type": "post",
                "dataType": "json",
                "data": {"refresh": refulsh},
                "success": function (json) {
                    if (json.code == 1) {
                        var book = confirm(json.message);
                        if (book) {
                            window.location = '/bs/matches';
                        }
                        return;
                    }
                    alert(json.message);
                    if (json.code == 200) {
                        if ($btn) $btn.hide();
                        $(".live .url input").val(json.data.url);
                        $(".live .name input").val(json.data.url_key);
                        $(".live").show();
                    }
                },
                "error": function () {

                }
            });
        }

        $("button.copy").click(function () {
            $(this).prev()[0].select();
            document.execCommand("Copy"); // 执行浏览器复制命令
            alert("已复制好，可贴粘。");
        });
    </script>
@endsection
