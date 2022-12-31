<?php

namespace iazaran\crudgeneratrix;

use mysqli;

class GeneratrixDB
{
    public mysqli $dbConnection;
    public array $dbRelationships = [];

    /***
     * Make a connection to MySQL
     *
     * @param string $dbName
     * @param string $dbUser
     * @param string $dbPass
     * @param string $dbHost
     * @param int $dbPort
     */
    public function __construct(
        protected string $dbName,
        protected string $dbUser,
        protected string $dbPass,
        protected string $dbHost = 'localhost',
        protected int    $dbPort = 3306,
    )
    {
        $this->dbConnection = new mysqli($this->dbHost, $this->dbUser, $this->dbPass, $this->dbName, $this->dbPort);
        if ($this->dbConnection->connect_error) {
            die('Connection failed: ' . $this->dbConnection->connect_error);
        }

        $sql = 'SELECT TABLE_NAME, COLUMN_NAME, REFERENCED_COLUMN_NAME, REFERENCED_TABLE_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND REFERENCED_TABLE_SCHEMA = DATABASE()';

        $result = $this->dbConnection->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $this->dbRelationships[$row['TABLE_NAME']] = [
                    'COLUMN_NAME' => $row['COLUMN_NAME'],
                    'REFERENCED_COLUMN_NAME' => $row['REFERENCED_COLUMN_NAME'],
                    'REFERENCED_TABLE_NAME' => $row['REFERENCED_TABLE_NAME'],
                ];
            }
        }
    }
}
