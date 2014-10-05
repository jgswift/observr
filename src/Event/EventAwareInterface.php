<?php
namespace observr\Event {
    interface EventAwareInterface {
        /**
         * Cancel event
         */
        public function cancel(EventInterface $event = null);
        
        /**
         * Complete event
         */
        public function complete(EventInterface $event);
        
        /**
         * Fail event
         */
        public function fail(EventInterface $event);
        
        /**
         * Succeed event
         */
        public function succeed(EventInterface $event);
        
        /**
         * Retrieve sender
         */
        public function getSender();
        
        /**
         * Update sender
         */
        public function setSender($sender);
    }
}