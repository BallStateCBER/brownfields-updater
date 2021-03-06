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

        $imports['Personal income'] = [
            'filename' => 'Economics_PersonalHouseholdIncome.csv',
            'headerRowCount' => 6,
            'sourceId' => 117,
            'categoryIds' => [
                'Per capita personal income (dollars) 2' => 47,
                'Median household income in the past 12 months (in 2015 Inflation- adjusted dollar)' => null
            ]
        ];

        $imports['Household income'] = [
            'filename' => 'Economics_PersonalHouseholdIncome.csv',
            'headerRowCount' => 6,
            'sourceId' => 118,
            'categoryIds' => [
                'Per capita personal income (dollars) 2' => null,
                'Median household income in the past 12 months (in 2015 Inflation- adjusted dollar)' => 5689
            ]
        ];

        $imports['Unemployment rate'] = [
            'filename' => 'Economics_UnemploymentRate.csv',
            'sourceId' => 119,
            'categoryId' => 569
        ];

        $imports['Birth measures'] = [
            'filename' => 'Health_BirthMeasures.csv',
            'sourceId' => 120,
            'categoryIds' => [
                '% Low Birthweight' => 5844,
                '% Very Low Birthweight' => 5845,
                '% Less than 37 weeks gestation' => 5846,
                '% Prenatal care beginning in the first trimester' => 5847,
                '% Unmarried' => 5848
            ]
        ];

        $imports['Cancer death and incidence rates'] = [
            'filename' => 'Health_CancerDeathandIncidenceRates.csv',
            'sourceId' => 122,
            'categoryIds' => [
                'Incidence Rate All Cancer(Age-Adjusted Incidence Rate (t)- cases per 100,000)' => 6001,
                'Death Rate All Cancer(Age- Adjusted Death Rate (t)- cases per 100,000)' => 6003,
                'Incidence Rate Lungs & Bronchus(Age- Adjusted Incidence Rate (t)-cases per 100,000)' => 6005,
                'Death Rate Lungs & Bronchus(Age- Adjusted Death Rate (t)- cases per 100,000)' => 6007
            ]
        ];

        $imports['Crude birth rate'] = [
            'filename' => 'Health_CrudeBirthRate.csv',
            'sourceId' => 123,
            'categoryId' => 5827
        ];

        $imports['Deaths by sex'] = [
            'filename' => 'Health_DeathBySex.csv',
            'sourceId' => 124,
            'categoryIds' => [
                'Total Death' => 5853,
                'Male Death' => 5854,
                'Female Death' => 5855
            ]
        ];

        $imports['Death rates'] = [
            'filename' => 'Health_DeathRates.csv',
            'sourceId' => 126,
            'categoryId' => 5852
        ];

        $imports['Fertility rates'] = [
            'filename' => 'Health_FertilityRates.csv',
            'sourceId' => 127,
            'categoryIds' => [
                'General Fertility Rates (GFR)' => 5849,
                'Total Fertility Rates (TFR)' => 5850
            ]
        ];

        $imports['Infant mortality'] = [
            'filename' => 'Health_InfantMortality.csv',
            'sourceId' => 128,
            'categoryId' => 5908
        ];

        $imports['Life expectancy'] = [
            'filename' => 'Health_LifeExpectancy.csv',
            'sourceId' => 129,
            'categoryId' => 5995
        ];

        $imports['Lung disease'] = [
            'filename' => 'Health_LungDisease.csv',
            'sourceId' => 130,
            'categoryIds' => [
                'Pediatric Asthma' => 5930,
                'Adult Asthma' => 5931,
                'COPD' => 6028,
                'Lung Cancer' => 6029
            ]
        ];

        $imports['Self-rated poor health'] = [
            'filename' => 'Health_selfratedpoorhealth.csv',
            'sourceId' => 131,
            'categoryId' => 5997
        ];

        $imports['Unhealthy days'] = [
            'filename' => 'Health_Unhealthydays.csv',
            'sourceId' => 132,
            'categoryIds' => [
                'poor physical healthy days' => 5999,
                'poor mental healthy days' => 6000
            ]
        ];

        $imports['Years of potential life lost'] = [
            'filename' => 'Health_YearsofPotienalLifeLost.csv',
            'sourceId' => 133,
            'categoryId' => 5996
        ];

        $imports['Birth rate by age group'] = [
            'filename' => 'Health_BirthRateByAgeGroup.csv',
            'sourceId' => 136,
            'categoryIds' => [
                'Number of births, mother aged 15-19' => 6042,
                'Number of births, mother aged 20-39' => 6035,
                'Number of births, mother aged 40-44' => 6043,
                'Female Population, ages 15-19' => 6040,
                'Female Population, ages 20-39' => 6031,
                'Female Population, ages 40-44' => 6041
            ]
        ];

        $imports['Death rate by cause'] = [
            'filename' => 'Health_DeathRateByCause.csv',
            'sourceId' => 125,
            'categoryIds' => [
                'Malignant neoplasms (cancer)' => 5868,
                'Diabetes mellitus' => 5872,
                'Alzheimer\'s disease' => 5876,
                'Major cardiovascular diseases' => 5880,
                'Influenza and pneumonia' => 5884,
                'Chronic lower respiratory diseases' => 5888,
                'Nephritis, nephrotic syndrome and nephrosis (kidney disease)' => 5896,
                'Motor Vehicle Accidents' => 5900,
                'Chronic Liver Disease and Cirrhosis' => 5892
            ]
        ];

        return $imports;
    }
}
