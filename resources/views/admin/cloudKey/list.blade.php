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
                    <th>
                        <button type="submit" class="btn btn-default">增加</button>
                    </th>
                </tr>
                <tr>
                    <th>关键字</th>
                    <th style="width: 60%">链接</th>
                    <th>操作</th>
                </tr>
            </form>
            </thead>
            <tbody>
            @foreach($filters as $filter)
                <tr>
                    <td><input name="" value="{{ $filter->keyword }}"></td>
                    <td><input style="width: 100%" value="{{ $filter->url }}"></td>
                    <td><a class="btn btn-sm btn-danger" href="javascript:void(0);" onclick="if(confirm('确定删除？'))location.href='/admin/cloudkeyword/update?status=-1&id='+('{{ $filter->id }}');">删除</a></td>
                </tr>
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
