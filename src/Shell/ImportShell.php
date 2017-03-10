<?php
namespace App\Shell;

use App\CsvImport\CsvImport;
use App\CsvImport\ImportDefinitions;
use Cake\Console\Shell;
use Cake\ORM\TableRegistry;

class ImportShell extends Shell
{
    public $ignoreCount = 0;
    public $statisticsTable;
    public $stepCount;
    public $toInsert = [];
    public $toOverwrite = [];
    public $autoImport;

    /**
     * Modifies the standard output of running 'cake import --help'
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->setDescription('CBER Brownfield Grant Writers\' Tool Data Importer');

        return $parser;
    }

    /**
     * Aborts the script with a styled error message
     *
     * @param null|string $message Message
     * @param int $exitCode Exit code
     * @return void
     */
    public function abort($message = null, $exitCode = self::CODE_ERROR)
    {
        if ($message) {
            $message = $this->helper('Colorful')->error($message);
        }
        parent::abort($message);
    }

    /**
     * Gets the value for $this->overwrite and prompts for
     * input if it has not been set
     *
     * @return bool
     */
    private function getOverwrite()
    {
        if ($this->overwrite == 'y') {
            return true;
        }
        if ($this->overwrite == 'n') {
            return false;
        }
        $this->overwrite = $this->in('Overwrite existing database records?', ['y', 'n'], 'y');

        return $this->getOverwrite();
    }

    /**
     * Returns a string containing the percentage of a task that is done
     *
     * @param int $step Current step number
     * @param int $stepCount Total number of steps
     * @return string
     */
    private function getProgress($step, $stepCount)
    {
        $percentDone = round(($step / $stepCount) * 100);
        $percentDone = str_pad($percentDone, 3, ' ', STR_PAD_LEFT);

        return $percentDone . '%';
    }

    /**
     * Aborts the script with information about an error concerning
     * a Statistic entity
     *
     * @param array $errors Errors
     * @return void
     */
    private function abortWithEntityError($errors)
    {
        $count = count($errors);
        $msg = __n('Error', 'Errors', $count) . ' creating a statistic entity: ';
        $msg = $this->helper('Colorful')->error($msg);
        $this->out($msg);
        $this->out(print_r($errors, true));
        $this->abort();
    }

    /**
     * Menu of available imports
     *
     * @return int Key for $this->availableImports()
     */
    private function menu()
    {
        $this->out('Available imports:');
        $available = $this->availableImports();
        foreach ($available as $k => $import) {
            $this->out("[$k] " . $this->helper('Colorful')->menuOption($import));
        }
        $this->out('');
        $msg = 'Please select an import to run ';
        if (count($available) > 1) {
            $msg .= '[0-' . (count($available) - 1) . ']';
        } else {
            $msg .= '[0]';
        }
        $msg .= ' or type \'all\'';
        $importSelection = $this->in($msg, null, 'all');

        $this->autoImport = $importSelection == 'all';
        if ($importSelection == 'all') {
            return true;
        }

        if ($this->availableImports($importSelection)) {
            return $importSelection;
        }

        $this->out($this->helper('Colorful')->error('Invalid selection'), 2);

        return $this->menu();
    }

    /**
     * Main method
     *
     * @param null|string $importName Name of specific import to run
     * @return mixed
     */
    public function main($importName = null)
    {
        // Process $importName parameter (e.g. "bin\cake import PopulationAge")
        $importSelection = false;
        if ($importName) {
            $importSelection = array_search($importName, $this->availableImports());
            if ($importSelection === false) {
                $this->out("Import \"$importName\" not found", 2);
            }
        }

        // Display menu of available imports
        if ($importSelection === false) {
            $importSelection = $this->menu();
        }

        // Run import(s)
        if ($this->autoImport) {
            $importCount = count($this->availableImports());
            for ($importNum = 0; $importNum < $importCount; $importNum++) {
                $this->import($importNum);
                $this->out();
            }
        } else {
            $this->import($importSelection);
        }
    }

    /**
     * Analyzes data collected for the chosen import, reports on errors,
     * reports on actions that will be taken by the import process, and
     * prompts to user to begin the import
     *
     * @param array $data Array of data for a statistics record (value, category_id, etc.)
     * @return void
     */
    private function prepareImport($data)
    {
        // Clear saved data form previous import
        $this->ignoreCount = 0;
        $this->stepCount = 0;
        $this->toInsert = [];
        $this->toOverwrite = [];

        // Get totals for what was returned
        $dataPointCount = count($data);
        $msg = number_format($dataPointCount) . __n(' data point ', ' data points ', $dataPointCount) . 'found';
        $this->out($msg, 2);

        // Break down insert / overwrite / ignore and catch errors
        $this->statisticsTable = TableRegistry::get('Data');
        $this->out('Preparing import...', 0);
        foreach ($data as $step => $row) {
            $percentDone = $this->getProgress($step, $dataPointCount);
            $msg = "Preparing import: $percentDone";
            $this->_io->overwrite($msg, 0);

            // Look for matching records
            $matchingRecords = $this->getMatchingRecords([
                'loc_type_id' => $row['loc_type_id'],
                'loc_id' => $row['loc_id'],
                'survey_date' => $row['survey_date'],
                'category_id' => $row['category_id']
            ]);

            // Mark for insertion
            if (empty($matchingRecords)) {
                $statEntity = $this->statisticsTable->newEntity($row);
                $errors = $statEntity->errors();
                if (! empty($errors)) {
                    $this->abortWithEntityError($errors);
                }
                $this->toInsert[] = $statEntity;
                continue;
            }

            // Increment ignore count
            if ($matchingRecords[0]['value'] == $row['value']) {
                $this->ignoreCount++;
                continue;
            }

            // Mark for overwriting
            $recordId = $matchingRecords[0]['id'];
            $statEntity = $this->statisticsTable->get($recordId);
            $statEntity = $this->statisticsTable->patchEntity($statEntity, $row);
            $errors = $statEntity->errors();
            if (! empty($errors)) {
                $this->abortWithEntityError($errors);
            }
            $this->toOverwrite[] = $statEntity;
        }
        $this->out();

        $this->stepCount = 0;
        $this->reportIgnored();
        $this->reportToInsert();
        $this->reportToOverwrite();
        $this->out();

        if ($this->stepCount == 0) {
            $this->out('Nothing to import');
            if (! $this->autoImport) {
                $this->_stop();
            }
            return false;
        }

        $begin = $this->in('Begin import?', ['y', 'n'], 'y');
        if ($begin == 'n') {
            if (! $this->autoImport) {
                $this->_stop();
            }
            return false;
        }
    }

