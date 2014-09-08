<?php
namespace observr {
    use qio;
    
    class Context implements qio\Context {
        const Event = 'event';
        
        private $options;
        private $params;
        private $created = false;
        
        public function __construct(array $options = array(), array $params = array()) {
            $this->options = $options;
            $this->params = $params;
        }

        public function create() {
            $this->created = true;
        }

        public function getOptions() {
            return $this->options;
        }

        public function getParameters() {
            return $this->params;
        }

        public function isCreated() {
            return $this->created;
        }
    }
}