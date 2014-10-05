<?php
namespace observr\Subject {
    use observr\Subject\Emitter\EmitterInterface as EmitterInterface;
    use observr\Subject\Emitter\EmitterTrait as EmitterTrait;
    use observr\Subject\Emitter\EmitterSubjectTrait as EmitterSubjectTrait;
    use observr\Subject\Emitter\EmitterSubjectInterface as EmitterSubjectInterface;
    use React\Promise\PromisorInterface;
    use React\Promise\Promise;
    use observr\Event;
    
    class SubjectEmitter implements 
            EmitterSubjectInterface, 
            EmitterInterface, 
            PromisorInterface
    {
        use FixtureTrait, EmitterSubjectTrait, EmitterTrait;
        
        private $promise;
        private $complete;
        private $failure;
        private $progress;
        
        /**
         * Default emitter constructor
         * @param string $name
         */
        function __construct($name) {
            $this->setName($name);
        }
        
        public function promise() {
            if(is_null($this->promise)) {
                $this->promise = new Promise(function($resolve,$reject,$progress) {
                    $this->complete = $resolve;
                    $this->failure = $reject;
                    $this->progress = $progress;
                });
            }
            
            return $this->promise;
        }

        /**
         * Handles promise fulfillment
         * @param mixed $value
         */
        public function complete($value = null) {
            $this->promise();
            
            call_user_func($this->complete, $value);
        }
        
        /**
         * Handles promise rejection
         * @param string $reason
         */
        public function fail($reason = null) {
            $this->promise();
            
            call_user_func($this->failure, $reason);
        }
        
        /**
         * Hanldes promise progress
         * @param integer $update
         */
        public function progress($update = null) {
            $this->promise();
            
            call_user_func($this->progress, $update);
        }
        
        /**
         * Performs filtering operation
         * @param mixed $subject
         * @param observr\Event $e
         */
        public function execute($subject, $e = null) {
            if(is_null($e)) {
                $e = new Event($subject);
            }
            
            try {
                $subject->setState($this->name, $e);

                $this->emit();
                
                $this->complete();
            } catch(\Exception $e) {
                $this->fail($e->getMessage());
            }
        }
    }
}