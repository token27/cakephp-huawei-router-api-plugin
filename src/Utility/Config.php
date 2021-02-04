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
