@extends('admin.layout.nav')

@section('content')
    <h1 class="page-header">账号列表</h1>

    <div class="row placeholders">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <form id="new_form" action="/admin/account/new/" method="post" onsubmit="return setRoles('new_form');">
                        <th>
                            {{ csrf_field() }}
                            <input type="hidden" name="roles" value="">
                        </th>
                        <th>
                            <input type="text" name="account" class="form-control" placeholder="用户名"
                                   value="{{ session('account','') }}" required>
                        </th>
                        <th>
                            <input type="text" name="email" class="form-control" placeholder="邮箱"
                                   value="{{ session('email','') }}" required>
                        </th>
                        <th>
                            <input type="text" name="name" class="form-control" placeholder="昵称"
                                   value="{{ session('name','') }}" required>
                        </th>
                        <th>
                            <ul style="list-style:none;white-space:nowrap;">
                                @foreach($roles as $role)
                                    <li style="float: left;margin-right: 5px;" >
                                        <input name="role_new_form" type="checkbox" value="{{$role->id}}">{{$role->name}}
                                    </li>
                                @endforeach
                            </ul>
                        </th>
                        <th>
                            <input type="text" name="password" class="form-control" placeholder="密码"
                                   value="{{ session('password','') }}" required>
                        </th>
                        <th>
                            <select name="status" class="form-control" required>
                                <option value="1">有效</option>
                                <option value="0">无效</option>
                            </select>
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
                    <th>用户名</th>
                    <th width="230px;">邮箱</th>
                    <th>昵称</th>
                    <th width="300px;">角色</th>
                    <th>密码</th>
                    <th width="100px;">状态</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($accounts as $account)
                    <form id="edit_{{$account->id}}" action="/admin/account/update/" method="post" onsubmit="return setRoles('edit_{{$account->id}}');">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{ $account->id }}">
                        <input type="hidden" name="roles" value="">
                        <tr>
                            <td>{{ $account->id }}</td>
                            <td>
                                <input type="text" name="account" class="form-control" placeholder="用户名"
                                       value="{{ $account->account }}" required>
                            </td>
                            <td>
                                <input type="text" name="email" class="form-control" placeholder="邮箱"
                                       value="{{ $account->email }}" required>
                            </td>
                            <td>
                                <input type="text" name="name" class="form-control" placeholder="昵称"
                                       value="{{ $account->name }}" required>
                            </td>
                            <td>
                                <ul style="list-style:none;white-space:nowrap;">
                                @foreach($roles as $role)
                                    <li style="float: left;margin-right: 5px;" >
                                        <input name="role_edit_{{$account->id}}" @if($account->hasRole($role->id)) checked @endif type="checkbox" value="{{$role->id}}">{{$role->name}}
                                    </li>
                                @endforeach
                                </ul>
                            </td>
                            <td>
                                <input type="text" name="password" class="form-control" placeholder="密码"
                                       value="******" required>
                            </td>
                            <td>
                                <select name="status" class="form-control" required>
                                    <option value="1" {{ $account->status==1?'selected':'' }}>有效</option>
                                    <option value="0" {{ $account->status==0?'selected':'' }}>无效</option>
                                </select>
                            </td>
                            <td>
                                <button type="submit" class="btn btn-xs btn-info">
                                    <span class="glyphicon glyphicon-ok"></span>保存
                                </button>
                                <a class="btn btn-xs btn-danger" href="/admin/account/delete?id={{ $account->id }}">
                                    <span class="glyphicon glyphicon-remove"></span>删除
                                </a>
                            </td>
                        </tr>
                    </form>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        function setRoles(id) {
            var roles = $("input[type=checkbox][name=role_" + id + "]:checked");//$("input[name=role_id]:checked");
            var roles_str = "";
            $.each(roles, function (index, obj) {
                roles_str += (index == 0) ? obj.value : ("," + obj.value);
            });
            $("#" + id)[0].roles.value = roles_str;
        }
    </script>
@endsection