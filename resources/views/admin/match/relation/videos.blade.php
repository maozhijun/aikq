@extends('admin.layout.nav')
@section('content')
    <h1 class="page-header">相关视频列表</h1>
    <div class="row">
        <div>
            <form class="form-inline">
                <div class="form-group">
                    <label>标题：</label>
                    <input type="text" name="title" value="{{ request('title', '') }}">
                </div>
                <div class="form-group" style="margin-left: 10px;">
                    <label>作者：</label>
                    <input type="text" name="author" value="{{ request('author', '') }}">
                </div>
                <button type="submit" class="btn btn-primary btn-sm">
                    <span class="glyphicon glyphicon-search"></span>搜索
                </button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>
                        <input type="hidden" name="cover" value="">
                        <button onmouseover="showCover(this);" onmouseout="hideCover();" style="float: left;" type="button" class="btn btn-default" onclick="uploadCover(this)">
                            <span class="glyphicon glyphicon-upload"></span>封面
                        </button>
                    </th>
                    <th>
                        <input style="width: 260px;" class="form-control input-form" name="title" value="" placeholder="标题">
                    </th>
                    <th>
                        <input style="width: 260px;" class="form-control input-form" name="labels" value="" placeholder="标签">
                    </th>
                    <th>
                        <input style="width: 260px;" class="form-control input-form" name="link" value="" placeholder="链接">
                    </th>
                    <th>
                        <p><button id="save_btn" type="button" onclick="saveVideo(this);" class="btn btn-success btn-sm">保存</button></p>
                    </th>
                </tr>
                <tr>
                    <th>封面</th>
                    <th>标题</th>
                    <th>标签</th>
                    <th width="300px;">链接</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($page as $video)
                    <tr>
                        <td>
                            <input type="hidden" name="id" value="{{$video->id}}" >
                            <input type="hidden" name="cover" value="{{$video->cover}}">
                            <button onmouseover="showCover(this);" onmouseout="hideCover();" style="float: left;" type="button" class="btn btn-default" onclick="uploadCover(this)">
                                <span class="glyphicon glyphicon-upload"></span>封面
                            </button>
                        </td>
                        <td><input style="width: 260px;" class="form-control input-form" name="title" value="{{$video->title}}" placeholder="标题"></td>
                        <td><input style="width: 260px;" class="form-control input-form" name="labels" value="{{ $video->labels }}" placeholder="标签"></td>
                        <td><input style="width: 260px;" class="form-control input-form" name="link" value="{{ $video->link }}" placeholder="链接"></td>
                        <td>
                            <p>
                                <button class="btn btn-xs btn-info" onclick="saveVideo(this);">
                                    <span class="glyphicon glyphicon-edit"></span>保存
                                </button>
                                <a class="btn btn-xs btn-danger" href="javascript:delVideo(this, '{{ $video->id }}');">
                                    <span class="glyphicon glyphicon-remove"></span>删除
                                </a>
                            </p>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $page or '' }}
        </div>
    </div>
@endsection
@section('extra_content')
    <form id="imageUploadForm" enctype="multipart/form-data" action="/admin/upload/cover" method="post">
        {{ csrf_field() }}
        <input type="file" id="ImageBrowse" name="cover" onchange="changeCoverImage()" style="position:absolute;clip:rect(0 0 0 0);"/>
    </form>
    <div id="cover_show" style="display: none; position:absolute;z-index:1000;"><img src="" style="max-width: 300px;max-height: 300px;" ></div>
@endsection
@section('js')
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        });

        function saveVideo(thisBtn) {
            var $thisBtn = $(thisBtn);
            var $tr = $thisBtn.parent().parent().parent();

            var id = $tr.find("input[name=id]").val();
            var cover = $tr.find("input[name=cover]").val();
            var title = $tr.find("input[name=title]").val();
            var labels = $tr.find("input[name=labels]").val();
            var link = $tr.find("input[name=link]").val();

            if ($.trim(title) == "") {
                alert("标题不能为空");
                return false;
            }
            if (title.length > 64) {
                alert("标题不能超过64字符");
                return false;
            }

            if ($.trim(labels) == "") {
                alert("标签不能为空");
                return false;
            }
            if (title.length > 255) {
                alert("标签不能超过255字符");
                return false;
            }

            if ($.trim(link) == "") {
                alert("链接不能为空");
                return false;
            }
            if (link.length > 255) {
                alert("链接不能超过255字符");
                return false;
            }

            var data = {"id": id, "cover":cover, "title": title, "labels": labels, "link": link};
            $thisBtn.button('loading');

            $.ajax({
                "url": "/admin/live/relation/videos/save",
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
                    $thisBtn.button('reset');
                },
                "error": function () {
                    alert("保存失败");
                    $thisBtn.button('reset');
                }
            });
            return false;
        }

        function delVideo(thisBtn, id) {
            if (!confirm("是否确认删除此视频？")){
                return;
            }
            var $thisBtn = $(thisBtn);
            $thisBtn.button('loading');

            $.ajax({
                "url": "/admin/live/relation/videos/del",
                "type": "post",
                "dataType": "json",
                "data": {"id": id},
                "success": function (json) {
                    if (json) {
                        alert(json.msg);
                        if (json.code == 200) {
                            location.reload();
                        }
                    } else {
                        alert("删除失败");
                    }
                    $thisBtn.button('reset');
                },
                "error": function () {
                    alert("删除失败");
                    $thisBtn.button('reset');
                }
            });
            return false;
        }
    </script>

    <script type="text/javascript">
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
@endsection