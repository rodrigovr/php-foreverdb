<?php

namespace rodrigovr\ForeverDB;

class ForeverDB_Class {
    
    private $pdo;
    private $id;
    private $name;
    
    public function __construct($pdo, $name, $id) 
    {
        $this->pdo = $pdo;
        $this->name = $name;
        $this->id = $id;
    }
    
    /**
     * Destroying a class will just mark it is not alive. There is not need to 
     * mark its objects as dead because creating this same class again will generate
     * a new class ID.
     */
    public function destroy()
    {
        $this->pdo->beginTransaction();
        $prepare = $this->pdo->prepare("INSERT INTO fdb_class INTO (alive, name) VALUES(, 0, :class)");
        $prepare->execute([':class' => $this->name]);
        $this->pdo->commit();
        
        // Makes this instance unusable
        $this->pdo = null;
    }
    
    public function findAll($where = array(), $latest = true)
    {
        
    }
    
    public function create($object)
    {
        
    }
    
}
