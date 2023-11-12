<?php
/**
 * Copyright 2023- FOSSBilling
 * SPDX-License-Identifier: Apache-2.0.
 *
 * @copyright FOSSBilling (https://www.fossbilling.org)
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 */

namespace Box\Mod\Health\Checks;

final class Example extends HealthCheck
{
    public function check(): array
    {
        return [
                'status' => HealthCheck::WARNING,
                'short_desc' => 'This is a warning.'
        ];
    }

    static function getDetails(): array
    {
        return array(
            'enabled' => true,
            'title' => 'Example check',
            'frequency' => 0, // in seconds. leave 0 to check with every run.
            'history' => [
                'depth' => 0 // Store the results of the last N+1 checks. Useful for tests that measure performance or such, not really for simple checks like this one.
            ]
        );
    }
}
