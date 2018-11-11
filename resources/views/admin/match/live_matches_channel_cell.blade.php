<div>
    <p>
        <select name="ad" style="display: none">
            <option value="1">有广告</option>
            <option value="2" @if(isset($channel) && $channel->ad == 2) selected @endif >无广告</option>
        </select>
        <select name="type" onclick="selectType(this);" style="display: none">
            @foreach($types as $id=>$type)
                <option value="{{$id}}" @if((!isset($channel) && $id == 99) || (isset($channel) && $channel->type == $id) ) selected @endif >{{$type}}</option>
            @endforeach
        </select>
        <input style="width: 140px;" name="name" value="{{$channel->name or '高清直播'}}" placeholder="名称">
        <input style="width: 280px;" name="content" value="{{$channel->content or ''}}" placeholder="内容">
        <select name="player" onchange="changePlatform(this);">
            <option value="1">自动选择</option>
            <option value="11" @if(isset($channel) && $channel->player == 11) selected @endif >iFrame</option>
            <option value="12" @if(isset($channel) && $channel->player == 12) selected @endif >ckPlayer</option>
            <option value="13" @if(isset($channel) && $channel->player == 13) selected @endif >m3u8</option>
            <option value="14" @if(isset($channel) && $channel->player == 14) selected @endif >flv</option>
            <option value="15" @if(isset($channel) && $channel->player == 15) selected @endif >rtmp</option>
            <option value="16" @if(isset($channel) && $channel->player == 16) selected @endif >外链</option>
            {{--<option value="17" @if(isset($channel) && $channel->player == 17) selected @endif >clappr</option>--}}
        </select>
        <select name="show">
            <option value="1">显示</option>
            <option value="2" @if(isset($channel) && $channel->show == 2) selected @endif >不显示</option>
        </select>
        <select name="platform">
            <option value="1">全部</option>
            <option value="2" @if(isset($channel) && $channel->platform == 2) selected @endif >电脑</option>
            <option value="3" @if(isset($channel) && $channel->platform == 3) selected @endif >手机</option>
        </select>
        <select name="use" style="display: none;">
            {{--<option value="1">通用</option>--}}
            <option value="2" @if(isset($channel) && $channel->use == 2) selected @endif >爱看球专用</option>
            {{--<option value="3" @if($channel->use == 3) selected @endif >黑土专用</option>--}}
        </select>
        <input type="number" style="width: 50px;" name="od" value="{{$channel->od or ''}}" placeholder="排序">
        <select name="isPrivate">
            <option value="2">有版权</option>
            <option value="1" @if(isset($channel) && $channel->isPrivate == 1) selected @endif >无版权</option>
        </select>
        <input style="width: 280px;" name="akq_url" value="{{$channel->akq_url or ''}}" placeholder="跳转链接">
        <button class="btn btn-success btn-xs" type="button" onclick="saveChannel(this, '{{$channel->id or ''}}', '{{$sport or 1}}');">保存</button>
        <button class="btn btn-danger btn-xs" type="button" onclick="delChannel(this, '{{$channel->id or ''}}');">删除</button>
    </p>
    <p @if(!isset($channel) || $channel->type != \App\Models\Match\MatchLiveChannel::kTypeCode) style="display: none;" @endif ><input style="width: 80%" name="h_content" value="{{$channel->h_content or ''}}" placeholder="高清链接" /></p>
    <p style="display: none;"><input style="width: 80%" name="h_content" value="" placeholder="高清链接" /></p>
</div>