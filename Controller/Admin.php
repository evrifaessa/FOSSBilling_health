<?php
/**
 * Copyright 2023- FOSSBilling
 * SPDX-License-Identifier: Apache-2.0.
 *
 * @copyright FOSSBilling (https://www.fossbilling.org)
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 */

namespace Box\Mod\Health\Controller;

use FOSSBilling\InjectionAwareInterface;

class Admin implements InjectionAwareInterface
{
    protected ?\Pimple\Container $di = null;

    public function setDi(\Pimple\Container $di): void
    {
        $this->di = $di;
    }

    public function getDi(): ?\Pimple\Container
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
