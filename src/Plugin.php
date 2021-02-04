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

namespace Token27\HuaweiRouterApi;

use Cake\Core\BasePlugin;
use Cake\Core\PluginApplicationInterface;
use Cake\Routing\RouteBuilder;
use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\Http\Middleware;
use Cake\Http\MiddlewareQueue;

/**
 * Plugin for HuaweiRouterApi
 */
class Plugin extends BasePlugin {

    /**
     * Load all the plugin configuration and bootstrap logic.
     *
     * The host application is provided as an argument. This allows you to load
     * additional plugin dependencies, or attach events.
     *
     * @param \Cake\Core\PluginApplicationInterface $app The host application
     * @return void
     */
    public function bootstrap(PluginApplicationInterface $app): void {
        /**
         * @note Optionally load additional huaweir router config defaults from local app config
         */
        if (file_exists(ROOT . DS . 'config' . DS . 'app_huawei_router.php')) {
            Configure::load('Token27/HuaweiRouterApi');
        } else {
            Configure::load('Token27/HuaweiRouterApi.app_huawei_router');
        }
    }

    /**
     * Add routes for the plugin.
     *
     * If your plugin has many routes and you would like to isolate them into a separate file,
     * you can create `$plugin/config/routes.php` and delete this method.
     *
     * @param \Cake\Routing\RouteBuilder $routes The route builder to update.
     * @return void
     */
    public function routes(RouteBuilder $routes): void {
        $routes->plugin(
                'Token27/HuaweiRouterApi',
                ['path' => '/huawei-router-api'],
                function (RouteBuilder $builder) {

            $builder->connect('/', ['controller' => 'Home', 'actions' => 'index']);

            $builder->fallbacks();
        }
        );
        parent::routes($routes);
    }

    /**
     * Add middleware for the plugin.
     *
     * @param \Cake\Http\MiddlewareQueue $middleware The middleware queue to update.
     * @return \Cake\Http\MiddlewareQueue
     */
    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue {

        return $middlewareQueue;
    }

}
