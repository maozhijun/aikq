@extends('admin.layout.nav')

@section('content')
    <div class="table-responsive">
        <h3>404 URL</h3>
        <div class="panel-heading">
            <form role="search" class="navbar-form navbar-left" action='/admin/seo/error_url'>
                <div class="form-group">
                    <input class="form-control" placeholder="access log" type="text" name="file"
                           value="{{ request('file','') }}">
                </div>
                <button type="submit" class="btn btn-default">确认</button>
            </form>
        </div>

        <table class="table table-striped">
            <thead>
            </thead>
            <tbody>
            @foreach($urls as $url)
                <form action="/admin/seo/error_url/add" enctype="multipart/form-data" method="post">
                    {{ csrf_field() }}
                <tr>
                    <input style="display: none" value="{{$url}}" name="url" />
                    <td><p style="word-wrap:break-word; width:500px;">{{$url}}</p></td>
                    <td>
                        @if(!in_array($url,$array))
                            <button type="submit" class="btn btn-sm btn-info"><span class="glyphicon glyphicon-ok"></span>增加</button>
                            @else
                            <button type="submit" class="btn btn-sm btn-danger btn-info"><span class="glyphicon glyphicon-ok"></span>已有</button>
                        @endif
                    </td>
                </tr>
                </form>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection

@section('js')
    <script type="text/javascript">

    </script>
@endsection
