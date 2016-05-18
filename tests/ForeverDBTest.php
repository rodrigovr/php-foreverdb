<?php
namespace rodrigovr\ForeverDB;

class ForeverDBTest extends \PHPUnit_Framework_TestCase {
    
    /** @var \rodrigovr\ForeverDB\ForeverDB */
    protected $fdb;
    /** @var \PDO */
    protected $pdo;
    
    public function setUp() 
    {
        parent::setUp();
        $this->pdo = new \PDO('sqlite:testdb.sqlite');
        $this->fdb = new ForeverDB($this->pdo);
        $this->fdb->initDatabase();
    }
    
    public function tearDown() 
    {
        unset($this->fdb);
        unset($this->pdo);
        unlink('testdb.sqlite'); // oh, the irony
    }
    
    public function testGetClass()
    {
        $class = $this->fdb->getClass('User');
        
        $this->assertFalse($class);
    }
}
