<?php
namespace observr\State\Notifier {
    interface NotifierAwareInterface {
        /**
         * @return NotifierInterface
         */
        function getNotifier();
    }
}
