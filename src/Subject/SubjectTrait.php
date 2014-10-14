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
            Observer::listener($this,$name)->watch($name, $observer);
        }
        
        /**
         * Detach observer from subject
         * @param string $name
         */
        public function detach($name) {
            Observer::listener($this,$name)->unwatch($name);
        }
    }
}