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
use Token27\HuaweiRouterApi\Utility\Config;
use Throwable;

class ApiController extends HuaweiRouterApiAppController {

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
            'message' => __('Silence is Golden.'),
        ];

        $this->set([
            'response' => $response,
            '_serialize' => 'response',
        ]);
        $this->RequestHandler->renderAs($this, 'json');
    }

    /**
     * Config method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function config() {


        $this->response->withType('application/json');

        $response = [
            'status' => 1,
            'message' => __('Ok.'),
            'settings' => [
                'ip' => Config::getRouterIp(),
                'username' => Config::getRouterUsername(),
                'password' => Config::getRouterPassword(),
            ],
        ];

        $this->set([
            'response' => $response,
            '_serialize' => 'response',
        ]);

        $this->RequestHandler->renderAs($this, 'json');
    }

    /**
     * Info method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function info() {

        $this->response->withType('application/json');

        $response = [
            'status' => 0,
            'message' => __('Silence is Golden.'),
        ];

        $login = false;
        try {
            $login = $this->HuaweiRouterApi->login();
        } catch (Throwable $ex) {
            $login = false;
        } catch (Exception $ex) {
            $login = false;
        }
        if ($login) {
            try {
                $stats = $this->HuaweiRouterApi->getStatus();
                $signal = $this->HuaweiRouterApi->getSignal();
                $newtwork = $this->HuaweiRouterApi->getNetwork();
                $cradle = $this->HuaweiRouterApi->getCraddleStatus();
                $connection = $this->HuaweiRouterApi->getDialup('connection');
                $response = [
                    'status' => 1,
                    'message' => __('Ok.'),
                    'stats' => $stats,
                    'signal' => $signal,
                    'newtwork' => $newtwork,
                    'cradle' => $cradle,
                    'connection' => $connection,
                ];
            } catch (Throwable $ex) {
                $response['message'] = $ex->getMessage();
            } catch (Exception $ex) {
                $response['message'] = $ex->getMessage();
            }
        } else {
            $response['message'] = __('Cannot login to the router.');
        }

        $this->set([
            'response' => $response,
            '_serialize' => 'response',
        ]);

        $this->RequestHandler->renderAs($this, 'json');
    }

    /**
     * Reboot method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function reboot() {

        $this->response->withType('application/json');

        $response = [
            'status' => 0,
            'message' => __('Silence is Golden.'),
        ];

        $login = false;
        try {
            $login = $this->HuaweiRouterApi->login();
        } catch (Throwable $ex) {
            $login = false;
        } catch (Exception $ex) {
            $login = false;
        }
        if ($login) {
            $rebooted = false;
            try {
                $rebooted = $this->huaweiRouterApi->reboot();
            } catch (Throwable $ex) {
                $rebooted = false;
            } catch (Exception $ex) {
                $rebooted = false;
            }
            if ($rebooted) {
                $response = [
                    'status' => 1,
                    'message' => __('Router rebooted successfully.'),
                ];
            } else {
                $response['message'] = __('Error while try to reboot the router, please try again later...');
            }
        } else {
            $response['message'] = __('Cannot login to the router.');
        }

        $this->set([
            'response' => $response,
            '_serialize' => 'response',
        ]);

        $this->RequestHandler->renderAs($this, 'json');
    }

    /**
     * Traffic method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function traffic() {

        $this->response->withType('application/json');

        $response = [
            'status' => 0,
            'message' => __('Silence is Golden.'),
        ];

        $login = false;
        try {
            $login = $this->HuaweiRouterApi->login();
        } catch (Throwable $ex) {
            $login = false;
        } catch (Exception $ex) {
            $login = false;
        }
        if ($login) {
            try {
                $traffic = $this->huaweiRouterApi->getTrafficStats();
                $month = $this->huaweiRouterApi->getMonthStats();
                $response = [
                    'status' => 1,
                    'message' => __('Ok.'),
                    'traffic' => $traffic,
                    'newtwork' => $month,
                ];
            } catch (Throwable $ex) {
                $response['message'] = $ex->getMessage();
            } catch (Exception $ex) {
                $response['message'] = $ex->getMessage();
            }
        } else {
            $response['message'] = __('Cannot login to the router.');
        }

        $this->set([
            'response' => $response,
            '_serialize' => 'response',
        ]);

        $this->RequestHandler->renderAs($this, 'json');
    }

}
