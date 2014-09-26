<?php
namespace observr\State\Notifier {
    class AggregateNotifier extends \ArrayObject implements NotifierInterface {
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