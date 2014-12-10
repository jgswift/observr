<?php
namespace observr\State\Listener {
    use observr\Event\EventAwareInterface as EventAwareInterface;
    use observr\State\Notifier\NotifierAwareInterface as NotifierAwareInterface;
    use observr\State\Notifier\NotifierInterface as NotifierInterface;
    use observr\State\Notifier\AggregateNotifier as AggregateNotifier;
    use observr\State\Listener\ListenerInterface as ListenerInterface;
    
    class AggregateListener implements \ArrayAccess, \IteratorAggregate, ListenerInterface, NotifierAwareInterface {
        /**
         * Locally stores listener/notifiers
         * @var array 
         */
        private $listeners = [];
        
        /**
         * Default constructor for AggregateListener
         * @param array $listeners
         */
        public function __construct(array $listeners = []) {
            $this->listeners = $listeners;
        }
        
        /**
         * Check if listener is at index
         * @param mixed $offset
         * @return boolean
         */
        public function offsetExists($offset) {
            return isset($this->listeners[$offset]);
        }

        /**
         * Retrieve listener from index
         * @param mixed $offset
         * @return mixed
         */
        public function offsetGet($offset) {
            if(isset($this->listeners[$offset])) {
                return $this->listeners[$offset];
            }
        }

        /**
         * Remove listener from index
         * @param mixed $offset
         */
        public function offsetUnset($offset) {
            if(isset($this->listeners[$offset])) {
                unset($this->listeners[$offset]);
            }
        }
        
        /**
         * Allows native iteration over AggregateListener
         * @return \ArrayIterator
         */
        public function getIterator() {
            return new \ArrayIterator($this->listeners);
        }
        
        /**
         * Ensure new value is Listener
         * @param mixed $offset
         * @param ListenerInterface $newval
         */
        public function offsetSet($offset, $newval) {
            if($newval instanceof ListenerInterface &&
               ($newval instanceof NotifierInterface || 
                $newval instanceof NotifierAwareInterface)) {
                $this->listeners[$offset] = $newval;
            }
        }
        
        /**
         * Check if event listeners are valid
         * @return boolean
         */
        public function isValid() {
            if(count($this->listeners) === 0) {
                return false;
            }
            
            foreach($this->listeners as $listener) {
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
        public function watch($name, callable $callable) {
            foreach($this->listeners as $listener) {
                $listener->watch($name, $callable);
            }
        }
        
        /**
         * passes unwatch to all listeners
         * @param string|callable $observer
         */
        public function unwatch($name = null) {
            foreach($this->listeners as $listener) {
                $listener->unwatch($name);
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
            
            foreach($this->listeners as $listener) {
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
            foreach($this->listeners as $listener) {
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
            
            foreach($this->listeners as $listener) {
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
            
            foreach($this->listeners as $listener) {
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
            foreach($this->listeners as $listener) {
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
            foreach($this->listeners as $listener) {
                $count += $listener->getRunCount();
            }
            
            return $count;
        }
    }
}