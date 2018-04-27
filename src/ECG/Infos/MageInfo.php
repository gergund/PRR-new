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

    public function getSession($magedir)
    {
        $file = $magedir.'/app/etc/env.php';

        if (!is_file($file) || !is_readable($file)) {
            return 'Unknown';
        }

        $mage_conf=require($file);

        if ($mage_conf['session']['save'] == 'redis'){
            return 'Redis';
        }elseif ($mage_conf['session']['save'] == 'files'){
            return 'Files';
        }else{
            return 'Unknown';
        }
    }

    public function getCache($magedir)
    {
        $file = $magedir.'/app/etc/env.php';

        if (!is_file($file) || !is_readable($file)) {
            return 'Unknown';
        }

        $mage_conf=require($file);

        if (isset($mage_conf['cache']['frontend']['default']['backend'])) {

            if ($mage_conf['cache']['frontend']['default']['backend'] == 'Cm_Cache_Backend_Redis') {
                return 'Redis';
            } else {
                return 'Unknown';
            }
        }else{
            return 'Files';
        }

    }

    public function getPageCache($magedir)
    {
        $file = $magedir.'/app/etc/env.php';

        if (!is_file($file) || !is_readable($file)) {
            return 'Unknown';
        }

        $mage_conf=require($file);

        if (isset($mage_conf['cache']['frontend']['page_cache']['backend'])) {

            if ($mage_conf['cache']['frontend']['page_cache']['backend'] == 'Cm_Cache_Backend_Redis') {
                return 'Redis';
            } else {
                return 'Unknown';
            }
        }else {
            return 'Files';
        }

    }

    protected function returnEmptyCacheType()
    {
        $cache_type = array(
            'config' => 'Unknown',
            'layout' => 'Unknown',
            'block_html' => 'Unknown',
            'collections' => 'Unknown',
            'reflection' => 'Unknown',
            'db_ddl' => 'Unknown',
            'eav' => 'Unknown',
            'customer_notification' => 'Unknown',
            'config_integration' => 'Unknown',
            'config_integration_api' => 'Unknown',
            'target_rule' => 'Unknown',
            'full_page' => 'Unknown',
            'translate' => 'Unknown',
            'config_webservice' => 'Unknown',
            'compiled_config' => 'Unknown',
        );
        return $cache_type;
    }

    public function getCacheType($magedir)
    {
        $file = $magedir.'/app/etc/env.php';

        if (!is_file($file) || !is_readable($file)) {
            return $this->returnEmptyCacheType();
        }

        $mage_conf=require($file);
        if(isset($mage_conf['cache_types']['config']))
        {
                    return $mage_conf['cache_types'];
        }else{
            return $this->returnEmptyCacheType();
        }

    }
}