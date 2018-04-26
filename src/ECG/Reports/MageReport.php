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

class MageReport
{
    public function PrepareReport(InputInterface $input, OutputInterface $output){

        $mage_info = new MageInfo();

        $magedir = $input->getOption('magento-dir');
        $version = $mage_info->getVersion($magedir);



        $output->writeln('');$output->writeln('');
        $output->writeln('Magento Parameters Table:');

        $table = new Table($output);
        $table->addRow(['Magento Version', $version]);


        $table->render();
    }
}