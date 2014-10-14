<?php
namespace observr\State {
    use observr\Event\EventAwareInterface as EventAwareInterface;
    use observr\Subject\FixtureInterface as FixtureInterface;
    use observr\Event\EventCancelException as EventCancelException;
    
    class StateObserver {
        /**
         * Locally store mixed subject to observe
         * @var mixed 
         */
        private $subject;
        
        /**
         * Locally store observer name
         * @var string
         */
        private $name;
        
        
        /**
         * Locally store observer namespace
         * @var string
         */
        private $namespace;
        
        /**
         * Locally stores observer validity
         * @var boolean
         */
        private $valid = false;
        
        /**
         * Default observer constructor
         * @param \observr\Subject\FixtureInterface $subject
         * @param string $name
         */
        public function __construct(FixtureInterface $subject, $name) {
            $this->subject = $subject;
            $this->parseName($name);
            list($this->name, $this->namespace) = self::parseName($name);
        }
        
        /**
         * Helper method to parse provided name
         * @param string $name
         */
        public static function parseName($name) {
            $parts = explode('.',$name);
            
            return [array_shift($parts),implode('.',$parts)];
        }
        
        /**
         * Retrieve subject fixture
         * @return \observr\Subject\FixtureInterface
         */
        public function getSubject() {
            return $this->subject;
        }
        
        /**
         * Retrieve observer name
         * @return string
         */
        public function getName() {
            return $this->name;
        }
        
        /**
         * Retrieve qualified observer name
         * @return string
         */
        public function getQualifiedName() {
            if(empty($this->namespace)) {
                return $this->name;
            }
            return $this->name.'.'.$this->namespace;
        }
        
        /**
         * Retrieve observer namespace
         * @return string
         */
        public function getNamespace() {
            return $this->namespace;
        }
        
        /**
         * Process event
         * @param \observr\Event\EventInterface $event
         * @param callable $callable
         * @return mixed
         */
        public function trigger(EventAwareInterface $event, callable $callable = null) {
            try {
                if(is_callable($callable)) {
                    $sender = $event->getSender();
                    if(empty($sender)) {
                        $sender = $this->subject;
                    }
                    return call_user_func_array($callable, [$sender, $event]);
                }
            } catch(\Exception $exception) {
                if($exception instanceof EventCancelException) {
                    $event->complete($event);
                    $event->cancel($event);
                } else {
                    $event->fail($event);
                    $event->setException($exception);
                }
            } finally {
                if(!$event->isCanceled() && !$event->isFailure()) {
                    $event->complete($event);

                    $this->valid = true;
                    $event->succeed($event);
                } else {
                    $this->invalidate();
                }
            }
        }
        
        /**
         * Check if observer is valid
         * @return boolean
         */
        public function isValid() {
            return $this->valid;
        }
        
        /**
         * Invalidate observer
         * @return bool
         */
        protected function invalidate() {
            return $this->valid = false;
        }
    }
}