<?php

/**
 * FOSSBilling.
 *
 * @copyright FOSSBilling (https://www.fossbilling.org)
 * @license   Apache-2.0
 *
 * Copyright FOSSBilling 2023
 *
 * This source file is subject to the Apache-2.0 License that is bundled
 * with this source code in the file LICENSE
 */

namespace Box\Mod\Health\Controller;

class Admin implements \Box\InjectionAwareInterface
{
    protected $di;

    /**
     * @param mixed $di
     */
    public function setDi($di)
    {
        $this->di = $di;
    }

    /**
     * @return mixed
     */
    public function getDi()
    {
        return $this->di;
    }

    public function fetchNavigation()
    {
        return [
            'subpages' => [
                [
                    'location' => 'system',
                    'label' => __trans('System health'),
                    'uri' => $this->di['url']->adminLink('health'),
                    'index' => 120,
                    'class' => '',
                ],
            ]
        ];
    }

    /**
     * Methods maps admin areas urls to corresponding methods
     * Always use your module prefix to avoid conflicts with other modules
     * in future.
     *
     * @example $app->get('/health/test',      'get_test', null, get_class($this)); // calls get_test method on this class
     * @example $app->get('/health/:id',        'get_index', array('id'=>'[0-9]+'), get_class($this));
     */
    public function register(\Box_App &$app)
    {
        $app->get('/health', 'get_index', [], static::class);
        $app->get('/health/test', 'get_test', [], static::class);
        $app->get('/health/user/:id', 'get_user', ['id' => '[0-9]+'], static::class);
        $app->get('/health/api', 'get_api', [], static::class);
    }

    public function get_index(\Box_App $app)
    {
        $this->di['is_admin_logged'];

        return $app->render('mod_health_index');
    }

    public function get_test(\Box_App $app)
    {
        // always call this method to validate if admin is logged in
        $this->di['is_admin_logged'];

        $params = [];
        $params['youparamname'] = 'yourparamvalue';

        return $app->render('mod_health_index', $params);
    }

    public function get_user(\Box_App $app, $id)
    {
        // always call this method to validate if admin is logged in
        $this->di['is_admin_logged'];

        $params = [];
        $params['userid'] = $id;

        return $app->render('mod_health_index', $params);
    }

    public function get_api(\Box_App $app, $id = null)
    {
        // always call this method to validate if admin is logged in
        $api = $this->di['api_admin'];
        $list_from_controller = $api->example_get_something();

        $params = [];
        $params['api_example'] = true;
        $params['list_from_controller'] = $list_from_controller;

        return $app->render('mod_health_index', $params);
    }
}
