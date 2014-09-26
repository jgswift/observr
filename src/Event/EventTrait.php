<?php
namespace observr\Event {
    use observr\Event as Event;
    
    trait EventTrait {
        /**
         * Locally store failure exception
         * @var \Exception
         */
        private $exception;
        
        /**
         * Locally store event state
         * @var string
         */
        public $state;
        
        /**
         * Retrieve failure exception
         * @return \Exception
         */
        public function getException() {
            return $this->exception;
        }
        
        /**
         * Update failure exception
         * @param \Exception $exception
         * @return \Exception
         */
        public function setException(\Exception $exception) {
            return $this->exception = $exception;
        }

        /**
         * Check if event is complete
         * @return bool
         */
        public function isComplete() {
            return ($this->state == Event::COMPLETE) ? true : false;
        }

        /**
         * Check if event is successful
         * @return bool
         */
        public function isSuccess() {
            return ($this->state == Event::SUCCESS) ? true : false;
        }
        
        /**
         * Check if event is failed
         * @return bool
         */
        public function isFailure() {
            return ($this->state == Event::FAILURE) ? true : false;
        }
        
        /**
         * Check if event is canceled
         * @return bool
         */
        public function isCanceled() {
            return ($this->state == Event::CANCEL) ? true : false;
        }
    }
}