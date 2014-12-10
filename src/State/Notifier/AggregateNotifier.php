<?php
namespace observr\State\Notifier {
    class AggregateNotifier implements \ArrayAccess, \IteratorAggregate, NotifierInterface {
        /**
         * Locally stores listener/notifiers
         * @var array 
         */
        private $notifiers = [];
        
        /**
         * Default constructor for AggregateListener
         * @param array $notifiers
         */
        public function __construct(array $notifiers = []) {
            $this->notifiers = $notifiers;
        }
        
        /**
         * Check if listener is at index
         * @param mixed $offset
         * @return boolean
         */
        public function offsetExists($offset) {
            return isset($this->notifiers[$offset]);
        }

        /**
         * Retrieve listener from index
         * @param mixed $offset
         * @return mixed
         */
        public function offsetGet($offset) {
            if(isset($this->notifiers[$offset])) {
                return $this->notifiers[$offset];
            }
        }

        /**
         * Remove listener from index
         * @param mixed $offset
         */
        public function offsetUnset($offset) {
            if(isset($this->notifiers[$offset])) {
                unset($this->notifiers[$offset]);
            }
        }
        
        /**
         * Allows native iteration over AggregateListener
         * @return \ArrayIterator
         */
        public function getIterator() {
            return new \ArrayIterator($this->notifiers);
        }
        
        /**
         * Ensure new value is Notifier
         * @param mixed $index
         * @param \observr\State\Notifier\NotifierInterface $newval
         */
        public function offsetSet($index, $newval) {
            if($newval instanceof NotifierInterface) {
                parent::offsetSet($index, $newval);
            }
        }
        
        /**
         * setState all notifiers
         * @param string $state
         * @param mixed $e
         */
        public function setState($state, $e = null) {
            foreach($this as $notifier) {
                $notifier->setState($state, $e);
            }
        }
        
        /**
         * Proxies notifiers
         * @param string $name
         * @param array $arguments
         */
        public function __call($name, $arguments) {
            foreach($this as $notifier) {
                if(is_callable([$notifier,$name])) {
                    call_user_func_array([$notifier,$name],$arguments);
                } 
            }
        }
    }
}