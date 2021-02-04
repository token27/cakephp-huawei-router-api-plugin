# CakePHP Huawei Router API plugin for CakePHP

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install this plugin as composer package is:
```
composer require token27/cakephp-huawei-router-api-plugin
```

Now load the plugin via the following:

```
    bin/cake plugin load -b Token27/HuaweiRouterApi
```

Or you can load the plugin in your `src/Application.php`'s bootstrap() using:

```php
$this->addPlugin('Token27/HuaweiRouterApi', ['routes' => true]);
```

If you want disable the plugin endpoints
```php
$this->addPlugin('Token27/HuaweiRouterApi', ['routes' => false]);
```

## Configuration

### Global configuration
The plugin allows some simple runtime configuration.
You may create a file called `app_huawei_router.php` inside your `config` folder (NOT the plugins config folder) to set the following values:

- File to create:

```
    your-path-app/config/app_huawei_router.php
```

- File content:
    
    ```
	return [
            'HuaweiRouterApi' => [
                /**
                 * The ip of the interface (the huawei router)
                 * 
                 * @var string
                 */
                'ip' => '192.168.100.1',
                /**
                 * The username of the router 
                 * 
                 * @var string
                 */
                'username' => 'admin',
                /**
                 * The password of the router 
                 * 
                 * @var string
                 */
                'password' => 'admin'
            ],
        ];
    ```

# API Endpoints

## Url
```
    your-domain.com/huawei-router-api/api/
```

## Table Example

This is an example of a table of endpoints, use this as a reference.

| METHOD | Endpoint | Parameters | Description |
| ------ | -------- | ---------- | --- |
| **GET** | /path/to | par1, par2, test | Description for this endpoint explaining the action it does |

  >
    Following there are all endpoints with method and parameters.
    The tables will explain for itself, just read the description to understand what is the endpoint's action it does.

### Show the currently configuration
Display the information from the configuration file (app_huawei_router.php).

| METHOD | Endpoint | Parameters | Description |
| ------ | -------- | ---------- | --- |
| **GET** | /config |  |  |


### View the router information
Get the router information stats, signal, network, connection.

| METHOD | Endpoint | Parameters | Description |
| ------ | -------- | ---------- | --- |
| **GET** | /info |  |  |


### Reboot the router
This is useful to restart the router and acquire a new IP.

| METHOD | Endpoint | Parameters | Description |
| ------ | -------- | ---------- | --- |
| **GET** | /reboot |  |  |


### Show router traffic stats

| METHOD | Endpoint | Parameters | Description |
| ------ | -------- | ---------- | --- |
| **GET** | /traffic |  |  |
