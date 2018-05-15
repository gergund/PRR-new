<?php
/**
 * Created by PhpStorm.
 * User: dgergun
 * Date: 26.04.18
 * Time: 10:45
 */

namespace ECG\Infos\Magento;

use ECG\Infos\InformationInterface;
use ECG\Util\ArrayReader;

class GeneralInformation implements InformationInterface
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
     * Get dataset title.
     *
     * @return string
     */
    public function getName()
    {
        return 'Magento General Configuration';
    }

    /**
     * Get version.
     *
     * @return string
     */
    private function getProductVersion()
    {
        /*
         * Read information from composer.
         */
        $composerPath = $this->magentoDir . '/composer.json';

        if (!is_file($composerPath) || !is_readable($composerPath)) {
            return 'Unknown';
        }

        $composerContent = file_get_contents($composerPath);
        if (preg_match('/"version":\s"(\S+)"/', $composerContent, $match) != 1) {
            $magentoVersion = 'Unknown';
        } else {
            $magentoVersion = $match[1];
        }

        return $magentoVersion;
    }

    /**
     * Get product description.
     *
     * @return string
     */
    private function getProductDescription()
    {
        /*
         * Read information from composer.
         */
        $composerPath = $this->magentoDir . '/composer.json';

        if (!is_file($composerPath) || !is_readable($composerPath)) {
            return 'Unknown';
        }

        $composerContent = file_get_contents($composerPath);

        if (preg_match('/"description": "(.+)"/', $composerContent, $match) != 1) {
            $productDescription = 'Unknown';
        } else {
            $productDescription = $match[1];
        }

        return $productDescription;
    }

    /**
     * Get session information.
     *
     * @return string
     */
    private function getSessionStorage()
    {
        $sessionStorage = 'Unknown';

        $envFilePath = $this->magentoDir . '/app/etc/env.php';

        if (is_file($envFilePath) && is_readable($envFilePath)) {

            $envConfig = include($envFilePath);
            $sessionSave = $this->reader->readDataOrNull($envConfig, 'session/save');

            switch ($sessionSave) {
                case 'redis':
                    $sessionStorage = 'Redis';
                    break;
                case 'files':
                    $sessionStorage = 'Files';
                    break;
            }
        }

        return $sessionStorage;
    }

    /**
     * Get cache storage.
     *
     * @return string
     */
    private function getCacheStorage()
    {
        $cacheStorage = 'Unknown';

        $envFilePath = $this->magentoDir . '/app/etc/env.php';

        if (is_file($envFilePath) && is_readable($envFilePath)) {
            $envConfig = include($envFilePath);

            $cacheBackend = $this->reader->readDataOrNull($envConfig, 'cache/frontend/default/backend');
            if ($cacheBackend === null) {
                $cacheStorage = 'Files';
            } elseif ($cacheBackend == 'Cm_Cache_Backend_Redis') {
                $cacheStorage = 'Redis';
            }
        }

        return $cacheStorage;
    }

    /**
     * Get page cache storage.
     *
     * @return string
     */
    private function getPageCacheStorage()
    {
        $pageCacheStorage = 'Unknown';

        $envFilePath = $this->magentoDir . '/app/etc/env.php';

        if (is_file($envFilePath) && is_readable($envFilePath)) {
            $envConfig = include($envFilePath);

            $pageCacheBackend = $this->reader->readDataOrNull($envConfig, 'cache/frontend/page_cache/backend');
            if ($pageCacheBackend === null) {
                $pageCacheStorage = 'Files';
            } elseif ($pageCacheBackend == 'Cm_Cache_Backend_Redis') {
                $pageCacheStorage = 'Redis';
            }
        }

        return $pageCacheStorage;
    }

    /**
     * Collect information about Magento instance.
     *
     * @return array
     */
    public function getData()
    {
        $data = [];

        $data['Product Description'] = $this->getProductDescription();
        $data['Magento Version'] = $this->getProductVersion();
        $data['Session Storage'] = $this->getSessionStorage();
        $data['Cache Storage'] = $this->getCacheStorage();
        $data['Page Cache Storage'] = $this->getPageCacheStorage();

        return $data;
    }
}
