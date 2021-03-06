<?php
namespace observr\Event {
    use observr\Event as Event;
    use observr\Subject\FixtureInterface as FixtureInterface;
    
    trait EventAwareTrait {
        /**
         * store sender
         * @var mixed
         */
        protected $sender = null;
        /**
         * @see \observr\Subject\FixtureInterface
         */
        abstract function isWatched();
        
        /**
         * @see \observr\Subject\FixtureInterface
         */
        abstract function setState($state, $e = null);
        
        /**
         * Retrieve sender
         * @return mixed
         */
        public function getSender() {
            return $this->sender;
        }
        
        public function setSender($sender) {
            $this->sender = $sender;
        }
        
        /**
         * Completes event
         */
        public function complete(EventInterface $event) {
            if($event instanceof Event) {
                $event->state = Event::COMPLETE;
            }
            
            $this->setFixtureState($event);
        }
        
        /**
         * Fails event
         */
        public function fail(EventInterface $event) {
            if($event instanceof Event) {
                $event->state = Event::FAILURE;
            }
            
            $this->setFixtureState($event);
        }
        
        /**
         * Cancels event
         */
        public function cancel(EventInterface $event = null) {
            if(is_null($event)) {
                throw new EventCancelException();
            }
            
            if($event instanceof Event) {
                $event->state = Event::CANCEL;
            }
            
            $this->setFixtureState($event);
        }
        
        /**
         * Succeeds event
         */
        public function succeed(EventInterface $event) {
            if($event instanceof Event) {
                $event->state = Event::SUCCESS;
            }
            
            $this->setFixtureState($event);
        }
        
        protected function setFixtureState(EventInterface $event) {
            if($this instanceof FixtureInterface && 
               $event instanceof Event &&
               $this->isWatched()) {
                $this->setState($event->state, $event);
            }
        }
    }
}