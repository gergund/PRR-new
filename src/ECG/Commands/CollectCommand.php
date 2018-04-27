<?php

namespace ECG\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use ECG\Action\ApplicationRoleAction;
use ECG\Action\DatabaseRoleAction;

/**
 * Report collection command.
 */
class CollectCommand extends Command
{
    /**
     * Available roles.
     *
     * @var array
     */
    protected $roles = ['application', 'database'];

    /**
     * Configure command.
     *
     * @return void
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('collect:data');
        $this->setDescription('Collecting HW and SW data for the report.');

        $this->addArgument(
            'role',
            InputArgument::REQUIRED,
            'Set server role type for collecting data.'
        );

        $this->addOption(
            'magento-dir',
            null,
            InputOption::VALUE_OPTIONAL,
            'Magento source directory',
            null
        );
    }

    /**
     * Interact with user.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $role = $input->getArgument('role');

        if (is_null($role)){
            $output->writeln('<error>Role is not defined, please specify it. For example: </error>');
            foreach ($this->roles as $item) {
                $output->writeln('<info>collect:data ' . $item . '</info>');
            }
        } elseif (!in_array($role, $this->roles, true)) {
            $output->writeln('<error>None of defined Role is matched. Use "application" or "database".</error>');
        }

        $magedir = $input->getOption('magento-dir');
        if (is_null($magedir)) {
            $output->writeln('<error>Option: magento-dir is not defined For example: collect:data application --magento-dir=/var/www/html/ </error>');
        }
    }

    /**
     * Execute command.
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $exitCode = 0;

        try {
            $action = $this->createAction($input, $output);
            $action->execute();
        } catch (\Exception $e) {
            $exitCode = 1;
            $output->writeln('<error>Error ocured:</error>');
            $output->writeln($e->getMessage());
        }

        return $exitCode;
    }

    /**
     * Create simple action.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return \ECG\Action\ActionInterface
     */
    private function createAction(InputInterface $input, OutputInterface $output)
    {
        $action = null;

        if ($input->getArgument('role') == 'application') {
            $action = new ApplicationRoleAction($input, $output);
        } elseif ($input->getArgument('role') == 'database') {
            $action = new DatabaseRoleAction($input, $output);
        }

        return $action;
    }
}
