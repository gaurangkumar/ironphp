<?php
/**
 * IronPHP : PHP Development Framework
 * Copyright (c) IronPHP (https://github.com/IronPHP/IronPHP).
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) IronPHP (https://github.com/IronPHP/IronPHP)
 *
 * @link
 * @since         0.0.1
 *
 * @license       MIT License (https://opensource.org/licenses/mit-license.php)
 * @auther        Gaurang Parmar <gaurangkumarp@gmail.com>
 */

namespace Friday\Foundation;

//use Closure; //comment does not affect on synonymous function, bound to closure & Application instance // *RECURSION*

class Application
{
    /**
     * The IronPHP framework version.
     *
     * @var string
     */
    const VERSION = '0.0.1-dev';

    /**
     * The base path for the IronPHP installation.
     *
     * @var string
     */
    protected $basePath;

    /**
     * FrontController instance.
     *
     * @var object
     */
    public $frontController;

    /**
     * Request instance.
     *
     * @var object
     */
    public $request;

    /**
     * Route instance.
     *
     * @var object
     */
    public $route;

    /**
     * Router instance.
     *
     * @var object
     */
    public $router;

    /**
     * Dispatcher instance.
     *
     * @var object
     */
    public $dispatcher;

    /**
     * Response instance.
     *
     * @var object
     */
    public $response;

    /**
     * Matched Route to uri.
     *
     * @var array
     */
    public $matchRoute;

    /**
     * Instanse of Session.
     *
     * @var \Friday\Helper\Session
     */
    public $session;

    /**
     * Instanse of Cookie.
     *
     * @var \Friday\Helper\Cookie
     */
    public $cookie;

    /**
     * Configurations from /config/*.php.
     *
     * @var array
     */
    public $config;

    /**
     * Create a new Friday application instance.
     *
     * @param string|null $basePath
     *
     * @return void
     */
    public function __construct($basePath = null)
    {
        if ($basePath) {
            $this->setBasePath($basePath);
        }

        date_default_timezone_set('Asia/Kolkata');

        $this->requireFile(
            $this->basePath('src/Friday/Helper/Helper.php')
        );

        $this->setIntallTime();

        $this->config['basePath'] = $this->basePath();

        $getenv = new \Friday\Environment\GetEnv(
            $this->basePath(),
            '.env'
        );
        $getenv->load();

        if (empty(env('APP_KEY'))) {
            echo 'APP_KEY is not defined in .env file, define it by command: php jarvis key';
        }

        $this->config['app'] = $this->requireFile(
            $this->basePath('config/app.php')
        );
        $this->config['db'] = $this->requireFile(
            $this->basePath('config/database.php')
        );
        define('CONFIG_LOADED', microtime(true));

        $this->session = new \Friday\Helper\Session();
        if (!$this->session->isRegistered()) {
            $this->session->register();
        }

        if (PHP_SAPI !== 'cli') {
            $this->cookie = new \Friday\Helper\Cookie();

            $this->frontController = new \Friday\Http\FrontController();

            $this->request = $this->frontController->request();
            define('REQUEST_CATCHED', microtime(true));

            $this->route = $this->frontController->route();
            \Friday\Http\Route::$instance = $this->route;
            define('ROUTES_LOADED', microtime(true));

            $this->requireFile(
                $this->basePath('app/Route/web.php')
            );
            $this->route->sortRoute();

            $this->router = $this->frontController->router();
            $this->matchRoute = $this->router->route(
                $this->route->routes,
                $this->request->uri,
                $this->request->serverRequestMethod
            );
            $this->request->setParam('Closure', $this->router->args);
            define('ROUTE_MATCHED', microtime(true));

            $this->dispatcher = $this->frontController->dispatcher();
            $action = $this->dispatcher->dispatch(
                $this->matchRoute,
                $this->request
            );
            define('DISPATCHER_INIT', microtime(true));

            $appController = new \Friday\Controller\Controller();
            $appController->initialize($this);
            if ($action[0] == 'output') {
                $output = $action[1];
            } elseif ($action[0] == 'controller_method') {
                $controller = $action[1];
                $method = $action[2];
                ob_start();
                $appController->handleController($controller, $method);
                $output = ob_get_clean();
            } elseif ($action[0] == 'view') {
                $view = $action[1];
                $data = $action[2];
                $viewPath = $this->findView($view);
                $output = $appController->render($viewPath, $data);
            }
            define('DISPATCHED', microtime(true));

            $this->response = $this->frontController->response($_SERVER['SERVER_PROTOCOL']);
            $this->response->addHeader()->send($output);
            define('RESPONSE_SEND', microtime(true));
        }
    }

    /**
     * Get the version number of the application.
     *
     * @return string
     */
    public function version()
    {
        return static::VERSION;
    }

    /**
     * Set the base path for the application.
     *
     * @param string $basePath
     *
     * @return $this
     */
    public function setBasePath($basePath)
    {
        $this->basePath = rtrim($basePath, '\/');

        return $this;
    }

