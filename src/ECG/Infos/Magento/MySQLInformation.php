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
     * Collect information about Magento instance.
     *
     * @return array
     */
    public function getData()
    {
        $data = [];

        $data['InnoDB Buffer Pool Size'] = sprintf('%s MB',round($this->mysqlinfo['innodb_buffer_pool_size']/1024/1024));
        $data['Max Connections'] = $this->mysqlinfo['max_connections'];
        $data['Max Connect Errors'] = $this->mysqlinfo['max_connect_errors'];


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

    private function returnEmptyMySQLInfo()
    {
        $this->mysqlinfo['innodb_buffer_pool_size'] = 'Unknown';
        $this->mysqlinfo['max_connections'] = 'Unknown';
        $this->mysqlinfo['max_connect_errors'] = 'Unknown';
    }

}