@if(isset($playoff))
    @if(isset($playoff['west']) && isset($playoff['east']))
        <div class="knockout_con knockout" style="display: ">
            <div class="part_con">
                <div class="part_item">
                    @include('mobile.subject.v2.cell.basket.basket_playoff_match_con_cell', ['item'=>isset($playoff['east']['up']['first'][0])?$playoff['east']['up']['first'][0]:null])
                    @include('mobile.subject.v2.cell.basket.basket_playoff_match_con_cell', ['item'=>isset($playoff['east']['up']['first'][1])?$playoff['east']['up']['first'][1]:null])
                </div>
                <div class="part_item">
                    @include('mobile.subject.v2.cell.basket.basket_playoff_match_con_cell', ['item'=>isset($playoff['east']['up']['half'])?$playoff['east']['up']['half']:null])
                </div>
                <div class="part_item">
                    @include('mobile.subject.v2.cell.basket.basket_playoff_match_con_cell', ['item'=>isset($playoff['east']['down']['half'])?$playoff['east']['down']['half']:null])
                </div>
                <div class="part_item">
                    @include('mobile.subject.v2.cell.basket.basket_playoff_match_con_cell', ['item'=>isset($playoff['east']['down']['first'][0])?$playoff['east']['down']['first'][0]:null])
                    @include('mobile.subject.v2.cell.basket.basket_playoff_match_con_cell', ['item'=>isset($playoff['east']['down']['first'][1])?$playoff['east']['down']['first'][1]:null])
                </div>
                <div class="part_item semi-final">
                    @include('mobile.subject.v2.cell.basket.basket_playoff_match_con_cell', ['item'=>isset($playoff['east']['final'])?$playoff['east']['final']:null])
                </div>
            </div>
            @include('mobile.subject.v2.cell.basket.basket_playoff_final_cell', ['item'=>isset($playoff['final'])?$playoff['final']:null])
            <div class="part_con">
                <div class="part_item">
                    @include('mobile.subject.v2.cell.basket.basket_playoff_match_con_cell', ['item'=>isset($playoff['west']['up']['first'][0])?$playoff['west']['up']['first'][0]:null])
                    @include('mobile.subject.v2.cell.basket.basket_playoff_match_con_cell', ['item'=>isset($playoff['west']['up']['first'][1])?$playoff['west']['up']['first'][1]:null])
                </div>
                <div class="part_item">
                    @include('mobile.subject.v2.cell.basket.basket_playoff_match_con_cell', ['item'=>isset($playoff['west']['up']['half'])?$playoff['west']['up']['half']:null])
                </div>
                <div class="part_item">
                    @include('mobile.subject.v2.cell.basket.basket_playoff_match_con_cell', ['item'=>isset($playoff['west']['down']['half'])?$playoff['west']['down']['half']:null])
                </div>
                <div class="part_item">
                    @include('mobile.subject.v2.cell.basket.basket_playoff_match_con_cell', ['item'=>isset($playoff['west']['down']['first'][0])?$playoff['west']['down']['first'][0]:null])
                    @include('mobile.subject.v2.cell.basket.basket_playoff_match_con_cell', ['item'=>isset($playoff['west']['down']['first'][1])?$playoff['west']['down']['first'][1]:null])
                </div>
                <div class="part_item semi-final">
                    @include('mobile.subject.v2.cell.basket.basket_playoff_match_con_cell', ['item'=>isset($playoff['west']['final'])?$playoff['west']['final']:null])
                </div>
            </div>
        </div>
    @elseif(isset($playoff['west']) || isset($playoff['add']))
        <div class="knockout_con knockout" style="display: ">
            <div class="part_con">
                <div class="part_item">
                    @include('mobile.subject.v2.cell.basket.basket_playoff_match_con_cell', ['item'=>isset($playoff['add']['up'][0])?$playoff['add']['up'][0]:null])
                    @include('mobile.subject.v2.cell.basket.basket_playoff_match_con_cell', ['item'=>null])
                </div>
                <div class="part_item">
                    @include('mobile.subject.v2.cell.basket.basket_playoff_match_con_cell', ['item'=>isset($playoff['west']['up']['first'][0])?$playoff['west']['up']['first'][0]:null])
                </div>
                <div class="part_item">
                    @include('mobile.subject.v2.cell.basket.basket_playoff_match_con_cell', ['item'=>isset($playoff['west']['up']['first'][1])?$playoff['west']['up']['first'][1]:null])
                </div>
                <div class="part_item">
                    @include('mobile.subject.v2.cell.basket.basket_playoff_match_con_cell', ['item'=>isset($playoff['add']['up'][1])?$playoff['add']['up'][1]:null])
                    @include('mobile.subject.v2.cell.basket.basket_playoff_match_con_cell', ['item'=>null])
                </div>
                <div class="part_item semi-final">
                    @include('mobile.subject.v2.cell.basket.basket_playoff_match_con_cell', ['item'=>isset($playoff['west']['up']['half'])?$playoff['west']['up']['half']:null])
                </div>
            </div>
            <?php
                if (isset($playoff['west']['final'])) {
                    $playoff['west']['final']['info']['hzone'] = 1; //手动改一下判断，cba没有东西部之分
                }
            ?>
            @include('mobile.subject.v2.cell.basket.basket_playoff_final_cell', ['item'=>isset($playoff['west']['final'])?$playoff['west']['final']:null])
            <div class="part_con">
                <div class="part_item">
                    @include('mobile.subject.v2.cell.basket.basket_playoff_match_con_cell', ['item'=>null])
                    @include('mobile.subject.v2.cell.basket.basket_playoff_match_con_cell', ['item'=>isset($playoff['add']['down'][0])?$playoff['add']['down'][0]:null])
                </div>
                <div class="part_item">
                    @include('mobile.subject.v2.cell.basket.basket_playoff_match_con_cell', ['item'=>isset($playoff['west']['down']['first'][0])?$playoff['west']['down']['first'][0]:null])
                </div>
                <div class="part_item">
                    @include('mobile.subject.v2.cell.basket.basket_playoff_match_con_cell', ['item'=>isset($playoff['west']['down']['first'][1])?$playoff['west']['down']['first'][1]:null])
                </div>
                <div class="part_item">
                    @include('mobile.subject.v2.cell.basket.basket_playoff_match_con_cell', ['item'=>null])
                    @include('mobile.subject.v2.cell.basket.basket_playoff_match_con_cell', ['item'=>isset($playoff['add']['down'][1])?$playoff['add']['down'][1]:null])
                </div>
                <div class="part_item semi-final">
                    @include('mobile.subject.v2.cell.basket.basket_playoff_match_con_cell', ['item'=>isset($playoff['west']['down']['half'])?$playoff['west']['down']['half']:null])
                </div>
            </div>
        </div>
    @endif
@endif