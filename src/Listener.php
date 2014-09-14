<?php
namespace observr {
    use qtil;
    
    class Listener {
        /**
         * array of subjects keyed by qtil identifier
         * @var array 
         */
        public static $subjects = [];
        
        /**
         * multidimensional array of closures dilineated by qtil identifier and event name
         * @var array 
         */
        public static $observers = [];
        
        /**
         * check if object event is being watched
         * @param mixed $subject
         * @param string $name
         * @return boolean
         */
        static function hasObserver($subject, $name) {
            $id = self::subject($subject);
            
            if($name instanceof qtil\Interfaces\Nameable) {
                $name = $name->getName();
            }

            return array_key_exists($name, self::$observers[$id] ) && 
                   !empty( self::$observers[$id][$name]);
        }

        /**
         * adds a closure to event listing
         * @param mixed|array|\qtil\Interfaces\Nameable $subject
         * @param string $name
         * @param callable $observer
         */
        static function addObserver($subject, $name, callable $observer) {
            $id = self::subject($subject);
            
            if(!is_string($name)) {
                if(is_array($name)) {
                    foreach($name as $n) {
                        self::addObserver($subject, $n, $observer);
                    }
                    return;
                } elseif($name instanceof qtil\Interfaces\Nameable) {
                    $name = $name->getName();
                }
            }

            self::$observers[$id][$name][] = $observer;
        }
        
        /**
         * Alias for addObservr
         * @see addObserver
         */
        static function watch($subject, $name, callable $observer) {
            self::addObserver($subject, $name, $observer);
        }

        /**
         * removes a closure from event listing
         * closure itself may be provided to remove only that specific callback
         * @param mixed $subject
         * @param string $name
         * @param callable $observer
         */
        static function removeObserver($subject, $name, callable $observer = null) {
            $id = self::subject($subject);
            
            if($name instanceof qtil\Interfaces\Nameable) {
                $name = $name->getName();
            }

            if(isset(self::$observers[$id][$name])) {
                if(isset($observer)) {
                    if(($key = array_search($observer, self::$observers[$id][$name]) !== false)) {
                        unset(self::$observers[$id][$name][$key]);
                    }
                } else {
                    unset(self::$observers[$id][$name]);
                }
            }
        }

        /**
         * internal function to setup subjects
         * @param mixed $object
         * @param boolean $new
         * @return string
         */
        public static function subject($object, &$new = false) {
            $id = qtil\Identifier::identify($object);

            if(array_key_exists($id,self::$subjects)) {
                self::$subjects[$id] = $object;
                return $id;
            }

            self::$observers[$id] = [];
            self::$subjects[$id] = $object;

            $new = true;

            return $id;
        }

        /**
         * like hasObserver but accepts array of states to check for watchers
         * @param mixed $object
         * @param mixed $state
         * @return boolean
         */
        public static function hasObservers($object, $name) {
            $id = self::subject($object);

            if($name instanceof qtil\Interfaces\Nameable) {
                $name = $name->getName();
            }
            
            if(!is_array($name)) {
                $name = [$name];
            }

            if(empty(self::$observers[$id])) {
                return false;
            }

            $ret = false;
            foreach($name as $s) {
                if(array_key_exists($s, self::$observers[$id])) {
                    $ret = true;
                }
            }

            return $ret;
        }

        /**
         * internal function to notify observers an event was triggered
         * @param mixed $object
         * @param string $state
         * @param mixed $e
         * @return array
         */
        protected static function notify($object, $name, $e=null) {
            $id = self::subject($object);

            if(empty(self::$observers[$id])) {
                return;
            }
            
            if($name instanceof qtil\Interfaces\Nameable) {
                $name = $name->getName();
            }

            if(!array_key_exists($name,self::$observers[$id])) {
                return;
            }

            $result = [];
            
            if (!empty(self::$observers[$id][$name]))  {
                $observers = self::$observers[$id][$name]; 
                self::$observers[$id][$name] = null; // PREVENTS RECURSION
                if($e instanceof Event) {
                    $e->name = $name;
                }
                $result = self::trigger($object,$observers,$e);
                self::$observers[$id][$name] = $observers;
            } 
            
            return $result;
        }
        
        /**
         * Helper method that runs observer callbacks
         * @param mixed $object
         * @param array $observers
         * @param mixed $e
         * @return array
         */
        protected static function trigger($object, array $observers, $e=null) {
            $result = [];
            if($e instanceof Event) {
                $args = [$e->sender,$e];
            } elseif(qtil\ArrayUtil::isIterable($e)) {
                $args = (array)$e;
            } elseif(!is_null($e)) {
                $args = [$e];
            } else {
                $args = [$object];
            }

            foreach($observers as $observer) {
                $result[] = call_user_func_array($observer, $args);
            }

            if($e instanceof Event) {
                $e->trigger($object);
            }
            
            return $result;
        }

        /**
         * retrieve all subject observers
         * @param mixed $object
         * @return array
         */
        public static function getObservers($object)  {
            $id = self::subject($object);
            return self::$observers[$id];
        }
        
        /**
         * performs event notification
         * @param mixed $object
         * @param string $newstate
         * @param mixed $eventArgs
         * @return mixed
         */
        static function state($object,$newstate=null,$eventArgs=null) {
            $id = qtil\Identifier::identify($object);
            
            if(!is_string($newstate)) {
                if(is_array($newstate)) {
                    $results = [];
                    foreach($newstate as $ns) {
                        $results[] = self::state($object,$ns,$eventArgs);
                    }
                    return $results;
                } elseif($newstate instanceof qtil\Interfaces\Nameable) {
                    $newstate = $newstate->getName();
                }
            }

            State::setState($object,$newstate);
            
            Emitter::stream($object,$newstate,$eventArgs);
            
            if(empty(self::$observers[$id])) {
                return;
            }

            if(empty(self::$observers[$id][$newstate])) {
                return;
            }

            return self::notify($object,$newstate,$eventArgs);
        }

        /**
         * detaches state entirely from subject
         * @param mixed $object
         * @param string $state
         * @return null
         */
        static function unwatch($object, $state) {
            $id = qtil\Identifier::identify($object);

            if(empty(self::$observers[$id])) {
                return;
            }
            
            if($state instanceof qtil\Interfaces\Nameable) {
                $state = $state->getName();
            }

            if(empty(self::$observers[$id][$state])) {
                return;
            }

            self::$observers[$id][$state] = null;
        }
    }
}