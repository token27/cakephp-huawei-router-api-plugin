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
declare(strict_types=1);

namespace Token27\HuaweiRouterApi\Controller;

# CAKEPHP

use Cake\Core\Configure;
use App\Controller\AppController as BaseController;

# PLUGIN
use Token27\HuaweiRouterApi\Utility\Config;

class HuaweiRouterApiAppController extends BaseController {

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('FormProtection');`
     *
     * @return void
     */
    public function initialize(): void {
        parent::initialize();
        $this->loadComponent('Token27/HuaweiRouterApi.HuaweiRouterApi');
        $this->HuaweiRouterApi->setRouterIpAddress(Config::getRouterIp());
        $this->HuaweiRouterApi->setRouterUsername(Config::getRouterUsername());
        $this->HuaweiRouterApi->setRouterPassword(Config::getRouterPassword());
    }

}
