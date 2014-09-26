<?php
namespace observr\Event {
    interface EventInterface {
        /**
         * Check if event is complete
         * @return bool
         */
        public function isComplete();
        
        /**
         * Check if event is successful
         * return bool
         */
        public function isSuccess();
        
        /**
         * Check if event has failed
         * @return bool
         */
        public function isFailure();
        
        /**
         * Check if event is canceled
         * @return bool
         */
        public function isCanceled();
        
        /**
         * Retrieve failure exception
         * @return \Exception
         */
        public function getException();
        
        /**
         * Update failure exception
         * @param \Exception $exception
         */
        public function setException(\Exception $exception);
    }
}
