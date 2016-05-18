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
    
    /** @return \PDO */
    public function getPDO() {
        return $this->pdo;
    }
    
    public function initDatabase() 
    {
       $this->pdo->exec('CREATE TABLE IF NOT EXISTS fdb_class    (id INTEGER PRIMARY KEY ASC, time INT, alive INT, name VARCHAR)');
       $this->pdo->exec('CREATE TABLE IF NOT EXISTS fdb_object   (id INTEGER PRIMARY KEY ASC, time INT, alive INT, class  INT, name VARCHAR)');
       $this->pdo->exec('CREATE TABLE IF NOT EXISTS fdb_attr_int (id INTEGER PRIMARY KEY ASC, time INT, alive INT, object INT, name VARCHAR, value INT)');
       $this->pdo->exec('CREATE TABLE IF NOT EXISTS fdb_attr_str (id INTEGER PRIMARY KEY ASC, time INT, alive INT, object INT, name VARCHAR, value TEXT)');
    }
    
    public function getClass($className)
    {
        $prepare = $this->pdo->prepare("SELECT * FROM fdb_class WHERE name = :class ORDER BY id DESC LIMIT 1");
        $result = $prepare->execute([':class' => $className]);
        
        if (!$result) {
            return false;
        }
        
        $row = $prepare->fetch(\PDO::FETCH_ASSOC);
        
        if ($row === false) {
            return false;
        }
        if (!$row['alive']) {
            return false;
        }
        return new ForeverDB_Class($this, $className, $row['id']);
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
        
        $prepare = $this->pdo->prepare("INSERT INTO fdb_class (time, alive, name) VALUES(:time, 1, :class)");
        $prepare->execute([':class' => $className, ':time'=>time()]);
        $id = $this->pdo->lastInsertId();
        $this->pdo->commit();
        
        return new ForeverDB_Class($this, $className, $id);
        
    }
    
}
