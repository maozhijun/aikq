@extends('admin.layout.nav')
@section('content')
    <h1 class="page-header">视频列表</h1>
    <div class="row">
        <div>
            <form class="form-inline">
                <div class="form-group">
                    <label>标题：</label>
                    <input type="text" name="title" value="{{ request('title', '') }}">
                </div>
                <div class="form-group" style="margin-left: 10px;">
                    <label>播放：</label>
                    <select name="player" style="height: 26px;">
                        <option value="">全部</option>
                        <option value="1">iframe</option>
                        <option value="2">m3u8</option>
                        <option value="3">mp4</option>
                        <option value="4">外链</option>
                    </select>
                </div>
                <div class="form-group" style="margin-left: 10px;margin-right: 10px;">
                    <label>竞技：</label>
                    <select name="sport" style="height: 26px;">
                        <option value="">全部</option>
                        <option value="1" @if(request('sport') == '1') selected @endif>足球</option>
                        <option value="2" @if(request('sport') == '2') selected @endif>篮球</option>
                    </select>
                </div>
                <div class="form-group" style="margin-left: 10px;margin-right: 10px;">
                    <label>标签：</label>
                    <input name="tag" value="{{request("tag")}}">
                </div>
                <button type="submit" class="btn btn-primary btn-sm">
                    <span class="glyphicon glyphicon-search"></span>搜索
                </button>
                <a type="button" class="btn btn-success btn-sm" target="_blank" href="/admin/live/videos/edit">新建视频</a>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th width="80px">ID</th>
                    <th width="15%">标题</th>
                    <th width="110px">封面</th>
                    <th width="20%">链接</th>
                    <th width="80px;">平台</th>
                    <th width="88px;">播放</th>
                    <th>标签</th>
                    <th width="100px;">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($pages as $video)
                    <tr>
                        <td>{{$video->id}}</td>
                        <td>{{$video->title}}</td>
                        <td>
                            @if(!empty($video->image))
                            <img src="{{$video->image}}" style="max-width: 100px;max-height: 100px;">
                            @endif
                        </td>
                        <td><div style="word-wrap:break-word;word-break:break-all">{{$video->link}}</div></td>
                        <td>{{$video->platformCn()}}</td>
                        <td>{{$video->playerCn()}}</td>
                        <td>
                            {{$video->tagsCn()}}
                        </td>
                        <td>
                            <a class="btn btn-xs btn-success" target="_blank" href="/admin/live/videos/edit?id={{$video->id}}">编辑</a>
                            @if($video->show == 1)
                                <button type="button" class="btn btn-xs btn-warning" onclick="displayVideo(this, '{{$video->id}}', 2)">隐藏</button>
                            @else
                                <button type="button" class="btn btn-xs btn-info" onclick="displayVideo(this, '{{$video->id}}', 1);">显示</button>
                            @endif
                            <button type="button" class="btn btn-xs btn-danger" onclick="delVideo(this, '{{$video->id}}');">删除</button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ isset($pages) ? $pages->links() : "" }}
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        });

        function delVideo(thisBtn, id) {
            if (!confirm("是否确认删除视频？")) {
                return false;
            }
            var $thisBtn = $(thisBtn);
            $thisBtn.button("loading");
            $.ajax({
                "type": "post",
                "url": "/admin/live/videos/del",
                "data": {"id": id},
                "success": function (data) {
                    if (data.code == 200) {
                        alert("删除成功");
                        location.reload();
                    } else {
                        alert(data.message);
                        $thisBtn.button("reset");
                    }
                },
                "error": function () {
                    alert("删除失败");
                    $thisBtn.button("reset");
                }
            });
        }

        function displayVideo(thisBtn, id, type) {
            var msg = type == 1 ? "显示" : "隐藏";
            if (!confirm("是否确认" + msg + "视频？")) {
                return false;
            }

            var $thisBtn = $(thisBtn);
            $thisBtn.button("loading");
            $.ajax({
                "type": "post",
                "url": "/admin/live/videos/display",
                "data": {"id": id, "type": type},
                "success": function (data) {
                    if (data.code == 200) {
                        alert(msg + "成功");
                        location.reload();
                    } else {
                        alert(data.message);
                        $thisBtn.button("reset");
                    }
                },
                "error": function () {
                    alert("删除失败");
                    $thisBtn.button("reset");
                }
            });
        }
    </script>
@endsection