<?php
/**
 * Created by PhpStorm.
 * User: dgergun
 * Date: 26.04.18
 * Time: 10:42
 */

namespace ECG\Reports;

use ECG\Infos\MageInfo;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Helper\TableSeparator;

class MageReport
{
    public function PrepareReport(InputInterface $input, OutputInterface $output){

        $mage_info = new MageInfo();

        $magedir = $input->getOption('magento-dir');

        $version = $mage_info->getVersion($magedir);
        $session = $mage_info->getSession($magedir);
        $cache = $mage_info->getCache($magedir);
        $pagecache = $mage_info->getPageCache($magedir);
        $cachetype = $mage_info->getCacheType($magedir);

        $output->writeln('');$output->writeln('');
        $output->writeln('Magento Parameters Table:');

        $table = new Table($output);
        $table->addRow(['Magento Version', $version]);
        $table->addRow(['Session storage', $session]);
        $table->addRow(['Cache storage', $cache]);
        $table->addRow(['Page Cache Storage', $pagecache]);

        $table->render();

        $output->writeln('');$output->writeln('');
        $output->writeln('Cache Types Settings:');

        $table = new Table($output);
        $table->addRow(['config',$cachetype['config']]);
        $table->addRow(['layout',$cachetype['layout']]);
        $table->addRow(['block_html',$cachetype['block_html']]);
        $table->addRow(['collections',$cachetype['collections']]);
        $table->addRow(['reflection',$cachetype['reflection']]);
        $table->addRow(['db_ddl',$cachetype['db_ddl']]);
        $table->addRow(['eav',$cachetype['eav']]);
        $table->addRow(['customer_notification',$cachetype['customer_notification']]);
        $table->addRow(['config_integration',$cachetype['config_integration']]);
        $table->addRow(['config_integration_api',$cachetype['config_integration_api']]);
        $table->addRow(['target_rule',$cachetype['target_rule']]);
        $table->addRow(['full_page',$cachetype['full_page']]);
        $table->addRow(['translate',$cachetype['translate']]);
        $table->addRow(['config_webservice',$cachetype['config_webservice']]);
        $table->addRow(['compiled_config',$cachetype['compiled_config']]);

        $table->render();
    }
}