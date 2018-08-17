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
    <h1 class="page-header">直播管理员签到</h1>
    <div class="row placeholders" style="text-align: left;">
        <div class="table-responsive">
            <div style="margin-left: 15px;">
                @if(!isset($sign) || $sign->status == 2)
                    <button type="button" class="btn btn-success sign" onclick="sign(this, 1);">上班打卡</button>
                @else
                    <button type="button" class="btn btn-primary sign" onclick="sign(this, 2);">下班打卡</button>
                @endif
            </div>

        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        });

        function sign(thisObj, status) {
            var msg = status == 1 ? '上班' : '下班';
            if (!confirm("是否确认打卡" + msg)) {
                return;
            }
            thisObj.setAttribute("disabled", "disabled");
            $.ajax({
                "url": "/admin/live/signs/save",
                "type": "post",
                "dataType": "json",
                "success": function (json) {
                    alert(json.message);
                    if (json.code == 200) {
                        location.reload();
                    }
                    thisObj.removeAttribute("disabled");
                },
                "error": function () {
                    alert("打卡失败");
                    thisObj.removeAttribute("disabled");
                }
            });
        }
    </script>
@endsection