    /**
     * Get the base path of the IronPHP installation.
     *
     * @param string $path Optionally, a path to append to the base path
     *
     * @return string
     */
    public function basePath($path = '')
    {
        return $this->basePath.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Find a file.
     *
     * @param string $path
     *
     * @return bool
     */
    public function findFile($path)
    {
        if (file_exists($path) && is_file($path)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Find a Model.
     *
     * @param string $model
     *
     * @return string full model file path
     */
    public function findModel($model)
    {
        $file = $this->basePath("app/Model/$model.php");
        if ($this->findFile($file)) {
            return $file;
        } else {
            throw new \Exception($file.' Model file is missing.');
            exit;
        }
    }

    /**
     * Find a View.
     *
     * @param string $view
     *
     * @return string full view file path
     */
    public function findView($view)
    {
        $file = $this->basePath("app/View/$view.php");
        if ($this->findFile($file)) {
            return $file;
        } else {
            throw new \Exception($file.' View file is missing.');
            exit;
        }
    }

    /**
     * Find a Template.
     *
     * @param string $template
     *
     * @return string full template file path
     */
    public function findTemplate($template)
    {
        $file = $this->basePath("app/Template/$template");
        if ($this->findFile($file)) {
            return $file;
        } elseif ($this->findFile($file.'.html')) {
            return $file.'.html';
        } elseif ($this->findFile($file.'.php')) {
            return $file.'.php';
        } else {
            throw new \Exception($file.' Template file is missing.');
            exit;
        }
    }

    /**
     * Find a Controller.
     *
     * @param string $controller
     *
     * @return bool
     */
    public function findController($controller)
    {
        $file = $this->basePath("app/Controller/$controller.php");
        if ($this->findFile($file)) {
            return true;
        } else {
            throw new \Exception($file.' Controller file is missing.');
            exit;
        }
    }

    /**
     * Check if Controller has method or not.
     *
     * @param object $controllerObj
     * @param string $method
     *
     * @return bool
     */
    public function hasMethod($controllerObj, $method)
    {
        if (method_exists($controllerObj, $method)) {
            return true;
        } else {
            throw new \Exception($method.' method is missing in '.get_class($controllerObj).'Controller.');
            exit;
        }
    }

    /**
     * Require a file.
     *
     * @param string $file
     *
     * @return void
     */
    public function requireFile($file)
    {
        if ($this->findFile($file)) {
            return require $file;
        } else {
            throw new \Exception($file.' file is missing.');
            exit;
        }
    }

    /**
     * Set Installtion Time/Version to app/install file used for checking updates.
     *
     * @return bool
     */
    public function setIntallTime()
    {
        $file = $this->basePath('app/install');
        if (!file_exists($file)) {
            $content = json_encode(['time'=>time(), 'version' => $this->version()]);
            file_put_contents($file, $content);
        }
    }

    /**
     * Get Installtion Time/Version to app/install file used for checking updates.
     *
     * @return array
     */
    public function getIntallTime()
    {
        $file = $this->basePath('app/install');
        if (!file_exists($file)) {
            $data = ['time'=>time(), 'version' => $this->version()];
            $content = json_encode($data);
            file_put_contents($file, $content);
        } else {
            $content = file_get_contents($file);
            $data = json_decode($content);
        }

        return $data;
    }

    /**
     * Set Application secret key.
     *
     * @return string
     */
    public function setKey()
    {
        $appKey = '';
        for ($i = 0; $i < 32; $i++) {
            $appKey .= chr(rand(0, 255));
        }
        $appKey = 'base64:'.base64_encode($appKey);
        $file = $this->basePath('.env');
        $lines = $this->parseEnvFile($file);
        $flag = false;
        foreach ($lines as $i => $line) {
            $lines[$i] = trim($line);
            $lines[$i] = trim($line, "\n");
            if (strpos($line, 'APP_KEY') !== false) {
                $data = explode('=', $line, 2);
                if (!isset($data[1]) || trim($data[1]) == '') {
                    $lines[$i] = 'APP_KEY='.$appKey;
                    $flag = true;
                }
            }
        }
        if ($flag == false) {
            $lines = ['APP_KEY='.$appKey] + $lines;
        }
        $data = implode("\n", $lines);
        if (file_put_contents($file, $data)) {
            putenv("APP_KEY=$appKey");
            $_ENV['APP_KEY'] = $appKey;
            $_SERVER['APP_KEY'] = $appKey;

            return true;
        } else {
            throw new \Exception('Failed to write in .env file.');
        }
    }

    /**
     * Parse .env file gets its lines in array.
     *
     * @param string $file
     *
     * @return array
     */
    public function parseEnvFile($file)
    {
        $this->ensureFileIsReadable($file);

        $lines = file($file);

        return $lines;
    }

    /**
     * Ensures the given filePath is readable.
     *
     *
     * @param string $file
     *
     * @throws \Exception
     *
     * @return void
     */
    protected function ensureFileIsReadable($file)
    {
        if (!is_readable($file) || !is_file($file)) {
            throw new \Exception(sprintf('Unable to read the environment file at %s.', $$file));
        }
    }

    /**
     * Get parameter passed in route.
     *
     * @return array
     */
    public function getRouteParam()
    {
        return $this->router->args;
    }
}
