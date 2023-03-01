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

/**
 * Health module Admin API.
 *
 * API can be access only by admins
 */

namespace Box\Mod\Health\Api;

class Admin extends \Api_Abstract
{
    public function list()
    {
        $checks = $this->getService()->getCheckList();

        return $checks;
    }

    public function run_check($data)
    {
        $required = array(
            'name' => 'You must provide a name for the check.',
        );
        $this->di['validator']->checkRequiredParamsForArray($required, $data);

        $check = $this->getService()->runCheck($data['name']);

        return $check;
    }

    public function run_all_checks()
    {
        $checks = $this->getService()->runAllChecks();

        return $checks;
    }
}
