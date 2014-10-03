<?php
namespace observr\Subject\Emitter {
    trait EmitterTrait {
        /**
         * Locally stores event name
         * @var string
         */
        protected $name;
        
        /**
         * @see \observr\Subject\FixtureInterface
         */
        abstract function setState($name, $e = null);
        
        /**
         * @see \observr\Subject\SubjectInterface
         */
        abstract function attach(callable $observer);
        
        /**
         * @see \observr\Subject\SubjectInterface
         */
        abstract function detach(callable $observer = null);
        
        /**
         * Explicit retrieve of name
         * @return string
         */
        public function getName() {
            return $this->name;
        }
        
        /**
         * Update emitter name
         * @param string $name
         */
        public function setName($name) {
            if(is_string($name)) {
                $this->name = $name;
            } else {
                throw new \InvalidArgumentException();
            }
        }
        
        /**
         * Emits event to self and returns event
         * @return \observr\Event
         */
        public function emit($e=null) {
            return $this->setState('on',$e);
        }
        
        /**
         * Alias for bind
         * @see bind
         */
        public function on(callable $callable) {
            return $this->bind($callable);
        }
        
        /**
         * adds callback to main event
         * @param callable $callable
         * @return \observr\Source
         */
        public function bind(callable $callable) {
            return $this->attach('on',$callable);
        }
        
        /**
         * removes callback from main event
         * @param callable $callable
         * @return observr\Source
         */
        public function unbind(callable $callable) {
            return $this->detach('on', $callable);
        }
        
        /**
         * creates source map
         * @param callable $callable
         * @return \observr\Source\Map
         */
        public function map(callable $callable) {
            return new EmitterMap($this, $callable);
        }
        
        /**
         * creates source filter
         * @param callable $callable
         * @return \observr\Source\Filter
         */
        public function filter(callable $callable) {
            return new EmitterFilter($this, $callable);
        }
        
        /**
         * Stringify emitter to get name
         * @return string
         */
        public function __toString() {
            return $this->getName();
        }
        
        /**
         * Alias for execution
         * @return mixed
         */
        public function __invoke() {
            return call_user_func_array([$this,'execute'],func_get_args());
        }
    }
}