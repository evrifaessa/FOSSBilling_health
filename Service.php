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

    public function install()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `health_checks` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
            `check_name` varchar(255) NOT NULL,
            `result` json NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8';

        $this->di['db']->exec($sql);

        return true;
    }

    /**
     * Runs health checks and adds the results to the database.
     */
    public function runChecks(): void
    {
        $checksDirectory = __DIR__ . '/Checks';

        $availableChecks = $this->getAvailableChecks($checksDirectory);

        foreach ($availableChecks as $checkName) {
            $frequency = $this->getCheckFrequency($checkName);

            // Check if it's time to run the check based on its frequency
            if ($frequency === 0 || $this->isCheckDue($checkName, $frequency)) {
                $checkResult = $this->runCheck($checkName);

                $this->addResultToDatabase($checkName, $checkResult);
            }
        }
    }

    /**
     * Gets the frequency of a health check.
     *
     * @param string $checkName
     *
     * @return int
     */
    protected function getCheckFrequency(string $checkName): int
    {
        $checkClass = "\\Box\\Mod\\Health\\Checks\\{$checkName}";
        $checkInstance = new $checkClass();

        $details = $checkInstance->getDetails();

        return isset($details['frequency']) ? (int)$details['frequency'] : 0;
    }

    /**
     * Checks if it's time to run a health check based on its frequency.
     *
     * @param string $checkName
     * @param int    $frequency
     *
     * @return bool
     */
    protected function isCheckDue(string $checkName, int $frequency): bool
    {
        $lastCheckTimestamp = $this->getLastCheckTimestamp($checkName);

        // If the last check timestamp is not set or it's past the frequency interval, it's considered due
        return (!$lastCheckTimestamp || (time() - $lastCheckTimestamp) >= $frequency);
    }

    /**
     * Gets the historical runs of a health check.
     *
     * @param string $checkName
     * @param int    $limit
     *
     * @return array
     */
    public function getCheckHistory(string $checkName, int $limit = 10): array
    {
        $sql = 'SELECT `result`, `timestamp` FROM `health_checks` WHERE `check_name` = ? ORDER BY `timestamp` DESC LIMIT ?';
        $results = $this->di['db']->fetchAll($sql, [$checkName, $limit]);

        $history = [];
        foreach ($results as $result) {
            $history[] = [
                'result' => json_decode($result['result'], true),
                'timestamp' => strtotime($result['timestamp']),
            ];
        }

        return $history;
    }

    /**
     * Gets the timestamp of the last run of a health check.
     *
     * @param string $checkName
     *
     * @return int|null
     */
    protected function getLastCheckTimestamp(string $checkName): ?int
    {
        $history = $this->getCheckHistory($checkName, 1);

        return !empty($history) ? $history[0]['timestamp'] : null;
    }

    /**
     * Runs a health check and returns the result.
     *
     * @param string $checkName
     *
     * @return array
     */
    protected function runCheck(string $checkName): array
    {
        $checkClass = "\\Box\\Mod\\Health\\Checks\\{$checkName}";
        $checkInstance = new $checkClass();

        return $checkInstance->check();
    }

    /**
     * Adds the result of a health check to the database.
     *
     * @param string $checkName
     * @param array  $result
     */
    protected function addResultToDatabase(string $checkName, array $result): void
    {
        $data = [
            ':check_name' => $checkName,
            ':result' => json_encode($result),
        ];

        $sql = 'INSERT INTO `health_checks` (`check_name`, `result`) VALUES (:check_name, :result)';
        $this->di['db']->exec($sql, $data);
    }

    /**
     * Gets a list of available health checks in the specified directory.
     *
     * @param string $directory
     *
     * @return array
     */
    protected function getAvailableChecks(string $directory): array
    {
        $checks = [];

        $files = scandir($directory);

        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..' && is_dir($directory . '/' . $file)) {
                $checks[] = $file;
            }
        }

        return $checks;
    }
}
