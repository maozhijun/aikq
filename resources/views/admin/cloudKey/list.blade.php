@extends('admin.layout.nav')

@section('content')
    <div class="table-responsive">
        <h3>云关键字</h3>
        <div class="panel-heading">
            <form role="search" class="navbar-form navbar-left" action='/admin/cloudkeyword/'>
                <div class="form-group">
                    <input class="form-control" placeholder="关键字" type="text" name="key"
                           value="{{ request('key','') }}">
                </div>
                <button type="submit" class="btn btn-default">搜索</button>
            </form>
        </div>

        <table class="table table-striped">
            <thead>
            <form role="search" class="navbar-form navbar-left" action='/admin/cloudkeyword/add'>
                <tr>
                    <th><input type="text" placeholder="关键字" name="key"></th>
                    <th><input type="text" placeholder="url" name="url"></th>
                    <th><input style="width: 50px" type="text" placeholder="等级" name="level"></th>
                    <th>
                        <button type="submit" class="btn btn-default">增加</button>
                    </th>
                </tr>
                <tr>
                    <th>关键字</th>
                    <th style="width: 60%">链接</th>
                    <th style="width: 5%">等级</th>
                    <th>操作</th>
                </tr>
            </form>
            </thead>
            <tbody>
            @foreach($filters as $filter)
                <form action="/admin/cloudkeyword/update" enctype="multipart/form-data" method="post">
                    {{ csrf_field() }}
                <tr>
                    <input style="display: none" name="status" value="{{ $filter->status }}">
                    <input style="display: none" name="id" value="{{ $filter->id }}">
                    <td><input name="key" value="{{ $filter->keyword }}"></td>
                    <td><input style="width: 100%" name="url" value="{{ $filter->url }}"></td>
                    <td><input style="width: 50px" name="level" value="{{ $filter->level }}"></td>
                    <td>
                        <button type="submit" class="btn btn-sm btn-info"><span class="glyphicon glyphicon-ok"></span>保存</button>
                        <a class="btn btn-sm btn-danger" href="javascript:void(0);" onclick="if(confirm('确定删除？'))location.href='/admin/cloudkeyword/update?status=-1&id='+('{{ $filter->id }}');">删除</a>
                    </td>
                </tr>
                </form>
            @endforeach
            </tbody>
        </table>
        {{ $filters->links() }}
    </div>
@endsection

@section('js')
    <script type="text/javascript">

    </script>
@endsection
