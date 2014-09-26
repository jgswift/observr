<?php
namespace observr\State {
    use observr\Event\EventAwareInterface as EventAwareInterface;
    use observr\State\Listener\ListenerInterface as ListenerInterface;
    
    abstract class StateListener implements ListenerInterface {
        /**
         * Locally store parent observer
         * @var \observr\State\StateObserver
         */
        private $parent;
        
        /**
         * Locally store observers
         * @var array
         */
        private $observers = [];
        
        /**
         * Stores run count
         * @var integer
         */
        private $increment = 0;
        
        /**
         * Default StateListener constructor
         * @param \observr\State\StateObserver $parent
         */
        public function __construct(StateObserver $parent) {
            $this->parent = $parent;
        }
        
        /**
         * retrieve runcount
         * @return integer
         */
        public function getRunCount() {
            return $this->increment;
        }
        
        /**
         * Add observer callable
         * @param callable $callable
         */
        public function watch(callable $callable) {
            $this->observers[] = $callable;
        }
        
        /**
         * Remove observer callbale
         * @param callable $callable
         */
        public function unwatch(callable $callable = null) {
            if(is_null($callable)) {
                $this->observers = [];
            } else {
                $key = array_search($callable,$this->observers);
                if($key) {
                    unset($this->observers[$key]);
                }
            }
        }
        
        /**
         * Retrieve observer state
         * @return array
         */
        public function getStates() {
            return [$this->parent->getName()];
        }
        
        /**
         * Check if observer is valid or successful
         * @return boolean
         */
        public function isValid() {
            return $this->parent->isValid();
        }
        
        /**
         * Check if listener has observers
         * @return boolean
         */
        public function isWatched() {
            return !empty($this->observers);
        }
        
        /**
         * Retrieve all observers
         * @return array
         */
        public function getWatchers() {
            return $this->observers;
        }
        
        /**
         * @see getWatchers
         * @return array
         */
        public function getObservers() {
            return $this->getWatchers();
        }
        
        /**
         * Retrieve parent observer
         * @return \observr\State\StateObserver
         */
        public function getParent() {
            return $this->parent;
        }
        
        /**
         * Retrieve parent observer subject
         * @return mixed
         */
        public function getSubject() {
            return $this->parent->getSubject();
        }
        
        /**
         * Retrieve parent observer name
         * @return type
         */
        public function getName() {
            return $this->parent->getName();
        }
        
        /**
         * Notifies all observers
         * @param \observr\Event\EventAwareInterface $event
         * @return mixed
         */
        public function notify(EventAwareInterface $event) {
            $results = [];
            if($this->isWatched()) {
                $observers = $this->getObservers();
                unset($this->observers); // prevent recursion
                foreach($observers as $obs) {
                    $results[] = $this->trigger($event,$obs);
                }
                $this->observers = $observers;
            } else {
                $this->trigger($event);
            }
            
            return $results;
        }
        
        /**
         * Triggers individual observer callable
         * @param \observr\Event\EventInterface $event
         * @param \observr\State\callable $callable
         * @return type
         */
        protected function trigger(EventAwareInterface $event, callable $callable = null) {
            $this->increment++;
            return $this->parent->trigger($event, $callable);
        }
    }
}