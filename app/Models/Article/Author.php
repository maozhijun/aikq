<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/8/3
 * Time: 12:56
 */

namespace App\Models\Article;


use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    public function appModel() {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'avatar'=>$this->avatar,
            'intro'=>$this->intro,
            'article_count'=>$this->article_count,
            'read_count'=>$this->read_count,
        ];
    }
}