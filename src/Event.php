<?php
namespace observr {
    use observr\Event\EventInterface as EventInterface;
    use observr\Event\EventAwareInterface as EventAwareInterface;
    use observr\Event\EventTrait as EventTrait;
    use observr\Event\EventAwareTrait as EventAwareTrait;
    use observr\Subject\SubjectTrait as SubjectTrait;
    use observr\Subject\FixtureTrait as FixtureTrait;
    
    class Event implements \ArrayAccess, \IteratorAggregate, EventInterface, EventAwareInterface, SubjectInterface {
        use EventTrait, EventAwareTrait, SubjectTrait, FixtureTrait;
        
        const FAILURE = 'fail';
        
        const COMPLETE = 'complete';
        const DONE = 'complete';
        
        const CANCEL = 'cancel';
        
        const SUCCESS = 'success';
        
        /**
         * Locally stores event data
         * @var array
         */
        private $data;
               
        function __construct($sender, array $args = null) {
            if(is_array($sender)) {
                $args = $sender;
                $sender = null;
            }
            
            if(!empty($sender)) {
                $this->sender = $sender;
            }
            
            if(is_array($args)) {
                $this->data = $args;
            } else {
                $this->data = [$args];
            }
        }

        /**
         * Allows native iteration over event data
         * @return \ArrayIterator
         */
        public function getIterator() {
            return new \ArrayIterator($this->data);
        }

        /**
         * Checks if event data exists at offset
         * @param mixed $offset
         * @return boolean
         */
        public function offsetExists($offset) {
            return isset($this->data[$offset]);
        }

        /**
         * Retrieve event data from offset
         * @param  $offset
         * @return mixed
         */
        public function offsetGet($offset) {
            if(isset($this->data[$offset])) {
                return $this->data[$offset];
            }
        }

        /**
         * Modify event data at offset
         * @param mixed $offset
         * @param mixed $value
         */
        public function offsetSet($offset, $value) {
            $this->data[$offset] = $value;
        }

        /**
         * Remove event data at offset
         * @param mixed $offset
         */
        public function offsetUnset($offset) {
            if(isset($this->data[$offset])) {
                unset($this->data[$offset]);
            }
        }
    }
}