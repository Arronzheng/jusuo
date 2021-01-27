<?php
/**
 * Created by PhpStorm.
 * User: cwq53
 * Date: 2019/10/25
 * Time: 14:32
 */

return [
    // HTTP 请求的超时时间（秒）
    'timeout' => 5.0,

    // 默认发送配置
    'default' => [
        // 网关调用策略，默认：顺序调用
        'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,

        // 默认可用的发送网关
        'gateways' => [
            'aliyun',
        ],
    ],
    // 可用的网关配置
    'gateways' => [
        'errorlog' => [
            'file' => '/tmp/easy-sms.log',
        ],
        'aliyun' => [
            'access_key_id' => 'LTAI4G4kZ6voorha41pHxgcb',
            'access_key_secret' => 'zFbqrF5mpns6pGqb6BsdUw0TL9c2Lq',
            'sign_name' => '千名营销',
        ],
    ],
];