<?php
namespace observr\Tests {
    use observr;
    
    /**
     * @package observr
     */
    class Collection extends ObservrTestCase {        
        function testCollectionGet() {
            $collection = new \observr\Collection([
                'foo' => 'bar'
            ]);
            
            $c = 0;
            
            $collection->attach('get',function($s,$e)use(&$c) {
                $c++;
            });
            
            $this->assertEquals('bar',$collection['foo']);
            $this->assertEquals(1,$c);
        }
        
        function testCollectionSet() {
            $collection = new \observr\Collection([
                'foo' => 'bar'
            ]);
            
            $c = 0;
            
            $collection->attach('set',function($s,$e)use(&$c) {
                $c++;
            });
            
            $collection['foo'] = 'baz';
            $this->assertEquals('baz',$collection['foo']);
            $this->assertEquals(1,$c);
        }
        
        function testCollectionExists() {
            $collection = new \observr\Collection([
                'foo' => 'bar'
            ]);
            
            $c = 0;
            
            $collection->attach('exists',function($s,$e)use(&$c) {
                $c++;
            });
            
            $this->assertEquals(true,isset($collection['foo']));
            $this->assertEquals(1,$c);
        }
        
        function testCollectionUnset() {
            $collection = new \observr\Collection([
                'foo' => 'bar'
            ]);
            
            $c = 0;
            
            $collection->attach('unset',function($s,$e)use(&$c) {
                $c++;
            });
            
            unset($collection['foo']);
            
            $this->assertEquals(false,isset($collection['foo']));
        }
    }
}