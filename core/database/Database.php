<?php


namespace core\database;


use mysqli;
use mysqli_result;
use mysqli_stmt;
use RuntimeException;

class Database
{
    private mysqli $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public static function create(string $host, string $database, string $user, string $password): Database
    {
        $conn = new mysqli($host, $user, $password, $database);

        if ($conn->connect_error) {
            throw new RuntimeException("Cannot connect to database: " . $conn->connect_error);
        }

        return new Database($conn);
    }
    
    public function close()
    {
        $this->connection->close();
    }
    
    public function getInsertId(): int 
    {
        return $this->connection->insert_id;
    }
    
    public function insert(string $sql, string $types, ...$values): bool
    {
        $statement = $this->queryStatement($sql, $types, ...$values);
        
        $result = $statement->execute();
        $statement->close();
        return $result;
    }

    public function query(string $sql, string $types, ...$values): mysqli_result
    {
        $statement = $this->queryStatement($sql, $types, ...$values);
        
        $statement->execute();
        return $statement->get_result();
    }
    
    private function queryStatement(string $sql, string $types, ...$values): mysqli_stmt
    {
        if ((!$statement = $this->connection->prepare($sql))) {
            throw new RuntimeException("Failed to prepare: " . $this->connection->error);
        }
        
        if($types != "")
        {
            $statement->bind_param($types, ...$values);
        }

        return $statement;
    }
}