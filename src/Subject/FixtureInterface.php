<?php
namespace observr\Subject {
    use observr\State\Notifier\NotifierInterface as NotifierInterface;
    
    interface FixtureInterface extends NotifierInterface {
        /**
         * Check if fixture is being observed
         * @return boolean
         */
        public function isWatched();
        
        /**
         * Check if state is valid
         * @return boolean
         */
        public function isState($name);
        
        /**
         * Retrieve fixture state
         * @return string
         */
        public function getState();
        
        /**
         * Remove fixture state
         */
        public function unsetState($name = null);
    }
}
