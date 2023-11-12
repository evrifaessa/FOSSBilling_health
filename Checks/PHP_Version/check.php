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
        $options = $req->getOptions();

        return [
            'values' => [
                /**
                 * To the developers, you've got some options here:
                 * - Checks that only have a "required" property will be "requirements". These will be displayed in red.
                 * - Checks that only have a "recommended" property will be "warnings". These will be displayed in orange.
                 * - Checks that have both of them will be evaluated to see which condition is/isn't met. For example, if the requirement is met but the recommendation isn't, it will be orange.
                 * Please note that the value must be set. You also must set one or more of the "required" and "recommended" properties.
                 */
                'php_version' => [
                    'value' => $options['php']['version'],
                    'required' => $options['php']['min_version']
                ],
            ]
        ];
    }

    public function getDetails()
    {
        $req = new \FOSSBilling\Requirements();
        $options = $req->getOptions();

        return array(
            'enabled' => true,
            'title' => 'PHP version',
            'description' => 'Makes sure your server is running the minimum required PHP version.',
            'frequency' => 0, // in seconds. leave 0 to check with every run.
            'history' => [
                'depth' => 0 // Store the results of the last N+1 checks. Useful for tests that measure performance or such, not really for simple checks like this one.
            ]
        );
    }

    public function getDocumentationLink()
    {
        return 'https://fossbilling.org/docs/php-version';
    }
}
