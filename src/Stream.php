<?php
namespace observr {
    use qtil;
    use qio;
    
    class Stream {
        use Subject {
            Subject::attach as _attach;
            Subject::detach as _detach;
            Subject::setState as _setState;
        }
        
        /**
         * Event name
         * @var string
         */
        public $name;
        
        /**
         * List of subjects
         * @var array
         */
        private $pointers = [];
        
        /**
         * Context
         * @var qio\Context 
         */
        private $context;
        
        /**
         * Enabled flag
         * @var boolean
         */
        private $enabled = false;
        
        /**
         * Creates event stream
         * @param string $name
         * @throws \InvalidArgumentException
         */
        function __construct($name) {
            if(is_string($name)) {
                $this->name = $name;
            } else {
                throw new \InvalidArgumentException();
            }
        }
        
        /**
         *  Closes event stream 
         */
        public function close() {
            if($this->enabled) {
                $this->enabled = false;
                Emitter::removeStream($this);
            }
        }

        /**
         * Retrieve all subjects
         * @return array
         */
        public function getPointer() {
            return $this->pointers;
        }

        /**
         * Check if open
         * @return boolean
         */
        public function isOpen() {
            return $this->enabled;
        }

        /**
         * Open stream
         */
        public function open() {
            if(!$this->enabled) {
                $this->enabled = true;
                Emitter::addStream($this);
            }
        }

        /**
         * Watches subject
         * @param mixed $pointer
         */
        public function watch($pointer) {
            $this->pointers[qtil\Identifier::identify($pointer)] = $pointer;
        }
        
        /**
         * Unwatches subject
         * @param mixed $pointer
         */
        public function unwatch($pointer) {
            $id = qtil\Identifier::identify($pointer);
            
            if(array_key_exists($id, $this->pointers)) {
                unset($this->pointers[$id]);
            }
        }
        
        /**
         * Check if watching
         * @param mixed $pointer
         * @return boolean
         */
        public function isWatching($pointer) {
            $id = qtil\Identifier::identify($pointer);
            
            return isset($this->pointers[$id]);
        }
        
        /**
         * Replace attach from Subject
         * @param \observr\callable $observer
         */
        public function attach(callable $observer) {
            $this->_attach($this->name,$observer);
        }
        
        /**
         * Replaces detach from subject
         * @param \observr\callable $observer
         */
        public function detach(callable $observer = null) {
            $this->_detach($this->name,$observer);
        }
    }
}