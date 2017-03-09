<?php
namespace App\Shell\Imports;

use App\Shell\ImportShell;

class PopulationAgeShell extends ImportShell
{
    /**
     * Run method
     *
     * @return void
     */
    public function run()
    {
        /**
         * fields to account for
         * loc_type_id
         * loc_id
         * survey_date (year . 0000)
         * source_id
         * category_id
         * value
         */
        $this->filename = 'Demographics_PopulationbyAge.csv';
        $this->headerRowCount = 5;
        $this->sourceId = 93; // American Community Survey (https://factfinder.census.gov/...)
        $this->categoryIds = [
            'Total' => 1,
            'Total Under 5 years old' => 6018,
            'Total 5 to 14' => 5723,
            'Total 15 to 24' => 5724,
            'Total  25 to 44' => 5725,
            'Total 45 to 59' => 5726,
            'Total 60 to 74' => 5727,
            'Total 75 and older' => 5728,
            'Total Under 18 years old' => 6019
        ];

        $this->out("\nRetrieving data from CSV file...");
        $this->import();
    }
}
