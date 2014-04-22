<?php
namespace observr {
    /**
     * Event class
     * @package observr
     */
    class Event extends \ArrayObject {
        use Subject;

        /**
         * Canceled variable, use to check if event is canceled
         * @var boolean 
         */
        public $canceled = false;
        
        /**
         * Specified by constructor, this object is what originated the event
         * @var mixed
         */
        public $sender = null;

        const DONE = 'done';
        const FAIL = 'fail';
        const ALWAYS = 'always';

        /**
         * Constructor for Event
         * @param mixed $sender
         * @param array $arguments
         */
        function __construct($sender,$arguments = []) {
            
            $this->sender = $sender;

            $nArgs = func_num_args();
            if( $nArgs > 2 ) {
                $arguments = func_get_args();
                array_shift( $arguments );
            }

            parent::__construct($arguments);
        }

        /**
         * Shortcut method to attach closure to done event
         * @param callable $callable
         */
        function done(callable $callable) {
            $this->attach(self::DONE, $callable);
        }

        /**
         * Shortcut method to attach closure to fail event
         * @param callable $callable
         */
        function fail(callable $callable) {
            $this->attach(self::FAIL, $callable);
        }

        /**
         * Shortcut method to attach closure to always event
         * @param callable $callable
         */
        function always(callable $callable) {
            $this->attach(self::ALWAYS, $callable);
        }

        /**
         * Cancels event
         * @param mixed $e
         * @return boolean
         */
        function cancel($e = null) {
            if(is_callable($e)) {
                return $this->canceled = $e();
            }

            $this->canceled = true;
            return false;
        }
    }
}

