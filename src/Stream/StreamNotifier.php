<?php
namespace observr\Stream {
    use observr\State\Notifier\NotifierInterface as NotifierInterface;
    
    class StreamNotifier implements NotifierInterface {
        /**
         * Locally stores streams
         * @var array
         */
        private $streams = [];
        
        /**
         * Adds stream to notifier
         * @param \observr\Stream\StreamInterface $stream
         */
        public function addStream(StreamInterface $stream) {
            $this->streams[] = $stream;
        }
        
        /**
         * Removes stream from notifier
         * @param \observr\Stream\StreamInterface $stream
         */
        public function removeStream(StreamInterface $stream) {
            $key = array_search($stream,$this->streams);
            if($key) {
                unset($this->streams[$key]);
            }
        }
        
        /**
         * Check if object is being streamed
         * @param mixed $object
         * @return boolean
         */
        public function isWatching($object) {
            foreach($this->streams as $stream) {
                if($stream->isWatching($object)) {
                    return true;
                }
            }
            
            return false;
        }
        
        /**
         * Retrieve notifier streams
         * @return array
         */
        public function getStreams() {
            return $this->streams;
        }
        
        /**
         * Updates all stream subjects
         * @param string $state
         * @param mixed $e
         */
        public function setState($state, $e = null) {
            $streams = $this->streams;
            $this->streams = []; // prevent recursion
            foreach($streams as $stream) {
                $stream->setState($state);
            }
            
            $this->streams = $streams;
        }
    }
}