<?php
namespace rodrigovr\ForeverDB;

class ForeverDB_ClassTest extends \PHPUnit_Framework_TestCase {
    
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
    
    public function testDestroy()
    {     
        $class = $this->fdb->createClass('User');
        
        $class->destroy();
        
        $this->assertFalse($this->fdb->getClass('User'));
    }
    
    public function testLoad() 
    {
        $class = $this->fdb->createClass('User');
        
        $jane = $class->load("Jane");
        
        $this->assertFalse($jane);
    }
    
    public function testCreate() 
    {
        $class = $this->fdb->createClass('User');
        
        $john = $class->create("John");
        
        $this->assertNotEmpty($john);
    }
}
