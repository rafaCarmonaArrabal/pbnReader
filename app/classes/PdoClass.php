<?php

namespace App\Classes;

use PDOException;

class PdoClass
{
    private $dsn = "mysql:host=localhost;dbname=test_db;charset=utf8";
    private $userName = "root";
    private $password = "";
    /**
     * The singleton instance
     *
     */
    private static $PDOInstance;

    /**
     * @var
     */
    private static $instance;

    /**
     * Creates a PDO instance representing a connection to a database and makes the instance available as a singleton
     * PDOInstance Singleton. With this we'll never have duplicity in the pdo and the class neither.
     */
    private function __construct()
    {
        if (!self::$PDOInstance) {
            try {
                self::$PDOInstance = new \PDO($this->dsn, $this->userName, $this->password);
            } catch (PDOException $e) {
                die("PDO CONNECTION ERROR: " . $e->getMessage() . "<br/>");
            }
        }
        return self::$PDOInstance;
    }

    /**
     * Singleton
     * @return PdoClass
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Initiates a transaction
     *
     * @return bool
     */
    public function beginTransaction()
    {
        return self::$PDOInstance->beginTransaction();
    }

    /**
     * Commits a transaction
     *
     * @return bool
     */
    public function commit()
    {
        return self::$PDOInstance->commit();
    }

    /**
     * Fetch the SQLSTATE associated with the last operation on the database handle
     *
     * @return string
     */
    public function errorCode()
    {
        return self::$PDOInstance->errorCode();
    }

    /**
     * Fetch extended error information associated with the last operation on the database handle
     *
     * @return array
     */
    public function errorInfo()
    {
        return self::$PDOInstance->errorInfo();
    }

    /**
     * Execute an SQL statement and return the number of affected rows
     *
     * @param string $statement
     */
    public function exec($statement)
    {
        return self::$PDOInstance->exec($statement);
    }

    /**
     * Retrieve a database connection attribute
     *
     * @param int $attribute
     * @return mixed
     */
    public function getAttribute($attribute)
    {
        return self::$PDOInstance->getAttribute($attribute);
    }

    /**
     * Return an array of available PDO drivers
     *
     * @return array
     */
    public function getAvailableDrivers()
    {
        return Self::$PDOInstance->getAvailableDrivers();
    }

    /**
     * Returns the ID of the last inserted row or sequence value
     *
     * @param string $name Name of the sequence object from which the ID should be returned.
     * @return string
     */
    public function lastInsertId($name = null)
    {
        return self::$PDOInstance->lastInsertId($name);
    }

    /**
     * Prepares a statement for execution and returns a statement object
     *
     * @param string $statement A valid SQL statement for the target database server
     * @param array $driver_options Array of one or more key=>value pairs to set attribute values for the PDOStatement obj
     * returned
     * @return \PDOStatement
     */
    public function prepare($statement, $driver_options = false)
    {
        if (!$driver_options) $driver_options = array();
        return self::$PDOInstance->prepare($statement, $driver_options);
    }

    /**
     * Executes an SQL statement, returning a result set as a PDOStatement object
     *
     * @param string $statement
     * @return \PDOStatement
     */
    public function query($statement)
    {
        return self::$PDOInstance->query($statement);
    }


    /**
     * Execute query and select one column only
     *
     * @param string $statement
     * @return mixed
     */
    public function queryFetchColAssoc($statement)
    {
        return self::$PDOInstance->query($statement)->fetchColumn();
    }

    /**
     * Quotes a string for use in a query
     *
     * @param string $input
     * @param int $parameter_type
     * @return string
     */
    public function quote($input, $parameter_type = 0)
    {
        return self::$PDOInstance->quote($input, $parameter_type);
    }

    /**
     * Rolls back a transaction
     *
     * @return bool
     */
    public function rollBack()
    {
        return self::$PDOInstance->rollBack();
    }

    /**
     * Set an attribute
     *
     * @param int $attribute
     * @param mixed $value
     * @return bool
     */
    public function setAttribute($attribute, $value)
    {
        return self::$PDOInstance->setAttribute($attribute, $value);
    }
}
