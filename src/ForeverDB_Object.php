<?php

namespace rodrigovr\ForeverDB;

class ForeverDB_Object {
    
    private $parent;
    private $class_id;
    private $object_id;
    
    public function __construct($parent) 
    {
        $this->parent = $parent;
    }
       
    public function delete()
    {
        
    }
    
    public function setInt($field_name, $field_value)
    {
        
    }
    
    public function getInt($field_name)
    {
        
    }
}
