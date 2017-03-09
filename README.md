# CBER Brownfield Grant Writers' Tool Data Importer

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
(todo)

After import
------------

- After an import completes, update the relevant method in `/Controller/ReportsController.php` in the Brownfield 
Grant Writers' Tool with the appropriate new year.
- Load `http://brownfield.cberdata.org/data_center/pages/clear_cache` to clear old cached charts/tables.
- If the relevant report description (set in the `chart_descriptions` database table) references data from the old 
year, update it to reflect the new year.
