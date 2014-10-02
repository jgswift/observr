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
    }
}