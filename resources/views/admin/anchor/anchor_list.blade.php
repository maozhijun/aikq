@extends('admin.layout.nav')
@section('content')
    <h1 class="page-header">主播列表</h1>
    <div class="row placeholders">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th colspan="9">
                        <button onclick="showRegister()" class="btn btn-success btn-sm" style="float: right;">新建</button>
                    </th>
                </tr>
                <tr>
                    <th width="5%">#</th>
                    <th width="20%">名字</th>
                    <th width="10%">头像</th>
                    <th>电话</th>
                    <th width="10%">热门</th>
                    <th width="10%">排序</th>
                    <th width="15%">操作</th>
                </tr>
                </thead>
                <tbody style="text-align: left;">
                @foreach($page as $anchor)
                    <form action="/admin/anchor/update" enctype="multipart/form-data" method="post" onsubmit="return checkFormData(this);">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{ $anchor->id }}">
                        <tr>
                            <td><h5>{{ $anchor->id }}</h5></td>
                            <td>
                                <input type="text" name="name" class="form-control" placeholder="昵称" value="{{ $anchor->name }}" required>
                            </td>
                            <td>
                                @if(!empty($anchor->icon))
                                    <p style="width: 180px;"><img style="max-width: 50px;max-height: 50px;" src="{{$anchor->icon}}"></p>
                                @endif
                                <p style="width: 180px;"><input type="file" name="icon"></p>
                            </td>
                            <td>
                                <input type="text" name="phone" class="form-control" placeholder="电话" value="{{ $anchor->phone }}" required>
                            </td>
                            <td>
                                <select name="hot" class="form-control" required>
                                    <option value="1" {{ $anchor->hot == 1 ? 'selected' : '' }}>热门</option>
                                    <option value="0" {{ $anchor->hot == 0 ? 'selected' : '' }}>一般</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" name="sort" class="form-control" placeholder="排序" value="{{ $anchor->sort }}" >
                            </td>
                            <td>
                                <p>
                                    <button type="submit" class="btn btn-sm btn-info"><span class="glyphicon glyphicon-ok"></span>保存</button>
                                    <a href="javascript:delAnchor('{{$anchor->id}}');" class="btn btn-sm btn-danger">删除</a>
                                </p>
                            </td>
                        </tr>
                    </form>
                @endforeach
                </tbody>
            </table>
            {{$page or ''}}
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript">
        function submit() {
            console.info('submit...');
        }
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        });

        function delAnchor(id) {
            if (!confirm('是否确认删除该主播')) {
                return;
            }
            $.ajax({
                "url": "/admin/anchor/delete",
                "type": "post",
                "data": {'_token':'{{csrf_token()}}',"id": id},
                "dataType": "json",
                "success": function (json) {
                    if (json && json.code == 0) {
                        toastr.success("删除成功");
                        location.reload();
                    } else if (json) {
                        toastr.error(json.msg);
                    } else {
                        toastr.error("删除失败");
                    }
                },
                "error": function () {
                    toastr.error("删除失败");
                }
            });
        }

        /**
         * 保存
         * @param formObj
         * @returns {boolean}
         */
        function checkFormData(formObj) {
            var name = formObj.name.value;
            var phone = formObj.phone.value;
            name = $.trim(name);
            phone = $.trim(phone);
            if (name == "") {
                alert("名字不能为空");
                return false;
            }
            if (name.length > 30) {
                alert("标题不能大于30字");
                return false;
            }
            if (phone == "") {
                alert("手机不能为空");
                return false;
            }
            return true;
        }


        function showRegister() {
            window.open('/admin/anchor/register', '', 'width=600,height=600,left=100,top=200')
        }
    </script>
@endsection