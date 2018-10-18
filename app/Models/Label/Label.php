<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/10/17
 * Time: 16:21
 */

namespace App\Models\Label;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Label extends Model
{
    public $timestamps = false;

    public function sameLabels() {
        $id = $this->id;
        $query = self::query();
        $query->whereRaw("id in (SELECT lid_same FROM label_groups WHERE lid_main =  $id) ");
        $query->orWhereRaw("id in (SELECT lid_main FROM label_groups WHERE lid_same =  $id) ");
        $sames = $query->get();
        return $sames;
    }

}