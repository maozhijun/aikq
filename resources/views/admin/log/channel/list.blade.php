@extends('admin.layout.nav')
@section("css")
    <style>
        select {
            height: 26px;
        }
        .form_datetime {
            width: 150px;
        }
        tbody {
            text-align: left;
        }
    </style>
@endsection
@section('content')
    <h1 class="page-header">直播线路操作记录</h1>
    <div class="row placeholders">
        <div class="table-responsive">
            <div style="margin-left: 35px;">
                <form class="navbar-form navbar-left" action="/admin/live/duties" style="padding: 0px;">
                    <div class="input-group input-group-sm">
                        <span class="input-group-addon">值班人员</span>
                        <select class="form-control input-sm" type="text" name="name">
                            <option value="">请选择</option>
                        </select>
                    </div>
                    <div class="input-group input-group-sm">
                        <span class="input-group-addon">值班开始</span>
                        <input class="form-control input-sm form_datetime" type="text" name="start_date" value="{{request('start_date')}}">
                    </div>
                    <div class="input-group input-group-sm">
                        <span class="input-group-addon">直播结束</span>
                        <input class="form-control input-sm form_datetime" type="text" name="end_date" value="{{request('end_date')}}">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">搜索</button>
                </form>
            </div>
            <table class="table table-striped" style="border-top: 1px solid #ccc">
                <thead>
                <tr>
                    <th>#</th>
                    <th style="width: 130px;">操作时间</th>
                    <th>操作记录</th>
                </tr>
                </thead>
                <tbody>
                @foreach($page as $log)
                    <tr>
                        <td>
                            <h5>{{ $log->id }}</h5>
                            <input type="hidden" name="id" value="{{ $log->id }}">
                        </td>
                        <td>{{substr($log->created_at, 0, 16)}}</td>
                        <td>
                            @if($log->status == 1)
                                <div>
                                    操作人：{{$log->newAdminName()}}
                                </div>
                            @else
                                <div>
                                    原操作人：{{$log->oldAdminName()}}
                                    操作内容：{{$log->old_name}}
                                </div>
                                <hr>
                                <div>
                                    修改人：{{$log->oldAdminName()}}
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$page}}
        </div>
    </div>
@endsection

@section('js')
    <link href="/css/admin/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
    <script type="text/javascript" src="/js/admin/bootstrap-datetimepicker.min.js" charset="UTF-8"></script>
    <script type="text/javascript" src="/js/admin/locales/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>
    <script>
        $(".form_datetime").datetimepicker({
            "language": 'zh-CN',
            "weekStart": 1,
            "todayBtn": 1,
            "autoclose": 1,
            "todayHighlight": 1,
            "startView": 2,
            "minView": 0,
            "forceParse": 0
        });
    </script>

    <script type="text/javascript">


    </script>
@endsection