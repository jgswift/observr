<?php
namespace observr {
    class Emitter {
        
        /**
         * List of streams
         * @var array 
         */
        public static $streams = [];
        
        /**
         * Adds stream to listener
         * @param \observr\Stream $stream
         */
        public static function addStream(Stream $stream) {
            self::$streams[] = $stream;
        }
        
        /**
         * Deletes stream from listener
         * @param \observr\Stream $stream
         */
        public static function removeStream(Stream $stream) {
            unset(self::$streams[array_search($stream,self::$streams)]);
        }
        
        /**
         * Streams event to outside observers
         * @param mixed $object
         * @param string $newstate
         * @param mixed $eventArgs
         */
        public static function stream($object,$newstate=null,$eventArgs=null) {
            if(!empty(self::$streams)) {
                foreach(self::$streams as $stream) {
                    if($stream->name === $newstate && 
                       $stream->isOpen() && 
                       $stream->isWatching($object)) {
                            $stream->setState($newstate,$eventArgs);
                    }
                }
            }
        }

    }
}