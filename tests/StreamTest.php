<?php
namespace observr\Tests {
    use observr;
    
    /**
     * @package observr
     */
    class StreamTest extends ObservrTestCase {        
        function testStreamLink() {
            $user = new observr\Tests\Mock\User;
            $user2 = new observr\Tests\Mock\User;
            
            $stream = new observr\Stream('login');
            
            $stream->watch($user);
            $stream->watch($user2);
            
            $c = 0;
            $stream->attach(function($sender,$e=null)use(&$c) {
                $c++;
            });
            
            
            $stream->open();
            
            $user->setState('login', new observr\Event($user));
            $user2->setState('login', new observr\Event($user2));
            
            $stream->close();
            
            $this->assertEquals(2,$c);
        }
        
        function testStreamUnwatch() {
            $user = new observr\Tests\Mock\User;
            $user2 = new observr\Tests\Mock\User;
            $stream = new observr\Stream('login');
            
            $stream->watch($user);
            $stream->watch($user2);
            
            $c = 0;
            $stream->attach(function($sender,$e=null)use(&$c) {
                $c++;
            });
            
            $stream->unwatch($user2);
            
            $stream->open();
            
            $user->setState('login', new observr\Event($user));
            $user2->setState('login', new observr\Event($user2));
            
            $stream->close();
            
            $this->assertEquals(1,$c);
        }
    }
}