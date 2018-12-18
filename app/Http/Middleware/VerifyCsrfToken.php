<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/live/ad/set-active',
        '/live/valid/code',
        '/api/transfer/save',

        '/admin/foreign/update',//后台翻译文章不用验证token
        '/admin/article/save',//后台翻译文章保存不用验证token
        '/admin/upload/cover',//后台上传封面图不需要验证token
    ];
}
