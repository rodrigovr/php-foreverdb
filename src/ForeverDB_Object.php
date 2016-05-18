<?php

namespace rodrigovr\ForeverDB;

class ForeverDB_Object {
    
    /** @var \rodrigovr\ForeverDB\ForeverDB_Class */
    private $class;
    private $id;
    private $name;
    
    public function __construct($class, $name, $id) 
    {
        $this->class = $class;
        $this->name = $name;
        $this->id = $id;
    }
    
    public function getClass()
    {
        return $this->class;
    }

    public function getName()
    {
        return $this->name;
    }

    public function delete()
    {
        $pdo = $this->class->getDB()->getPDO();
        
        $pdo->beginTransaction();
        $prepare = $pdo->prepare("INSERT INTO fdb_object (time, alive, class, name) VALUES(:time, 0, :class, :object)");
        $prepare->execute([
            ':time'=>time(),
            ':class' => $this->class->getName(), 
            ':object' => $this->name,
            ]);
        
        $pdo->commit();
        
        // Makes this instance unusable
        $this->class = null;
        $this->id    = null;
        $this->name  = null;
    }
    
    private function setT($type, $field, $value) 
    {
        $pdo = $this->class->getDB()->getPDO();
        $prepare = $pdo->prepare("INSERT INTO fdb_attr_$type (time, alive, object, name, value) VALUES(:time, 1, :object, :name, :value)");
        $prepare->execute([
            ':time'   => time(),
            ':object' => $this->id,
            ':name'   => $field,
            ':value'  => $value
            ]);
    }
    
    private function getT($type, $field)
    {
        $pdo = $this->class->getDB()->getPDO();
        
        $prepare = $pdo->prepare("SELECT * FROM fdb_attr_$type WHERE object = :object and name = :name ORDER BY id DESC LIMIT 1");
        $result = $prepare->execute([':object' => $this->id, ':name' => $field]);
        
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
        return $row['value'];
    }
    
    private function unsetT($type, $field) 
    {
        $pdo = $this->class->getDB()->getPDO();
        $prepare = $pdo->prepare("INSERT INTO fdb_attr_$type (time, alive, object, name) VALUES(:time, 0, :object, :name)");
        $prepare->execute([
            ':time'   => time(),
            ':object' => $this->id,
            ':name'   => $field,
            ]);
    }
    
    public function setInt($field, $value)
    {
        $this->setT('int', $field, $value);
    }
    
    public function getInt($field)
    {
        return $this->getT('int', $field);
    }
    
    public function unsetInt($field)
    {
        $this->unsetT('int', $field);
    }
    
    public function setString($field, $value)
    {
        $this->setT('str', $field, $value);
    }
    
    public function getString($field)
    {
        return $this->getT('str', $field);
    }
    
    public function unsetString($field)
    {
        $this->unsetT('str', $field);
    }
}
