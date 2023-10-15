<?php
/**
 * Copyright 2023- FOSSBilling
 * SPDX-License-Identifier: Apache-2.0.
 *
 * @copyright FOSSBilling (https://www.fossbilling.org)
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 */

namespace Box\Mod\Health\Checks;

final class PHP_Version extends HealthCheck
{
    public function check()
    {
        $req = new \FOSSBilling\Requirements();
        return $req->isPhpVersionOk();
    }

    public function getDetails()
    {
        $req = new \FOSSBilling\Requirements();
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