<?php
/**
 * Copyright 2023- FOSSBilling
 * SPDX-License-Identifier: Apache-2.0.
 *
 * @copyright FOSSBilling (https://www.fossbilling.org)
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 */

namespace Box\Mod\Health\Checks;

final class Unsafe_Functions extends HealthCheck
{
    public function check(): array
    {
        // List of the unsafe PHP functions
        $unsafeFunctions = ['eval', 'system', 'exec', 'shell_exec', 'passthru'];
        $enabledFunctions = array_filter($unsafeFunctions, 'function_exists');

        if (count($enabledFunctions) > 0) {
            return [
                'status' => HealthCheck::NEEDS_ATTENTION,
                'short_desc' => 'You should disable some of the functions enabled in your system',
                'long_desc' => 'The following unsafe PHP functions are currently enabled: **' . implode('**, **', $enabledFunctions) . '**. Please make sure they are disabled as FOSSBilling won\'t be utilizing them and they are potentially harmful to your system if exploited.'
            ];
        } else {
            return [
                'status' => HealthCheck::OK,
                'short_desc' => 'No enabled unsafe PHP functions found',
            ];
        }
    }

    static function getDetails(): array
    {
        return array(
            'enabled' => true,
            'title' => 'Unsafe PHP functions',
            'frequency' => 0, // in seconds. leave 0 to check with every run.
            'history' => [
                'depth' => 0 // Store the results of the last N+1 checks. Useful for tests that measure performance or such, not really for simple checks like this one.
            ]
        );
    }
}
