<?php
namespace rodrigovr\ForeverDB;

class ForeverDB_ObjectTest extends \PHPUnit_Framework_TestCase {
    
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
    
    public function testDelete()
    {     
        $class = $this->fdb->createClass('User');
        
        $john = $class->create('john');
        
        $john->delete();
        
        $this->assertFalse($class->find("john"));
        
    }
    
    public function testSetInt()
    {
        $class = $this->fdb->createClass('User');
        
        $john = $class->create('john');
        
        $john->setInt('age', 31);
        $john->setInt('income', 100000);
        
        $this->assertEquals(31, $john->getInt('age'));
        $this->assertEquals(100000, $john->getInt('income'));
    }
    
    public function testUnsetInt()
    {
        $class = $this->fdb->createClass('User');
        
        $john = $class->create('john');
        
        $john->setInt('age', 31);
        $john->setInt('income', 100000);
        
        $john->unsetInt('income');
        
        $this->assertNotFalse($john->getInt('age'));
        $this->assertFalse($john->getInt('income'));
    }
    
}
