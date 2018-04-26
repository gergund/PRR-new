<?php
/**
 * Created by PhpStorm.
 * User: dgergun
 * Date: 29.12.15
 * Time: 18:20
 */

namespace ECG\Reports;

use ECG\Infos\OSInfo;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OSReport {

    public function PrepareReport(InputInterface $input, OutputInterface $output){

        $os_info = new OSInfo();
        $platform = $os_info->getPlatform();
        $release = $os_info->getRelease();
        $kernel = $os_info->getKernel();
        $arch = $os_info->getArchitecture();
        $compiler =$os_info->getCompiler();
        $threading = $os_info->getThreading();
        $hostname = $os_info->getHostName();
        $selinux = $os_info->getSElinux();
        $virtualized = $os_info->getVirtualized();
        $processors = $os_info->getProcessors();
        $model = $os_info->getModel();
        $memory = $os_info->getMemory();
        $httpserver = $os_info->getHTTPserver();

        $output->writeln('');$output->writeln('');
        $output->writeln('OS Parameters Table:');

        $table = new Table($output);
        $table->addRow(['Platform', $platform]);
        $table->addRow(['Release', $release]);
        $table->addRow(['Kernel', $kernel]);
        $table->addRow(['Architecture', $arch]);
        $table->addRow(['Threading', $threading]);
        $table->addRow(['Compiler', $compiler]);
        $table->addRow(['Hostname', $hostname]);
        $table->addRow(['SELinux', $selinux]);
        $table->addRow(['Virtualized', $virtualized]);
        $table->addRow(['Processors', $processors]);
        $table->addRow(['Model', $model]);
        $table->addRow(['Memory', $memory]);
        $table->addRow(['HTTP server', $httpserver]);


        $table->render();
    }

}