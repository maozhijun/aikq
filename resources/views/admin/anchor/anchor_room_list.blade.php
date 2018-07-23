@extends('admin.layout.nav')
@section('content')
    <h1 class="page-header">主播房间列表</h1>
    <div class="row placeholders">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="20%">房间名字</th>
                    <th width="10%">主播</th>
                    <th width="5%">封面</th>
                    <th width="15%">状态</th>
                    <th width="20%">预约比赛</th>
                    <th width="20%">流地址</th>
                    <th width="15%">操作</th>
                </tr>
                </thead>
                <tbody style="text-align: left;">
                @foreach($page as $room)
                    <form action="/admin/anchor/room/update" enctype="multipart/form-data" method="post" onsubmit="return checkFormData(this);">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{ $room->id }}">
                        <tr>
                            <td><h5>{{ $room->id }}</h5></td>
                            <td>
                                <input type="text" name="name" class="form-control" placeholder="房间名字" value="{{ $room->title }}" required>
                            </td>
                            <td>{{ $room->anchor->name }}</td>
                            <td>
                                @if(!empty($room->cover)) <p style="width: 180px;"><img style="max-width: 50px;max-height: 50px;" src="{{$room->cover}}"></p> @endif
                                <p style="width: 50px;"><input type="file" name="cover"></p>
                            </td>
                            <td>
                                <select name="status" class="form-control" required>
                                    <option value="1" {{ $room->status == \App\Models\Anchor\AnchorRoom::kStatusLiving ? 'selected' : '' }}>直播中</option>
                                    <option value="2" {{ $room->status == \App\Models\Anchor\AnchorRoom::kStatusClose ? 'selected' : '' }}>关播</option>
                                    <option value="0" {{ $room->status == \App\Models\Anchor\AnchorRoom::kStatusNormal ? 'selected' : '' }}>其他</option>
                                </select>
                            </td>
                            <td>
                                <?php
                                    $matches = $room->getTagMatch();
//                                    dump($matches);
                                ?>
                                    @foreach($matches as $match)
                                        {{$match['hname']}} vs {{$match['aname']}}
                                    @endforeach
                            </td>
                            <td>
                                {{$room->url}}
                            </td>
                            <td>
                                <p>
                                    <button type="submit" class="btn btn-sm btn-info"><span class="glyphicon glyphicon-ok"></span>保存</button>
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