<?php
namespace observr\Source {
    use qtil;
    use observr;
    
    class Filter {
        use qtil\Executable;
        
        /**
         * Reference to primary source emitter
         * @var observr\Source 
         */
        protected $source;
        
        /**
         * Filtering function, must return boolean
         * @var callable
         */
        protected $callable;
        
        /**
         * List of other filters
         * @var array 
         */
        protected $filters = [];
        
        /**
         * Default filter constructor
         * @param observr\Source $source
         * @param callable $callable
         */
        public function __construct(observr\Source $source, callable $callable) {
            $this->source = $source;
            $this->callable = $callable;
        }
        
        /**
         * Performs filtering operation
         * @param mixed $subject
         * @param observr\Event $e
         */
        public function execute($subject, $e = null) {
            if(is_null($e)) {
                $e = new observr\Event($subject);
            }
            
            $callable = $this->callable;
            
            if($callable($subject, $e)) {
                $this->source->emit($e);

                if(method_exists($subject, 'setState')) {
                    $subject->setState($this->source->name, $e);
                }

                foreach($this->filters as $map) {
                    $map->execute($subject, $e);
                }
            }
        }
        
        /**
         * Adds subordinate filters
         * @param \observr\Source\Filter $filter
         * @return \observr\Source\Filter
         */
        public function merge(Filter $filter) {
            $this->filters[] = $filter;
            
            return $this;
        }
        
        /**
         * Creates subordinate map
         * @param callable $callable
         * @return \observr\Source\Map
         */
        public function map(callable $callable) {
            return new Map($this->source, $callable);
        }
        
        /**
         * Create subordinate filter
         * @param callable $callable
         * @return \observr\Source\Filter
         */
        public function filter(callable $callable) {
            return new Filter($this->source, $callable);
        }
    }
}