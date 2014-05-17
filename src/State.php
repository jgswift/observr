<?php
namespace observr {
    use qtil;
    
    class State {
        
        /**
         * array of states keyed by qtil identifier
         * @var array 
         */
        public static $state = [];

        
        /**
         * Checks objects current state
         * @param mixed $object
         * @param string $state
         * @return boolean
         */
        static function isState($object, $state) {
            $id = Listener::subject($object);

            if(array_key_exists($id,self::$state)) {
                if(self::$state[$id] === $state) {
                    return true;
                } elseif(empty(self::$state[$id]) && $state === false) {
                    return true;
                }
            }

            return false;
        }

        /**
         * Returns objects current state
         * @param mixed $object
         * @return string
         */
        static function getState($object) {
            $id = qtil\Identifier::identify($object);

            if(empty(self::$state[$id])) {
                return;
            }

            return self::$state[$id];
        }

        /**
         * Update state
         * @param mixed $object
         * @param string $state
         */
        static function setState($object,$state) {
            $id = qtil\Identifier::identify($object);
            
            self::$state[$id] = $state;
        }
    }
}