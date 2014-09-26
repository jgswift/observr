<?php
namespace observr\Subject {
    use observr\Observer as Observer;
    
    trait SubjectTrait {
        /**
         * Attach observer to subject
         * @param string $name
         * @param callable $observer
         */
        public function attach($name, callable $observer) {
            Observer::listener($this,$name)->watch($observer);
        }
        
        /**
         * Detach observer from subject
         * @param string $name
         * @param callable $observer
         */
        public function detach($name, callable $observer = null) {
            Observer::listener($this,$name)->unwatch($observer);
        }
        
        /**
         * Check if subject has observers
         * @return boolean
         */
        public function isWatched() {
            return Observer::hasObservers($this);
        }
        
        /**
         * Check if subject state is valid
         * @param string $name
         * @return boolean
         */
        public function isState($name) {
            return Observer::isState($this,$name);
        }
        
        /**
         * Retrieve subject states
         * @return type
         */
        public function getState() {
            return Observer::getState($this);
        }
        
        /**
         * Update subject states and notifies observers
         * @param string $name
         * @param mixed $e
         * @return mixed
         */
        public function setState($name,$e=null) {
            return Observer::setState($this,$name,$e);
        }
        
        /**
         * Remove subject state container
         * @param mixed $name
         */
        public function unsetState($name = null) {
            Observer::unsetState($this,$name);
        }
    }
}