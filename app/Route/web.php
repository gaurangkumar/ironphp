<?php
/**
 * IronPHP : PHP Development Framework
 * Copyright (c) IronPHP (https://github.com/IronPHP/IronPHP)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @package       IronPHP
 * @copyright     Copyright (c) IronPHP (https://github.com/IronPHP/IronPHP)
 * @link          
 * @since         0.0.1
 * @license       MIT License (https://opensource.org/licenses/mit-license.php)
 * @auther        Gaurang Parmar <gaurangkumarp@gmail.com>
 */

use Friday\Http\Route;

/**
 *--------------------------------------------------------------------------
 * Web Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register web routes for your application. Now
 * create something great!
 *
 */

//route with controller@method
Route::get('/', 'IndexController@Index');
Route::get('/page1', 'IndexController@Index');

//route with view only (always GET method)
Route::view('/view', 'index', ['name' => 'IronPHP']);

//route with callable
Route::get('/callable', function () {
    echo 'callable';
});
