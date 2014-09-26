<?php
namespace observr\Stream {
    use observr\Subject\FixtureInterface as FixtureInterface;
    
    interface StreamSubjectInterface extends FixtureInterface {
        /**
         * Attach observer to subject
         * @param callable $observer
         */
        public function attach(callable $observer);
        
        /**
         * Detach observer from subject
         * @paran callable $observer
         */
        public function detach(callable $observer = null);
    }
}
