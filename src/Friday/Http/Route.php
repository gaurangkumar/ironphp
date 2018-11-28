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

namespace Friday\Http;

class Route implements RouteInterface
{
    /**
     * All registered route.
     *
     * @var array
     */
    public $routes = null;

    /**
     * Route instance.
     *
     * @var Route
     */
    public static $instance;

    /**
     * Create Route instance.
     *
     * @return void
     */
    public function __construct(/*$path, $controllerClass*/)
    {
        //$this->path = $path;
        //$this->controllerClass = $controllerClass;
    }

    /**
     * register a GET method route.
     *
     * @param string               $route
     * @param string|callable|null $mix
     * @param string|null          $view
     *
     * @return bool
     */
    public function get($route, $mix = null, $view = null)
    {
        self::$instance->register('GET', $route, $mix, $view);
    }

    /**
     * register a POST method route.
     *
     * @param string               $route
     * @param string|callable|null $mix
     * @param string|null          $view
     *
     * @return bool
     */
    public function post($route, $mix = null, $view = null)
    {
        self::$instance->register('POST', $route, $mix, $view);
    }

    /**
     * register a GET method route with view.
     *
     * @param string      $route
     * @param string|null $view
     * @param array       $data
     *
     * @return bool
     */
    public function view($route, $view = null, array $data = [])
    {
        self::$instance->register('GET', $route, null, $view, $data);
    }

    /**
     * register a route.
     *
     * @param string               $route
     * @param string|callable|null $mix
     * @param string|null          $view
     * @param array                $data
     *
     * @return void
     */
    public function register($method, $route, $mix = null, $view = null, $data = [])
    {
        $route = trim($route, '/ ');
        $array = $route === '' ? [] : explode('/', $route);
        $size = count($array);
        $route = '/'.$route;
        if (strpos($route, '{') !== false) {
            $to = 0;
            $param = true;
            foreach ($array as $i => $uriPiece) {
                $uriPiece = trim($uriPiece);
                if (strpos($uriPiece, '{') !== false) {
                    if (
                        strpos($uriPiece, '{') === 0 &&
                        strpos($uriPiece, '}') !== false &&
                        strpos($uriPiece, '}') === (strlen($uriPiece) - 1)
                    ) {
                        $args[$uriPiece] = rtrim(ltrim($uriPiece, '{'), '}');
                    } else {
                        $args[$uriPiece] = null;
                    }
                } else {
                    $to = $i + 1;
                    $args[$uriPiece] = null;
                }
            }
            $base_size = $to;
            $base_route = array_slice($array, 0, $to, true);
        } else {
            $param = false;
            $base_size = $size;
            $args = null;
            $base_route = $route;
        }
        $base_route = is_array($base_route) ? implode('/', $base_route) : $base_route;
        if (trim($base_route) === '') {
            $base_route = '/';
        }
        self::$instance->routes[] = [$method, $route, $mix, $view, $data, $args, $size, $base_size, $param];
    }

    /**
     * sort registered routes by there base uri.
     *
     * @return void
     */
    public function sortRoute()
    {
        $sort = uasort($this->routes, function ($a, $b) {
            if ($a[7] == $b[7]) {
                return 0;
            }

            return ($a[7] > $b[7]) ? -1 : 1;
        });
    }
}
