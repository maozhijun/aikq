@extends('admin.layout.nav')
@section('content')
    <h1 class="page-header">播放器广告设置</h1>
    <div class="row">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                {{--<form method="post" action="/admin/live/player/images/save" enctype="multipart/form-data">--}}
                    {{--<tr>--}}
                        {{--{{csrf_field()}}--}}
                        {{--<th>--}}
                            {{--<select name="type">--}}
                                {{--@foreach($typeCns as $key=>$name)--}}
                                    {{--<option value="{{$key}}">{{$name}}</option>--}}
                                {{--@endforeach--}}
                            {{--</select>--}}
                        {{--</th>--}}
                        {{--<th>--}}
                            {{--<input type="file" name="image" style="width: 200px;float: left;margin-right: 5px;" required>--}}
                            {{--<span style="display: none;">--}}
                                {{--<input name="name" placeholder="微信名称" style="width: 160px;">--}}
                                {{--<input name="text" placeholder="广告语">--}}
                            {{--</span>--}}
                            {{--<div style="clear: both"></div>--}}
                        {{--</th>--}}
                        {{--<th><button type="submit" class="btn btn-xs btn-success">保存</button></th>--}}
                    {{--</tr>--}}
                {{--</form>--}}
                <tr>
                    <th width="200px;">广告类型</th>
                    <th>图片</th>
                    <th width="100px;">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($typeCns as $type=>$val)
                    <form method="post" action="/admin/live/player/images/save" enctype="multipart/form-data">
                        {{csrf_field()}}
                        <tr>
                            <td>
                                <select name="type" required>
                                    @foreach($typeCns as $key=>$name)
                                        <option value="{{$key}}" @if($type == $key) selected @endif >{{$name}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                @if(!empty($images[$type])) <img src="{{$images[$type]}}" style="max-width: 70%;max-height: 100px;" /> @endif
                                <input type="file" name="image" style="width: 200px;float: left;margin-right: 5px;">
                                <span @if($type != 'cd') style="display: none;" @endif >
                                    <input name="name" placeholder="微信名称" style="width: 160px;" value="{{$images['cd_name'] or ''}}">
                                    <input name="text" placeholder="广告语" value="{{$images['cd_text'] or ''}}">
                                </span>
                                <div style="clear: both"></div>
                            </td>
                            <td>
                                <button type="submit" class="btn btn-xs btn-success">保存</button>
                                {{--<a class="btn btn-xs btn-danger" href="javascript:delImage('{{$image->id}}')">删除</a>--}}
                            </td>
                        </tr>
                    </form>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript">
        $("select[name=type]").change(function () {
            var type = this.value;
            if (type == 'cd') {//倒计时广告
                $(this).parent().next().find("span").show();
            } else {
                $(this).parent().next().find("span").hide();
            }
        });

        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        });

        function delImage(id) {
            if (!confirm('是否确认删除该播放器广告图片？')) {
                return;
            }
            location.href = '/cms/akq/ad/del-image?id=' + id;
        }
    </script>
@endsection