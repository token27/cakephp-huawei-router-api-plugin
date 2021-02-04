<?php

/**
 * 
 * Version      1.2.7
 * Created      04/02/2021
 * Modified     05/02/2021
 * 
 * @author      Axel Natanael Chacon Juan
 * @copyright   2021 Token27
 * @github      https://github.com/token27/
 * @webiste     https://www.token27.com/
 */
/**
 * This file configures default behavior for all workers
 *
 * To modify these parameters, copy this file into your own CakePHP config directory or copy the array into your existing file.
 */
return [
    'HuaweiRouterApi' => [
        /**
         * The ip of the interface (the huawei router)
         * 
         * @var string
         */
        'ip' => env('HUAWEI_ROUTER_IP', '192.168.100.1'),
        /**
         * The username of the router 
         * 
         * @var string
         */
        'username' => env('HUAWEI_ROUTER_USERNAME', 'admin'),
        /**
         * The password of the router 
         * 
         * @var string
         */
        'password' => env('HUAWEI_ROUTER_PASSWORD', 'admin')
    ],
];
