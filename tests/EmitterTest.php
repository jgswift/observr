<?php
namespace observr\Tests {
    use observr;
    
    /**
     * @package observr
     */
    class EmitterTest extends ObservrTestCase {
        function testEmitterPromise() {
            $click = new observr\Emitter('click');
            
            $getPromise = function($result,$error=null) use ($click) {
                if($error) {
                    $click->fail($error);
                } else {
                    $click->complete($result);
                }
                return $click->promise();
            };
            
            $button = new Mock\Button;
            
            $c=0;
            $getPromise('hello')->then(
                function($value)use(&$c) {
                    $c++;
                }
            );
                
            $click($button);
            $this->assertEquals(1,$c);
        }

        function testEmitterBasic() {
            $click = new observr\Emitter('click');
            
            $button = new Mock\Button;
            
            $c = 0;
            
            $button->attach($click, function($s,$e)use(&$c) {
                $c++;
            });
            
            $click->attach(function($s,$e)use(&$c) {
                $c++;
            });
            
            $click($button);
            
            $this->assertEquals(2,$c);
        }
        
        function testCombinedEventSource() {
            $click = new observr\Emitter('click');
            $mouseup = new observr\Emitter('mouseup');
            $mousedown = new observr\Emitter('mousedown');
            
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
            $click = new observr\Emitter('click');
            
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
    }
}