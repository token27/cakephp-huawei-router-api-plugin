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

use Cake\Network\Http\Client;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;
use Cake\Core\Configure;

# PLUGIN
use Token27\HuaweiRouterApi\Controller\HuaweiRouterApiAppController;

# OTHERS
use Throwable;

class HomeController extends HuaweiRouterApiAppController {

    /**
     * Constructor hook method.
     *
     * Implement this method to avoid having to overwrite
     * the constructor and call parent.
     *
     */
    public function initialize(): void {
        parent::initialize();
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index() {

        $this->response->withType('application/json');

        $response = [
            'status' => 0,
            'message' => __('Silence is Golden1.'),
        ];

        $this->set([
            'response' => $response,
            '_serialize' => 'response',
        ]);
        $this->RequestHandler->renderAs($this, 'json');
    }

}
