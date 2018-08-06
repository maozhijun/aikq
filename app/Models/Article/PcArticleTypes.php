<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/8/3
 * Time: 16:43
 */

namespace App\Models\Article;


use Illuminate\Database\Eloquent\Model;

class PcArticleTypes extends Model
{
    const kStatusShow = 1, kStatusHide = 2;
}