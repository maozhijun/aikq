<div style="margin-left: 10px;padding-bottom: 10px;">
    <input type="hidden" name="id" value="{{$channel->id or ''}}" >
    <input type="hidden" name="cover" value="{{$channel->cover or ''}}" >
    <button onmouseover="showCover(this);" onmouseout="hideCover();" style="float: left;margin-right: 5px;" type="button" class="btn btn-default" onclick="uploadCover(this)">
        @if(isset($channel) && !empty($channel->cover))<span class="glyphicon glyphicon-upload"></span>封面图
        @else
            上传封面
        @endif
    </button>
    <input class="form-control input-form" style="width: 160px;" name="title" placeholder="标题" value="{{$channel->title or ''}}" >
    <select class="form-control input-form" name="platform" style="width: 80px;" >
        <option value="1">全部</option>
        <option value="2" @if(isset($channel) && $channel->platform == 2) selected @endif >电脑</option>
        <option value="3" @if(isset($channel) && $channel->platform == 3) selected @endif >手机</option>
    </select>
    <select class="form-control input-form" name="type" style="width: 80px;" >
        {{--<option value="0">全部</option>--}}
        <option value="1" @if(isset($channel) && $channel->type == 1) selected @endif >录像</option>
        {{--<option value="2" @if(isset($channel) && $channel->type == 2) selected @endif >集锦</option>--}}
    </select>
    <select class="form-control input-form" name="player" style="width: 120px;" onchange="changePlayer(this);" >
        @foreach($players as $p_id=>$p_name)
            <option value="{{$p_id}}" @if( (isset($channel) && $channel->player == $p_id) || (!isset($channel) && $p_id == 16) ) selected @endif >{{$p_name}}</option>
        @endforeach
    </select>
    <input class="form-control input-form" style="width: 300px;" name="content" placeholder="内容" value="{{$channel->content or ''}}" >
    <input class="form-control input-form" type="number" style="width: 80px;" name="od" placeholder="排序" value="{{$channel->od or ''}}">
    <button type="button" class="btn btn-sm btn-success" type="button" onclick="saveVideoChannel(this, '{{$channel->id or ''}}');">保存</button>
    <button type="button" class="btn btn-sm btn-danger" type="button" onclick="delVideoChannel(this, '{{$channel->id or ''}}');">删除</button>
    @if(isset($channel))
        <?php
            $sv_id = $channel->sv_id;
            if (strlen($sv_id) >= 4) {
                $first = substr($sv_id, 0, 2);
                $second = substr($sv_id, 2, 4);
                $player_url = env('WWW_URL')."/live/subject/video/" . $first . "/" . $second . "/" . $sv_id . ".html";
            }
        ?>
        {{--@if(isset($player_url)) <a href="{{$player_url}}" target="_blank" class="btn btn-default">预览</a> @endif--}}
    @endif
    <div style="clear: both;"></div>
</div>