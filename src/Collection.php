<?php
namespace observr {
    use qtil;
    
    class Collection extends qtil\Collection {
        use Subject;
        
        /**
         * Array accessor that notifies subject of access
         * @param mixed $offset
         * @return mixed
         */
        public function offsetGet($offset) {
            if($this->hasObservers('get')) {
                $e = new observr\Event($this,[
                    'offset'=>$offset
                ]);
                
                $this->setState('get', $e);
                if($e->canceled) {
                    return null;
                }
            }
            return parent::offsetGet($offset);
        }
        
        /**
         * Array mutator that notifies subject of change
         * @param mixed $offset
         * @param mixed $value
         */
        public function offsetSet($offset, $value) {
            if($this->hasObservers('set')) {
                $e = new observr\Event($this,[
                    'offset'=>$offset,
                    'value'=>$value
                ]);
                
                $this->setState('set', $e);
                if($e->canceled) {
                    return;
                }
            }
            
            parent::offsetSet($offset, $value);
        }
        
        /**
         * Array index check that notifies subject of check
         * @param mixed $offset
         */
        public function offsetExists($offset) {
            if($this->hasObservers('exists')) {
                $e = new observr\Event($this,[
                    'offset'=>$offset
                ]);
                
                $this->setState('exists', $e);
                if($e->canceled) {
                    return;
                }
            }
            
            parent::offsetExists($offset);
        }
        
        /**
         * Array item remove method that notifies subject of removal
         * @param mixed $offset
         */
        public function offsetUnset($offset) {
            if($this->hasObservers('unset')) {
                $e = new observr\Event($this,[
                    'offset'=>$offset
                ]);
                
                $this->setState('unset', $e);
                if($e->canceled) {
                    return;
                }
            }
            
            parent::offsetUnset($offset);
        }
    }
}
