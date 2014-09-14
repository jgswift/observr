<?php
namespace observr\Source {
    use qtil;
    use observr;
    
    class Map extends Filter {
        use qtil\Executable;
        
        /**
         * Mapping operation
         * @param mixed $subject
         * @param observr\Event $e
         */
        public function execute($subject, $e = null) {
            if(is_null($e)) {
                $e = new observr\Event($subject);
            }
            
            $callable = $this->callable;
            
            $this->source->emit($e);
            
            $callable($subject, $e);
            
            if(method_exists($subject, 'setState')) {
                $subject->setState($this->source->name, $e);
            }
            
            foreach($this->filters as $map) {
                $map->execute($subject, $e);
            }
        }
    }
}