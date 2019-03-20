<div class="match_con match" style="display: none;">
    @if(isset($lives) && count($lives) > 0)
        @foreach($lives as $key=>$matches)
            <p class="match_list_date">{{$key}}</p>
            <div class="match_list_con">
                @foreach($matches as $match)
                    @include('mobile.cell.v2.match_item_cell', ['match'=>$match, 'hideLeague'=>true])
                @endforeach
            </div>
        @endforeach
    @endif
</div>