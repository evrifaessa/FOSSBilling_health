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
    public function check(): array
    {
        $req = new \FOSSBilling\Requirements();
        $options = $req->getOptions();

        if ($req->isPhpVersionOk()) {
            return [
                'status' => HealthCheck::OK,
                'short_desc' => 'You are using PHP ' . $options['php']['version'] . '. It\'s still receiving updates and will reach its EOL in 2 years.',
                'long_desc' => 'You are using PHP ' . $options['php']['version'] . '. FOSSBilling only supports PHP versions over ' . $options['php']['min_version'] . '.'
            ];
        } else {
            return [
                'status' => HealthCheck::NEEDS_ATTENTION,
                'short_desc' => 'You are using an unsupported version of PHP.',
                'long_desc' => 'You are using an unsupported version of PHP (' . $options['php']['version'] . '). FOSSBilling only supports PHP versions over ' . $options['php']['min_version'] . '.\nFor the security of you and your clients\' data, you must switch to a newer version of PHP as soon as possible.'
            ];
        }
    }

    static function getDetails(): array
    {
        return array(
            'enabled' => true,
            'title' => 'PHP version',
            'frequency' => 0, // in seconds. leave 0 to check with every run.
            'documentation' => 'https://fossbilling.org/docs/php-version',
            'history' => [
                'depth' => 0 // Store the results of the last N+1 checks. Useful for tests that measure performance or such, not really for simple checks like this one.
            ]
        );
    }
}
