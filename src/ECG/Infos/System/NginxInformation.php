<?php
/**
 * Created by PhpStorm.
 * User: dgergun
 * Date: 13.07.18
 * Time: 11:17
 */

namespace ECG\Infos\System;


use ECG\Infos\InformationInterface;
use ECG\Util\ArrayReader;

class NginxInformation implements InformationInterface
{
    /**
     * Array reader.
     *
     * @var ArrayReader
     */
    private $reader = null;

    /**
     * Nginx configuration file
     *
     * @var string
     */
    private $nginx_conf = '';

    /**
     * Construct.
     *
     * @param array $config
     */
    public function __construct()
    {
        $this->reader = new ArrayReader();

        $this->detectNginx();
    }

    /**
     * Get dataset title.
     *
     * @return string
     */
    public function getName()
    {
        return 'Nginx Configuration';
    }

    /**
     * Collect information about operation system.
     *
     * @return array
     */
    public function getData()
    {
        $data = [];

        if($this->detectNginx()){
            $data['Nginx Worker Processes'] = $this->getWorkerProcesses() ;
            $data['Nginx Worker Connections'] = $this->getWorkerConnections() ;
        }
        else{
            $data['Nginx'] = 'Not Found';
        }

        return $data;
    }

    public function detectNginx()
    {
        $content = shell_exec('ps aux');
        $content = trim($content);

        if (preg_match('/(nginx)/', $content, $match) == 1) {
            if (preg_match('/nginx.+-c (.+)/', $content, $match) > 0 )
            {
                $this->nginx_conf = $match[1];
                return true;
            }

            $this->nginx_conf = '/etc/nginx/nginx.conf';
            return true;
        }

        return false;
    }

    private function getWorkerProcesses()
    {
       $content = file_get_contents($this->nginx_conf);
       $content = trim($content);

       if(preg_match('/worker_processes (.+);/',$content,$match) == 1){
           return $match[1];
       }

       return 'Unknown';
    }

    private function getWorkerConnections()
    {
        $content = file_get_contents($this->nginx_conf);
        $content = trim($content);

        if(preg_match('/worker_connections (\d+);/',$content,$match) == 1){
            return $match[1];
        }

        return 'Unknown';
    }
}