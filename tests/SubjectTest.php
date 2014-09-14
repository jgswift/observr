<?php
namespace observr\Tests {
    use observr;
    
    /**
     * @package observr
     */
    class SubjectTest extends ObservrTestCase {
        function testCombinedEventSource() {
            $click = new observr\Source('click');
            $mouseup = new observr\Source('mouseup');
            $mousedown = new observr\Source('mousedown');
            
            $combined = $click->map(function($sender, $e) {
                return $e['x'] = 'Click';
            })->merge($mousedown->map(function($sender,$e) {
                return $e['x'] = 'Mousedown';
            }))->merge($mouseup->map(function($sender,$e) {
                return $e['x'] = 'Mouseup';
            }));
            
            $button = new Mock\Button;
            
            $c = 0;
            $button->attach($click,function($sender,$e)use(&$c) {
                $this->assertEquals('Click',$e['x']);
                $c++;
            });
            
            $button->attach($mouseup,function($sender,$e)use(&$c) {
                $this->assertEquals('Mouseup',$e['x']);
                $c++;
            }); 
            
            $button->attach($mousedown,function($sender,$e)use(&$c) {
                $this->assertEquals('Mousedown',$e['x']);
                $c++;
            }); 
            
            $combined($button);
            
            $this->assertEquals(3,$c);
        }
        
        function testEventFilter() {
            $click = new observr\Source('click');
            
            $doOK = $click
              ->filter(function($button,$e) {
                if($button instanceof Mock\Button) {
                    return true;
                }
                
                return false;
            })->map(function($button,$e) {
                $button->value = 'Ok';
            });
            
            $button = new Mock\Button;
            
            $c = 0;
            $button->attach($click,function($sender,$e)use(&$c) {
                $c++;
            });
            
            $doOK($button);
            
            $this->assertEquals(1,$c);
            $this->assertEquals('Ok',$button->value);
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
        
        function testState() {
            $user = new Mock\User;
            
            $user->setState('login');
            
            $this->assertEquals(true,$user->isState('login'));
            
            $this->assertEquals('login',$user->getState());
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
    }
}