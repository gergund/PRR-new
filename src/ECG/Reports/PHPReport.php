<?php
/**
 * Created by PhpStorm.
 * User: dgergun
 * Date: 26.04.18
 * Time: 10:56
 */

namespace ECG\Reports;

use ECG\Infos\PHPInfo;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PHPReport
{
    public function PrepareReport(InputInterface $input, OutputInterface $output){

        $php_info = new PHPInfo();
        $version = $php_info->getVersion();
        $display_errors = $php_info->getDisplayErrors();
        $memory_limit = $php_info->getMemoryLimit();
        $log_errors = $php_info->getLogErrors();

        $output->writeln('');$output->writeln('');
        $output->writeln('PHP Parameters Table:');

        $table = new Table($output);
        $table->addRow(['PHP Version', $version]);
        $table->addRow(['Display errors',$display_errors]);
        $table->addRow(['Memory limit',$memory_limit]);
        $table->addRow(['Log errors',$log_errors]);


        $table->render();
    }
}