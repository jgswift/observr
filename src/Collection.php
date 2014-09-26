<?php
namespace observr {
    use observr\Subject\SubjectAccessTrait as SubjectAccessTrait;
    use observr\Subject\SubjectInterface as SubjectInterface;
    
    class Collection implements \ArrayAccess, SubjectInterface {
        use SubjectAccessTrait;
        
        /**
         * Locally store array data
         * @var array 
         */
        protected $data;
        
        /**
         * Default collection constructor
         * @param array $data
         */
        public function __construct(array $data = []) {
            $this->data = $data;
        }
        
        /**
         * Array accessor that notifies subject of access
         * @param mixed $offset
         * @return mixed
         */
        public function &offsetGet($offset) {
            if(!$this->trigger('get',['offset'=>$offset])) {
                return;
            }
            
            return $this->data[$offset];
        }
        
        /**
         * Array mutator that notifies subject of change
         * @param mixed $offset
         * @param mixed $value
         */
        public function offsetSet($offset, $value) {
            if(!$this->trigger('set',[
                    'offset'=>$offset,
                    'value'=>$value
                ])) {
                return;
            }
            
            return $this->data[$offset] = $value;
        }
        
        /**
         * Array index check that notifies subject of check
         * @param mixed $offset
         */
        public function offsetExists($offset) {
            if(!$this->trigger('exists',[
                    'offset'=>$offset
                ])) {
                return false;
            }
            
            return isset($this->data[$offset]);
        }
        
        /**
         * Array item remove method that notifies subject of removal
         * @param mixed $offset
         */
        public function offsetUnset($offset) {
            if(!$this->trigger('unset',[
                    'offset'=>$offset
                ])) {
                return;
            }

            if(isset($this->data[$offset])) {
                unset($this->data[$offset]);
            }
        }
    }
}
