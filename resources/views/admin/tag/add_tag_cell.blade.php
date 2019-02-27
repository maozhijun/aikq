<div class="input-group form-group col-lg-2" style="float: left;margin-right: 10px;">
    <span class="input-group-addon">竞技</span>
    <select class="form-control" name="sport" onchange="findLeagueTag(this.value, 'league{{isset($mul_id) ? $mul_id : ''}}');" id="sport">
        <option value="">请选择</option>
        <option value="1" @if(isset($sport) && $sport["tag_id"] == 1) selected @endif >足球</option>
        <option value="2" @if(isset($sport) && $sport["tag_id"] == 2) selected @endif >篮球</option>
    </select>
</div>

<div class="input-group form-group col-lg-3" style="float: left;margin-right: 10px;">
    <span class="input-group-addon">赛事</span>
    <select class="form-control" name="league" id="league{{isset($mul_id) ? $mul_id : ''}}" onchange="findTeam(this.value, 'sport', 'teams{{isset($mul_id) ? $mul_id : ''}}');">
        <option value="">请选择</option>
    </select>
    <span class="input-group-addon tagBtn" onclick="addTag(this, 2, 'match_tag')">加标签</span>
</div>

<div class="input-group form-group col-lg-3" style="float: left;margin-right: 10px;">
    <span class="input-group-addon">球队</span>
    <select class="form-control" name="teams" id="teams{{isset($mul_id) ? $mul_id : ''}}">
        <option value="">请选择</option>
    </select>
    <span class="input-group-addon tagBtn" onclick="addTag(this, 3, 'team_tag{{isset($mul_id) ? $mul_id : ''}}');">加标签</span>
</div>

<div class="input-group form-group col-lg-3">
    <span class="input-group-addon">球员</span>
    <input class="form-control" placeholder="球员全称" value="">
    <span class="input-group-addon tagBtn" onclick="addTag(this, 4, 'player_tag{{isset($mul_id) ? $mul_id : ''}}')">加标签</span>
</div>

<div style="clear: both;"></div>
<figure class="highlight">
    <ul class="list-inline" id="match_tag{{isset($mul_id) ? $mul_id : ''}}">
        <li>赛事标签：</li>
        @if(isset($tags["match"]))
            @foreach($tags["match"] as $match)
                <li id="{{$match["tag_id"]}}" tid="{{$match["tid"]}}">
                    <span>{{$match["name"]}}</span>
                    <a class="btn btn-xs btn-danger" onclick="delTag(this, '{{$match["id"]}}')">删除</a>
                </li>
            @endforeach
        @endif
    </ul>
    <ul class="list-inline" id="team_tag{{isset($mul_id) ? $mul_id : ''}}">
        <li>球队标签：</li>
        @if(isset($tags["team"]))
            @foreach($tags["team"] as $team)
                <li id="{{$team["tag_id"]}}">
                    <span>{{$team["name"]}}</span>
                    <a class="btn btn-xs btn-danger" onclick="delTag(this, '{{$team["id"]}}')">删除</a></li>
            @endforeach
        @endif
    </ul>
    <ul class="list-inline" id="player_tag{{isset($mul_id) ? $mul_id : ''}}">
        <li>球员标签：</li>
        @if(isset($tags["player"]))
            @foreach($tags["player"] as $player)
                <li id="{{$player["tag_id"]}}">
                    <span>{{$player["name"]}}</span>
                    <a class="btn btn-xs btn-danger" onclick="delTag(this, '{{$player["id"]}}')">删除</a>
                </li>
            @endforeach
        @endif
    </ul>
</figure>