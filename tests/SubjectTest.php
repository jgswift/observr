<?php
namespace observr\Tests {
    use observr;
    
    /**
     * @package observr
     */
    class SubjectTest extends ObservrTestCase {
        function testSubjectSenderOverride() {
            $user = new Mock\User;
            
            $button = new Mock\Button;
            
            $sender = null;
            
            $user->attach('login',function($s, $e = null)use(&$sender) {
                $sender = $s;
            });
            
            $user->setState('login',new observr\Event($button));
            $this->assertInstanceOf('observr\Tests\Mock\Button',$sender);
        }
        
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
            
            $user->attach('login',function($s,$e=null) { /* .. */ });
            
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
            
            $this->assertEquals(true,$e->isCanceled());
        }
        
        function testUnwatchAll() {
            $user = new Mock\User;
            
            $c = 0;
            $user->attach('login',function($s,$e)use(&$c) {
                $c++;
            });
            
            // clears out all observers so c will remain 0
            $user->unsetState('login');
            
            $user->setState('login');
            
            $this->assertEquals(0,$c);
        }
        
        function testEventArrayArg() {
            $user = new Mock\User;
            
            $user->attach('login',function($user, $e) {
                return $e[0];
            });
            
            // uses array as argument
            $token = $user->setState('login',['hello']);
            
            $this->assertEquals(['hello'],$token);
        }
        
        function testBlankState() {
            $user = new Mock\User;
            
            $user->setState('login');
            
            $this->assertEquals(true,$user->isState('login'));
            
            $this->assertEquals(['login'],$user->getState());
        }
        
        function testMultipleNotify() {
            $user = new Mock\User;
            
            $c=0;
            $user->attach('login',function()use(&$c) {
                $c++;
            });
            
            $user->attach('enter',function()use(&$c) {
                $c++;
            });
            
            $user->setState(['login','enter']);
            
            $this->assertEquals(2,$c);
        }
        
        function testMultipleAttach() {
            $user = new Mock\User;
            
            $c=0;
            $user->attach(['login','enter'],function()use(&$c) {
                $c++;
            });
            
            $user->setState(['login','enter']);
            
            $this->assertEquals(2,$c);
        }
        
        function testEventAfter() {
            $user = new Mock\User;
            
            $user->attach('login',function($s,$e) {
                $e->cancel();
            });
            
            $user->attach('logout',function($s,$e) {
                throw new \Exception('omg!');
                // something
            });
            
            $user->attach('editprofile',function($s,$e) {
                // nothing
            });
            
            $e = new observr\Event($user);
            
            $match = [
                'complete',
                'success',
                'failure',
                'complete',
                'canceled'
            ];
            
            $results = [];
            
            $e->attach(observr\Event::COMPLETE,function()use(&$results) {
                $results[] = 'complete';
            });
            
            $e->attach(observr\Event::CANCEL,function()use(&$results) {
                $results[] = 'canceled';
            });
            
            $e->attach(observr\Event::FAILURE,function()use(&$results) {
                $results[] = 'failure';
            });
            
            $e->attach(observr\Event::SUCCESS,function()use(&$results) {
                $results[] = 'success';
            });
            
            $user->setState('editprofile',$e);
            
            $user->setState('logout',$e);
            
            $user->setState('login',$e);
            
            $this->assertEquals($match,$results);
        }
        
        function testEmptyEventAfter() {
            $user = new Mock\User;
            
            $e = new observr\Event($user);
            
            $match = [
                'complete',
                'success',
                'complete',
                'success',
                'complete',
                'success'
            ];
            
            $results = [];
            
            $e->attach(observr\Event::COMPLETE,function()use(&$results) {
                $results[] = 'complete';
            });
            
            $e->attach(observr\Event::CANCEL,function()use(&$results) {
                $results[] = 'canceled';
            });
            
            $e->attach(observr\Event::FAILURE,function()use(&$results) {
                $results[] = 'failure';
            });
            
            $e->attach(observr\Event::SUCCESS,function()use(&$results) {
                $results[] = 'success';
            });
            
            $user->setState('editprofile',$e);
            
            $user->setState('logout',$e);
            
            $user->setState('login',$e);
            
            $this->assertEquals($match,$results);
        }
    }
}