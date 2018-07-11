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
    <h1 class="page-header" style="margin-bottom: 5px;">专题赛事列表</h1>
    <div style="margin-bottom: 15px;">
        <a href="/admin/subject/leagues/edit" target="_blank" class="btn btn-primary">添加专题赛事</a>
        <div style="margin-top: 15px;">
            <form action="/admin/subject/leagues" method="get">
                <select name="sport" style="width: 100px;" class="form-control input-form">
                    <option value="">类型</option>
                    <option value="1" @if(request('sport', '') == "1") selected @endif >足球</option>
                    <option value="2" @if(request('sport', '') == "2") selected @endif >篮球</option>
                </select>
                <input name="name" style="width: 160px; margin-left: 10px;" value="{{request('name', '')}}" class="form-control input-form" placeholder="赛事名称">
                <select name="status" style="width: 100px; margin-left: 10px;" class="form-control input-form">
                    <option value="">状态</option>
                    <option value="1" @if(request('status', '') == "1") selected @endif >显示</option>
                    <option value="2" @if(request('status', '') == "2") selected @endif >隐藏</option>
                </select>
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
                    <th width="60px;">#</th>
                    <th width="120px;">赛事信息</th>
                    <th>简介</th>
                    <th width="120px;">排序</th>
                    <th width="120px;">操作</th>
                </tr>
                </thead>
                <tbody style="text-align: left;">
                @foreach($page as $s_league)
                    <tr>
                        <td>{{ $s_league->id }}</td>
                        <td>
                            <p><b>{{$s_league->name}}</b>（{{$s_league->sportCn()}}）</p>
                            <p><img src="{{$s_league->icon}}" onerror="this.src='/img/icon_team_default.png'" style="max-width: 200px;max-height: 200px;"></p>
                        </td>
                        <td>{!! $s_league->contentHtml() !!}</td>
                        <td>{{$s_league->od}}</td>
                        <td>
                            <a class="btn btn-primary btn-sm" href="/admin/subject/leagues/edit?id={{$s_league->id}}" target="_blank">修改</a>
                            @if($s_league->status == 1)
                                <button type="button" class="btn btn-danger btn-sm" onclick="changeSL(this, '{{$s_league->id}}', '2');">隐藏</button>
                            @else
                                <button type="button" class="btn btn-success btn-sm" onclick="changeSL(this, '{{$s_league->id}}', '1');">显示</button>
                            @endif
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

        /**
         * 保存外链
         * @param formObj
         * @returns {boolean}
         */
        function changeSL(thisObj, id, status) {
            var msg;
            if (status == 1) {
                msg = "显示";
            } else {
                msg = "隐藏";
            }
            if (!confirm("是否确认" + msg + "专题")) {
                return false;
            }
            thisObj.setAttribute('disabled', 'disabled');
            $.ajax({
                "url": "/admin/subject/leagues/change",
                "data": {"id": id, "status": status},
                "type": "post",
                "dataType": "json",
                "success": function (json) {
                    if (json) {
                        alert(json.msg);
                        if (json.code == 200) {
                            location.reload();
                        }
                    } else {
                        thisObj.removeAttribute('disabled', '');
                    }
                },
                "error": function () {
                    alert("保存失败");
                    thisObj.removeAttribute('disabled', '');
                }
            });
            return false;
        }
    </script>
@endsection