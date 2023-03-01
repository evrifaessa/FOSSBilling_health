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

namespace Box\Mod\Health\Checks;

class MySQL_Version extends \Box_HealthCheck
{
    public function check()
    {
        true // TODO: Actually implement this.
    }

    public function getDetails()
    {
        $req = new Box_Requirements();
        $options = $req->getOptions();

        return array(
            'title' => 'MySQL version',
            'description' => 'Makes sure FOSSBilling is operating on a supported MySQL version.',
            'value' => '0.0.0',
            'recommended' => '0.0.0',
        );
    }
}