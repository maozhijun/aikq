@extends('admin.layout.nav')
@section('content')
    <h1 class="page-header">开播房间信息</h1>
    <div class="row placeholders">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>主播</th>
                    <th>房间标题</th>
                    <th>开播时间</th>
                    <th width="50%">流地址</th>
                </tr>
                </thead>
                <tbody style="text-align: left;">
                @foreach($page as $room)
                    <tr>
                        <td>{{ $room->anchor->name }}</td>
                        <td>
                            <input type="text" class="form-control" value="{{ $room->title }}" readonly>
                        </td>
                        <td>
                            {{ substr($room->start_at,5,11) }}
                        </td>
                        <td>
@if($room->live_status==\App\Models\Anchor\AnchorRoom::kLiveStatusLiving)
<textarea rows="6" style="width: 100%" readonly>{{ $room->live_flv or '' }}
{{ $room->live_rtmp or '' }}
{{ $room->live_m3u8 or '' }}
</textarea>
@endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$page or ''}}
        </div>
    </div>
@endsection