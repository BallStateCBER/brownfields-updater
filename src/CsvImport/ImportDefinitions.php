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
            'headerRowCount' => 6,
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
            'sourceId' => 94, // American Community Survey (https://factfinder.census.gov/...)
            'categoryIds' => [
                'Average Household Size' => 348
            ]
        ];

        $imports['Dependency ratio'] = [
            'filename' => 'Demographics_DependencyRatio.csv',
            'sourceId' => 95, // American Community Survey (https://factfinder.census.gov/...)
            'categoryIds' => [
                'Total Population' => 1,
                'Total 0 to 14 years old' => 6012,
                'Total Over 65 years old' => 6013
            ]
        ];

        $imports['Disability age breakdown'] = [
            'filename' => 'Demographics_DisabilityAgeBreakdown.csv',
            'sourceId' => 96,
            'categoryIds' => [
                'Total' => null,
                'Total Under 5 years' => null,
                'Total Under 5 years old with disability' => null,
                'Total 5-17 year' => null,
                'Total 5-17 year old with disability' => 6014,
                'Total 18-34 years' => null,
                'Total 18-34 years with a disability' => 6015,
                'Total 35-64' => null,
                'Total 35-64 years with a disability' => 6016,
                'Total 65-74 years old' => null,
                'Total 65-74 with a disability' => 5803,
                'Total 75 years and older' => null,
                'Total 75 years and older with a disability' => 5804
            ]
        ];

        $imports['Disabled population'] = [
            'filename' => 'Demographics_DisabilityPopulation.csv',
            'sourceId' => 97,
            'categoryIds' => [
                'Total Population' => null,
                'Total Population with a disability' => 5794
            ]
        ];

        $prefix = 'Total; Estimate; Population 25 years and over';
        $imports['Educational attainment'] = [
            'filename' => 'Demographics_EducationAttainment.csv',
            'sourceId' => 98,
            'categoryIds' => [
                $prefix => 453,
                "$prefix - Less than 9th grade" => 6017,
                "$prefix - 9th to 12th grade, no diploma" => 456,
                "$prefix - High school graduate (includes equivalency)" => 457,
                "$prefix - Some college, no degree" => 6018,
                "$prefix - Associate's degree" => 460,
                "$prefix - Bachelor's degree" => 461,
                "$prefix - Graduate or professional degree" => 6019
            ]
        ];

        $imports['Female age breakdown'] = [
            'filename' => 'Demographics_FemalesAgeBreakdown.csv',
            'sourceId' => 99,
            'categoryIds' => [
                'Total Females' => 271,
                'Total 0 to 14' => 5735,
                'Total 15 to 44' => 5736,
                'Total Over 44' => 5737
            ]
        ];

        /* Values were converted from a 0 to 1 scale to a 0 to 100 scale
         * to match the format of older values already in the database */
        $imports['Free and reduced lunch'] = [
            'filename' => 'Demographics_FreeandReducedLunch.csv',
            'sourceId' => 100,
            'categoryIds' => [
                'Free Lunch' => 5780,
                'Reduce Lunch' => 5781,
                'Free+Reduce Lunch' => 5782
            ]
        ];

        $imports['Households with people under 18, breakdown by type'] = [
            'filename' => 'Demographics_Householdunder18breakdown.csv',
            'sourceId' => 103,
            'categoryIds' => [
                'Total' => 346,
                'In family household:- In married-couple family' => 5762,
                'In family households: - In male householder, no wife present, family' => 5764,
                'In family households: - In female householder, no husband present, family' => 5766,
                'In nonfamily households' => 5768
            ]
        ];

        $imports['Households with people over 60'] = [
            'filename' => 'Demographics_Householdwithover60.csv',
            'sourceId' => 102,
            'categoryId' => 6025
        ];

        $imports['High school graduation rate'] = [
            'filename' => 'Demographics_HighSchoolGraduationRates_WEIRDDATA.csv',
            'sourceId' => 101,
            'categoryId' => 5396,
            'headers' => [
                'fips',
                'year',
                'schoolCorpId',
                'schoolCorpName',
                'Cohort N',
                'Grad N',
                'value'
            ]
        ];

        $imports['Households with people under 18'] = [
            'filename' => 'Demographics_Householdwithunder18.csv',
            'sourceId' => 104,
            'categoryId' => 438
        ];

        $imports['Population'] = [
            'filename' => 'Demographics_Population.csv',
            'sourceId' => 105,
            'categoryId' => 1
        ];

        $imports['Population and housing units density'] = [
            'filename' => 'Demographics_PopulationandHousingUnitsDensity.csv',
            'sourceId' => 106,
            'categoryId' => 350
        ];

        $imports['Population by sex'] = [
            'filename' => 'Demographics_PopulationbySex.csv',
            'sourceId' => 107,
            'categoryIds' => [
                'Male Total Population' => 270,
                'Female Total Population' => 271
            ]
        ];

        $imports['Poverty'] = [
            'filename' => 'Demographics_Poverty.csv',
            'sourceId' => 108,
            'categoryIds' => [
                'Poverty Percent, All Ages' => 5686,
                'Poverty Percent, Age 0-17' => 5688
            ]
        ];

        $imports['Public assistance, TANF'] = [
            'filename' => 'Demographics_PublicAssistance.csv',
            'headerRowCount' => 6,
            'sourceId' => 109,
            'categoryIds' => [
                'TANF' => 5785,
                'SNAP' => null,
                'WIC' => null
            ]
        ];

        $imports['Public assistance, SNAP'] = [
            'filename' => 'Demographics_PublicAssistance.csv',
            'headerRowCount' => 6,
            'sourceId' => 110,
            'categoryIds' => [
                'TANF' => null,
                'SNAP' => 5787,
                'WIC' => null
            ]
        ];

        $imports['Public assistance, WIC'] = [
            'filename' => 'Demographics_PublicAssistance.csv',
            'headerRowCount' => 6,
            'sourceId' => 111,
            'categoryIds' => [
                'TANF' => null,
                'SNAP' => null,
                'WIC' => 5783
            ]
        ];

        $imports['Employment trend'] = [
            'filename' => 'Economics_Employment Trend.csv',
            'sourceId' => 112,
            'categoryId' => 5815
        ];

        $imports['Employment growth'] = [
            'filename' => 'Economics_EmploymentGrowth.csv',
            'sourceId' => 112,
            'categoryId' => 5815
        ];

        $imports['Federal spending'] = [
            'filename' => 'Economics_FederalSpending.csv',
            'sourceId' => 114,
            'categoryId' => 5822
        ];

        $imports['Income inequality'] = [
            'filename' => 'Economics_IncomeInequality.csv',
            'sourceId' => 115,
            'categoryId' => 5668
        ];

        $imports['Percentage Share of Total Establishments'] = [
            'filename' => 'Economics_PercentageShareofTotalEstablishments.csv',
            'sourceId' => 116,
            'categoryIds' => [
                'Manufacturing Establishments' => 5812,
                'Logistics Establishments' => 5811,
                'Total Establishments' => 5810
            ]
        ];

        return $imports;
    }
}
