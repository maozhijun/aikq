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
    <h1 class="page-header">直播线路中断记录</h1>
    <div class="row placeholders">
        <div class="table-responsive">
            <div style="margin-left: 35px;">
                <form class="navbar-form navbar-left" action="/admin/live/off/logs" style="padding: 0px;">
                    <div class="input-group input-group-sm">
                        <span class="input-group-addon">球队名称</span>
                        <input class="form-control input-sm" style="width: 160px;" name="mname" value="{{request('mname')}}">
                    </div>
                    {{--<div class="input-group input-group-sm">--}}
                        {{--<span class="input-group-addon">类型</span>--}}
                        {{--<select class="form-control" name="status">--}}
                            {{--<option value="">全部</option>--}}
                            {{--<option value="1" @if(request('status') == '1') selected @endif >新增线路</option>--}}
                            {{--<option value="2" @if(request('status') == '2') selected @endif >修改线路</option>--}}
                        {{--</select>--}}
                    {{--</div>--}}
                    <div class="input-group input-group-sm">
                        <span class="input-group-addon">记录开始时间</span>
                        <input class="form-control input-sm form_datetime" style="width: 140px;" type="text" name="start" value="{{request('start')}}">
                    </div>
                    <div class="input-group input-group-sm">
                        <span class="input-group-addon">记录结束时间</span>
                        <input class="form-control input-sm form_datetime" style="width: 140px;" type="text" name="end" value="{{request('end')}}">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">搜索</button>
                </form>
            </div>
            <table class="table table-striped" style="border-top: 1px solid #ccc">
                <thead>
                <tr>
                    <th style="width: 130px;">记录时间</th>
                    <th style="width: 130px;">比赛时间</th>
                    <th>比赛</th>
                    <th>状态</th>
                    <th>线路</th>
                </tr>
                </thead>
                <tbody>
                @foreach($page as $log)
                    <tr>
                        <td>
                            <input type="hidden" name="id" value="{{ $log->id }}">
                            {{substr($log->created_at, 0, 16)}}
                        </td>
                        <td>{{substr($log->match_time, 0, 16)}}</td>
                        <td>{{$log->hname}} VS {{$log->aname}}</td>
                        <td>
                            <label class="label label-{{$log->live_status == 1 ? 'success' : 'danger'}}">{{$log->getStatusCn()}}</label>
                        </td>
                        <td>{{$log->getPlatformCn()}}线路《{{$log->ch_name}}》</td>
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