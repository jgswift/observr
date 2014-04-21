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
        
        /**
         * When multiple observers present and results are needed(rarely) then 
         * emitter will result in an array containing all of the aggregate results
         */
        function testMultipleStateChangeReturnValue() {
            $user = new Mock\User;
            
            $user->attach('login',function() {
                return 1;
            });
            
            $user->attach('login',function() {
                return 2;
            });
            
            $c = $user->setState('login');
            
            $this->assertEquals([1,2],$c);
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
        
        function testUnwatchAll() {
            $user = new Mock\User;
            
            $c = 0;
            $user->attach('login',function($s,$e)use(&$c) {
                $c++;
            });
            
            // clears out all observers so c will remain 0
            $user->clearState('login');
            
            $user->setState('login');
            
            $this->assertEquals(0,$c);
        }
        
        function testEventArrayArg() {
            $user = new Mock\User;
            
            $user->attach('login',function($token) {
                return $token;
            });
            
            // uses array as argument
            $token = $user->setState('login',['hello']);
            
            $this->assertEquals(['hello'],$token);
        }
    }
}