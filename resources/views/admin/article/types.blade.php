@extends('admin.layout.nav')
@section("css")
    <style>
        select {
            height: 26px;
        }
        .tbody-left tbody td {
            text-align: left;
        }
    </style>
@endsection
@section('content')
    <h1 class="page-header">文章类型列表</h1>
    <div class="row placeholders">
        <div class="table-responsive">
            <div style="margin-left: 10px;" >
                <form action="/admin/article/types" class="navbar-form navbar-left" action="" style="padding: 0px;margin-bottom: 20px;">
                    <div class="input-group input-group-sm">
                        <span class="input-group-addon">分类名称</span>
                        <input class="form-control input-sm" type="text" name="name" value="{{request('name')}}">
                    </div>
                    <div class="input-group input-group-sm">
                        <span class="input-group-addon">英文名称</span>
                        <input class="form-control input-sm" type="text" name="name_en" value="{{request('name_en')}}">
                    </div>
                    <div class="input-group input-group-sm">
                        <span class="input-group-addon">状态</span>
                        <select name="status" style="height: 30px;">
                            <option value="">全部</option>
                            <option value="1" @if(request('status') == 1) selected @endif >显示</option>
                            <option value="2" @if(request('status') == 2) selected @endif >不显示</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">搜索</button>
                </form>
            </div>

            <table class="table table-striped tbody-left" style="border-top: 1px solid #eee;">
                <thead>
                <tr>
                    <form id="new_form" action="/admin/article/types/save" method="post" onsubmit="return setRoles('new_form');">
                        <th>
                            {{ csrf_field() }}
                            <input type="hidden" name="roles" value="">
                        </th>
                        <th>
                            <input type="text" name="name" placeholder="分类名称"
                                   value="{{ session('name','') }}" required>
                        </th>
                        <th>
                            <input type="text" name="name_en" placeholder="英文名称"
                                   value="{{ session('name_en','') }}" required>
                        </th>
                        <th>
                            <input type="text" name="status" placeholder="排序" style="width: 50px;"
                                   value="{{ session('status','') }}">
                        </th>
                        <th>
                            <select name="status">
                                <option value="1" {{ request('status') == 1 ? 'selected': '' }}>显示</option>
                                <option value="0" {{ request('status') == 2 ? 'selected': '' }}>不显示</option>
                            </select>
                        </th>
                        <th colspan="2">
                            <button type="submit" class="btn btn-sm btn-success">
                                <span class="glyphicon glyphicon-plus"></span>新建
                            </button>
                        </th>
                    </form>
                </tr>
                <tr>
                    <th>#</th>
                    <th>分类名称</th>
                    <th>英文名称</th>
                    <th>排序</th>
                    <th>状态</th>
                    <th>创建时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($page as $type)
                    <form action="/admin/article/types/save" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{ $type->id }}">
                        <input type="hidden" name="roles" value="">
                        <tr>
                            <td>{{ $type->id }}</td>
                            <td>
                                <input type="text" name="name" value="{{ $type->name }}" required>
                            </td>
                            <td>
                                <input type="text" name="name_en" value="{{ $type->name_en }}" required >
                            </td>
                            <td>
                                <input name="od" value="{{$type->od}}" style="width: 50px;">
                            </td>
                            <td>
                                <select name="status">
                                    <option value="1" {{ $type->status==1?'selected':'' }}>显示</option>
                                    <option value="0" {{ $type->status==2?'selected':'' }}>不显示</option>
                                </select>
                            </td>
                            <td>{{$type->created_at}}</td>
                            <td>
                                <button type="submit" class="btn btn-xs btn-info">
                                    <span class="glyphicon glyphicon-ok"></span>保存
                                </button>
                            </td>
                        </tr>
                    </form>
                @endforeach
                </tbody>
            </table>
            {{$page or ''}}
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
    </script>
@endsection