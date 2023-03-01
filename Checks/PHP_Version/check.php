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

class PHP_Version extends \FOSSBilling_HealthCheck
{
    public function check()
    {
        $req = new Box_Requirements();
        return $req->isPhpVersionOk();
    }

    public function getDetails()
    {
        $req = new Box_Requirements();
        $options = $req->getOptions();

        return array(
            'title' => 'PHP version',
            'description' => 'Makes sure your server is running the minimum required PHP version.',
            'value' => $options['php']['version'],
            'recommended' => $options['php']['min_version'],
        );
    }

    public function getDocumentationLink()
    {
        return 'https://fossbilling.org/docs/php-version';
    }
}