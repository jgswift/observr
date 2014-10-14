<?php
namespace observr\Stream {
    
    use observr\State\StateEngine as StateEngine;
    use observr\Event as Event;
    use observr\State\Listener\AggregateListener as AggregateListener;
    use observr\State\Listener\ListenerInterface as ListenerInterface;
    use observr\State\StateObserver as StateObserver;
    
    class StreamEngine extends StateEngine {
        /**
         * Locally stores listeners
         * @var array 
         */
        private $listeners = [];
                
        /**
         * Default engine constructor
         */
        function __construct() {
            parent::__construct(new StreamNotifier());
        }
        
        /**
         * Retrieve all listeners
         * @return array
         */
        public function getListeners() {
            return $this->listeners;
        }
        
        /**
         * Retrieve listener for object
         * Creates listener if none exists
         * @param mixed $object
         * @param string|array|null $name
         * @return \observr\State\Listener\AggregateListener
         */
        public function listener($object,$name = null) {
            $uid = spl_object_hash($object);
            if(!isset($this->listeners[$uid])) {
                $this->listeners[$uid] = [];
            }
            
            if(is_null($name)) {
                return new AggregateListener($this->listeners[$uid]);
            }
            
            if(!is_array($name)) {
                $name = [$name];
            }
            
            $listeners = [];
            
            foreach($name as $n) {
                list($unqualifiedName, $namespace) = StateObserver::parseName((string)$n);
                $qualifiedName = $unqualifiedName.'.'.$namespace;
                
                if(!isset($this->listeners[$uid][$unqualifiedName])) {
                    $listener = new StreamListener($this->notifier, new StateObserver($object, $qualifiedName));
                    
                    $this->listeners[$uid][$unqualifiedName] = $listener;
                }
                
                $listeners[] = $this->listeners[$uid][$unqualifiedName];
            }
            
            return new AggregateListener($listeners);
        }
        
        /**
         * Check if object is being watched
         * @param mixed $object
         * @return boolean
         */
        public function hasObservers($object) {
            $listener = $this->listener($object);
            
            if($listener instanceof ListenerInterface) {
                if($listener->isWatched()) {
                    return true;
                }
            }
            
            return false;
        }
        
        /**
         * Check if listener state is valid
         * @param mixed $object
         * @param string $name
         * @return boolean
         */
        public function isState($object,$name) {
            return $this->listener($object,$name)->isValid();
        }
        
        /**
         * REtrieve listener states
         * @param mixed $object
         * @return array
         */
        public function getState($object) {
            $listeners = $this->listener($object);
            
            $states = [];
            foreach($listeners as $listener) {
                if($listener->isValid()) {
                    $states = array_merge($states,$listener->getStates());
                }
            }
            
            return $states;
        }
        
        /**
         * Update and notify listener state
         * @param mixed $object
         * @param string $name
         * @param mixed $e
         * @return mixed
         */
        public function setState($object, $name, $e=null) {
            if(is_null($e)) {
                $e = new Event($object);
            } elseif(is_array($e)) {
                $e = new Event($object, $e);
            }
            
            if(!is_array($name)) {
                $name = [$name];
            }
            
            return $this->listener($object,$name)->notify($e);
        }
        
        /**
         * Remove listeners by name
         * @param mixed $object
         * @param string $name
         */
        public function unsetState($object, $name) {
            $uid = spl_object_hash($object);
            
            if(isset($this->listeners[$uid]) &&
               isset($this->listeners[$uid][$name])) {
                unset($this->listeners[$uid][$name]);
            }
        }
    }
}