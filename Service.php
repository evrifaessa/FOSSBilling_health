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
     * Runs the checks that are due and adds the results to the database.
     * 
     * @return true
     * @throws Exception
     */
    public function runChecks(): true
    {
        foreach ($this->getChecks() as $checkName => $checkDetails) {
            $this->runCheck($checkName, false);
        }

        return true;
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
     * Gets the latest check result for a health check.
     *
     * @param string $checkName
     *
     * @return array|null
     */
    public function getLatestCheckResult(string $checkName): ?array
    {
        $sql = 'SELECT `result`, `timestamp` FROM `health_checks` WHERE `check_name` = ? ORDER BY `timestamp` DESC LIMIT 1';
        $result = $this->di['db']->getRow($sql, [$checkName]);

        if ($result) {
            return [
                'result' => json_decode($result['result'], true),
                'timestamp' => $result['timestamp']    
            ];
        }

        return null;
    }

    /**
     * Gets a list of the health checks
     *
     * @return array
     */
    public function getChecks(): array
    {
        $checks = [];
        $directory = __DIR__ . '/Checks';

        $files = scandir($directory);

        foreach ($files as $checkName) {
            if ($checkName !== '.' && $checkName !== '..' && is_dir($directory . '/' . $checkName)) {
                $checks[$checkName] = [
                    'details' => $this->getCheckDetails($checkName),
                    'latest' => $this->getLatestCheckResult($checkName),
                ];
            }
        }

        return $checks;
    }

    /**
     * Gets a list of the health checks grouped by their status codes.
     *
     * The health checks are grouped into the following categories:
     * - 0: Unknown/Haven't run yet
     * - 1: Needs attention
     * - 2: Warning
     * - 3: Success
     *
     * @return array An associative array where keys are status codes and values are arrays of health checks.
     */
    public function getGroupedChecks(): array
    {
        $checks = $this->getChecks();
        
        $groupedChecks = [
            0 => [], // Unknown/Haven't run yet
            1 => [], // Needs attention
            2 => [], // Warning
            3 => [], // Success
        ];

        foreach ($checks as $check) {
            $statusCode = $check['latest']['result']['status'] ?? 0;

            $groupedChecks[$statusCode][] = $check;
        }

        return $groupedChecks;
    }

    /**
     * Gets the details of a health check.
     *
     * @param string $checkName
     *
     * @return array
     */
    public function getCheckDetails(string $checkName): array
    {
        $checkClass = "\\Box\\Mod\\Health\\Checks\\{$checkName}";
        $checkInstance = new $checkClass();

        return $checkInstance->getDetails();
    }

    /**
     * Gets the stored results of a health check.
     *
     * @param string $checkName
     * @param int|null $limit
     *
     * @return array
     */
    public function getCheckResults(string $checkName, ?int $limit = null): array
    {
        $limitClause = ($limit !== null) ? 'LIMIT ' . $limit : '';

        $sql = 'SELECT `id`, `result`, `timestamp` FROM `health_checks` WHERE `check_name` = ? ORDER BY `timestamp` DESC ' . $limitClause;
        $results = $this->di['db']->getAll($sql, [$checkName]);

        $formattedResults = [];
        foreach ($results as $result) {
            $formattedResults[] = json_decode($result['result'], true);
        }

        return $formattedResults;
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
     * Gets the frequency of a health check.
     *
     * @param string $checkName
     *
     * @return int
     */
    protected function getCheckFrequency(string $checkName): int
    {
        $details = $this->getCheckDetails($checkName);

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
     * Runs a health check and returns the result.
     *
     * @param string $checkName
     * @param bool $forced Run the check forcefully. Even if it's not due yet.
     * 
     * @return array
     */
    public function runCheck(string $checkName, bool $forced): array
    {
        $checkClass = "\\Box\\Mod\\Health\\Checks\\{$checkName}";
        $checkInstance = new $checkClass();

        $frequency = $this->getCheckFrequency($checkName);

        if ($frequency === 0 || $this->isCheckDue($checkName, $frequency) || $forced) {
            $result = $checkInstance->check();
            $this->addResultToDatabase($checkName, $result);

            return $result;
        }

        return false;
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
}
