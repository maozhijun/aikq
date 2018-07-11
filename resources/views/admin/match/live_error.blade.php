@extends('admin.layout.nav')
@section('content')
    <h1 class="page-header">播放出错</h1>
    <div>
        <form class="form-inline" action="/admin/live/error/delete">
            <button type="submit" class="btn btn-primary">
                <span class="glyphicon glyphicon-search"></span>清空记录
            </button>
        </form>
    </div>
    <hr>

    <div class="row">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>比赛信息</th>
                    <th>内容</th>
                    <th>类型</th>
                    <th>播放</th>
                    {{--<th>操作</th>--}}
                </tr>
                </thead>
                <tbody>
                @foreach($lives as $d)
                    <?php
                    $match = $d['match'];
                    $player = '其他';
                    switch ($d['player'])
                    {
                        case 11:
                            $player = 'iFrame';
                            break;
                        case 12:
                            $player = 'ckPlayer';
                            break;
                        case 13:
                            $player = 'm3u8';
                            break;
                        case 14:
                            $player = 'flv';
                            break;
                        case 15:
                            $player = 'rtmp';
                            break;
                        case 1:
                            $player = '自动';
                            break;
                    }

                    $types = \App\Models\Match\MatchLiveChannel::kTypeArrayCn;
                    ?>
                    <tr>
                        <td width="40%">{{$match['time']}} {{$match['hname']}} vs {{$match['aname']}}</td>
                        <td width="40%">{{$d['content']}}</td>
                        <td width="10%">{{$types[$d['type']]}}</td>
                        <td width="10%">{{$player}}</td>
                        {{--<td>删除</td>--}}
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
    </script>
@endsection