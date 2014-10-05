<?php
namespace observr\Subject\Emitter {
    use observr\Event as Event;
    use observr\Subject\FixtureInterface as FixtureInterface;
    
    class EmitterMap extends EmitterFilter {
        
        /**
         * Mapping operation
         * @param mixed $subject
         * @param observr\Event $e
         */
        public function execute($subject, $e = null) {
            if(is_null($e)) {
                $e = new Event($subject);
            }
            
            $callable = $this->callable;
            
            $this->source->emit($e);
            
            $callable($subject, $e);
            
            if($subject instanceof FixtureInterface) {
                $subject->setState($this->source->getName(), $e);
            }

            foreach($this->filters as $map) {
                $map->execute($subject, $e);
            }
        }
    }
}