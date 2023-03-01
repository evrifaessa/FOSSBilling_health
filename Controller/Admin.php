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

    public function register(\Box_App &$app)
    {
        $app->get('/health', 'get_index', [], static::class);
        $app->get('/health/check/:id', 'get_check', ['id' => '[A-Za-z0-9\_]+'], static::class);
    }

    public function get_index(\Box_App $app)
    {
        $this->di['is_admin_logged'];

        return $app->render('mod_health_index');
    }

    public function get_check(\Box_App $app, $id)
    {
        $this->di['is_admin_logged'];

        $params = [];
        $params['check_id'] = $id;

        return $app->render('mod_health_check', $params);
    }
}
