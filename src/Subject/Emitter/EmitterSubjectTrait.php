<?php
namespace observr\Subject\Emitter {
    use observr\Observer as Observer;
    
    trait EmitterSubjectTrait {
        /**
         * Attach observer to subject
         * @param string $name
         * @param callable $observer
         */
        public function attach(callable $observer) {
            Observer::listener($this,'on')->watch($observer);
        }
        
        /**
         * Detach observer from subject
         * @param string $name
         * @param callable $observer
         */
        public function detach(callable $observer = null) {
            Observer::listener($this,'on')->unwatch($observer);
        }
    }
}