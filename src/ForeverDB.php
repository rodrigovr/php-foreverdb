<?php

namespace rodrigovr\ForeverDB;

/**
 * ForeverDB is a ORM component that never overwrites existing data.
 * Each operation (insert, update and delete) will always create new rows.
 * 
 */
class ForeverDB {
   
    /** @var \PDO */
    private $pdo;
    
    /**
     * 
     * @param \PDO $pdo
     */
    public function __construct($pdo) 
    {
        $this->pdo = $pdo;
    } 
    
    public function initDatabase() 
    {
       $this->pdo->exec('CREATE TABLE IF NOT EXISTS fdb_class (id INTEGER PRIMARY KEY ASC,  alive INT, name VARCHAR)');
    }
    
    public function getClass($className)
    {
        $prepare = $this->pdo->prepare("SELECT * FROM fdb_class WHERE alive = 1 and name = :class");
        $result = $prepare->execute([':class' => $className]);
        
        if (!$result) {
            return false;
        }
        
        $row = $prepare->fetch();
        
        if ($row === false) {
            return false;
        }
        
        $class = new ForeverDB_Class($this->pdo, $className, $row[0]['id']);
    }
    
    /**
     * 
     * @param string $className
     * @return \rodrigovr\ForeverDB\ForeverDB_Class
     */
    public function createClass($className)
    {
        $this->pdo->beginTransaction();
        $class = $this->getClass($className);
        if ($class) {
            $this->pdo->commit();
            return $class;
        }
        
        $prepare = $this->pdo->prepare("INSERT INTO fdb_class INTO (alive, name) VALUES(, 1, :class)");
        $prepare->execute([':class' => $className]);
        
        $class = new ForeverDB_Class($this->pdo, $className, $this->pdo->lastInsertId());
        $this->pdo->commit();
        return $class;
    }
    
}
