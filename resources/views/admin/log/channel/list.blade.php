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
                <form class="navbar-form navbar-left" action="/admin/live/channel/logs" style="padding: 0px;">
                    <div class="input-group input-group-sm">
                        <span class="input-group-addon">操作人</span>
                        <input class="form-control input-sm" style="width: 80px;" name="admin" value="{{request('admin')}}">
                    </div>
                    <div class="input-group input-group-sm">
                        <span class="input-group-addon">球队名称</span>
                        <input class="form-control input-sm" style="width: 80px;" name="mname" value="{{request('mname')}}">
                    </div>
                    <div class="input-group input-group-sm">
                        <span class="input-group-addon">类型</span>
                        <select class="form-control" name="status">
                            <option value="">全部</option>
                            <option value="1" @if(request('status') == '1') selected @endif >新增线路</option>
                            <option value="2" @if(request('status') == '2') selected @endif >修改线路</option>
                        </select>
                    </div>
                    <div class="input-group input-group-sm">
                        <span class="input-group-addon">操作开始</span>
                        <input class="form-control input-sm form_datetime" style="width: 140px;" type="text" name="start" value="{{request('start')}}">
                    </div>
                    <div class="input-group input-group-sm">
                        <span class="input-group-addon">操作结束</span>
                        <input class="form-control input-sm form_datetime" style="width: 140px;" type="text" name="end" value="{{request('end')}}">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">搜索</button>
                </form>
            </div>
            <table class="table table-striped" style="border-top: 1px solid #ccc">
                <thead>
                <tr>
                    <th style="width: 130px;">操作时间</th>
                    <th style="width: 80px;">操作人</th>
                    <th style="width: 80px;">类型</th>
                    <th>比赛</th>
                    <th>操作记录</th>
                </tr>
                </thead>
                <tbody>
                @foreach($page as $log)
                    <tr>
                        <td>
                            <input type="hidden" name="id" value="{{ $log->id }}">
                            {{substr($log->created_at, 0, 16)}}
                        </td>
                        <td>{{$log->admin_name}}</td>
                        <td>{{$log->getStatusCn()}}</td>
                        <td>
                            <p>{{$log->hname}}</p>
                            <p>VS</p>
                            <p>{{$log->aname}}</p>
                        </td>
                        <td>
                            @if($log->status == 1)
                                <div>
                                    <label class="label label-info">
                                        {{$log->new_name}} &nbsp;&nbsp;&nbsp;{{$log->getNewShow()}}
                                        &nbsp;&nbsp;&nbsp;{{$log->getNewPlatform()}}
                                        &nbsp;&nbsp;&nbsp;{{$log->getNewPrivate()}}
                                        &nbsp;&nbsp;&nbsp;{{$log->getNewPlayerCn()}}
                                        &nbsp;&nbsp;&nbsp;排序：{{$log->new_od or '-'}}
                                    </label>
                                    <p style="margin-top: 5px;">
                                        <label class="label label-danger">线路内容：{{$log->new_content}}</label>
                                    </p>
                                </div>
                            @else
                                <div>
                                    <label class="label label-primary">原线路：</label>
                                    <label class="label label-default">
                                        {{$log->old_name}} &nbsp;&nbsp;&nbsp;{{$log->getOldShow()}}
                                        &nbsp;&nbsp;&nbsp;{{$log->getOldPlatform()}}
                                        &nbsp;&nbsp;&nbsp;{{$log->getOldPrivate()}}
                                        &nbsp;&nbsp;&nbsp;{{$log->getOldPlayerCn()}}
                                        &nbsp;&nbsp;&nbsp;排序：{{$log->old_od or '-'}}
                                    </label>
                                    <p style="margin-top: 5px;">
                                        <label class="label label-danger">线路内容：{{$log->old_content}}</label>
                                    </p>
                                </div>
                                <hr>
                                <div>
                                    <label class="label label-success">新线路：</label>
                                    <label class="label label-info">
                                        {{$log->new_name}} &nbsp;&nbsp;&nbsp;{{$log->getNewShow()}}
                                        &nbsp;&nbsp;&nbsp;{{$log->getNewPlatform()}}
                                        &nbsp;&nbsp;&nbsp;{{$log->getNewPrivate()}}
                                        &nbsp;&nbsp;&nbsp;{{$log->getNewPlayerCn()}}
                                        &nbsp;&nbsp;&nbsp;排序：{{$log->new_od or '-'}}
                                    </label>
                                    <p style="margin-top: 5px;">
                                        <label class="label label-danger">线路内容：{{$log->new_content}}</label>
                                    </p>
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