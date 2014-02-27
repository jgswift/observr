<?php
namespace observr {
    use kindentfy;
    class Listener {
        /**
         * array of subjects keyed by kindentifier
         * @var array 
         */
        public static $subjects = [];
        
        /**
         * multidimensional array of closures dilineated by kindentifier and event name
         * @var array 
         */
        public static $observers = [];
        
        /**
         * array of states keyed by kindentifier
         * @var array 
         */
        public static $state = [];
        
        /**
         * array of kindentifiers
         * @var array 
         */
        public static $ids = [];

        /**
         * check if object event is being watched
         * @param object $subject
         * @param string $name
         * @return boolean
         */
        static function hasObserver($subject, $name) {
            $id = self::subject($subject);

            return array_key_exists($name, self::$observers[$id] ) && 
                   !empty( self::$observers[$id][$name]);
        }

        /**
         * adds a closure to event listing
         * @param object $subject
         * @param string $name
         * @param Closure $observer
         */
        static function addObserver($subject, $name, $observer) {
            $id = self::subject($subject);

            self::$observers[$id][$name][] = $observer;
        }

        /**
         * removes a closure from event listing
         * closure itself may be provided to remove only that specific callback
         * @param object $subject
         * @param string $name
         * @param Closure $observer
         */
        static function removeObserver($subject, $name, $observer = null) {
            $id = self::subject($subject);

            if(isset(self::$observers[$id][$name])) {
                if(isset($observer )) {
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
         * @param object $object
         * @param boolean $new
         * @return string
         */
        private static function subject($object, &$new = false) {
            $id = kindentfy\Identifier::identify($object);

            if(in_array($id,self::$ids)) {
                self::$subjects[$id] = $object;
                return $id;
            }

            self::$observers[$id] = [];
            self::$state[$id] = [];
            self::$subjects[$id] = $object;
            self::$ids[] = $id;

            $new = true;

            return $id;
        }

        /**
         * like hasObserver but accepts array of states to check for watchers
         * @param object $object
         * @param mixed $state
         * @return boolean
         */
        public static function hasObservers($object, $state) {
            $id = self::subject($object);

            if(!is_array($state)) {
                $state = [$state];
            }

            if(empty(self::$observers[$id])) {
                return false;
            }

            $ret = false;
            foreach($state as $s) {
                if(array_key_exists($s, self::$observers[$id])) {
                    $ret = true;
                }
            }

            return $ret;
        }

        /**
         * internal function to notify observers an event was triggered
         * @param object $object
         * @param string $state
         * @param Event $e
         * @return null
         */
        protected static function notify($object, $state, $e = null) {
            $id = self::subject($object);
            

            if(empty(self::$observers[$id])) {
                return;
            }

            if(!array_key_exists($state,self::$observers[$id])) {
                return;
            }

            $result = [];
            
            self::$state[$id] = $state;
            if (!empty(self::$observers[$id][$state]))  {
                $observers = self::$observers[$id][$state]; 

                self::$observers[$id][$state] = null; // PREVENTS RECURSION

                if($e instanceof Event) {
                    $args = [$e->sender];
                } else {
                    $args = [$object];   
                }
                
                if(!is_null($e)) {
                    $args[] = $e;
                }

                foreach($observers as $observer) {
                    if($observer instanceof \Closure) {
                        @$observer->bindTo( $object );
                    }

                    $result[] = call_user_func_array( $observer, $args);
                }

                if($e instanceof Event) {
                    if( self::hasObservers($e,[Event::FAIL,Event::DONE,Event::ALWAYS])) {
                        $xe = new Event( $object );
                        if( $e->canceled ) {
                            $e->setState(Event::FAIL, $xe);
                        } else {
                            $e->setState(Event::DONE, $xe);
                        }

                        $e->setState( Event::ALWAYS, $xe);
                    }
                }

                self::$observers[$id][$state] = $observers;
            }
            
            if(count($result) === 1) {
                return $result[0];
            }
            
            return $result;
        }

        /**
         * retrieve all subject observers
         * @param object $object
         * @return array
         */
        public static function getObservers($object)  {
            $id = self::subject($object);
            return self::$observers[$id];
        }

        /**
         * Checks objects current state
         * @param object $object
         * @param string $state
         * @return boolean
         */
        static function isState($object, $state) {
            $id = self::subject($object);

            if(array_key_exists($id,self::$state)) {
                if(self::$state[$id] === $state) {
                    return true;
                } elseif(empty( self::$state[$id] ) && $state === false) {
                    return true;
                }
            }

            return false;
        }

        /**
         * Returns objects current state
         * @param object $object
         * @return string
         */
        static function getState($object) {
            $id = kindentfy\Identifier::identify($object);

            if( empty( self::$state[$id] )) {
                return;
            }

            return self::$state[$id];
        }

        /**
         * performs event notification
         * @param object $object
         * @param string $newstate
         * @param mixed $eventArgs
         * @return mixed
         */
        static function state($object,$newstate=null,$eventArgs=null) {
            $id = kindentfy\Identifier::identify($object);

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
         * @param object $object
         * @param string $state
         * @return null
         */
        static function unwatch($object, $state) {
            $id = kindentfy\Identifier::identify($object);

            if(empty(self::$observers[$id])) {
                return;
            }

            if(empty(self::$observers[$id][$state])) {
                return;
            }

            self::$observers[$id][$state] = null;
        }
    }
}