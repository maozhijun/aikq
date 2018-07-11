@extends('admin.layout.nav')
@section('content')
    <h1 class="page-header">权限列表</h1>
    <div class="row placeholders">
        <div class="table-responsive">
            <div style="margin-left: 35px;">
                <form role="search" class="navbar-form navbar-left" action="" style="padding: 0px;">
                    <div class="input-group input-group-sm">
                        <span class="input-group-addon">权限名称</span>
                        <input class="form-control input-sm" type="text" name="name" value="{{request('name')}}">
                    </div>
                    <div class="input-group input-group-sm">
                        <span class="input-group-addon">父权限ID</span>
                        <input class="form-control input-sm" type="text" name="parent" value="{{request('parent')}}">
                    </div>
                    <div class="input-group input-group-sm">
                        <span class="input-group-addon">是否目录</span>
                        <select name="isMenu" style="height: 30px;">
                            <option value="">全部</option>
                            <option value="1" @if(request('isMenu') == '1') selected @endif>是</option>
                            <option value="2" @if(request('isMenu') == '2') selected @endif>否</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">搜索</button>
                </form>
            </div>
            <table class="table table-striped" style="border-top: 1px solid #ccc">
                <thead>
                <tr>
                    <form id="formRes" action="/admin/resources/save" method="post" onsubmit="return submitRes();">
                        <th></th>
                        <th>
                            <input name="name" class="form-control" placeholder="权限名称" required>
                        </th>
                        <th>
                            <input name="action" class="form-control" placeholder="访问路径" required>
                        </th>
                        <th>
                            <input type="number" name="parent" class="form-control" placeholder="父权限">
                        </th>
                        <th>
                            <select name="is_menu" class="form-control">
                                <option value="2">否</option>
                                <option value="1">是</option>
                            </select>
                        </th>
                        <th>
                            <input type="number" name="menu_level" class="form-control" placeholder="目录级别">
                        </th>
                        <th>
                            <input type="number" name="od" class="form-control" placeholder="排序">
                        </th>
                        <th>
                            <button type="submit" class="btn btn-sm btn-primary">
                                <span class="glyphicon glyphicon-plus"></span>新建
                            </button>
                        </th>
                    </form>
                </tr>
                <tr>
                    <th>#</th>
                    <th>权限名称</th>
                    <th>访问路径</th>
                    <th style="width: 100px;">父权限</th>
                    <th style="width: 90px;">是否目录</th>
                    <th style="width: 120px;">目录级别</th>
                    <th style="width: 90px;">排序</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($resources as $resource)
                    <tr>
                        <td><h5>{{ $resource->id }}</h5></td>
                        <td>
                            <input id="name_{{ $resource->id }}" class="form-control" value="{{ $resource->name }}" required>
                        </td>
                        <td>
                            <input id="action_{{ $resource->id }}" class="form-control" value="{{ $resource->action }}" required>
                        </td>
                        <td>
                            <input id="parent_{{ $resource->id }}" class="form-control" value="{{ $resource->parent }}" required>
                        </td>
                        <td>
                            <select class="form-control" id="is_menu_{{ $resource->id }}">
                                <option value="2">否</option>
                                <option value="1" @if($resource->is_menu == 1) selected @endif>是</option>
                            </select>
                        </td>
                        <td>
                            <input id="menu_level_{{ $resource->id }}" class="form-control" value="{{$resource->menu_level}}" required>
                        </td>
                        <td>
                            <input id="od_{{ $resource->id }}" class="form-control" value="{{$resource->od}}" required>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="editRes('{{$resource->id}}');">
                                <span class="glyphicon glyphicon-ok"></span>保存
                            </button>

                            <button class="btn btn-sm btn-danger" onclick="delRes('{{ $resource->id }}');">
                                <span class="glyphicon glyphicon-remove"></span>删除
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$resources}}
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">

        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        });

        function submitRes() {

            var data = $("#formRes").serialize();

            saveRes(data);

            return false;
        }

        function editRes(id) {
            if (!confirm("是否确认修改？")) {
                return;
            }

            var name = $("#name_" + id).val();
            var action = $("#action_" + id).val();
            var parent = $("#parent_" + id).val();
            var is_menu = $("#is_menu_" + id).val();
            var menu_level = $("#menu_level_" + id).val();
            var od = $("#od_" + id).val();

            var data = {};
            data["id"] = id;
            data["name"] = name;
            data["action"] = action;
            data["parent"] = parent;
            data["is_menu"] = is_menu;
            data["menu_level"] = menu_level;
            data["od"] = od;

            saveRes(data);
        }

        function saveRes(data) {
            $.ajax({
                "url": "/admin/resources/save",
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
                        alert("保存失败");
                    }
                }
            });
        }

        function delRes(id) {
            if (!confirm("是否确认删除权限？")) {
                return;
            }
            $.ajax({
                "url": "/admin/resources/del",
                "type": "post",
                "data": {"id": id},
                "dataType": "json",
                "success": function (json) {
                    if (json) {
                        alert(json.msg);
                        if (json.code == 200) {
                            location.reload();
                        }
                    } else {
                        alert("保存失败");
                    }
                }
            });
        }
    </script>
@endsection