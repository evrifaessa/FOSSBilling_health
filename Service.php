<?php
/**
 * Copyright 2023- FOSSBilling
 * SPDX-License-Identifier: Apache-2.0.
 *
 * @copyright FOSSBilling (https://www.fossbilling.org)
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 */

namespace Box\Mod\Health;

use FOSSBilling\InjectionAwareInterface;

class Service implements InjectionAwareInterface
{
    protected ?\Pimple\Container $di = null;

    public function setDi(\Pimple\Container $di): void
    {
        $this->di = $di;
    }

    public function getDi(): ?\Pimple\Container
    {
        return $this->di;
    }

    /**
     * Returns a list of all the checks.
     *
     * @return array An array of all the checks.
     */
    public function getCheckList()
    {
        // @TODO: Read the check data from the database.
    }

    /**
     * Loads all the checks in the Checks directory.
     *
     * @return array An array of all the checks.
     */
    public function loadChecks()
    {
        $dirs = glob(__DIR__ . '/Checks/*', GLOB_ONLYDIR);
        $checks = array();

        foreach ($dirs as $dir) {
            include_once $dir . '/check.php';

            $class = 'Box\\Mod\\Health\\Checks\\' . basename($dir);
            $checks[] = new $class();
        }

        return $checks;

        // @TODO: Save the check data to the database.
    }

    /**
     * Run a specific check.
     *
     * @param string $name The name of the check to run.
     *
     * @return array The results of the check.
     */
    public function runCheck($name)
    {
        // @TODO: Save the check data to the database.
    }

    /**
     * Run all the checks.
     *
     * @return array An array of all the checks.
     */
    public function runAllChecks()
    {
        $checks = $this->loadChecks();

        foreach ($checks as $check) {
            $check->check();
        }

        return $checks;

        // @TODO: Save the check data to the database.
    }
}
