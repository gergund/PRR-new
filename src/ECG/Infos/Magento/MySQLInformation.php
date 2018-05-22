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
use PDO;

class MySQLInformation implements InformationInterface
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
     * MySQL info.
     *
     * @var array
     */
    private $mysqlinfo = array();

    /**
     * MySQL status info.
     *
     * @var array
     */
    private $mysql_status_info = array();

    /**
     * MySQL socket.
     *
     * @var array
     */
    private $mysql_socket = null;

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

            $this->findMySQLSocket();

            if (!is_null($this->mysql_socket)){
                $this->runMySQLInfo();
                $this->runMySQLStatusInfo();
            }
            else {

                $this->returnEmptyMySQLInfo();
            }

        }else{

            $this->returnEmptyMySQLInfo();
        }
    }

    /**
     * Get dataset description.
     *
     * @return string
     */
    public function getName()
    {
        return 'MySQL Configuration';
    }

    /**
     * Collect information about MySQL instance.
     *
     * @return array
     */
    public function getData()
    {
        $data = [];

        $data['InnoDB Buffer Pool Size'] = sprintf('%s MB',round($this->mysqlinfo['innodb_buffer_pool_size']/1024/1024));
        $data['InnoDB Buffer Pool Pages Free'] = $this->mysql_status_info['Innodb_buffer_pool_pages_free'];
        $data['InnoDB Data and Index Size'] = $this->InnoDBDataIndexSize();
        $data['Query Cache Size'] = sprintf('%s MB',round($this->mysqlinfo['query_cache_size']/1024/1024));
        $data['Query Cache Limit'] = sprintf('%s MB',round($this->mysqlinfo['query_cache_limit']/1024/1024));
        $data['Query Cache Hits'] = $this->mysql_status_info['Qcache_hits'];
        $data['Max Connections'] = $this->mysqlinfo['max_connections'];
        $data['Max Used Connections'] = $this->mysql_status_info['Max_used_connections'];
        $data['Threads Connected'] = $this->mysql_status_info['Threads_connected'];
        $data['Max Connect Errors'] = $this->mysqlinfo['max_connect_errors'];
        $data['Aborted Connections'] = $this->mysql_status_info['Aborted_connects'];
        $data['Aborted Clients'] = $this->mysql_status_info['Aborted_clients'];
        $data['Max Allowed Packet'] =  sprintf('%s MB',round($this->mysqlinfo['max_allowed_packet']/1024/1024));
        $data['MySQL uptime'] = $this->mysql_status_info['Uptime'];

        return $data;
    }

    private function findMySQLSocket()
    {
        $envFilePath = $this->magentoDir . '/app/etc/env.php';
        $envConfig = include($envFilePath);

        if (isset($envConfig['db']['connection']['default']))
        {
            $this->mysql_socket = array(
                'host' => $envConfig['db']['connection']['default']['host'],
                'dbname' => $envConfig['db']['connection']['default']['dbname'],
                'username' => $envConfig['db']['connection']['default']['username'],
                'password' => $envConfig['db']['connection']['default']['password']
                );
        }
    }

    private function runMySQLInfo()
    {
        $dsn = 'mysql:dbname='.$this->mysql_socket['dbname'].';host='.$this->mysql_socket['host'];
        $connection = new PDO($dsn, $this->mysql_socket['username'],$this->mysql_socket['password'] );
        $sth=$connection->prepare("SHOW VARIABLES");
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_KEY_PAIR);
        $this->mysqlinfo = $result;

    }

    private function runMySQLStatusInfo()
    {
        $dsn = 'mysql:dbname='.$this->mysql_socket['dbname'].';host='.$this->mysql_socket['host'];
        $connection = new PDO($dsn, $this->mysql_socket['username'],$this->mysql_socket['password'] );
        $sth=$connection->prepare("SHOW STATUS");
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_KEY_PAIR);
        $this->mysql_status_info = $result;
    }

    private function returnEmptyMySQLInfo()
    {
        $this->mysqlinfo['innodb_buffer_pool_size'] = 'Unknown';
        $this->mysqlinfo['max_connections'] = 'Unknown';
        $this->mysqlinfo['Threads_connected'] = 'Unknown';
        $this->mysqlinfo['max_connect_errors'] = 'Unknown';
        $this->mysqlinfo['query_cache_size'] = 'Unknown';
        $this->mysqlinfo['query_cache_limit'] = 'Unknown';
        $this->mysqlinfo['max_allowed_packet'] = 'Unknown';

        $this->mysql_status_info['Max_used_connections'] = 'Unknown';
        $this->mysql_status_info['Threads_connected'] = 'Unknown';
        $this->mysql_status_info['Aborted_connects'] = 'Unknown';
        $this->mysql_status_info['Aborted_clients'] = 'Unknown';
        $this->mysql_status_info['Uptime'] = 'Unknown';
        $this->mysql_status_info['Qcache_hits'] = 'Unknown';
        $this->mysql_status_info['Innodb_buffer_pool_pages_free'] = 'Unknown';

    }

    private  function InnoDBDataIndexSize()
    {
        if (!is_null($this->mysql_socket)){

            $dsn = 'mysql:dbname='.$this->mysql_socket['dbname'].';host='.$this->mysql_socket['host'];
            $connection = new PDO($dsn, $this->mysql_socket['username'],$this->mysql_socket['password'] );
            $sth=$connection->prepare("SELECT SUM(data_length+index_length) Total_InnoDB_Bytes FROM information_schema.tables WHERE engine='InnoDB'");
            $sth->execute();
            $result = $sth->fetch();

            $data_size = $result['Total_InnoDB_Bytes'];

            return sprintf('%s MB', round($data_size/1024/1024));

        }else
        {
            return 'Unknown';
        }
    }

}