@extends('admin.layout.nav')

@section('content')
    <div class="table-responsive">
        <h3>敏感关键字</h3>
        <div class="panel-heading">
            <form role="search" class="navbar-form navbar-left" action='/admin/filter/'>
                <div class="form-group">
                    <input class="form-control" placeholder="关键字" type="text" name="key"
                           value="{{ request('key','') }}">
                </div>
                <button type="submit" class="btn btn-default">搜索</button>
            </form>
        </div>

        <table class="table table-striped">
            <thead>
            <form role="search" class="navbar-form navbar-left" action='/admin/filter/add'>
                <tr>
                    <th><input type="text" placeholder="关键字" name="key"></th>
                    <th>
                        <button type="submit" class="btn btn-default">增加</button>
                    </th>
                </tr>
                <tr>
                    <th>关键字</th>
                    <th>操作</th>
                </tr>
            </form>
            </thead>
            <tbody>
            @foreach($filters as $filter)
                <tr>
                    <td>{{ $filter->key }}</td>
                    <td><a class="btn btn-sm btn-danger" href="javascript:void(0);" onclick="if(confirm('确定删除？'))location.href='/admin/filter/del?key='+encodeURIComponent('{{ $filter->key }}');">删除</a></td>
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
