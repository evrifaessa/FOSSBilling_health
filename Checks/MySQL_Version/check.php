<?php
/**
 * Copyright 2023- FOSSBilling
 * SPDX-License-Identifier: Apache-2.0.
 *
 * @copyright FOSSBilling (https://www.fossbilling.org)
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 */

namespace Box\Mod\Health\Checks;

final class MySQL_Version extends HealthCheck
{
    public function check()
    {
        return true; // TODO: Actually implement this.
    }

    public function getDetails()
    {
        $req = new \FOSSBilling\Requirements();
        $options = $req->getOptions();

        return array(
            'title' => 'MySQL version',
            'description' => 'Makes sure FOSSBilling is operating on a supported MySQL version.',
            'value' => '0.0.0',
            'recommended' => '0.0.0',
        );
    }
}