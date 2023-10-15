<?php
/**
 * Copyright 2023- FOSSBilling
 * SPDX-License-Identifier: Apache-2.0.
 *
 * @copyright FOSSBilling (https://www.fossbilling.org)
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
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
