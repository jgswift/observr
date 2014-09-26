<?php
namespace observr\State\Notifier {
    interface NotifierInterface {
        /**
         * Notify event state
         */
        function setState($state, $e = null);
    }
}