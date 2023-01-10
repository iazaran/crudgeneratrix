### PHP CRUD Generatrix

> Generating CRUD features in PHP and can be used on API projects to accept requests from a single POST endpoint. Please take a look at samples.php

#### Installation:
- By composer: `composer require iazaran/crudgeneratrix`

#### Features:
- **Get information about the tables and columns**
The `information` method can generate information about the tables and columns. So frontend can see the columns and their types.
- **Generate simple CRUD automatically**
The `create`, `read`, `update` and `delete` methods can accept the different parameters that determine the target table(s) and column(s) to apply CRUD. You can add your custom method to do more process on specific request.
- **Customizable search**
The `search` method can be used for listing records based on some conditions. Conditions can be customize and join each others by different type of conditional operators. Relationships can be used like `read` method. Limitation and offset can be applied to limit records count and start index, that will be useful for pagination too.
- **Can be used by a single endpoint in API**
This package doesn't serve API features, but you can set an endpoint to accept those parameters and do CRUD features like GraphQL. There is a method `api` that accepts all parameters and will implement any type of features of this package.

#### Run Web App:
- There is a sample in here. You can create a DB and some tables and records. Make sure you set foreign keys too.
- Now update samples.php and run `php samples.php`.
- Some samples:
```php
// Getting information about a table and some columns
$generatrixCRUD::information(
    ['countries' => ['countryCode', 'countryName']]
);

// Reading a specific row from a table and related table(s) and columns based on different relationship directions
$generatrixCRUD::read(
    ['cities' => ['cityName']],
    30,
    ['hotels' => ['name']],
    'LEFT'
);
$generatrixCRUD::read(
    ['hotels' => ['name']],
    30,
    ['cities' => ['cityName']],
    'RIGHT'
);

// Using custom method as callback
// Without callback like: [{"cityName":"Al Ain","name":"Radisson Blu Hotel & Resort, Al Ain"},{"cityName":"Al Ain","name":"Danat Al Ain Resort"},{"cityName":"Al Ain","name":"Mercure Grand Jebel Hafeet Al Ain Hotel"}]
// With callback like: {"cityName":[{"name":"Radisson Blu Hotel & Resort, Al Ain"},{"name":"Danat Al Ain Resort"},{"name":"Mercure Grand Jebel Hafeet Al Ain Hotel"}]}
$generatrixCRUD::read(
    ['cities' => ['cityName']],
    30,
    ['hotels' => ['name']],
    'LEFT',
    ['CustomMethods', 'groupByFirstColumn']
);

// Create multiple rows
$generatrixCRUD::create(
    ['countries' => [
        'countryCode' => ['US', 'GB'],
        'countryName' => ['United State', 'Great Britain'],
    ]]
);

// Update specific row
$generatrixCRUD::update(
    ['countries' => [
        'countryCode' => 'ES',
        'countryName' => 'Spain',
    ]],
    237
);

// Delete specific row
$generatrixCRUD::delete(
    ['countries'],
    237
);

// Search for multiple columns (AND, OR, XOR, ...) of target table (=, LIKE, NOT, ...) and list them ('AND' will be considered for joining conditions of conditions) You can add relationships like read method
$generatrixCRUD::search(
    ['OR' => [
        '=' => ['cityName' => 'dubai'],
        'LIKE' => ['cityName' => 'old'],
    ]],
    ['cities' => ['cityName']],
    [],
    '',
    10,
    5
);

// To use as a single method for all type of features. You can use any method name in here as `type`
$generatrixCRUD::api(
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
);
```

------------
[eazaran@gmail.com](mailto:eazaran@gmail.com "eazaran@gmail.com")
