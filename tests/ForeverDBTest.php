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
    
    public function teardown() 
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
    
    /**
     * @depends testGetClass
     */
    public function testCreateClass()
    {
        $class = $this->fdb->createClass('User');
        
        // PHP 5.4 compatibility
        $dummy = new ForeverDB_Class(null, null, null);
        $this->assertInstanceOf(get_class($dummy), $class);
    }
}
