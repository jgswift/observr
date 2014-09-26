<?php
namespace observr\Subject {
    interface SubjectInterface extends FixtureInterface {
        /**
         * Attach observer to subject
         * @param string $name
         * @param string $observer
         */
        public function attach($name, callable $observer);
        
        /**
         * Detach observer to subject
         * @param string $name
         * @param string $observer
         */
        public function detach($name, callable $observer = null);
    }
}
