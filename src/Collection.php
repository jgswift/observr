<?php
namespace observr {
    use qtil;
    
    class Collection extends qtil\Collection {
        use Subject;
        
        private function trigger($event, array $args = []) {
            if($this->hasObservers($event)) {
                $e = new Event($this,$args);
                
                $this->setState($event, $e);
                if($e->canceled) {
                    return false;
                }
            }
            
            return true;
        }
        
        /**
         * Array accessor that notifies subject of access
         * @param mixed $offset
         * @return mixed
         */
        public function offsetGet($offset) {
            if(!$this->trigger('get',['offset'=>$offset])) {
                return;
            }
            
            return parent::offsetGet($offset);
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
            
            parent::offsetSet($offset, $value);
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
            
            return parent::offsetExists($offset);
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

            parent::offsetUnset($offset);
        }
    }
}
