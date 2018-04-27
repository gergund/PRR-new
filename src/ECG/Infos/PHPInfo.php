<?php
/**
 * Created by PhpStorm.
 * User: dgergun
 * Date: 26.04.18
 * Time: 10:57
 */

namespace ECG\Infos;


class PHPInfo
{
    public function getVersion()
    {
        return phpversion();
    }

    public  function  getDisplayErrors()
    {
       return ini_get('display_errors');
    }

    public function getMemoryLimit()
    {
        return ini_get('memory_limit');
    }

    public function getLogErrors()
    {
        return ini_get('log_errors');
    }

    public function getMaxExecution()
    {
        return ini_get('max_execution_time');
    }

    public function getMaxInput()
    {
        return ini_get('max_input_time');
    }

    public function getOpcacheMemoryConsumption()
    {
        $max_consumption = opcache_get_configuration()['directives']['opcache.memory_consumption'];
        return sprintf('%s MB ',$max_consumption/1024/1024);
    }

    public function getOpcacheMaxAcceleratedFiles()
    {
        $max_files = opcache_get_configuration()['directives']['opcache.max_accelerated_files'];
        return sprintf('%s',$max_files);
    }

    public function getOpcacheValidateTimestamps()
    {
        return opcache_get_configuration()['directives']['opcache.validate_timestamps'];
    }
}