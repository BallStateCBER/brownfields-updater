<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\Filesystem\Folder;
use Cake\ORM\TableRegistry;

class ImportShell extends Shell
{

    public $stepCount;
    public $statisticsTable;

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
        $msg = 'Please select an import to run: ';
        if (count($available) > 1) {
            $msg .= '[0-' . (count($available) - 1) . ']';
        } else {
            $msg .= '[0]';
        }
        $importNum = $this->in($msg);
        if ($this->availableImports($importNum)) {
            return $importNum;
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
        $importNum = false;
        if ($importName) {
            $importNum = array_search($importName, $this->availableImports());
            if ($importNum === false) {
                $this->out("Import \"$importName\" not found", 2);
            }
        }

        // Display menu of available imports
        if ($importNum === false) {
            $importNum = $this->menu();
        }

        // Run import
        $importName = $this->availableImports($importNum);
        $importClass = "App\\Shell\\Imports\\{$importName}Shell";
        $importObj = new $importClass();

        return $importObj->run();
    }

    /**
     * Analyzes data collected for the chosen import, reports on errors,
     * reports on actions that will be taken by the import process, and
     * prompts to user to begin the import
     *
     * @return void
     */
    private function prepareImport()
    {
        if (empty($this->apiCallResults)) {
            $this->abort('No data returned');
        }

        // Get totals for what was returned
        $dataPointCount = 0;
        foreach ($this->apiCallResults as $fips => $data) {
            $dataPointCount += count($data);
        }
        $locationCount = count($this->apiCallResults);
        $msg = number_format($dataPointCount) . __n(' data point ', ' data points ', $dataPointCount);
        $msg .= 'found for ' . number_format($locationCount) . ' locations';
        $this->out($msg, 2);

        // Break down insert / overwrite / ignore and catch errors
        $Location = new Location();
        $this->statisticsTable = TableRegistry::get('Data');
        $this->out('Preparing import...', 0);
        $step = 0;
        foreach ($this->apiCallResults as $fips => $data) {
            $locationId = $Location->getIdFromCode($fips, $this->locationTypeId);
            if (! $locationId) {
                $this->abort("FIPS code $fips does not correspond to any known county.");
            }
            foreach ($data as $category => $value) {
                $step++;
                $percentDone = $this->getProgress($step, $dataPointCount);
                $msg = "Preparing import: $percentDone";
                $this->_io->overwrite($msg, 0);

                // Look for matching records
                $matchingRecords = $this->getMatchingRecords($locationId, $category);

                // Prepare record for inserting / overwriting
                $newRecord = [
                    'loc_type_id' => $this->locationTypeId,
                    'loc_id' => $locationId,
                    'survey_date' => $this->surveyDate,
                    'category_id' => $this->categoryIds[$category],
                    'value' => $value,
                    'source_id' => $this->sourceId
                ];

                // Mark for insertion
                if (empty($matchingRecords)) {
                    $statEntity = $this->statisticsTable->newEntity($newRecord);
                    $errors = $statEntity->errors();
                    if (! empty($errors)) {
                        $this->abortWithEntityError($errors);
                    }
                    $this->toInsert[] = $statEntity;
                    continue;
                }

                // Increment ignore count
                $recordedValue = $matchingRecords[0]['value'];
                if ($recordedValue == $value) {
                    $this->ignoreCount++;
                    continue;
                }

                // Mark for overwriting
                $recordId = $matchingRecords[0]['id'];
                $statEntity = $this->statisticsTable->get($recordId);
                $statEntity = $this->statisticsTable->patchEntity($statEntity, $newRecord);
                $errors = $statEntity->errors();
                if (! empty($errors)) {
                    $this->abortWithEntityError($errors);
                }
                $this->toOverwrite[] = $statEntity;
            }
        }
        $this->out();

        $this->stepCount = 0;
        $this->reportIgnored();
        $this->reportToInsert();
        $this->reportToOverwrite();
        $this->out();

        if ($this->stepCount == 0) {
            $this->out('Nothing to import');
            $this->_stop();
        }

        $begin = $this->in('Begin import?', ['y', 'n'], 'y');
        if ($begin == 'n') {
            $this->_stop();
        }
    }

    /**
     * Returns an array of records that match the current data location, date, and category
     *
     * @param int $locationId Location ID
     * @param string $category Category name
     * @return array
     */
    private function getMatchingRecords($locationId, $category)
    {
        if (! isset($this->categoryIds[$category])) {
            $this->abort("Unrecognized category: $category");
        }
        $conditions = [
            'loc_type_id' => $this->locationTypeId,
            'loc_id' => $locationId,
            'survey_date' => $this->surveyDate,
            'category_id' => $this->categoryIds[$category]
        ];
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
     * @return bool
     */
    protected function import()
    {
        $this->prepareImport();

        $step = 0;
        $percentDone = $this->getProgress($step, $this->stepCount);
        $msg = "Importing: $percentDone";
        $this->out($msg, 0);
        $statisticsTable = TableRegistry::get('Statistics');

        // Insert
        if (! empty($this->toInsert)) {
            foreach ($this->toInsert as $i => $statEntity) {
                $step++;
                $percentDone = $this->getProgress($step, $this->stepCount);
                $msg = "Importing: $percentDone";
                $this->_io->overwrite($msg, 0);
                $statisticsTable->save($statEntity);
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
                    $statisticsTable->save($statEntity);
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
        $importNames = array_keys($this->getImportDefinitions());

        if (empty($importNames)) {
            $this->abort('No imports are available to run');
        }

        if ($key !== null) {
            return isset($importNames[$key]) ? $importNames[$key] : false;
        }

        sort($importNames);

        return $importNames;
    }

    public function getImportDefinitions()
    {
        $imports = [];

        $imports['Population by age'] = [
            'filename' => 'Demographics_PopulationbyAge.csv',
            'headerRowCount' => 5,
            'sourceId' => 93, // American Community Survey (https://factfinder.census.gov/...)
            'categoryIds' => [
                'Total' => 1,
                'Total Under 5 years old' => 6018,
                'Total 5 to 14' => 5723,
                'Total 15 to 24' => 5724,
                'Total  25 to 44' => 5725,
                'Total 45 to 59' => 5726,
                'Total 60 to 74' => 5727,
                'Total 75 and older' => 5728,
                'Total Under 18 years old' => 6019
            ]
        ];

        return $imports;
    }
}
