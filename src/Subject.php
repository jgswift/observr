<?php
namespace observr {
    /**
     * Subject trait
     * @package observr
     */
    trait Subject {
        /**
         * attaches observer to subject
         * @param string $name
         * @param callable $observer
         * @return self
         */
        function attach($name,callable $observer) {
            Listener::addObserver($this,$name,$observer);
            return $this;
        }

        /**
         * detaches observer from subject
         * @param string $name
         * @param callable $observer
         * @return self
         */
        public function detach($name,callable $observer=null) {
            if(Listener::hasObserver($this,$name)) {
                Listener::removeObserver($this,$name,$observer);
            }
            return $this;
        }

        /**
         * check if current state matches given state
         * @param string $state
         * @return boolean
         */
        public function isState($state) {
            return State::isState($this,$state);
        }

        /**
         * check is subject has any observers
         * @return boolean
         */
        public function hasObservers() {
            $numArgs = func_num_args();

            $args = [];
            if($numArgs == 1) {
                $a = func_get_arg(0);
                if(is_string($a)) {
                    $args[] = $a;
                } elseif(is_array($a)) {
                    $args = $a;
                }
            } else {
                $args = func_get_args();
            }

            return Listener::hasObservers($this,$args);
        }

        /**
         * returns subjects current state
         * @return string
         */
        public function getState() {
            return State::getState($this);
        }

        /**
         * changes subject state
         * @param string $state
         * @param mixed|Event $e
         * @return mixed
         */
        public function setState($state,$e=null)  {
            if(func_num_args() > 2) {
                $args = func_get_args();
                array_shift($args);
                $e = $args;
            }
            return Listener::state($this,$state,$e);
        }

        /**
         * clear subject state
         * @param string $state
         * @return self
         */
        public function clearState($state) {
            Listener::unwatch($this,$state);
            return $this;
        }
    }
}