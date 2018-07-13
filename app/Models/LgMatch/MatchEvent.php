<?php

namespace App\Models\LgMatch;

use Illuminate\Database\Eloquent\Model;

class MatchEvent extends Model
{
    //类型\n1进球\n3黄牌\n7点球\n9两黄一红\n11换人\n如果还有其他继续补充
    //
    protected $connection = 'match';
    public $timestamps = false;

    public function kindCn() {
        $kind = $this->kind;
        $cn = "";
        switch ($kind) {
            case 1:
                $cn = $this->getPlayerName() . "（进球）";
                break;
            case 2:
                $cn = $this->getPlayerName() . "（红牌）";
                break;
            case 3:
                $cn = $this->getPlayerName() . "（黄牌）";
                break;
            case 7:
                $cn = $this->getPlayerName() . "（点球）";
                break;
            case 8:
                $cn = $this->getPlayerName() . "（乌龙球）";
                break;
            case 9:
                $cn = $this->getPlayerName() . "（两黄一红）";
                break;
            case 11:
                $cn = $this->getPlayerName() . "（换上） " . $this->getPlayerName2() . "（换下）";
                break;
        }
        return $cn;
    }

    public function kindCss() {
        $kind = $this->kind;
        $css = "";
        switch ($kind) {
            case 1:
            case 7:
            case 8:
                $css = "goal";
                break;
            case 2:
            case 9:
                $css = "red";
                break;
            case 3:
                $css = "yellow";
                break;
            case 11:
                $css = "exchange";
                break;
        }
        return $css;
    }

    public function kindImg() {
        $kind = $this->kind;
        $css = "";
        switch ($kind) {
            case 1:
            case 7:
            case 8:
                $css = "/img/image_goal.png";
                break;
            case 2:
            case 9:
                $css = "/img/image_red.png";
                break;
            case 3:
                $css = "/img/image_yellow.png";
                break;
            case 11:
                $css = "/img/image_exchange.png";
                break;
        }
        return $css;
    }

    public function getPlayerName() {
        if (!empty($this->player_name_j)) {
            return $this->player_name_j;
        }
        if (!empty($this->player_name_f)) {
            return $this->player_name_f;
        }
        if (!empty($this->player_name_sb)) {
            return $this->player_name_sb;
        }
        return "";
    }

    public function getPlayerName2() {
        if (!empty($this->player_name_j2)) {
            return $this->player_name_j2;
        }
        if (!empty($this->player_name_f2)) {
            return $this->player_name_f2;
        }
        if (!empty($this->player_name_sb2)) {
            return $this->player_name_sb2;
        }
        return "";
    }

    public static function getMatchEvents($mid) {
        $events = MatchEvent::query()->where('mid', $mid)->orderBy('happen_time')->get();
        $host_events = [];//格式：[time=>[], time=>[], ...];
        $away_events = [];//格式：[time=>[], time=>[], ...];

        $host_temp_time = 0;
        $away_temp_time = 0;
        $last_event_time = 0;
        if (isset($events) && count($events) > 0) {
            foreach ($events as $event) {
                if ($event->is_home == 1) {//主队事件
                    if ($event->happen_time != $host_temp_time) {
                        $host_temp_time = $event->happen_time;
                    }
                    if (!isset($host_events[$host_temp_time])) {
                        $host_events[$host_temp_time] = [$event];
                    } else {
                        $host_events[$host_temp_time][] = $event;
                    }
                } else {//客队事件
                    if ($event->happen_time != $away_temp_time) {
                        $away_temp_time = $event->happen_time;
                    }
                    if (!isset($away_events[$away_temp_time])) {
                        $away_events[$away_temp_time] = [$event];
                    } else {
                        $away_events[$away_temp_time][] = $event;
                    }
                }
            }
            $last_event_time = $event->happen_time;
        }

        return ['host_events'=>$host_events, 'away_events'=>$away_events, 'last_event_time'=>$last_event_time];
    }
}
