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
    <h1 class="page-header">标签列表</h1>
    <div class="row placeholders">
        <div class="table-responsive">
            <div style="margin-left: 10px;" >
                <form action="" class="navbar-form navbar-left" action="" style="padding: 0px;margin-bottom: 20px;">
                    <div class="input-group input-group-sm">
                        <span class="input-group-addon">标签</span>
                        <input class="form-control input-sm" type="text" name="label" value="{{request('label')}}">
                    </div>
                    {{--<div class="input-group input-group-sm">--}}
                        {{--<span class="input-group-addon">同义词</span>--}}
                        {{--<input class="form-control input-sm" type="text" name="group" value="{{request('group')}}">--}}
                    {{--</div>--}}
                    <button type="submit" class="btn btn-primary btn-sm">搜索</button>
                </form>
            </div>

            <table class="table table-striped tbody-left" style="border-top: 1px solid #eee;">
                <thead>
                <tr>
                    <th width="130px;">#</th>
                    <th width="200px;">标签名称</th>
                    <th>同义词</th>
                    <th width="250px;">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($page as $label)
                    <tr>
                        <td>
                            <input type="hidden" name="id" value="{{ $label->id }}">
                            {{ $label->id }}
                        </td>
                        <td>{{ $label->label }}</td>
                        <td>
                            <?php
                                $sameLabels = $label->sameLabels();
                            ?>
                            @foreach($sameLabels as $same)
                                <a class="btn btn-xs btn-danger" onclick="delLabelGroup(this, '{{$same->id}}', '{{$label->id}}');"><span class="glyphicon glyphicon-remove"></span>{{$same->label}}</a>
                            @endforeach
                        </td>
                        <td style="vertical-align: center;">
                            <div class="input-group input-group-sm" style="width: 200px;float: left;">
                                <input class="form-control input-sm" type="text" name="label" value="">
                                <span class="input-group-addon"
                                      style="cursor: pointer;background-color: rgb(36,189,99);color: white;"
                                      onclick="saveLabelGroup(this, '{{$label->id}}');">添加近义词</span>
                            </div>
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
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        });

        function saveLabelGroup(thisBtn, labelId) {
            var $thisBtn = $(thisBtn);
            var sameLabel = $thisBtn.prev().val();
            if ($.trim(sameLabel) == "") {
                alert("近义词不能为空");
                return;
            }
            if (!confirm("是否确认添加近义词？")) {
                return;
            }

            var $thisBtn = $(thisBtn);
            thisBtn.setAttribute('disabled', 'disabled');
            $.ajax({
                "url": "/admin/labels/group/save",
                "type": "post",
                "dataType": "json",
                "data": {"label_id": labelId, "label": sameLabel},
                "success": function (json) {
                    if (json) {
                        alert(json.msg);
                        if (json.code == 200) {
                            location.reload();
                        }
                    } else {
                        alert("保存义词失败");
                    }
                    thisBtn.removeAttribute('disabled');
                },
                "error": function () {
                    alert("保存义词失败");
                    thisBtn.removeAttribute('disabled');
                }
            });
        }

        function delLabelGroup(thisBtn, sameId, labelId) {
            if (!confirm("是否确认删除此近义词关系？")) {
                return;
            }
            var $thisBtn = $(thisBtn);
            $thisBtn.button('loading');
            $.ajax({
                "url": "/admin/labels/group/del",
                "type": "post",
                "dataType": "json",
                "data": {"label_id": labelId, "same_id": sameId},
                "success": function (json) {
                    if (json) {
                        alert(json.msg);
                        if (json.code == 200) {
                            location.reload();
                        }
                    } else {
                        alert("删除近义词失败");
                    }
                    $thisBtn.button('reset');
                },
                "error": function () {
                    alert("删除近义词失败");
                    $thisBtn.button('reset');
                }
            });
        }
    </script>
@endsection