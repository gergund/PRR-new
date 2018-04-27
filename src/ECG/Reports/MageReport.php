<?php
/**
 * Created by PhpStorm.
 * User: dgergun
 * Date: 26.04.18
 * Time: 10:42
 */

namespace ECG\Reports;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use ECG\Infos\Magento\GeneralInformation;
use ECG\Infos\Magento\CacheInformation;

class MageReport
{
    public function PrepareReport(InputInterface $input, OutputInterface $output)
    {
        /*
         * General information.
         */
        $generalInformation = new GeneralInformation([
            'magento_dir' => $input->getOption('magento-dir'),
        ]);

        $table = new Table($output);
        $table->setHeaders(['Magento Parameters']);
        foreach ($generalInformation->getData() as $key => $value) {
            $table->addRow([$key, $value]);
        }
        $table->render();

        /*
         * Cache types.
         */
        $cacheInformation = new CacheInformation([
            'magento_dir' => $input->getOption('magento-dir'),
        ]);

        $table = new Table($output);
        $table->setHeaders(['Cache Parameters']);
        foreach ($cacheInformation->getData() as $key => $value) {
            $table->addRow([$key, $value]);
        }
        $table->render();
    }
}