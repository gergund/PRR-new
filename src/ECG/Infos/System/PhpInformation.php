<?php

namespace ECG\Infos\System;

use ECG\Infos\InformationInterface;
use ECG\Util\ArrayReader;

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

        $this->Exec();

    }

    /**
     * Get dataset title.
     *
     * @return string
     */
    public function getName()
    {
        return 'PHP Parameters';
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
        $data['Display Errors'] = $this->phpinfo['Display Errors'] ;
        $data['Memory Limit'] = $this->phpinfo['Memory Limit'];
        $data['Log Errors'] = $this->phpinfo['Log Errors'];
        $data['Max Execution Time'] = $this->phpinfo['Max Execution Time'] ;
        $data['Max Input Time'] = $this->phpinfo['Max Input Time'];
        $data['OpCache Memory Consumption'] = $this->getOpcacheMemoryConsumption();
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
        $cmd = 'vendor/adoy/fastcgi-client/fcgiget.php '.$this->phpfpm_socket.$this->tmpfile.' 2>&1';
        $content = shell_exec($cmd);
        $content = trim($content);

        if (preg_match('/a:7(.+)/', $content, $match) == 1) {

            $config = unserialize($match[0]);
            $this->phpinfo = $config;
        }

        unlink($this->tmpfile);
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
