# CBER Brownfield Grant Writers' Tool Data Importer

[![Build Status](https://travis-ci.org/BallStateCBER/brownfields-updater.svg?branch=development)](https://travis-ci.org/BallStateCBER/brownfields-updater)
[![Code Climate](https://codeclimate.com/repos/58c87aab1ad9f8026500004f/badges/f2334785c15ce05ff698/gpa.svg)](https://codeclimate.com/repos/58c87aab1ad9f8026500004f/feed)
[![Test Coverage](https://codeclimate.com/repos/58c87aab1ad9f8026500004f/badges/f2334785c15ce05ff698/coverage.svg)](https://codeclimate.com/repos/58c87aab1ad9f8026500004f/coverage)
[![Issue Count](https://codeclimate.com/repos/58c87aab1ad9f8026500004f/badges/f2334785c15ce05ff698/issue_count.svg)](https://codeclimate.com/repos/58c87aab1ad9f8026500004f/feed)

Reads CSV files and updates the database for the [Brownfield Grant Writers' Tool](http://brownfield.cberdata.org/)
website, produced by [Ball State University](http://bsu.edu)'s
[Center for Business and Economic Research](http://cberdata.org).

## Usage

When installed on the same server as the Brownfield Grant Writers' Tool and `config/app.php` is set up with the correct
database connection settings, this app interfaces with the website's database.

To view the menu and select an import:

    cd C:\path\to\app
    bin\cake import

The selected import proceeds thusly:

1. Data is pulled from a CSV file
2. This data is checked for errors and it's determined whether this is data that
it needs to **insert** into the database, data that needs to **update** existing records,
or data that is already present in the database and can be **ignored**
3. Assuming there's data to import, the script asks for confirmation to proceed and
for permission to overwrite existing records if appropriate.
4. *MAGIC*

Adding new imports
-------------------------
Add a new set of parameters to the return value of `ImportDefinitions::getDefinitions()`
```
$imports['Name of input for menu'] = [
    // Required
    'filename' => 'filename.csv',
    'sourceId' => ...,
    
    // Required (one of these two)  
    'categoryId' => ..., // if a single category ID for the entire file
    'categoryIds' => [ // if multiple
        'Exact category name from CSV file' => {categoryId},
        ...
    ],
    
    // Optional
    'headerRowCount' => ..., // Defaults to 5
    'headers' => [ // Defaults to the following
        'fips',
        'locationName',
        'year',
        'dataCategoryName',
        'value'
    ],
    'locationTypeId' => ... // Used if file has no 'fips' column
];
```

After import
------------

- After an import completes, update the report-specific method in `/Controller/ReportsController.php` in the Brownfield 
Grant Writers' Tool with the appropriate new year.
- If the new data belongs to different data categories than old data, update the report-specific methods in
  - `/Model/CsvReport.php`
  - `/Model/ExcelReport.php`
  - `/Model/SvgChartReport.php`
  - `/Model/TableReport.php`
- If the relevant report description (set in the `chart_descriptions` database table) mentions the previously-used
year, update it to the new year.
- Load `http://brownfield.cberdata.org/data_center/pages/clear_cache` to clear old cached charts/tables.

Data Categories Tree Repair
---------------------------

The `data_categories` database table is arranged in a tree structure via the `parent_id`, `lft`, and `rght` fields. 
When new data categories are manually added to the database and given non-null `parent_id` values in order to group
them with related categories, their `lft` and `rght` values need to then be updated with 
`$dataCategoriesTable->recover()` to avoid breaking CakePHP's 
[TreeBehavior](https://book.cakephp.org/3.0/en/orm/behaviors/tree.html). To accomplish this, just run the following 
command:

    cd C:\path\to\app
    bin\cake repair
