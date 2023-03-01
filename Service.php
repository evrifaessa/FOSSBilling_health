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

/**
 * This file is a delegate for module. Class does not extend any other class.
 *
 * All methods provided in this example are optional, but function names are
 * still reserved.
 */

namespace Box\Mod\Health;

class Service
{
    protected $di;

    public function setDi(mixed $di)
    {
        $this->di = $di;
    }

    public function getDi()
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
            error_log($dir);

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
