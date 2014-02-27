<?php
namespace observr\Tests {
    use observr;
    
    /**
     * @package observr
     */
    class SubjectTest extends ObservrTestCase {        
        function testSubjectDetach() {
            $user = new Mock\User;
            
            $c = 0;
            $user->attach('login',function()use(&$c) {
                $c++;
            });
            
            $user->detach('login');
            
            $user->setState('login');
            
            $this->assertEquals(0,$c);
        }
        
        function testSubjectStateChange() {
            $user = new Mock\User;
            
            $c = 0;
            $user->attach('login',function()use(&$c) {
                $c++;
            });
            
            $user->setState('login');
            
            $this->assertEquals(1,$c);
        }
        
        function testStateEventStateChange() {
            $user = new Mock\User;
            
            $c = 0;
            $event = new observr\Event($user);
            $event->attach(observr\Event::DONE,function($s,$e)use(&$c) {
                $c++;
            });
            
            $user->attach('login',function($s,$e) { /* .. */ });
            
            $user->setState('login',$event);
            
            $this->assertEquals(1,$c);
        }
        
        function testStateChangeReturnValue() {
            $user = new Mock\User;
            
            $user->attach('login',function() {
                return 1;
            });
            
            $c = $user->setState('login');
            
            $this->assertEquals(1,$c);
        }
        
        function testEventCancel() {
            $user = new Mock\User;
            
            $user->attach('login',function($s,$e) {
                // if(!credentials.valid)
                $e->cancel(); // LOGIN FAILED
            });
            
            $e = new observr\Event($user);
            
            $user->setState('login',$e);
            
            $this->assertEquals(true,$e->canceled);
        }
    }
}