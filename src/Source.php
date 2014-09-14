<?php
namespace observr {
    use qtil;
    
    class Source implements qtil\Interfaces\Nameable {
        use Subject, qtil\Executable;
        
        /**
         * Event name
         * @var string
         */
        public $name;

        /**
         * Default event source constructor
         * @param string $name
         */
        function __construct($name) {
            $this->name = $name;
        }
        
        /**
         * Explicit retrieve of name
         * @return string
         */
        public function getName() {
            return $this->name;
        }
        
        /**
         * Emits event to self and returns event
         * @return \observr\Event
         */
        public function emit() {
            $this->setState('on', new Event($this));
            
            return new Event($this, func_get_args());
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
            return new Source\Map($this, $callable);
        }
        
        /**
         * creates source filter
         * @param callable $callable
         * @return \observr\Source\Filter
         */
        public function filter(callable $callable) {
            return new Source\Filter($this, $callable);
        }
        
        /**
         * Alias for emit
         * @see emit
         */
        public function execute($sender) {
            $this->emit();
        }
    }
}