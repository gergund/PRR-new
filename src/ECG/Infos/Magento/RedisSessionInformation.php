<?php
/**
 * Created by PhpStorm.
 * User: dgergun
 * Date: 08.05.18
 * Time: 12:43
 */

namespace ECG\Infos\Magento;

use ECG\Infos\InformationInterface;
use ECG\Util\ArrayReader;

use Credis_Client;

class RedisSessionInformation implements InformationInterface
{

    /**
     * Array reader.
     *
     * @var ArrayReader
     */
    private $reader = null;

    /**
     * Magento document root path.
     *
     * @var string
     */
    private $magentoDir = null;

    /**
     * Redis info.
     *
     * @var array
     */
    private $redisinfo = array();

    /**
     * Redis socket.
     *
     * @var array
     */
    private $redis_socket = null;

    /**
     * Construct.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->reader = new ArrayReader();

        $this->magentoDir = $this->reader->readDataOrNull($config, 'magento_dir');

        if( !is_null($this->magentoDir)){

            $this->findRedisSocket();

            if (!is_null($this->redis_socket)){
                $this->runRedisInfo();
            }
            else {

                $this->returnEmptyRedisInfo();
            }

        }else{

            $this->returnEmptyRedisInfo();
        }
    }

    /**
     * Get dataset description.
     *
     * @return string
     */
    public function getName()
    {
        return 'Redis Session Settings';
    }

    /**
     * Collect information about Magento instance.
     *
     * @return array
     */
    public function getData()
    {
        $data = [];

        $data['Redis Version'] = $this->redisinfo['redis_version'];
        $data['Used Memory'] = sprintf('%s MB', round($this->redisinfo['used_memory']/1024/1024));
        $data['Used Memory Peak'] = sprintf('%s MB', round($this->redisinfo['used_memory_peak']/1024/1024));
        $data['Expired keys'] = $this->redisinfo['expired_keys'];
        $data['Evicted keys'] = $this->redisinfo['evicted_keys'];

        return $data;
    }

    private function findRedisSocket()
    {
        $envFilePath = $this->magentoDir . '/app/etc/env.php';
        $envConfig = include($envFilePath);

        if (isset($envConfig['session']['redis']))
        {
            $this->redis_socket = array(
                'host' => $envConfig['session']['redis']['host'],
                'port' => $envConfig['session']['redis']['port']
                );
        }
    }

    private function runRedisInfo()
    {
        $redis = new Credis_Client($this->redis_socket['host'],$this->redis_socket['port']);
        $this->redisinfo = $redis->info();
    }

    private function returnEmptyRedisInfo()
    {
        $this->redisinfo['redis_version'] = 'Unknown';
        $this->redisinfo['used_memory'] = 'Unknown';
        $this->redisinfo['used_memory_peak'] = 'Unknown';
        $this->redisinfo['expired_keys'] = 'Unknown';
        $this->redisinfo['evicted_keys'] = 'Unknown';
    }

}