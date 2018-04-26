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
}