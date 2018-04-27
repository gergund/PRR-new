<?php

namespace ECG\Infos\System;

use ECG\Infos\InformationInterface;
use ECG\Util\ArrayReader;

class PhpInformation implements InformationInterface
{
    /**
     * Array reader.
     *
     * @var ArrayReader
     */
    private $reader = null;

    /**
     * Construct.
     *
     * @param array $config
     */
    public function __construct()
    {
        $this->reader = new ArrayReader();
    }

    /**
     * Get dataset title.
     *
     * @return string
     */
    public function getName()
    {
        return 'PHP Parameters';
    }

    /**
     * Collect information about operation system.
     *
     * @todo Collect information for HTTP server.
     * Current information is collected for CLI only.
     *
     * @return array
     */
    public function getData()
    {
        $data = [];

        $data['PHP Version'] = phpversion();
        $data['Display Errors'] = ini_get('display_errors');
        $data['Memory Limit'] = ini_get('memory_limit');
        $data['Log Errors'] = ini_get('log_errors');
        $data['Max Execution Time'] = ini_get('max_execution_time');
        $data['Max Input Time'] = ini_get('max_input_time');
        $data['OpCache Memory Consumption'] = $this->getOpcacheMemoryConsumption();
        $data['OpCache Max Accelerated Files'] = $this->getOpcacheMaxAcceleratedFiles();
        $data['OpCache Validate Timestamps'] = $this->getOpcacheValidateTimestamps();

        return $data;
    }

    /**
     * Get OpCache memory consumption.
     *
     * @return string
     */
    private function getOpcacheMemoryConsumption()
    {
        $opcacheConfig = opcache_get_configuration();
        $consumption = $this->reader->readDataOrNull($opcacheConfig, 'directives/opcache.memory_consumption');
        return sprintf('%s MB ', $consumption / 1024 / 1024);
    }

    /**
     * Get OpCache max accelerated files.
     *
     * @return string
     */
    private function getOpcacheMaxAcceleratedFiles()
    {
        $opcacheConfig = opcache_get_configuration();
        $maxFiles = $this->reader->readDataOrNull($opcacheConfig, 'directives/opcache.max_accelerated_files');
        return sprintf('%s', $maxFiles);
    }

    /**
     * Get OpCache validate timestamps.
     *
     * @return string
     */
    private function getOpcacheValidateTimestamps()
    {
        $opcacheConfig = opcache_get_configuration();
        $validateTimestamps = $this->reader->readDataOrNull($opcacheConfig, 'directives/opcache.validate_timestamps');
        return $validateTimestamps;
    }
}
