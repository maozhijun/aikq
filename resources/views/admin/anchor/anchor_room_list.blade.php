@extends('admin.layout.nav')
@section('content')
    <h1 class="page-header">主播房间列表</h1>
    <div class="row placeholders">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th width="20%">房间标题</th>
                    <th>主播</th>
                    <th>封面</th>
                    <th>房间状态</th>
                    <th>直播状态</th>
                    <th>最后开播时间</th>
                    {{--<th width="20%">预约比赛</th>--}}
                    {{--<th>流地址</th>--}}
                    <th>操作</th>
                </tr>
                </thead>
                <tbody style="text-align: left;">
                @foreach($page as $room)
                    <form action="/admin/anchor/room/update" enctype="multipart/form-data" method="post"
                          onsubmit="return checkFormData(this);">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{ $room->id }}">
                        <tr>
                            <td><h5>{{ $room->id }}</h5></td>
                            <td>
                                <input type="text" name="name" class="form-control" placeholder="房间标题"
                                       value="{{ $room->title }}" required>
                            </td>
                            <td>{{ $room->anchor->name }}</td>
                            <td>
                                @if(!empty($room->cover))
                                    <p style="width: 180px;">
                                        <img style="max-width: 50px;max-height: 50px;" src="{{$room->cover}}">
                                    </p>
                                @endif
                                <p style="width: 50px;"><input type="file" name="cover"></p>
                            </td>
                            <td>
                                <select name="status" class="form-control" required>
                                    <option value="1" {{ $room->status == \App\Models\Anchor\AnchorRoom::kStatusValid ? 'selected' : '' }}>
                                        正常
                                    </option>
                                    <option value="0" {{ $room->status == \App\Models\Anchor\AnchorRoom::kStatusInvalid ? 'selected' : '' }}>
                                        小黑屋
                                    </option>
                                </select>
                            </td>
                            <td>
                                <label class="label {{ $room->live_status==\App\Models\Anchor\AnchorRoom::kLiveStatusLiving?'label-success':'label-danger' }}">
                                    {{ $room->live_status==\App\Models\Anchor\AnchorRoom::kLiveStatusLiving?'直播中':'关播' }}
                                </label>
                            </td>
                            <td>
                                {{ substr($room->start_at,5,11) }}
                            </td>
                            {{--<td>--}}
                                {{--@if($room->live_status==\App\Models\Anchor\AnchorRoom::kLiveStatusLiving)--}}
                                    {{--<textarea class="form-control" readonly>--}}
                                        {{--{{ $room->live_flv or '' }}--}}
                                        {{--{{ $room->live_rtmp or '' }}--}}
                                        {{--{{ $room->live_m3u8 or '' }}--}}
                                    {{--</textarea>--}}
                                {{--@endif--}}
                            {{--</td>--}}
                            <td>
                                <p>
                                    <button type="submit" class="btn btn-sm btn-info">
                                        <span class="glyphicon glyphicon-ok"></span>保存
                                    </button>
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

        /**
         * 保存
         * @param formObj
         * @returns {boolean}
         */
        function checkFormData(formObj) {
            var name = formObj.name.value;
            name = $.trim(name);
            if (name == "") {
                alert("名字不能为空");
                return false;
            }
            if (name.length > 30) {
                alert("标题不能大于30字");
                return false;
            }
            return true;
        }
    </script>
@endsection