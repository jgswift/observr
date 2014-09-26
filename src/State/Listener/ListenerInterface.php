<?php
namespace observr\State\Listener {
    use observr\Event\EventAwareInterface as EventAwareInterface;
    
    interface ListenerInterface {
        /**
         * Retrieve Listener name
         * @return string
         */
        public function getName();
        
        /**
         * Check if Listener is valid
         * @return bool
         */
        public function isValid();
        
        /**
         * Add callable to Listener
         */
        public function watch(callable $callable);
        
        /**
         * Remove callable from listener
         */
        public function unwatch(callable $callable = null);
        
        /**
         * Checks if any callables have been added
         */
        public function isWatched();
        
        /**
         * Trigger all callables
         */
        public function notify(EventAwareInterface $event);
        
        /**
         * Retrieve state(s)
         * @return array|string
         */
        public function getStates();
        
        /**
         * Retrieve run count
         * @return integer
         */
        public function getRunCount();
    }
}