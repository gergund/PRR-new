<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 27.12.2015
 * Time: 13:55
 */

namespace ECG\Commands;

//
use ECG\Reports\OSReport;
use ECG\Reports\MageReport;
use ECG\Reports\PHPReport;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;




class CollectCommand extends Command
{

    protected $roles = ['application','database'];

    protected function configure()
    {
        $this
            ->setName('collect:data')
            ->setDescription('Collecting HW and SW data for the report ')
            ->addArgument(
                'role',
                InputArgument::REQUIRED,
                'Set server role type for collecting data'
            )
            ->addOption('magento-dir',null,InputOption::VALUE_OPTIONAL,'Magento source directory', null )
        ;
    }

    protected function interact(InputInterface $input, OutputInterface $output){

        $role = $input->getArgument('role');
        if (is_null($role)){
            $output->writeln('<error>Role is not defined, please specify it. For example: </error>');
            foreach($this->roles as $item){
                $output->writeln('<info>collect:data '.$item.'</info>');
            }
        }
        else {
               if(!in_array($role, $this->roles, true)){
                   $output->writeln('<error>None of defined Role is matched. Use application or database </error>');
               }
            }

        $magedir = $input->getOption('magento-dir');
        if (is_null($magedir)){
            $output->writeln('<error>Option: magento-dir is not defined For example: collect:data application --magento-dir=/var/www/html/ </error>');
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $role = $input->getArgument('role');

        if ($role) {
            switch($role){
                case 'application':
                    echo "Application Role: ";
                    $this->role_application_execute($input,$output);
                    break;
                case 'database':
                    echo "Database Role: ";
                    $this->role_database_execute($input,$output);
                    break;
            }
        }

    }

    protected function role_application_execute(InputInterface $input, OutputInterface $output)
    {
        $os_report = new OSReport();
        $os_report->PrepareReport($input,$output);

        $php_report = new PHPReport();
        $php_report->PrepareReport($input,$output);

        $mage_report = new MageReport();
        $mage_report->PrepareReport($input,$output);
    }

    protected function role_database_execute(InputInterface $input, OutputInterface $output)
    {
        $text = "Database execution statement";
        $output->writeln($text);
    }

}