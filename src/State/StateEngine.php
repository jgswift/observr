<?php
namespace observr\State {
    use observr\State\Notifier\NotifierAwareInterface;
    use observr\State\Notifier\NotifierInterface;
    
    abstract class StateEngine implements NotifierAwareInterface {       
        /**
         * Locally store stream notifier for statestream wrappers
         * @var \observr\Stream\StreamNotifier
         */
        protected $notifier;
        
        /**
         * Default engine constructor
         */
        function __construct(NotifierInterface $notifier) {
            $this->notifier = $notifier;
        }
        
        /**
         * Retrieve NotifierInterface
         * @return \observr\State\Notifier\NotifierInterface
         */
        public function getNotifier() {
            return $this->notifier();
        }
        
        /**
         * Retrieve all listeners
         * @return array
         */
        abstract public function getListeners();
        
        /**
         * Retrieve listener for object
         * Creates listener if none exists
         * @param mixed $object
         * @param string|array|null $name
         * @return \observr\State\Listener\AggregateListener
         */
        abstract public function listener($object,$name = null);
        
        /**
         * Check if object is being watched
         * @param mixed $object
         * @return boolean
         */
        abstract public function hasObservers($object);
        
        /**
         * Check if listener state is valid
         * @param mixed $object
         * @param string $name
         * @return boolean
         */
        abstract public function isState($object,$name);
        
        /**
         * REtrieve listener states
         * @param mixed $object
         * @return array
         */
        abstract public function getState($object);
        
        /**
         * Update and notify listener state
         * @param mixed $object
         * @param string $name
         * @param mixed $e
         * @return mixed
         */
        abstract public function setState($object, $name, $e=null);
        
        /**
         * Remove listeners by name
         * @param mixed $object
         * @param string $name
         */
        abstract public function unsetState($object, $name);
    }
}