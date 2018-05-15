<?php

namespace ECG\Infos\System;

use ECG\Infos\InformationInterface;
use ECG\Util\ArrayReader;

use Adoy\FastCGI\Client;


class PhpInformation implements InformationInterface
{
    /**
     * Array reader.
     *
     * @var ArrayReader
     */
    private $reader = null;

     /**
     * PHP-FPM socket.
     *
     * @var string
     */
    private $phpfpm_socket = null;

    /**
     * Temporary File.
     *
     * @var string
     */
    private $tmpfile = '/tmp/info.php';

    /**
     * PHP info.
     *
     * @var array
     */
    private $phpinfo = array();

    /**
     * Construct.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->reader = new ArrayReader();

        $this->phpfpm_socket = $this->reader->readDataOrNull($config, 'php-fpm');

        if( !is_null($this->phpfpm_socket)){
            $this->Exec();
        }else{

            $this->phpinfo['PHP Version'] = 'Unknown';
            $this->phpinfo['Display Errors'] = 'Unknown' ;
            $this->phpinfo['Memory Limit'] = 'Unknown';
            $this->phpinfo['Log Errors'] = 'Unknown';
            $this->phpinfo['Error Log File'] = 'Unknown';
            $this->phpinfo['Max Execution Time'] = 'Unknown';
            $this->phpinfo['Max Input Time'] = 'Unknown';
            $this->phpinfo['OpCache Memory Usage'] = 'Unknown';
        }
    }

    /**
     * Get dataset title.
     *
     * @return string
     */
    public function getName()
    {
        return 'PHP Configuration';
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

        $data['PHP Version'] = $this->phpinfo['PHP Version'];
        $data['PHP Modules'] = $this->getPHPModules(1);
        $data[' '] = $this->getPHPModules(2);
        $data['  '] = $this->getPHPModules(3);
        $data['   '] = $this->getPHPModules(4);
        $data['    '] = $this->getPHPModules(5);
        $data['Display Errors'] = $this->phpinfo['Display Errors'] ;
        $data['Memory Limit'] = $this->phpinfo['Memory Limit'];
        $data['Log Errors'] = $this->phpinfo['Log Errors'];
        $data['Error Log File'] = $this->phpinfo['Error Log File'];
        $data['Max Execution Time'] = $this->phpinfo['Max Execution Time'] ;
        $data['Max Input Time'] = $this->phpinfo['Max Input Time'];
        $data['OpCache Memory Consumption'] = $this->getOpcacheMemoryConsumption();
        $data['OpCache Memory Usage'] = sprintf('%s MB',$this->phpinfo['OpCache Memory Usage']);
        $data['OpCache Max Accelerated Files'] = $this->getOpcacheMaxAcceleratedFiles();
        $data['OpCache Validate Timestamps'] = $this->getOpcacheValidateTimestamps();

        return $data;
    }

    /**
     * Running code through PHP-FPM socket.
     *
     * @return void
     */
    private function Exec()
    {
        $this->createTemporaryFile();
        $this->runTemporaryFile();
    }


    /**
     * Create temporary file.
     *
     * @return void
     */
    private function createTemporaryFile()
    {
        touch($this->tmpfile);


        $code =<<<'EOF'
<?php
    
class FCGIInformation
{

    public function __construct()
    {
        $data = array();

        $data['PHP Version'] = phpversion();
        $data['Display Errors'] = ini_get('display_errors');
        $data['Memory Limit'] = ini_get('memory_limit');
        $data['Log Errors'] = ini_get('log_errors');
        $data['Error Log File'] = ini_get('error_log');
        $data['Max Execution Time'] = ini_get('max_execution_time');
        $data['Max Input Time'] = ini_get('max_input_time');
        $data['OpCache Memory Usage'] = round(opcache_get_status()['memory_usage']['used_memory']/1024/1024); 

        print (serialize($data));
    }

}

$fcgi_info = new FCGIInformation();
EOF;

        if(is_writable($this->tmpfile)){
            file_put_contents($this->tmpfile,$code);
        }
        

    }

    /**
     * Run temporary file.
     *
     * @return void
     */
    private function runTemporaryFile()
    {
        $url=array(
            'path' => $this->tmpfile,
            'query' => $this->tmpfile,
            'req' => $this->tmpfile,
            'uri' => $this->tmpfile
        );

        $params = array(
            'GATEWAY_INTERFACE' => 'FastCGI/1.0',
            'REQUEST_METHOD'    => 'GET',
            'SCRIPT_FILENAME'   => $url['path'],
            'SCRIPT_NAME'       => $url['req'],
            'QUERY_STRING'      => $url['query'],
            'REQUEST_URI'       => $url['uri'],
            'DOCUMENT_URI'      => $url['req'],
            'SERVER_SOFTWARE'   => 'php/fcgiclient',
            'REMOTE_ADDR'       => '127.0.0.1',
            'REMOTE_PORT'       => '9985',
            'SERVER_ADDR'       => '127.0.0.1',
            'SERVER_PORT'       => '80',
            'SERVER_NAME'       => php_uname('n'),
            'SERVER_PROTOCOL'   => 'HTTP/1.1',
            'CONTENT_TYPE'      => '',
            'CONTENT_LENGTH'    => 0
        );

        if (preg_match('/unix:/', $this->phpfpm_socket, $match) == 1) {

            $client = new Client($this->phpfpm_socket, -1);

        }else{

            $host = explode(':',$this->phpfpm_socket)[0];
            $port = explode(':',$this->phpfpm_socket)[1];

            $client = new Client($host,$port);
        }


        $response = $client->request($params, false)."\n";

        if (preg_match('/a:8(.+)/', $response, $match) == 1) {

            $config = unserialize($match[0]);
            $this->phpinfo = $config;
        }

        unlink($this->tmpfile);
    }

    /**
     * Get PHP modules list.
     *
     * @return string
     */
    private function getPHPModules($rows)
    {
        $begin=0;$length=115;
        for($i=1;$i<=$rows;$i++)
        {
            $result = substr(implode(", ", get_loaded_extensions()),$begin,$length);
            $begin=$begin+$length;
        }

        return $result;
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
