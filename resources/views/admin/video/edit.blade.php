@extends('admin.layout.nav')
@section("css")
    <style>
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
    <h1 class="page-header">{{isset($video) ? "修改视频" : "新建视频"}}</h1>
    <div class="row">
        <div class="col-lg-12">
            <form class="form" method="post" action="/admin/live/video/save/">
                {{ csrf_field() }}
                <input type="hidden" name="id" value="{{ isset($video) ? $video->id:'' }}">
                <input type="hidden" name="tags">

                <div class="input-group form-group">
                    <span class="input-group-addon">视频标题</span>
                    <input type="text"
                           name="title"
                           value="{{ session('title', isset($video) ? $video->title:'') }}"
                           class="form-control"
                           placeholder="标题"
                           required autofocus>
                    <span class="input-group-addon">{{isset($video) ? mb_strlen($video->title) : 0}}字</span>
                </div>

                <div class="input-group form-group">
                    <span class="input-group-addon">播放链接</span>
                    <input type="text"
                           name="link"
                           value="{{ session('link', isset($video) ? $video->link:'') }}"
                           class="form-control"
                           placeholder="播放链接"
                           required autofocus>
                </div>

                <div class="input-group form-group col-lg-3">
                    <span class="input-group-addon">播放方式</span>
                    <select class="form-control" name="player">
                        @foreach($players as $id=>$player)
                        <option @if(isset($video) && $video->player == $id) selected @endif value="{{$id}}">{{$player}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="input-group form-group col-lg-3">
                    <span class="input-group-addon">播放平台</span>
                    <select class="form-control" name="platform">
                        @foreach($platforms as $id=>$platform)
                        <option @if(isset($video) && $video->platform == $id) @endif value="{{$id}}">{{$platform}}</option>
                        @endforeach
                    </select>
                </div>

                @include("admin.tag.add_tag_cell")

                <div class="checkbox">
                    <button type="button" class="btn btn-sm btn-default" onclick="uploadCover()">
                        <span class="glyphicon glyphicon-upload"></span>上传封面
                    </button>
                </div>

                <img class="img-thumbnail" id="coverImage" src="{{ isset($video) ? $video->image:'' }}" style="display: {{!empty($video->image) ? 'block' : 'none'}};" >
                <input type="hidden" name="image" id="coverImageInput" value="{{ isset($video) ? $video->image : '' }}">

                <div style="text-align: center;margin-top: 10px;">
                    <button type="button" onclick="save()" class="btn btn-info">保存</button>
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
    <script type="text/javascript" src="/js/admin/articleTag.js"></script>
    <!-- 实例化编辑器 -->
    <script type="text/javascript">
        if ($('#coverImage')[0].src == '') {
            $('#coverImage').hide();
        }


        function uploadCover() {
            $('#ImageBrowse').click();
        }

        function changeCoverImage() {
            $('.btn').button('loading');
            var formData = new FormData($('#imageUploadForm')[0]);
            $.ajax({
                type: 'POST',
                url: '/admin/upload/cover',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    $('#coverImageInput')[0].value = $('#coverImage')[0].src = data;
                    $('#coverImage').show();
                    $('.btn').button('reset');
                },
                error: function (data) {
                    console.log("error");
                    $('.btn').button('reset');
                }
            });
        }

        function formVerify(form) {//验证
            var title = $.trim(form.title.value);
            var link = $.trim(form.link.value);
            var image = form.image.value;
            var sport = form.sport.value;
            var match = $("#match_tag li:gt(0)");
            var team = $("#team_tag li:gt(0)");
            var player = $("#player_tag li:gt(0)");

            if (title.length == 0) {
                toastr.error('必须填写标题');
                return false;
            }

            if (title.length > 64) {
                toastr.error('标题不能大于64字符');
                return false;
            }

            if (link.length == 0) {
                toastr.error('必须填写播放链接');
                return false;
            }
            if (link.length > 500) {
                toastr.error('播放链接不能大于500字符');
                return false;
            }
            if (sport == "") {
                toastr.error('必须选择竞技');
                return false;
            }
            if (image == "") {
                toastr.error('必须上传封面图');
                return false;
            }
            if (match.length == 0 && team.length == 0 && player.length == 0) {
                if (!confirm("还未填写标签，是否继续保存？")) {
                    return false;
                }
            }

            return true;
        }

        function save() {
            form = $('form')[0];
            form.action.value = 'save';
            if (formVerify(form)) {
                form.tags.value = formatTags("match_tag", "team_tag", "player_tag");
                postForm(form);
            }
        }

        function postForm(form) {
            $('.btn').button('loading');
            var formData = new FormData(form);
            console.log(formData);
            $.ajax({
                type: 'POST',
                url: '/admin/live/videos/save',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    if (data.code == 200) {
                        form.id.value = data.id;
                        alert("保存成功！");
                        location.href = "/admin/live/videos/edit?id=" + data.id;
                    } else {
                        toastr.error(data.message);
                    }
                    $('.btn').button('reset');
                },
                error: function (data) {
                    alert('保存失败');
                    $('.btn').button('reset');
                }
            });
        }

        $(function () {
            $("input[name=title]").keyup(function () {
                var count = this.value.length;
                $(this).next().html(count + '字');
            });
            @if(isset($tags["sport"]))
            findLeagueTag($("#sport").val(), "league");//获取赛事、联赛标签
            @endif
        });
    </script>
@endsection