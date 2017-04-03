<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\ORM\TableRegistry;

class RepairShell extends Shell
{
    /**
     * Modifies the standard output of running 'cake import --help'
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->setDescription('CBER Brownfield Grant Writers\' Tool Database Repair');

        return $parser;
    }

    /**
     * Main method
     *
     * @return mixed
     */
    public function main()
    {
        $begin = $this->in(
            'Repair tree structure of data_categories database table?',
            ['y', 'n'],
            'y'
        );
        if ($begin) {
            $this->recover();
            $this->out('Repair complete');
        }
    }

    /**
     * Recovers the tree structure of the data_categories database table
     *
     * @return void
     */
    private function recover()
    {
        $dataCategoriesTable = TableRegistry::get('DataCategories');
        $dataCategoriesTable->recover();
    }
}
