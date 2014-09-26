<?php
namespace observr\Subject\Emitter {
    use observr\Event as Event;
    use observr\Subject\Emitter\EmitterInterface as EmitterInterface;
    use observr\Subject\FixtureInterface as FixtureInterface;
    
    class EmitterFilter {
        
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
        public function __construct(EmitterInterface $source, callable $callable) {
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
                $e = new Event();
            }
            
            $callable = $this->callable;
            
            if($callable($subject, $e)) {
                $this->source->emit($e);

                if($subject instanceof FixtureInterface) {
                    $subject->setState($this->source->getName(), $e);
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
        public function merge(EmitterFilter $filter) {
            $this->filters[] = $filter;
            
            return $this;
        }
        
        /**
         * Creates subordinate map
         * @param callable $callable
         * @return \observr\Source\Map
         */
        public function map(callable $callable) {
            return new EmitterMap($this->source, $callable);
        }
        
        /**
         * Create subordinate filter
         * @param callable $callable
         * @return \observr\Source\Filter
         */
        public function filter(callable $callable) {
            return new EmitterFilter($this->source, $callable);
        }
        
        /**
         * Callable filter
         * @return mixed
         */
        public function __invoke() {
            return call_user_func_array([$this,'execute'],func_get_args());
        }
    }
}