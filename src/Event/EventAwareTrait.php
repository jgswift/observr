<?php
namespace observr\Event {
    use observr\Event as Event;
    use observr\Subject\FixtureInterface as FixtureInterface;
    
    trait EventAwareTrait {
        /**
         * Completes event
         */
        public function complete(EventInterface $event) {
            $event->state = Event::COMPLETE;
            $this->setFixtureState($event);
        }
        
        /**
         * Fails event
         */
        public function fail(EventInterface $event) {
            $event->state = Event::FAILURE;
            $this->setFixtureState($event);
        }
        
        /**
         * Cancels event
         */
        public function cancel(EventInterface $event = null) {
            if(is_null($event)) {
                throw new EventCancelException();
            }
            $event->state = Event::CANCEL;
            $this->setFixtureState($event);
        }
        
        /**
         * Succeeds event
         */
        public function succeed(EventInterface $event) {
            $event->state = Event::SUCCESS;
            $this->setFixtureState($event);
        }
        
        protected function setFixtureState(EventInterface $event) {
            if($this instanceof FixtureInterface && 
               $this->isWatched()) {
                $this->setState($event->state, $event);
            }
        }
    }
}