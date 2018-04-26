<?php
/**
 * Created by PhpStorm.
 * User: dgergun
 * Date: 26.04.18
 * Time: 10:45
 */

namespace ECG\Infos;


class MageInfo
{
    public function getVersion($magedir)
    {
        $file = $magedir.'/composer.json';

        if (!is_file($file) || !is_readable($file)) {
            return 'Unknown';
        }

        $contents = file_get_contents($file);
        if (preg_match('/"version":\s"(\S+)"/', $contents, $match) != 1   ) {
            $mageversion =  'Unknown';
        }else{
            $mageversion=  $match[1];
        }


        if (preg_match('/"description": "(.+)"/', $contents, $match) != 1   ) {
            $magedir = 'Unknown';
        }else{
            $magedistr = $match[1];
        }

        return ''.$mageversion.' '.$magedistr;



    }
}