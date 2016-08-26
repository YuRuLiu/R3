<?php

class Database
{
    const DATABASE_HOST = 'localhost';
    const DATABASE_NAME = 'transfer';
    const DATABASE_USERNAME = 'root';
    const DATABASE_PASSWORD = '';

    private $connection = null;

    public function __construct()
    {
        $dsn = sprintf('mysql:dbname=%s;host=%s', static::DATABASE_NAME, static::DATABASE_HOST);

        try{
            $this->connection = new PDO($dsn, static::DATABASE_USERNAME, static::DATABASE_PASSWORD);
            $this->connection->query('SET NAMES UTF8');
        } catch (PDOException $e) {
            $serverError = array(
                "result" => "false",
                "message" => "Server Error"
            );

            echo json_encode($serverError);
        }
    }

    /**
     * Execute select query
     *
     * @param   string  SQL select query
     * @return  array
     */
    public function select($sql)
    {
        $statement = $this->connection->query($sql, PDO::FETCH_ASSOC);

        return $statement->fetchAll();
    }

    /**
     * Execute update query
     *
     * @param   string  SQL update query
     * @return  int     number of affected rows
     */
    public function update($sql)
    {
        $rowEffect = $this->exec($sql);
        if ($rowEffect > 0) {

            return true;
        } else {

            return false;
        }
    }

    /**
     * Execute insert query
     *
     * @param   string  SQL insert query
     * @return  bool
     */
    public function insert($sql)
    {
        $rowEffect = $this->exec($sql);
        if ($rowEffect > 0) {

            return true;
        } else {

            return false;
        }
    }

    /**
     * Execute delete query
     *
     * @param   string  SQL delete query
     * @return  int     number of affected rows
     */
    public function delete($sql)
    {
        return $this->exec($sql);
    }

    /**
     * Last insert id
     *
     * @return  int
     */
    public function lastInsertId()
    {
        return (int)$this->connection->lastInsertId();
    }

    /**
     * Execute any SQL query
     *
     * @param   string  SQL query
     * @return  int     number of affected rows
     */
    public function exec($sql)
    {
        return $this->connection->exec($sql);
    }

    public function transaction()
    {
        return $this->connection->beginTransaction();
    }

    public function commit()
    {
        return $this->connection->commit();
    }

    public function rollBack()
    {
        return $this->connection->rollBack();
    }

    public function prepare($sql)
    {
        return $this->connection->prepare($sql);
    }
}
