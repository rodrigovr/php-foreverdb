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
    
    public function testFind() 
    {
        $class = $this->fdb->createClass('User');
        
        $jane = $class->find("Jane");
        
        $this->assertFalse($jane);
    }
    
    public function testCreate() 
    {
        $class = $this->fdb->createClass('User');
        
        $john = $class->create("John");
        
        $this->assertNotEmpty($john);
    }
    
    public function testFindAll()
    {
        $class = $this->fdb->createClass('Place');
        $places = ['Buenos Aires','São Paulo','New York','London','Tokio'];
        
        foreach($places as $place) {
            $class->create($place);
        }
        
        $found = $class->findAll();
        
        $this->assertEquals(count($places), count($found));
        
        foreach($found as $place) {
            $this->assertTrue( in_array($place->getName(), $places) );
        }
    }
    

    public function testFindAll_withRemoval()
    {
        $class = $this->fdb->createClass('Place');
        $places = ['Buenos Aires','São Paulo','New York','London','Tokio'];
        
        foreach($places as $place) {
            $class->create($place);
        }
        
        $idx = rand(0, 4);
        $class->find($places[$idx])->delete();
        unset($places[$idx]);
        
        $found = $class->findAll();
        
        $this->assertEquals(count($places), count($found));
        
        foreach($found as $place) {
            $this->assertTrue( in_array($place->getName(), $places) );
        }
    }
}
