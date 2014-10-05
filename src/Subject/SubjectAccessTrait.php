<?php
namespace observr\Subject {
    use observr\Event as Event;
    
    trait SubjectAccessTrait {
        use SubjectTrait;
        
        /**
         * Helper method to reduce code duplication
         * @param string $event
         * @param array $args
         * @return boolean
         */
        protected function trigger($event, array $args = []) {
            if($this->isWatched()) { // CHECK FOR OBSERVERS
                $e = new Event($this, $args);
                
                $this->setState($event, $e);
                if($e->isCanceled()) {
                    return false; // EVENT CANCELLED TRIGGER
                }
            }
            
            return true; // ASSUME VALID
        }
    }
}
