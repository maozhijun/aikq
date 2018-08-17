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
    <h1 class="page-header">打卡记录列表</h1>
    <div class="row placeholders">
        <div class="table-responsive">
            <div style="margin-left: 35px;">
                <form class="navbar-form navbar-left" action="" style="padding: 0px;">
                    <div class="input-group input-group-sm">
                        <span class="input-group-addon">值班人员</span>
                        <input class="form-control input-sm" type="text" name="name" value="{{request('name')}}">
                    </div>
                    <div class="input-group input-group-sm">
                        <span class="input-group-addon">状态</span>
                        <select name="status" class="form-control">
                            <option value="">全部</option>
                            <option value="1" @if(request('status') == '1') selected @endif >值班中</option>
                            <option value="2" @if(request('status') == '2') selected @endif >下班</option>
                        </select>
                    </div>
                    <div class="input-group input-group-sm">
                        <span class="input-group-addon">打卡开始</span>
                        <input class="form-control input-sm form_datetime" type="text" name="start" value="{{request('start')}}">
                    </div>
                    <div class="input-group input-group-sm">
                        <span class="input-group-addon">打卡结束</span>
                        <input class="form-control input-sm form_datetime" type="text" name="end" value="{{request('end')}}">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">搜索</button>
                </form>
            </div>
            <table class="table table-striped" style="border-top: 1px solid #ccc">
                <thead>
                <tr>
                    <th>#</th>
                    <th>值班人员</th>
                    <th>状态</th>
                    <th>打卡时间</th>
                </tr>
                </thead>
                <tbody>
                @foreach($page as $sign)
                    <tr>
                        <td>{{ $sign->id }}</td>
                        <td>{{ $sign->name }}</td>
                        <td>{{ $sign->getStatusCn() }}</td>
                        <td>
                            上班：{{substr($sign->on_time, 0, 16)}}
                            <span style="margin-left: 30px;color: #00b7ee">下班：{{substr($sign->off_time, 0, 16)}}</span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$page or ''}}
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

        function selectManager(thisObj) {
            var $obj = $(thisObj);
            var $tr = $obj.parent().parent();
            var $name = $tr.find("input[name=name]");
            var index = thisObj.selectedIndex;
            var name = thisObj.options[index].text;
            $name.val(name);
        }

        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        });


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

        function saveDuty(thisObj) {
            var $obj = $(thisObj);
            var $tr = $obj.parent().parent();

            var id = $tr.find("input[name=id]").val();
            var name = $tr.find("input[name=name]").val();
            var start_date = $tr.find("input[name=start_date]").val();
            var end_date = $tr.find("input[name=end_date]").val();
            var openid = $tr.find("select[name=openid]").val();

            if (name == "") {
                alert("请选择值班人员");
                return;
            }
            if (start_date == "") {
                alert("请填写值班开始时间");
                return;
            }
            if (end_date == "") {
                alert("请填写值班结束时间");
                return;
            }
            if (end_date <= start_date) {
                alert("值班结束时间必须大于值班开始时间");
                return;
            }

            var data = {"id": id, "name": name, "start_date": start_date, "end_date": end_date, "openid": openid};

            $.ajax({
                "url": "/admin/live/duties/save",
                "type": "post",
                "data": data,
                "dataType": "json",
                "success": function (json) {
                    if (json) {
                        alert(json.message);
                        if (json.code == 200) {
                            location.reload();
                        }
                    } else {
                        alert("保存失败");
                    }
                }
            });
        }

        function delDuty(id) {
            if (!confirm("是否确认删除值班？")) {
                return;
            }
            $.ajax({
                "url": "/admin/live/duties/del",
                "type": "post",
                "data": {"id": id},
                "dataType": "json",
                "success": function (json) {
                    if (json) {
                        alert(json.message);
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