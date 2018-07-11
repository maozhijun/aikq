@extends('admin.layout.nav')

@section('content')
    <h1 class="page-header">角色列表</h1>
    <div class="row">
        <form action="/admin/roles/">
            <label>角色名称</label>
            <input type="text" placeholder="角色名称" name="name" value="{{request('name')}}">
            <button class="btn btn-sm btn-primary">搜索</button>
            <a class="btn btn-sm btn-success" href="/admin/roles/detail" target="_blank">新建</a>
        </form>
    </div>
    <div class="row">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>角色名称</th>
                    <th>拥有权限</th>
                    <th width="150px;">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($roles as $role)
                <tr>
                    <td>{{$role->id}}</td>
                    <td>{{$role->name}}</td>
                    <td>
                        @if(isset($role->resources))
                        @foreach($role->resources as $index=>$res)
                            {{ $index > 0 ? ('、' . $res->name) : $res->name}}
                        @endforeach
                        @endif
                    </td>
                    <td>
                        <a class="btn btn-sm btn-info" href="/admin/roles/detail?id={{$role->id}}">
                            <span class="glyphicon glyphicon-ok"></span>修改
                        </a>

                        <button class="btn btn-sm btn-danger" onclick="delRole('{{ $role->id }}', '{{$role->name}}');">
                            <span class="glyphicon glyphicon-remove"></span>删除
                        </button>
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
        /**
         * 异步表单验证
         */
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        });

        function delRole(id, name) {
            if (!confirm("是否确认删除角色：《" + name + "》")) {
                return;
            }
            $.ajax({
                "url": "/admin/roles/del",
                "type": "post",
                "dataType": "json",
                "data": {"id": id},
                "success": function (json) {
                    if (json && json.code == 0) {
                        alert("删除成功");
                        location.reload();
                    } else if (json) {
                        alert(json.msg);
                    } else {
                        alert("删除失败");
                    }
                },
                "error": function () {
                    alert("删除失败");
                }
            });
        }

    </script>
@endsection