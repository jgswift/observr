<?php
namespace observr\State {
    use observr\Subject\FixtureInterface as FixtureInterface;
    use observr\Stream\StreamSubjectInterface as StreamSubjectInterface;
    use observr\Subject\SubjectTrait as SubjectTrait;
    use observr\Stream\StreamInterface as StreamInterface;
    use observr\Observer as Observer;
    use observr\State\Listener\ListenerInterface as ListenerInterface;
    use observr\State\Notifier\NotifierAwareInterface as NotifierAwareInterface;
    
    class StateStream implements StreamInterface, FixtureInterface, StreamSubjectInterface {
        use SubjectTrait {
            SubjectTrait::attach as _attach;
            SubjectTrait::detach as _detach;
        }
        
        /**
         * List of subjects
         * @var array
         */
        private $pointers = [];
        
        /**
         * Enabled flag
         * @var boolean
         */
        private $enabled = false;
        
        /**
         *
         * @var observr\State\StateListener
         */
        private $listener;
        
        /**
         * Creates event stream
         * @param string|ListenerInterface $listener
         * @throws \InvalidArgumentException
         */
        function __construct($listener) {
            if(is_string($listener)) {
                $listener = Observer::listener($this,$listener);
            }
            
            if($listener instanceof ListenerInterface &&
               $listener instanceof NotifierAwareInterface) {
                $this->listener = $listener;
            } else {
                throw new \InvalidArgumentException;
            }
        }
        
        /**
         *  Closes event stream 
         */
        public function close() {
            if($this->enabled) {
                $this->enabled = false;
                $this->listener->getNotifier()->removeStream($this);
            }
        }

        /**
         * Retrieve all subjects
         * @return array
         */
        public function getSubjects() {
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
                $this->listener->getNotifier()->addStream($this);
            }
        }

        /**
         * Watches subject
         * @param mixed $pointer
         */
        public function watch($pointer) {
            $id = spl_object_hash($pointer);
            
            $this->pointers[$id] = $pointer;
        }
        
        /**
         * Unwatches subject
         * @param mixed $pointer
         */
        public function unwatch($pointer) {
            $id = spl_object_hash($pointer);
            
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
            $id = spl_object_hash($pointer);
            
            return isset($this->pointers[$id]);
        }
        
        /**
         * Replace attach from Subject
         * @param \observr\callable $observer
         */
        public function attach(callable $observer) {
            $this->_attach($this->listener->getName(),$observer);
        }
        
        /**
         * Replaces detach from subject
         * @param \observr\callable $observer
         */
        public function detach(callable $observer = null) {
            $this->_detach($this->listener->getName(),$observer);
        }
    }
}