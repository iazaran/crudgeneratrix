<?php

namespace iazaran\crudgeneratrix;

use mysqli_result;

class GeneratrixCRUD
{
    public static GeneratrixDB $generatrixDB;
    public static string $responseType;

    /**
     * Assign inputs to the CRUD generator
     *
     * @param GeneratrixDB $generatrixDB
     * @param string $responseType
     */
    public function __construct(
        GeneratrixDB $generatrixDB,
        string       $responseType = 'JSON',
    )
    {
        self::$generatrixDB = $generatrixDB;
        self::$responseType = $responseType;
    }

    /**
     * Return information of given table(s)
     *
     * @param array $table
     * @return array|string|bool
     */
    public static function information(array $table): array|string|bool
    {
        $tableName = array_key_first($table);
        $sql = "SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$tableName'";

        $columnNames = [];
        if ($table[$tableName][0] != '*') {
            foreach ($table[$tableName] as $column) {
                $columnNames[] = "COLUMN_NAME = '$column'";
            }
        }

        if (count($columnNames) > 0) {
            $sql .= " AND (" . implode(' OR ', $columnNames) . ")";
        }

        $result = self::$generatrixDB->dbConnection->query($sql);

        $response = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $response[] = $row;
            }
        }

        return match (self::$responseType) {
            'JSON' => json_encode($response),
            default => $response,
        };
    }

    /**
     * Handle CREATE request
     *
     * @param array $table
     * @param array $callback
     * @return bool|mysqli_result|string
     */
    public static function create(array $table, array $callback = []): mysqli_result|bool|string
    {
        $tableName = array_key_first($table);
        $sql = "INSERT INTO $tableName";

        $columnNames = [];
        $rowCounter = 0;
        foreach ($table[$tableName] as $key => $value) {
            $columnNames[] = $key;
            $rowCounter = count($value);
        }

        $sql .= " (" . implode(', ', $columnNames) . ") VALUES";

        for ($i = 0; $i < $rowCounter; $i++) {
            $sql .= " (";
            $columnValues = [];
            foreach ($table[$tableName] as $value) {
                $columnValues[] = "'$value[$i]'";
            }
            $sql .= implode(', ', $columnValues) . ")";
            if ($i < $rowCounter - 1) {
                $sql .= ", ";
            }
        }

        $result = self::$generatrixDB->dbConnection->query($sql);

        if (count($callback) == 2 && is_callable($callback)) {
            $result = call_user_func_array($callback, (array)$result);
        }

        return match (self::$responseType) {
            'JSON' => json_encode($result),
            default => $result,
        };
    }

    /**
     * Handle READ request
     *
     * @param int $id
     * @param array $table
     * @param array $relationships
     * @param string $relationshipDirection
     * @param array $callback
     * @return array|false|string
     */
    public static function read(int $id, array $table, array $relationships = [], string $relationshipDirection = 'LEFT', array $callback = []): bool|array|string
    {
        $tableName = array_key_first($table);

        $columnNames = ["$tableName.*"];
        if ($table[$tableName][0] != '*') {
            $columnNames = [];
            foreach ($table[$tableName] as $column) {
                $columnNames[] = "$tableName.$column";
            }
        }

        $columnNames = implode(', ', $columnNames);
        $sql = "SELECT $columnNames";

        $relatedSql = [];
        if (count($relationships) > 0) {
            foreach ($relationships as $key => $value) {
                $relatedSql[$key] = " JOIN $key";

                $columnNames = ["$key.*"];
                if ($value[0] != '*') {
                    $columnNames = [];
                    foreach ($value as $column) {
                        $columnNames[] = "$key.$column";
                    }
                }

                $columnNames = implode(', ', $columnNames);
            }

            $sql .= ", $columnNames";
        }

        $sql .= " FROM $tableName";

        if (count($relatedSql) > 0) {
            foreach ($relatedSql as $key => $value) {
                if ($relationshipDirection == 'LEFT') {
                    $sql .= $value . " ON $tableName." . self::$generatrixDB->dbRelationships[$key]['REFERENCED_COLUMN_NAME'] . " = $key." . self::$generatrixDB->dbRelationships[$key]['COLUMN_NAME'];
                } else {
                    $sql .= $value . " ON $tableName." . self::$generatrixDB->dbRelationships[$tableName]['COLUMN_NAME'] . " = $key." . self::$generatrixDB->dbRelationships[$tableName]['REFERENCED_COLUMN_NAME'];
                }
            }
        }

        $sql .= " WHERE $tableName.id = $id";

        $result = self::$generatrixDB->dbConnection->query($sql);

        $response = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $response[] = $row;
            }
        }

        if (count($callback) == 2 && is_callable($callback)) {
            $response = call_user_func_array($callback, $response);
        }

        return match (self::$responseType) {
            'JSON' => json_encode($response),
            default => $response,
        };
    }

    /**
     * Handle UPDATE request
     *
     * @param int $id
     * @param array $table
     * @param array $callback
     * @return bool|mysqli_result|string
     */
    public static function update(int $id, array $table, array $callback = []): mysqli_result|bool|string
    {
        $tableName = array_key_first($table);
        $sql = "UPDATE $tableName";

        $columns = [];
        foreach ($table[$tableName] as $key => $value) {
            $columns[] = "$key = '$value'";
        }

        $sql .= " SET " . implode(', ', $columns) . " WHERE id = $id";

        $result = self::$generatrixDB->dbConnection->query($sql);

        if (count($callback) == 2 && is_callable($callback)) {
            $result = call_user_func_array($callback, (array)$result);
        }

        return match (self::$responseType) {
            'JSON' => json_encode($result),
            default => $result,
        };
    }

    /**
     * Handle DELETE request
     *
     * @param int $id
     * @param array $table
     * @param array $callback
     * @return bool|mysqli_result|string
     */
    public static function delete(int $id, array $table, array $callback = []): mysqli_result|bool|string
    {
        $sql = "DELETE FROM $table[0] WHERE id = $id";

        $result = self::$generatrixDB->dbConnection->query($sql);

        if (count($callback) == 2 && is_callable($callback)) {
            $result = call_user_func_array($callback, (array)$result);
        }

        return match (self::$responseType) {
            'JSON' => json_encode($result),
            default => $result,
        };
    }

    /**
     * Handle SEARCH request
     *
     * @param array $search
     * @param array $table
     * @param array $relationships
     * @param string $relationshipDirection
     * @param int $offset
     * @param int $limit
     * @param array $callback
     * @return array|false|string
     */
    public static function search(array $search, array $table, array $relationships = [], string $relationshipDirection = 'LEFT', int $offset = 0, int $limit = 1000, array $callback = []): bool|array|string
    {
        $tableName = array_key_first($table);

        $columnNames = ["$tableName.*"];
        if ($table[$tableName][0] != '*') {
            $columnNames = [];
            foreach ($table[$tableName] as $column) {
                $columnNames[] = "$tableName.$column";
            }
        }

        $columnNames = implode(', ', $columnNames);
        $sql = "SELECT $columnNames";

        $relatedSql = [];
        if (count($relationships) > 0) {
            foreach ($relationships as $key => $value) {
                $relatedSql[$key] = " JOIN $key";

                $columnNames = ["$key.*"];
                if ($value[0] != '*') {
                    $columnNames = [];
                    foreach ($value as $column) {
                        $columnNames[] = "$key.$column";
                    }
                }

                $columnNames = implode(', ', $columnNames);
            }

            $sql .= ", $columnNames";
        }

        $sql .= " FROM $tableName";

        if (count($relatedSql) > 0) {
            foreach ($relatedSql as $key => $value) {
                if ($relationshipDirection == 'LEFT') {
                    $sql .= $value . " ON $tableName." . self::$generatrixDB->dbRelationships[$key]['REFERENCED_COLUMN_NAME'] . " = $key." . self::$generatrixDB->dbRelationships[$key]['COLUMN_NAME'];
                } else {
                    $sql .= $value . " ON $tableName." . self::$generatrixDB->dbRelationships[$tableName]['COLUMN_NAME'] . " = $key." . self::$generatrixDB->dbRelationships[$tableName]['REFERENCED_COLUMN_NAME'];
                }
            }
        }

        $sql .= ' WHERE ';
        $searchesArray = [];
        foreach ($search as $key => $value) {
            $searchArray = [];
            $conditionsOperator = $key;
            foreach ($value as $k => $v) {
                if ($k == 'LIKE') {
                    $searchArray[] = "$tableName." . array_key_first($v) . " $k '%" . $v[array_key_first($v)] . "%'";
                } else {
                    $searchArray[] = "$tableName." . array_key_first($v) . " $k '" . $v[array_key_first($v)] . "'";
                }
            }
            $searchesArray[] = '(' . implode(" $conditionsOperator ", $searchArray) . ')';
        }
        $sql .= implode(' AND ', $searchesArray) . " LIMIT $offset, $limit";

        $result = self::$generatrixDB->dbConnection->query($sql);

        $response = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $response[] = $row;
            }
        }

        if (count($callback) == 2 && is_callable($callback)) {
            $response = call_user_func_array($callback, $response);
        }

        return match (self::$responseType) {
            'JSON' => json_encode($response),
            default => $response,
        };
    }

    /**
     * To use as a single method for all type of features
     *
     * @param string $type
     * @param array $table
     * @param int|null $id
     * @param array $relationships
     * @param string $relationshipDirection
     * @param array $search
     * @param int $offset
     * @param int $limit
     * @param array $callback
     * @return array|bool|mysqli_result|string
     */
    public static function api(string $type, array $table, int $id = null, array $relationships = [], string $relationshipDirection = 'LEFT', array $search = [], int $offset = 0, int $limit = 1000, array $callback = []): mysqli_result|bool|array|string
    {
        return match ($type) {
            'information' => self::information($table),
            'create' => self::create($table, $callback),
            'read' => self::read($id, $table, $relationships, $relationshipDirection, $callback),
            'update' => self::update($id, $table, $callback),
            'delete' => self::delete($id, $table, $callback),
            'search' => self::search($search, $table, $relationships, $relationshipDirection, $offset, $limit, $callback),
        };
    }
}
