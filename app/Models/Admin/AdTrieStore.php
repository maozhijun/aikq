<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class AdTrieStore extends Model
{
    //
    const kTrieLevelFilter=1;//直接过滤
    const kTrieLevelReview=2;//需要审核
    const kTrieLevelReplace=3;//替换为*

    protected $primaryKey = 'key';

    protected $keyType = 'string';


}
