<?php
namespace App\Location;

use App\CsvImport\CsvImport;
use Cake\Cache\Cache;
use Cake\Network\Exception\InternalErrorException;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\TableRegistry;

/**
 * A shared interface for the various location classes (city, county, state, etc.)
 */
class Location
{

    /**
     * Returns the ID of a location (city, county, etc.)
     * based on the location type and code (FIPS, district ID, etc.)
     *
     * @param string $locCode Location code (FIPS, district ID, etc.)
     * @param string $locTypeId Location type ID (2: county, 3: state, etc.)
     * @return int
     * @throws NotFoundException
     */
    public function getIdFromCode($locCode, $locTypeId)
    {
        $cacheKey = 'getIdFromCode(';
        $cacheKey .= is_array($locCode) ? implode(',', $locCode) : $locCode;
        $cacheKey .= ", $locTypeId)";
        $cached = Cache::read($cacheKey);
        if ($cached) {
            return $cached;
        }

        switch ($locTypeId) {
            case 2: // county
                $tableName = 'Counties';
                $conditions = [
                    'fips' => $locCode
                ];
                break;
            case 3: // state
                $tableName = 'States';
                $conditions = [
                    'fips' => $locCode
                ];
                break;
            case 4: // country, assumed to be USA
                Cache::write($cacheKey, 1);

                return 1;
            case 5: // tax district
                list($dlgfFistrictId, $countyFips) = $locCode;
                $countiesTables = TableRegistry::get('Counties');
                $result = $countiesTables->find('all')
                    ->select(['id'])
                    ->where(['fips' => $countyFips])
                    ->first();
                $countyId = $result ? $result->id : null;
                $tableName = 'TaxDistricts';
                $conditions = [
                    'dlgf_districtId' => $dlgfFistrictId,
                    'countyId' => $countyId
                ];
                break;
            case 6: // school corporation
                $tableName = 'SchoolCorps';
                $conditions = [
                    'corp_no' => $locCode,
                ];
                break;
            default:
                throw new NotFoundException("Location type ID $locTypeId not recognized");
        }

        $table = TableRegistry::get($tableName);
        $result = $table->find('all')
            ->select(['id'])
            ->where($conditions)
            ->first();

        if (empty($result)) {
            $msg = 'Location matching conditions ' . print_r($conditions, true) .
                ' not found in ' . $tableName . ' table';
            throw new NotFoundException($msg);
        }

        $locId = $result->id;

        if ($locId) {
            Cache::write($cacheKey, $locId);

            return $locId;
        }

        throw new NotFoundException("Location with type $locTypeId and code $locCode not found");
    }

    /**
     * Returns the location type ID corresponding to the specified CSV import file row
     *
     * @param CsvImport $import CsvImport object
     * @param array $row Row of data from CSV file
     * @return int
     * @throws InternalErrorException
     */
    public function getLocationTypeId($import, $row)
    {
        if ($import->locationTypeId) {
            return $import->locationTypeId;
        }

        // Assume that any non-country and non-state FIPS code corresponds to a county
        if (isset($import->headers['fips'])) {
            switch ($row['fips']) {
                case 0:
                    return 4; // Country
                case 18000:
                    return 3; // State
                default:
                    return 2; // County
            }
        }

        if (isset($import->headers['taxDistrictId'])) {
            return 5; // Tax district
        }

        if (isset($import->headers['schoolCorpId'])) {
            return 6; // School corporation
        }

        throw new InternalErrorException('Location type ID cannot be determined for row: ' . print_r($row, true));
    }

    /**
     * Returns the location code (FIPS code, tax district ID, etc.) for the specified CSV import file row
     *
     * @param array $row Row of data from CSV file
     * @return string
     * @throws InternalErrorException
     */
    public function getLocationCode($row)
    {
        $locationCodeFields = [
            'fips',
            'taxDistrictId',
            'schoolCorpId'
        ];

        foreach ($locationCodeFields as $field) {
            if (isset($row[$field])) {
                return $row[$field];
            }
        }

        throw new InternalErrorException('Location ID cannot be determined for row: ' . print_r($row, true));
    }
}
