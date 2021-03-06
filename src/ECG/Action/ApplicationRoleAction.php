<?php

namespace ECG\Action;

use ECG\Infos\System\NginxInformation;
use Symfony\Component\Console\Helper\Table;

use ECG\Infos\Magento\GeneralInformation as MagentoGeneralInformation;
use ECG\Infos\Magento\CacheInformation as MagentoCacheInformation;
use ECG\Infos\System\GeneralInformation as SystemGeneralInformation;
use ECG\Infos\System\PhpInformation;
use ECG\Infos\Magento\RedisCacheInformation;
use ECG\Infos\Magento\RedisPageCacheInformation;
use ECG\Infos\Magento\RedisSessionInformation;
use ECG\Infos\Magento\MySQLInformation;

class ApplicationRoleAction extends AbstractCommandAction implements ActionInterface
{
    /**
     * Execute "application" role action.
     *
     * @return void
     */
    public function execute()
    {
        /*
         * Render tables for each available information provider.
         */
        foreach ($this->getInformationList() as $info) {
            $table = new Table($this->getOutput());
            $table->setHeaders([$info->getName()]);
            foreach ($info->getData() as $key => $value) {
                $table->addRow([$key, $value]);
            }
            $table->render();
        }
    }

    /**
     * Get list of information providers assigned to "application" role.
     *
     * @return \ECG\Infos\InformationInterface[]
     */
    private function getInformationList()
    {
        $list = [];

        $list[] = new SystemGeneralInformation();
        $list[] = new PhpInformation(['php-fpm' => $this->getInput()->getOption('php-fpm')]);
        $list[] = new NginxInformation();
        $list[] = new MagentoGeneralInformation(['magento_dir' => $this->getInput()->getOption('magento-dir')]);
        $list[] = new MagentoCacheInformation(['magento_dir' => $this->getInput()->getOption('magento-dir')]);
        $list[] = new RedisSessionInformation(['magento_dir' => $this->getInput()->getOption('magento-dir')]);
        $list[] = new RedisCacheInformation(['magento_dir' => $this->getInput()->getOption('magento-dir')]);
        $list[] = new RedisPageCacheInformation(['magento_dir' => $this->getInput()->getOption('magento-dir')]);
        $list[] = new MySQLInformation(['magento_dir' => $this->getInput()->getOption('magento-dir')]);

        return $list;
    }
}
