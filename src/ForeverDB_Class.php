<?php

namespace rodrigovr\ForeverDB;

class ForeverDB_Class {
    /** @var \rodrigovr\ForeverDB\ForeverDB */
    private $fdb;
    private $id;
    private $name;
    
    public function __construct($fdb, $name, $id) 
    {
        $this->fdb = $fdb;
        $this->name = $name;
        $this->id = $id;
    }
    
    public function getDB() {
        return $this->fdb;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getId() {
        return $this->id;
    }
    
    /**
     * Destroying a class will just mark that it is not alive. There is not need to 
     * mark its objects as dead because creating this same class again will generate
     * a new class ID.
     */
    public function destroy()
    {
        $pdo = $this->fdb->getPDO();
        
        $pdo->beginTransaction();
        $prepare = $pdo->prepare("INSERT INTO fdb_class (time, alive, name) VALUES(:time, 0, :class)");
        $prepare->execute([':class' => $this->name, ':time'=>time()]);
        $pdo->commit();
        
        // Makes this instance unusable
        $this->fdb = null;
    }
    
    public function findAll($where = array())
    {
        
    }
    
    public function create($name)
    {
        $pdo = $this->fdb->getPDO();
        
        $pdo->beginTransaction();
        
        $object = $this->load($name);
        if ($object) {
            $pdo->commit();
            return $object;
        }
        
        $prepare = $pdo->prepare("INSERT INTO fdb_object (time, alive, class, name) VALUES(:time, 1, :class, :object)");
        $prepare->execute([
            ':time'=>time(),
            ':class' => $this->name, 
            ':object' => $name,
            ]);

        $id  = $pdo->lastInsertId();
        
        $pdo->commit();
        
        return new ForeverDB_Object($this, $name, $id);
    }
    
    public function load($name)
    {
        $pdo = $this->fdb->getPDO();
        
        $prepare = $pdo->prepare("SELECT * FROM fdb_object WHERE class = :class and name = :name ORDER BY id DESC LIMIT 1");
        $result = $prepare->execute([':class' => $this->name, ':name' => $name]);
        
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
        return new ForeverDB_Object($this, $name, $row['id']);
    }
    
}
