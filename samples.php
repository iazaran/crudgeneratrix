<?php

require_once 'src/GeneratrixDB.php';
require_once 'src/GeneratrixCRUD.php';

class CustomMethods
{
    /**
     * Sample class and method to use as callback
     *
     * @param ...$responses
     * @return array
     */
    public static function groupByFirstColumn(...$responses): array
    {
        $tempResponse = [];
        foreach ($responses as $response) {
            $firstColumn = array_key_first($response);
            array_shift($response);
            $tempResponse[$firstColumn][] = $response;
        }

        return $tempResponse;
    }
}

// Creating instances and specify the result type like JSON or RAW array of data
$generatrixDB = new \iazaran\crudgeneratrix\GeneratrixDB('locations', 'DBUser', 'DBPassword995!', 'localhost');
$generatrixCRUD = new \iazaran\crudgeneratrix\GeneratrixCRUD($generatrixDB, 'JSON');

try {
    // Getting information about a table and some columns
    var_dump($generatrixCRUD::information(
        ['countries' => ['countryCode', 'countryName']]
    ));

    // Reading a specific row from a table and related table(s) and columns based on different relationship directions
    var_dump($generatrixCRUD::read(
        ['cities' => ['cityName']],
        30,
        ['hotels' => ['name']],
        'LEFT'
    ));
    var_dump($generatrixCRUD::read(
        ['hotels' => ['name']],
        30,
        ['cities' => ['cityName']],
        'RIGHT'
    ));

    // Using custom method as callback
    // Without callback like: [{"cityName":"Al Ain","name":"Radisson Blu Hotel & Resort, Al Ain"},{"cityName":"Al Ain","name":"Danat Al Ain Resort"},{"cityName":"Al Ain","name":"Mercure Grand Jebel Hafeet Al Ain Hotel"}]
    // With callback like: {"cityName":[{"name":"Radisson Blu Hotel & Resort, Al Ain"},{"name":"Danat Al Ain Resort"},{"name":"Mercure Grand Jebel Hafeet Al Ain Hotel"}]}
    var_dump($generatrixCRUD::read(
        ['cities' => ['cityName']],
        30,
        ['hotels' => ['name']],
        'LEFT',
        ['CustomMethods', 'groupByFirstColumn']
    ));

    // Create multiple rows
    var_dump($generatrixCRUD::create(
        ['countries' => [
            'countryCode' => ['US', 'GB'],
            'countryName' => ['United State', 'Great Britain'],
        ]]
    ));

    // Update specific row
    var_dump($generatrixCRUD::update(
        ['countries' => [
            'countryCode' => 'ES',
            'countryName' => 'Spain',
        ]],
        237
    ));

    // Delete specific row
    var_dump($generatrixCRUD::delete(
        ['countries'],
        237
    ));

    // Search for multiple columns (AND, OR, XOR, ...) of target table (=, LIKE, NOT, ...) and list them ('AND' will be considered for joining conditions of conditions) You can add relationships like read method
    var_dump($generatrixCRUD::search(
        ['OR' => [
            '=' => ['cityName' => 'dubai'],
            'LIKE' => ['cityName' => 'old'],
        ]],
        ['cities' => ['cityName']],
        [],
        '',
        10,
        5
    ));

    // To use as a single method for all type of features. You can use any method name in here as `type`
    var_dump($generatrixCRUD::api(
        'search',
        ['cities' => ['cityName']],
        null,
        [],
        '',
        ['OR' => [
            '=' => ['cityName' => 'dubai'],
            'LIKE' => ['cityName' => 'old'],
        ]],
        10,
        5,
        []
    ));
} catch (Exception $e) {
    var_dump($e->getMessage());
}
