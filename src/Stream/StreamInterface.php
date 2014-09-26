<?php
namespace observr\Stream {
    interface StreamInterface {
        /**
         * Close stream
         */
        public function close();

        /**
         * Retrieve all subjects
         * @return array
         */
        public function getSubjects();

        /**
         * Check if open
         * @return boolean
         */
        public function isOpen();

        /**
         * Open stream
         */
        public function open();

        /**
         * Watches subject
         * @param mixed $pointer
         */
        public function watch($pointer);
        
        /**
         * Unwatches subject
         * @param mixed $pointer
         */
        public function unwatch($pointer);
        
        /**
         * Check if watching
         * @param mixed $pointer
         * @return boolean
         */
        public function isWatching($pointer);
    }
}