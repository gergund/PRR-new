<?php

namespace ECG\Action;

use Symfony\Component\Console\Helper\Table;

class DatabaseRoleAction extends AbstractCommandAction implements ActionInterface
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
     * Get list of information providers assigned to "database" role.
     *
     * @return \ECG\Infos\InformationInterface[]
     */
    private function getInformationList()
    {
        $list = [];

        return $list;
    }
}