    /**
     * Returns an array of records that match the current data location, date, and category
     *
     * @param array $conditions Conditions for where()
     * @return array
     */
    private function getMatchingRecords($conditions)
    {
        $results = $this->statisticsTable->find('all')
            ->select(['id', 'value'])
            ->where($conditions)
            ->toArray();
        if (count($results) > 1) {
            $msg = 'Problem: More than one statistics record found matching ' . print_r($conditions, true);
            $this->abort($msg);
        }

        return $results;
    }

    /**
     * Outputs a message about data that will be ignored
     *
     * @return void
     */
    private function reportIgnored()
    {
        if (! $this->ignoreCount) {
            return;
        }

        $ignoreCount = $this->ignoreCount;
        $msg = number_format($ignoreCount) . ' ' . __n('statistic has', 'statistics have', $ignoreCount);
        $msg .= ' already been recorded and will be ' . $this->helper('Colorful')->importRedundant('ignored');
        $this->out($msg);
    }

    /**
     * Outputs a message about data that will be inserted
     *
     * @return void
     */
    private function reportToInsert()
    {
        if (empty($this->toInsert)) {
            return;
        }

        $insertCount = count($this->toInsert);
        $msg = number_format($insertCount) . ' ' . __n('statistic', 'statistics', $insertCount);
        $msg .= ' will be ' . $this->helper('Colorful')->importInsert('added');
        $this->out($msg);
        $this->stepCount += $insertCount;
    }

    /**
     * Outputs a message about data that will be overwritten
     *
     * @return void
     */
    private function reportToOverwrite()
    {
        if (empty($this->toOverwrite)) {
            return;
        }

        $overwriteCount = count($this->toOverwrite);
        $msg = number_format($overwriteCount) . ' existing ' . __n('statistic', 'statistics', $overwriteCount);
        $msg .= ' will be ' . $this->helper('Colorful')->importOverwrite('overwritten');
        $this->out($msg);
        if ($this->getOverwrite()) {
            $this->stepCount += $overwriteCount;
        }
    }

    /**
     * Prepares an import and conducts inserts and updates where appropriate
     *
     * @param int $importNum Import number
     * @return bool
     */
    protected function import($importNum)
    {
        $imports = ImportDefinitions::getDefinitions();
        $importName = $this->availableImports($importNum);

        $this->out(str_pad('', strlen($importName), '-'));
        $this->out($importName);
        $this->out(str_pad('', strlen($importName), '-'));

        $importObj = new CsvImport($imports[$importName]);
        $this->out('Reading ' . $importObj->filename . '...');
        $importObj->readCsv();

        $prepResult = $this->prepareImport($importObj->data);
        if (! $prepResult) {
            return true;
        }

        $step = 0;
        $percentDone = $this->getProgress($step, $this->stepCount);
        $msg = "Importing: $percentDone";
        $this->out($msg, 0);

        // Insert
        if (! empty($this->toInsert)) {
            foreach ($this->toInsert as $i => $statEntity) {
                $step++;
                $percentDone = $this->getProgress($step, $this->stepCount);
                $msg = "Importing: $percentDone";
                $this->_io->overwrite($msg, 0);
                if (! $this->statisticsTable->save($statEntity)) {
                    $this->out();
                    $this->abortWithEntityError($statEntity->errors());
                }
            }
        }

        // Overwrite
        if (! empty($this->toOverwrite)) {
            if ($this->getOverwrite()) {
                foreach ($this->toOverwrite as $i => $statEntity) {
                    $step++;
                    $percentDone = $this->getProgress($step, $this->stepCount);
                    $msg = "Importing: $percentDone";
                    $this->_io->overwrite($msg, 0);
                    if (! $this->statisticsTable->save($statEntity)) {
                        $this->out();
                        $this->abortWithEntityError($statEntity->errors());
                    }
                }
            } else {
                $this->out();
                $overwriteCount = count($this->toOverwrite);
                $msg = $overwriteCount . ' updated ' . __n('statistic', 'statistics', $overwriteCount) . ' ignored';
                $msg = $this->helper('Colorful')->importOverwriteBlocked($msg);
                $this->out($msg);
            }
        }

        $this->out();
        $msg = $this->helper('Colorful')->success('Import complete');
        $this->out($msg);

        return true;
    }

    /**
     * Returns array of names of available imports, or a specific
     * import name if $key is provided. Returns FALSE if $key is
     * invalid and aborts if no imports are available.
     *
     * @param int $key Numeric key for specifying an import
     * @return array|string|bool
     */
    private function availableImports($key = null)
    {
        $importNames = array_keys(ImportDefinitions::getDefinitions());

        if (empty($importNames)) {
            $this->abort('No imports are available to run');
        }

        sort($importNames);

        if ($key !== null) {
            return isset($importNames[$key]) ? $importNames[$key] : false;
        }

        return $importNames;
    }
}
