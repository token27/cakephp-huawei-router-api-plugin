<?php

namespace Token27\HuaweiRouterApi\Utility;

use Cake\Core\App;
use Cake\Core\Configure;
use RuntimeException;

class Config {

    public static function getRouterIp() {
        return Configure::read('HuaweiRouterApi.ip', '192.168.100.1');
    }

    public static function getRouterUsername() {
        return Configure::read('HuaweiRouterApi.username', 'admin');
    }

    public static function getRouterPassword() {
        return Configure::read('HuaweiRouterApi.password', 'admin');
    }

}
