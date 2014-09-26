<?php
namespace observr\Stream {
    use observr\State\StateObserver as StateObserver;
    use observr\State\StateListener as StateListener;
    use observr\State\Notifier\NotifierAwareInterface as NotifierAwareInterface;
    use observr\Event\EventAwareInterface as EventAwareInterface;
    
    class StreamListener extends StateListener implements NotifierAwareInterface {
        /**
         * Locally store stream notifier
         * @var \observr\Stream\StreamNotifier 
         */
        private $notifier;
        
        /**
         * Stream listener constructor
         * @param \observr\Stream\StreamNotifier $notifier
         * @param \observr\State\StateObserver $parent
         */
        function __construct(StreamNotifier $notifier, StateObserver $parent) {
            parent::__construct($parent);
            $this->notifier = $notifier;
        }
        
        /**
         * Retrieve notifier
         * @return \observr\Stream\StreamNotifier
         */
        public function getNotifier() {
            return $this->notifier;
        }
        
        /**
         * Update notifier
         * @param \observr\Event\EventAwareInterface $event
         * @return mixed
         */
        public function notify(EventAwareInterface $event) {
            if($this->notifier->isWatching($this->getSubject())) {
                $this->notifier->setState($this->getName(), $event);
            }
            
            return parent::notify($event);
        }
    }
}