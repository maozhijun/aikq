@extends('admin.layout.nav')
@section('css')
    <style>
        .input-form {
            display: inline;
            vertical-align: bottom;
            float: left;
        }
    </style>
@endsection
@section('content')
    <h1 class="page-header" style="margin-bottom: 5px;">专题集锦列表</h1>
    <div style="margin-bottom: 15px;">
        <a href="/admin/subject/specimens/edit" target="_blank" class="btn btn-primary">添加集锦</a>
        <div style="margin-top: 15px;">
            <form action="/admin/subject/specimens" method="get">
                <select name="s_lid" style="width: 130px;" class="form-control input-form">
                    <option value="">联赛专题</option>
                    @foreach($leagues as $league)
                        <option value="{{$league->id}}" @if(request('s_lid', '') == $league->id) selected @endif >{{$league->name}}</option>
                    @endforeach
                </select>
                <input name="title" style="width: 260px; margin-left: 10px;" value="{{request('title', '')}}" class="form-control input-form" placeholder="标题">
                <button style="margin-left: 10px;" class="btn btn-primary">搜索</button>
            </form>
            <div style="clear: both"></div>
        </div>
    </div>
    <div class="row placeholders">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>基本信息</th>
                    <th>封面</th>
                    <th>播放参数</th>
                    <th width="120px;">操作</th>
                </tr>
                </thead>
                <tbody style="text-align: left;">
                @foreach($page as $specimen)
                    <tr>
                        <td>
                            <p><b>专题：{{isset($specimen->subjectLeague) ? $specimen->subjectLeague->name : ''}}</b></p>
                            <p><b>标题：</b>{{$specimen->title}}</p>
                        </td>
                        <td>
                            <img src="{{$specimen->cover}}" style="max-width: 200px;max-height: 200px;" >
                        </td>
                        <td>
                            <p><b>链接：</b>{{$specimen->link}}</p>
                            <p><b>播放：</b>{{$specimen->playerCn()}}</p>
                            <p><b>显示：</b>{{$specimen->showCn()}}</p>
                            <p><b>排序：</b>{{$specimen->od}}</p>
                        </td>
                        <td>
                            <a class="btn btn-primary btn-sm" href="/admin/subject/specimens/edit?id={{$specimen->id}}" target="_blank">修改</a>
                            <a type="button" class="btn btn-danger btn-sm" onclick="delSp(this, '{{$specimen->id}}');">删除</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
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
        function delSp(thisObj, id) {
            if (!confirm('是否确认删除此集锦？')) {
                return;
            }
            thisObj.setAttribute('disabled', 'disabled');
            $.ajax({
                "url": "/admin/subject/specimens/del",
                "type": "post",
                "data": {"id": id},
                "dataType": "json",
                "success": function (json) {
                    if (json) {
                        alert(json.msg);
                        if (json.code == 200) location.reload();
                    } else {
                        alert("删除失败");
                    }
                    thisObj.removeAttribute('disabled');
                },
                "error": function () {
                    alert("删除失败");
                    thisObj.removeAttribute('disabled');
                }
            });
        }
    </script>
@endsection