<?php
namespace observr\State\Listener {
    use observr\Event\EventAwareInterface as EventAwareInterface;
    use observr\State\Notifier\NotifierAwareInterface as NotifierAwareInterface;
    use observr\State\Notifier\NotifierInterface as NotifierInterface;
    use observr\State\Notifier\AggregateNotifier as AggregateNotifier;
    use observr\State\Listener\ListenerInterface as ListenerInterface;
    
    class AggregateListener extends \ArrayObject implements ListenerInterface, NotifierAwareInterface {
        
        /**
         * Ensure new value is Listener
         * @param mixed $index
         * @param ListenerInterface $newval
         */
        public function offsetSet($index, $newval) {
            if($newval instanceof ListenerInterface &&
               $newval instanceof NotifierInterface) {
                parent::offsetSet($index, $newval);
            }
        }
        
        /**
         * Check if event listeners are valid
         * @return boolean
         */
        public function isValid() {
            if(count($this) === 0) {
                return false;
            }
            
            foreach($this as $listener) {
                if(!$listener->isValid()) {
                    return false;
                }
            }
            
            return true;
        }
        
        /**
         * passes watch to all listeners
         * @param callable $callable
         */
        public function watch(callable $callable) {
            foreach($this as $listener) {
                $listener->watch($callable);
            }
        }
        
        /**
         * passes unwatch to all listeners
         * @param callable $callable
         */
        public function unwatch(callable $callable = null) {
            foreach($this as $listener) {
                $listener->unwatch($callable);
            }
        }
        
        /**
         * Checks if all listeners are watched;
         * @return boolean
         */
        public function isWatched() {
            if(count($this) === 0) {
                return false;
            }
            
            foreach($this as $listener) {
                if(!$listener->isWatched()) {
                    return false;
                }
            }
            
            return true;
        }
        
        /**
         * notifies all listeners
         * @param \observr\Event\EventInterface $event
         * @return array
         */
        public function notify(EventAwareInterface $event) {
            $results = [];
            foreach($this as $listener) {
                $results = array_merge($results,$listener->notify($event));
            }
            
            return $results;
        }
        
        /**
         * Retrieves all states from listeners
         * @return array
         */
        public function getStates() {
            $states = [];
            
            foreach($this as $listener) {
                $states = array_merge($states,$listener->getStates());
            }
            return $states;
        }
        
        /**
         * Retrieves all names of listeners
         * @return array
         */
        public function getName() {
            $names = [];
            
            foreach($this as $listener) {
                $names[] = $listener->getName();
            }
            
            return $names;
        }
        
        /**
         * retrieve aggregate notifier for all listeners
         * @return \observr\State\Notifier\AggregateNotifier
         */
        public function getNotifier() {
            $notifiers = [];
            foreach($this as $listener) {
                if($listener instanceof NotifierAwareInterface) {
                    $notifiers[] = $listener->getNotifier();
                }
            }
            
            return new AggregateNotifier($notifiers);
        }
        
        /**
         * Retrieve run count
         * @return integer
         */
        public function getRunCount() {
            $count = 0;
            foreach($this as $listener) {
                $count += $listener->getRunCount();
            }
            
            return $count;
        }
    }
}