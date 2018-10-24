<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Upload extends Model
{

    public function getUrl()
    {
        return Storage::disk($this->disks)->url($this->path);
    }

    public function getEvnUrl() {
        $prefix = $this->env;
        return $prefix."/".$this->disks."/".$this->path;
    }
}
