<?php
namespace observr\Subject\Emitter {
    interface EmitterInterface {
        public function getName();
        
        /**
         * Emit state
         */
        public function emit($e = null);
        
        /**
         * Alias for bind
         * @see bind
         */
        public function on(callable $callable);
        
        /**
         * adds callback to main event
         * @param callable $callable
         * @return \observr\Source
         */
        public function bind(callable $callable);
        
        /**
         * removes callback from main event
         * @param callable $callable
         * @return observr\Source
         */
        public function unbind(callable $callable);
        
        /**
         * creates source map
         * @param callable $callable
         * @return \observr\Source\Map
         */
        public function map(callable $callable);
        
        /**
         * creates source filter
         * @param callable $callable
         * @return \observr\Source\Filter
         */
        public function filter(callable $callable);
        
        /**
         * Alias for getName
         * @return string
         */
        public function __toString();
    }
}