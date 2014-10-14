<?php
namespace observr\Stream {
    use observr\State\StateObserver as StateObserver;
    use observr\State\StateListener as StateListener;
    use observr\State\Notifier\NotifierAwareInterface as NotifierAwareInterface;
    use observr\State\Notifier\NotifierInterface as NotifierInterface;
    use observr\Event\EventAwareInterface as EventAwareInterface;
    
    class StreamListener extends StateListener implements NotifierAwareInterface {
        /**
         * Locally store stream notifier
         * @var \observr\State\Notifier\NotifierInterface
         */
        private $notifier;
        
        /**
         * Stream listener constructor
         * @param \observr\State\Notifier\NotifierInterface $notifier
         * @param \observr\State\StateObserver $parent
         */
        function __construct(NotifierInterface $notifier, StateObserver $parent) {
            parent::__construct($parent);
            $this->notifier = $notifier;
        }
        
        /**
         * Retrieve notifier
         * @return \observr\State\Notifier\NotifierInterface
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