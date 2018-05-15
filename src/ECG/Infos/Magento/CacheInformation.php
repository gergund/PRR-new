<?php

namespace ECG\Infos\Magento;

use ECG\Infos\InformationInterface;
use ECG\Util\ArrayReader;

class CacheInformation implements InformationInterface
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
     * Construct.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->reader = new ArrayReader();
        
        $this->magentoDir = $this->reader->readDataOrNull($config, 'magento_dir');
    }

    /**
     * Get dataset description.
     *
     * @return string
     */
    public function getName()
    {
        return 'Magento Cache Configuration';
    }

    /**
     * Get cache configuration data.
     *
     * @return array
     */
    public function getData()
    {
        $data = $this->getCacheConfigTemplate();

        $envFilePath = $this->magentoDir . '/app/etc/env.php';

        if (is_file($envFilePath) && is_readable($envFilePath)) {
            $envConfig = include($envFilePath);

            $cacheConfig = $this->reader->readDataOrNull($envConfig, 'cache_types');
            if (is_array($cacheConfig)) {
                foreach ($cacheConfig as $key => $value) {
                    $data[$key] = $value;
                }
            }
        }

        return $data;
    }

    /**
     * Get cache configuration template.
     *
     * @return array
     */
    private function getCacheConfigTemplate()
    {
        $data = [
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
        ];

        return $data;
    }
}
