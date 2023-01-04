### PHP CRUD Generatrix

> Generating CRUD features in PHP automatically and can be used on API projects to accept requests from a single POST endpoint. Please take a look at samples.php

#### Installation:
- By composer: `composer require iazaran/crudgeneratrix`

#### Features:
- **Get information about the tables and columns**
The `information` method can generate information about the tables and columns. So frontend can see the columns and their types.
- **Generate simple CRUD automatically**
The `create`, `read`, `update` and `delete` methods can accept the different parameters that determine the target table(s) and column(s) to apply CRUD. You can add your custom method to do more process on specific request.
- **Customizable search**
The `search` method can be used for listing records based on some conditions. Conditions can be customize and join each others by different type of conditional operators. Relationships can be used like `read` method. Limitation can be applied to limit records count.
- **Can be used by a single endpoint in API**
This package doesn't serve API features, but you can set an endpoint to accept those parameters and do CRUD features like GraphQL.

#### Run Web App:
- There is a sample in here. You can create a DB and some tables and records. Make sure you set foreign keys too.
- Now update samples.php and run `php samples.php`.
- Some samples:
```php
// Creating instances and specify the result type like JSON or RAW array of data
$generatrixDB = new GeneratrixDB('locations', 'DBUser', 'DBPassword', 'localhost');
$generatrixCRUD = new GeneratrixCRUD($generatrixDB, 'JSON');

// Getting information about a table and some columns
$generatrixCRUD::information(['tt_countries' => ['countryCode', 'countryName']]);
// Reading a specific row from a table and related table(s) and columns based on different relationship directions
$generatrixCRUD::read(30, ['tt_cities' => ['cityName']], ['tt_hotels' => ['name']], 'LEFT');
$generatrixCRUD::read(30, ['tt_hotels' => ['name']], ['tt_cities' => ['cityName']], 'RIGHT');
// Using custom method as callback. You can see the sample of CustomMethods class in samples.php
$generatrixCRUD::read(30, ['tt_cities' => ['cityName']], ['tt_hotels' => ['name']], 'LEFT', ['CustomMethods', 'groupByFirstColumn']);
// Create multiple rows
$generatrixCRUD::create(['tt_countries' => ['countryCode' => ['US', 'GB'], 'countryName' => ['United State', 'Great Britain']]]);
// Update specific row
$generatrixCRUD::update(237, ['tt_countries' => ['countryCode' => 'ES', 'countryName' => 'Spain']]);
// Delete specific row
$generatrixCRUD::delete(237, ['tt_countries']);
// Search for multiple columns (AND, OR, XOR, ...) of target table (=, LIKE, NOT, ...) and list them ('AND' will be considered for joining conditions of conditions) You can add relationships like read method
$generatrixCRUD::search(['OR' => ['=' => ['cityName' => 'dubai'], 'LIKE' => ['cityName' => 'old']]], ['tt_cities' => ['cityName']], [], '', 10, []);
```

------------
[eazaran@gmail.com](mailto:eazaran@gmail.com "eazaran@gmail.com")
