<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/10/17
 * Time: 16:22
 */

namespace App\Models\Label;


use Illuminate\Database\Eloquent\Model;

class LabelGroup extends Model
{
    public $timestamps = false;

    public static function findGroup($label_id, $label_id2) {
        $query = self::query();
        $query->where(function ($andQuery) use ($label_id, $label_id2) {
            $andQuery->where('lid_main', $label_id);
            $andQuery->where('lid_same', $label_id2);
        });
        $query->orWhere(function ($andQuery) use ($label_id, $label_id2) {
            $andQuery->where('lid_same', $label_id);
            $andQuery->where('lid_main', $label_id2);
        });
        $query->first();
    }

    public static function saveLabelGroup($lid_main, $lid_same) {
        if ($lid_main == $lid_same) return;
        $group = self::findGroup($lid_main, $lid_same);
        if (!isset($group)) {
            $group = new LabelGroup();
            $group->lid_main = $lid_main;
            $group->lid_same = $lid_same;
            $group->save();
        }
        return $group;
    }

}