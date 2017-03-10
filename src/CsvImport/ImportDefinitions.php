<?php
namespace App\CsvImport;

class ImportDefinitions
{
    /**
     * Returns the definitions of each supported import
     *
     * @return array
     */
    public static function getDefinitions()
    {
        $imports = [];

        $imports['Population by age'] = [
            'filename' => 'Demographics_PopulationbyAge.csv',
            'headerRowCount' => 5,
            'sourceId' => 93, // American Community Survey (https://factfinder.census.gov/...)
            'categoryIds' => [
                'Total' => 1,
                'Total Under 5 years old' => 6010,
                'Total 5 to 14' => 5723,
                'Total 15 to 24' => 5724,
                'Total  25 to 44' => 5725,
                'Total 45 to 59' => 5726,
                'Total 60 to 74' => 5727,
                'Total 75 and older' => 5728,
                'Total Under 18 years old' => 6011
            ]
        ];

        $imports['Average household size'] = [
            'filename' => 'Demographics_AverageHouseholdSize.csv',
            'headerRowCount' => 5,
            'sourceId' => 94, // American Community Survey (https://factfinder.census.gov/...)
            'categoryIds' => [
                'Average Household Size' => 348
            ]
        ];

        return $imports;
    }
}
