<?php

require_once 'src/GeneratrixDB.php';
require_once 'src/GeneratrixCRUD.php';

$generatrixDB = new \iazaran\crudgeneratrix\GeneratrixDB('locations', 'DBUser', 'DBPassword', 'localhost');
$generatrixAPI = new \iazaran\crudgeneratrix\GeneratrixCRUD($generatrixDB, 'JSON');

try {
    var_dump($generatrixAPI::information(['tt_countries' => ['countryCode', 'countryName']]));
    var_dump($generatrixAPI::read(30, ['tt_cities' => ['cityName']], ['tt_hotels' => ['name']], 'LEFT'));
    var_dump($generatrixAPI::read(30, ['tt_hotels' => ['name']], ['tt_cities' => ['cityName']], 'RIGHT'));
    var_dump($generatrixAPI::create(['tt_countries' => ['countryCode' => ['US', 'GB'], 'countryName' => ['United State', 'Great Britain']]]));
    var_dump($generatrixAPI::update(237, ['tt_countries' => ['countryCode' => 'ES', 'countryName' => 'Spain']]));
    var_dump($generatrixAPI::delete(237, ['tt_countries']));
} catch (Exception $e) {
    var_dump($e->getMessage());
}
