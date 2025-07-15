<?php
namespace PatrykNamyslak;

use Exception;
use PDOException;
/**
 * * Database class for managing database connections and queries
 */
class Patbase {
    public \PDO $connection;
    public string $table;
    /**
     * @param string $host : Host for your database e.g localhost.
     * @param string $database_name : Name of the database.
     * @param string $username : Username for your database.
     * @param string $password : Password for your database.
     */
    
    // Constructor to initialize the database connection
    // and set the default table name
    // Default table is 'users' if not specified
    public function __construct(?string $host='localhost', string $database_name, string $username, string $password, string $table = 'users') {
        // Set table
        $this->table = $table;
        // Create a new PDO connection with safeguards
        try{
            $this->connection = new \PDO("mysql:host={$host};dbname={$database_name}", $username, $password);
        }catch(PDOException $e){
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
    // Query the database and return results
    public function query(string $query): Query{
        return new Query(query: $query, database: $this);
    }
    // Execute a prepared statement
    public function execute(string $sql, array $params = []): bool {
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute($params);
    }
    public function connection(): \PDO {
        return $this->connection;
    }
}



class Query extends Patbase{
    protected string $query;
    public \PDO $connection;
    protected function __construct(string $query, Patbase $database){
        $this->query = $query;
        $this->connection = $database->connection;
    }
    public function fetch(){
        $stmt = $this->connection->query($this->query);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $data;
    }
    /**
     * A non prepared statement query [FOR INTERNAL USE ONLY]
     */
    public function fetchAll(){
        $stmt = $this->connection->query($this->query);
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $data;
    }
}

?>