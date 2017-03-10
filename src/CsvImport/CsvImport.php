<?php
namespace App\CsvImport;

use App\Location\Location;
use Cake\Network\Exception\InternalErrorException;
use Csv\Reader;

class CsvImport
{
    /**
     * A maps of data category names in the CSV file (keys) to Brownfield site data category IDs (values)
     *
     * @var array
     */
    public $categoryIds = [];

    /**
     * The number of rows that come before any data to be imported
     *
     * @var int
     */
    public $headerRowCount = 0;

    /**
     * Labels to use to refer to each column
     *
     * @var array
     */
    public $headers = [];

    public $categoryId;
    public $data = [];
    public $filename;
    public $locationTypeId;
    public $sourceId;
    const IMPORT_FILE_DIR = APP . 'ImportFiles';

    /**
     * CsvImport constructor
     *
     * @param array $params Properties for new object
     */
    public function __construct($params)
    {
        $fields = [
            'categoryId' => null,
            'categoryIds' => [],
            'filename' => null,
            'headerRowCount' => 0,
            'headers' => [
                'fips',
                'locationName',
                'year',
                'dataCategoryName',
                'value'
            ],
            'locationTypeId' => null,
            'sourceId' => null
        ];
        foreach ($fields as $field => $default) {
            $this->$field = isset($params[$field]) ? $params[$field] : $default;
        }
    }

    /**
     * Reads this import's CSV file and populates $this->data
     *
     * @return void
     */
    public function readCsv()
    {
        $filepath = CsvImport::IMPORT_FILE_DIR . DS . $this->filename;
        $reader = new Reader($filepath, [
            'header' => $this->headers
        ]);

        $Location = new Location();
        foreach ($reader as $rowNum => $row) {
            if ($rowNum <= $this->headerRowCount) {
                continue;
            }

            // Trim whitespace from all values
            foreach ($row as $fieldName => &$value) {
                $value = trim($value);
            }

            $categoryId = $this->getCategoryId($row);
            $locationTypeId = $Location->getLocationTypeId($this, $row);
            $locationCode = $Location->getLocationCode($row);
            $locationId = $Location->getIdFromCode($locationCode, $locationTypeId);

            $this->data[] = [
                'loc_id' => $locationId,
                'loc_type_id' => $locationTypeId,
                'category_id' => $categoryId,
                'source_id' => $this->sourceId,
                'survey_date' => $row['year'] . '0000',
                'value' => $row['value']
            ];
        }
    }

    /**
     * Returns the category ID for the specified row
     *
     * @param array $row Row of data from CSV file
     * @return int
     * @throws InternalErrorException
     */
    public function getCategoryId($row)
    {
        if ($this->categoryId) {
            return $this->categoryId;
        }

        if (isset($this->categoryIds[$row['dataCategoryName']])) {
            return $this->categoryIds[$row['dataCategoryName']];
        }

        throw new InternalErrorException('Category ID cannot be determined for row: ' . print_r($row, true));
    }
}
