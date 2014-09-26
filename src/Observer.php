<?php
namespace observr {
    class Observer {
        /**
         * Facade engine instance
         * @var \observr\State\StateEngine 
         */
        private static $engine;
        
        /**
         * Retrieve engine facade
         * @return \observr\State\StateEngine 
         */
        public static function getEngine() {
            if(!isset(self::$engine)) {
                self::$engine = new State\StateEngine;
            }
            return self::$engine;
        }
        
        /**
         * Pass calls to Engine
         * @param string $name
         * @param array $arguments
         * @return mixed
         */
        public static function __callStatic($name, $arguments) {
            return call_user_func_array([self::getEngine(),$name],$arguments);
        }
    }
}