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

------------
[eazaran@gmail.com](mailto:eazaran@gmail.com "eazaran@gmail.com")
