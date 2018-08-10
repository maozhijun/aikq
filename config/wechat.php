<?php

return [
    /*
     * Debug 模式，bool 值：true/false
     *
     * 当值为 false 时，所有的日志都不会记录
     */
    'debug' => false,

    /*
     * 使用 Laravel 的缓存系统
     */
    'use_laravel_cache' => env('APP_DEBUG', false),

    /*
     * 账号基本信息，请从微信公众平台/开放平台获取
     */
    'app_id' => env('WECHAT_APPID', ''),                                      // AppID
    'secret' => env('WECHAT_SECRET', ''),                    // AppSecret
    'token' => env('WECHAT_TOKEN', ''),                                    // Token
    'aes_key' => env('WECHAT_AES_KEY', ''),     // EncodingAESKey

    /*
     * 日志配置
     *
     * level: 日志级别，可选为：
     *                 debug/info/notice/warning/error/critical/alert/emergency
     * file：日志文件位置(绝对路径!!!)，要求可写权限
     */
    'log' => [
        'level' => env('WECHAT_LOG_LEVEL', 'debug'),
        'file' => env('WECHAT_LOG_FILE', storage_path('logs/wechat/' . date('Ymd') . '.log')),
    ],

    /*
     * OAuth 配置
     *
     * scopes：公众平台（snsapi_userinfo / snsapi_base），开放平台：snsapi_login
     * callback：OAuth授权完成后的回调页地址(如果使用中间件，则随便填写。。。)
     */
    'oauth' => [
        'scopes' => array_map('trim', explode(',', env('WECHAT_OAUTH_SCOPES', 'snsapi_userinfo'))),
        'callback' => env('WECHAT_OAUTH_CALLBACK', '/oauth_callback'),
        'only_wechat_browser'=> "true",
    ],

    /*
     * 微信支付
     */
    'payment' => [
        'merchant_id' => env('WECHAT_PAYMENT_MERCHANT_ID', 'your-mch-id'),
        'key' => env('WECHAT_PAYMENT_KEY', 'key-for-signature'),
        'cert_path' => storage_path('cert/apiclient_cert.pem'),
        'key_path' => storage_path('cert/apiclient_key.pem'),
        // 'device_info'     => env('WECHAT_PAYMENT_DEVICE_INFO', ''),
        // 'sub_app_id'      => env('WECHAT_PAYMENT_SUB_APP_ID', ''),
        // 'sub_merchant_id' => env('WECHAT_PAYMENT_SUB_MERCHANT_ID', ''),
        // ...
    ],

    /*
     * 开发模式下的免授权模拟授权用户资料
     *
     * 当 enable_mock 为 true 则会启用模拟微信授权，用于开发时使用，开发完成请删除或者改为 false 即可
     */
    'enable_mock' => env('WECHAT_ENABLE_MOCK', false),
    'mock_user' => [
        "openid" => "",
        // 以下字段为 scope 为 snsapi_userinfo 时需要
        "nickname" => "小明",
        "sex" => "1",
        "province" => "广西",
        "city" => "桂林",
        "country" => "中国",
        "headimgurl" => "http://wx.qlogo.cn/mmopen/ajNVdqHZLLBAEKF8jHucaPsOyhHtyU4Jk5dZGm1EJFW45iarBHibuMvdkhV02Cy0rOKaICroQMZDuic487fgDB6Hg/0",
    ],
];
