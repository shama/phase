<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

Router::parseExtensions('xml', 'html');

Router::connect('/phase/about', array('controller' => 'installer', 'action' => 'about'));
if (!is_dir(Configure::read('PhaseRoot'))) {
    Router::connect('/go', array('controller' => 'installer', 'action' => 'go'));
    Router::connect('/*', array('controller' => 'installer', 'action' => 'about'));
} else {
    Router::connect('/', array('controller' => 'posts', 'action' => 'home'));
    Router::connect(
        '/atom',
        array('controller' => 'posts', 'action' => 'archives'),
        array('ext' => 'xml')
    );
    Router::connect('/archives', array('controller' => 'posts', 'action' => 'archives'));
    Router::connect(
        '/:year/:month/:day/:slug',
        array('controller' => 'posts', 'action' => 'viewDated'),
        array('pass' => array('year', 'month', 'day','slug'))
    );
    Router::connect('/*', array('controller' => 'posts', 'action' => 'view'));
}

