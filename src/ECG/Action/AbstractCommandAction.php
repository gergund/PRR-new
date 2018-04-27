<?php

namespace ECG\Action;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommandAction implements ActionInterface
{
    /**
     * Console input instance.
     *
     * @var InputInterface
     */
    private $input = null;

    /**
     * Console output instance.
     *
     * @var OutputInterface
     */
    private $output = null;

    /**
     * Constructor.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * Get input instance.
     *
     * @return InputInterface
     */
    protected function getInput()
    {
        return $this->input;
    }

    /**
     * Get output instance.
     *
     * @return OutputInterface
     */
    protected function getOutput()
    {
        return $this->output;
    }

    /**
     * Execute action.
     *
     * @return void
     */
    abstract public function execute();
}
