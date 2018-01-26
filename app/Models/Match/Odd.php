<?php

namespace App\Models\Match;

use Illuminate\Database\Eloquent\Model;

class Odd extends Model
{
    const k_odd_type_asian = 1;//亚盘
    const k_odd_type_ou = 2;//大小
    const k_odd_type_europe = 3;//欧赔
    const k_odd_type_corner = 4;//角球

    const default_banker_id = 2;//默认公司id 取sb
    const default_calculate_cid = 5;//默认计算用的博彩公司id 取bet365
    const default_weide_cid = 8;//韦德菠菜公司id
    const default_king_cid = 12;//金宝博;

    const default_minglu_cid = 9;//明陞
    const default_liji_cid = 14;//利记

    //
    public $connection = "match";
    public $timestamps = false;

    /**
     * 获取数据库名
     */
    public static function getDatabaseName(){
        return env('DB_DATABASE_MATCH', 'moro');
    }

    public function getPanKouText() {
        $text = $this->middle2;
        if ($this->type == self::k_odd_type_asian) {
            if ($this->middle2 == 0) {
                return "平手";
            }
            $text = self::panKouText($text);
        }
        return $text;
    }

    /**
     * @param $middle float 盘口
     * @param bool $isAway 是否是客队
     * @param bool $isGoal 是否是大小球
     * @return string
     */
    public static function panKouText ($middle, $isAway = false, $isGoal = false) {
        if ($isGoal || $middle == 0){
            $prefix = "";
        } else{
            if ($isAway){
                $prefix = $middle < 0 ? "让" : "受让";
            }else{
                $prefix = $middle < 0 ? "受让" : "让";
            }
        }
        $text = $middle;
        $middle = abs($middle);
        switch ($middle) {
            case 7: $text = "七球"; break;
            case 6.75: $text = "六半/七球"; break;
            case 6.5: $text = "六球半"; break;
            case 6.25: $text = "六球/六半"; break;
            case 6: $text = "六球"; break;
            case 5.75: $text = "五半/六球"; break;
            case 5.5: $text = "五球半"; break;
            case 5.25: $text = "五球/五半"; break;
            case 5: $text = "五球"; break;
            case 4.75: $text = "四半/五球"; break;
            case 4.5: $text = "四球半"; break;
            case 4.25: $text = "四球/四半"; break;
            case 4: $text = "四球"; break;
            case 3.75: $text = "三半/四球"; break;
            case 3.5: $text = "三球半"; break;
            case 3.25: $text = "三球/三半"; break;
            case 3: $text = "三球"; break;
            case 2.75: $text = "两半/三球"; break;
            case 2.5: $text = "两球半"; break;
            case 2.25: $text = "两球/两半"; break;
            case 2: $text = "两球"; break;
            case 1.75: $text = "球半/两球"; break;
            case 1.5: $text = "球半"; break;
            case 1.25: $text = "一球/球半"; break;
            case 1: $text = "一球"; break;
            case 0.75: $text = "半/一"; break;
            case 0.5: $text = "半球"; break;
            case 0.25: $text = "平手/半球"; break;
            case 0: $text = "平手"; break;
        }
        if (!is_numeric($text)) {
            return $prefix . $text;
        }
        return $text;
    }

    /**
     * 盘口转字符串显示
     * @param $middle
     * @return int
     */
    public static function getOddMiddleString($middle,$withMinus = true){
        //是否小数
        $isMinus = $middle < 0;
        //取绝对值
        $middle = abs($middle);
        //分离小数
        $decimal = (100*$middle)%100;
        $first = '';
        $second = '';
        switch ($decimal){
            //没有半球盘
            case 0:
            case 50:
                $first = $middle;
                break;
            case 25:
                $first = floor($middle);
                $second = $first.'.5';
                break;
            case 75:
                $second = ceil($middle);
                $first = $second - 0.5;
                break;
        }
        if (strlen($second) > 0){
            return ($isMinus&&$withMinus?'-':'').$first.'/'.$second;
        }
        else{
            return ($isMinus&&$withMinus?'-':'').$middle;
        }
    }

}
