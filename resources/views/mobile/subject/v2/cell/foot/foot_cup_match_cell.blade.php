<div class="match_con match" style="display: none;">
    @if(isset($schedules) && count($schedules) > 0)
        <?php $count = count($schedules); ?>
        @foreach($schedules as $index=>$stage)
            <p class="match_list_date @if($index+1==$count)on @endif">{{$stage['name']}}</p>
            @if(isset($stage['groupMatch']))
                @foreach($stage['groupMatch'] as $group=>$items)
                    <p class="match_list_date">{{$group}}组</p>
                    <div class="match_list_con">
                        @foreach($items['matches'] as $match)
                            @include('mobile.cell.v2.match_item_cell', ['match'=>$match, 'hideLeague'=>true])
                        @endforeach
                    </div>
                @endforeach
            @elseif(isset($stage['combo']))
                    <div class="match_list_con">
                        @foreach($stage['combo'] as $key=>$comb)
                            @foreach($comb['matches'] as $match)
                                @include('mobile.cell.v2.match_item_cell', ['match'=>$match, 'hideLeague'=>true])
                            @endforeach
                        @endforeach
                    </div>
            @elseif(isset($stage['matches']))
                <div class="match_list_con">
                    @foreach($stage['matches'] as $match)
                        @include('mobile.cell.v2.match_item_cell', ['match'=>$match, 'hideLeague'=>true])
                    @endforeach
                </div>
            @endif
        @endforeach
    @endif
</div>