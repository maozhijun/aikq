<?php
/**
 * Created by PhpStorm.
 * User: ricky
 * Date: 2017/10/20
 * Time: 11:07
 */

namespace App\Models\LgMatch;

use Illuminate\Database\Eloquent\Model;

class MatchData extends Model
{
    public $connection = "match";

    public $timestamps = false;

    public function yellowPercent($isHost = true) {
        $h_yellow = is_numeric($this->h_yellow) ? $this->h_yellow : 0;
        $a_yellow = is_numeric($this->a_yellow) ? $this->a_yellow : 0;
        if (($h_yellow + $a_yellow) == 0) {
            return 0;
        }

        if ($isHost) {
            return round( ($h_yellow / ($h_yellow + $a_yellow)), 4) * 100;
        } else {
            return round( ($a_yellow / ($h_yellow + $a_yellow)), 4) * 100;
        }
    }

    public function redPercent($isHost = true) {
        $h_red = is_numeric($this->h_red) ? $this->h_red : 0;
        $a_red = is_numeric($this->a_red) ? $this->a_red : 0;
        if (($h_red + $a_red) == 0) {
            return 0;
        }

        if ($isHost) {
            return round( ($h_red / ($h_red + $a_red)), 4) * 100;
        } else {
            return round( ($a_red / ($h_red + $a_red)), 4) * 100;
        }
    }

    public function cornerPercent($isHost = true) {
        $h_corner = is_numeric($this->h_corner) ? $this->h_corner : 0;
        $a_corner = is_numeric($this->a_corner) ? $this->a_corner : 0;
        if (($h_corner + $a_corner) == 0) {
            return 0;
        }

        if ($isHost) {
            return round( ($h_corner / ($h_corner + $a_corner)), 4) * 100;
        } else {
            return round( ($a_corner / ($h_corner + $a_corner)), 4) * 100;
        }
    }

    public function shootPercent($isHost = true) {
        $h_shoot = is_numeric($this->h_shoot) ? $this->h_shoot : 0;
        $a_shoot = is_numeric($this->a_shoot) ? $this->a_shoot : 0;
        $total_shoot = $h_shoot + $a_shoot;
        if ($total_shoot == 0) {
            return 0;
        }
        if ($isHost) {
            return round($h_shoot / $total_shoot, 2) * 100;
        } else {
            return round($a_shoot / $total_shoot, 2) * 100;
        }
    }

    public function shootInTargetPercent($isHost = true) {
        $h = is_numeric($this->h_shoot_in_target) ? $this->h_shoot_in_target : 0;
        $a = is_numeric($this->a_shoot_in_target) ? $this->a_shoot_in_target : 0;
        $total_shoot = $h + $a;
        if ($total_shoot == 0) {
            return 0;

        }
        if ($isHost) {
            return round($h / $total_shoot, 2) * 100;
        } else {
            return round($a / $total_shoot, 2) * 100;
        }
    }

    public function hShootPercent() {
        $h_shoot = is_numeric($this->h_shoot) ? $this->h_shoot : 0;
        $a_shoot = is_numeric($this->a_shoot) ? $this->a_shoot : 0;
        $total_shoot = $h_shoot + $a_shoot;
        if ($total_shoot > 0) {
            return round($h_shoot / $total_shoot, 2) * 100;
        }
        return 0;
    }

    public function aShootPercent() {
        $h_shoot = is_numeric($this->h_shoot) ? $this->h_shoot : 0;
        $a_shoot = is_numeric($this->a_shoot) ? $this->a_shoot : 0;
        $total_shoot = $h_shoot + $a_shoot;
        if ($total_shoot > 0) {
            return round($a_shoot / $total_shoot, 2) * 100;
        }
        return 0;
    }

    public function hShootInTarget() {
        $h = is_numeric($this->h_shoot_in_target) ? $this->h_shoot_in_target : 0;
        $a = is_numeric($this->a_shoot_in_target) ? $this->a_shoot_in_target : 0;
        $total_shoot = $h + $a;
        if ($total_shoot > 0) {
            return round($h / $total_shoot, 2) * 100;
        }
        return 0;
    }

    public function aShootInTarget() {
        $h = is_numeric($this->h_shoot_in_target) ? $this->h_shoot_in_target : 0;
        $a = is_numeric($this->a_shoot_in_target) ? $this->a_shoot_in_target : 0;
        $total_shoot = $h + $a;
        if ($total_shoot > 0) {
            return round($a / $total_shoot, 2) * 100;
        }
        return 0;
    }

    public function hAttPercent() {
        $h = is_numeric($this->h_attack) ? $this->h_attack : 0;
        $a = is_numeric($this->a_attack) ? $this->a_attack : 0;
        $total = $h + $a;
        if ($total > 0) {
            return round($h / $total, 2) * 100;
        }
        return 0;
    }

    public function aAttPercent() {
        $h = is_numeric($this->h_attack) ? $this->h_attack : 0;
        $a = is_numeric($this->a_attack) ? $this->a_attack : 0;
        $total = $h + $a;
        if ($total > 0) {
            return round($a / $total, 2) * 100;
        }
        return 0;
    }

    public function hDangerAttPercent() {
        $h = is_numeric($this->h_attack) ? $this->h_attack : 0;
        $a = is_numeric($this->a_attack) ? $this->a_attack : 0;
        $total = $h + $a;
        if ($total > 0) {
            return round($h / $total, 2) * 100;
        }
        return 0;
    }

    public function aDangerAttPercent() {
        $h = is_numeric($this->h_danger_attack) ? $this->h_danger_attack : 0;
        $a = is_numeric($this->a_danger_attack) ? $this->a_danger_attack : 0;
        $total = $h + $a;
        if ($total > 0) {
            return round($a / $total, 2) * 100;
        }
        return 0;
    }


}