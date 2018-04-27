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
}