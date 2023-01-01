### PHP CRUD Generatrix

> Generating CRUD features in PHP automatically and can be used on API projects to accept requests from a single POST endpoint. Please take a look at samples.php

#### Features:
- **Get information about the tables and columns**
The `information` method can generate information about the tables and columns. So frontend can see the columns and their types.
- **Generate simple CRUD automatically**
The `create`, `read`, `update` and `delete` methods can accept the different parameters that determine the target table(s) and column(s) to apply CRUD. You can add your custom method to do more process on specific requests. (WIP: adding action to the existing method to handle specific requests and processes)
- **Can be used by a single endpoint in API**
This package doesn't serve API features, but you can set an endpoint to accept those parameters and do CRUD features like GraphQL.

#### Run Web App:
- There is a sample in here. You can create a DB and some tables and records. Make sure you set foreign keys too.
- Now update samples.php and run `php samples.php`.
- Some samples:
```php
// Creating instances
$generatrixDB = new GeneratrixDB('locations', 'DBUser', 'DBPassword', 'localhost');
$generatrixAPI = new GeneratrixCRUD($generatrixDB, 'JSON');

// Getting information about a table and some columns
$generatrixAPI::information(['tt_countries' => ['countryCode', 'countryName']]);
// Reading a specific row from a table and related table(s) and columns based on different relationship directions
$generatrixAPI::read(30, ['tt_cities' => ['cityName']], ['tt_hotels' => ['name']], 'LEFT');
$generatrixAPI::read(30, ['tt_hotels' => ['name']], ['tt_cities' => ['cityName']], 'RIGHT');
// Create multiple rows
$generatrixAPI::create(['tt_countries' => ['countryCode' => ['US', 'GB'], 'countryName' => ['United State', 'Great Britain']]]);
// Update specific row
$generatrixAPI::update(237, ['tt_countries' => ['countryCode' => 'ES', 'countryName' => 'Spain']]);
// Delete specific row
$generatrixAPI::delete(237, ['tt_countries']);
```

------------
[eazaran@gmail.com](mailto:eazaran@gmail.com "eazaran@gmail.com")
