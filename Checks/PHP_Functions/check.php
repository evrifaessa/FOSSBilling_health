<?php
/**
 * Copyright 2023- FOSSBilling
 * SPDX-License-Identifier: Apache-2.0.
 *
 * @copyright FOSSBilling (https://www.fossbilling.org)
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 */

namespace Box\Mod\Health\Checks;

final class PHP_Functions extends HealthCheck
{
    public function check()
    {
        // List of the unsafe PHP functions
        $unsafeFunctions = ['eval', 'system', 'exec', 'shell_exec', 'passthru'];

        // Check if any of the unsafe functions are available
        $results = [];
        foreach ($unsafeFunctions as $function) {
            $results[$function] = [
                'value' => function_exists($function),
                'required' => false,
            ];
        }

        return [
            'values' => $results,
        ];
    }

    public function getDetails()
    {
        return array(
            'enabled' => true,
            'title' => 'Unsafe PHP functions',
            'description' => 'Makes sure the unsafe PHP functions are disabled.',
            'frequency' => 0, // in seconds. leave 0 to check with every run.
            'history' => [
                'depth' => 0 // Store the results of the last N+1 checks. Useful for tests that measure performance or such, not really for simple checks like this one.
            ]
        );
    }
}
