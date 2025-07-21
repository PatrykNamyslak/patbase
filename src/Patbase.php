<?php
namespace PatrykNamyslak;

use Exception;
use PDOException;
/**
 * * Database class for managing database connections and queries
 */
class Patbase {
    public \PDO $connection;
    public string $db;
    public string $database;
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
    public function __construct(string $database_name, string $username, string $password, string $table = 'users', string $host='localhost') {
        // Set table
        $this->table = $table;
        // Set database
        $this->db = $database_name;
        $this->database = $database_name;
        // Create a new PDO connection with safeguards
        try{
            $this->connection = new \PDO("mysql:host={$host};dbname={$database_name}", $username, $password);
        }catch(PDOException $e){
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
    // Query the database and return results
    public function query(string $query): Query{
        return new Query(query: $query, database: $this, params: NULL);
    }
    /**
     * Alias for $this->query();
     */
    public function prepare(string $query, ?array $params): Query{
        return new Query($query, $this, $params);
    }

    public function connection(): \PDO {
        return $this->connection;
    }
}



class Query extends Patbase{
    protected string $query;
    protected ?array $params;
    public \PDO $connection;


    protected function __construct(string $query, Patbase $database, ?array $params){
        $this->query = $query;
        $this->params = $params;
        $this->connection = $database->connection;
    }

    public function fetch(){
        if ($this->params){
            $stmt = $this->connection->prepare($this->query);
            $stmt->execute($this->params);
        }else{
            $stmt = $this->connection->query($this->query);
        }
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $data;
    }
    /**
     * Executes a non prepared and a prepared statement
     */
    public function fetchAll(){
        if ($this->params){
            $stmt = $this->connection->prepare($this->query);
            $stmt->execute($this->params);
        }else{
            $stmt = $this->connection->query($this->query);
        }
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $data;
    }
    public function execute(){
        if ($this->params){
            $stmt = $this->connection->prepare($this->query);
            $stmt->execute($this->params);
        }else{
            $stmt = $this->connection->query($this->query);
        }
    }
}

?>