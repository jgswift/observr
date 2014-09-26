<?php
namespace observr {
    use observr\Event\EventInterface as EventInterface;
    use observr\Event\EventAwareInterface as EventAwareInterface;
    use observr\Subject\SubjectInterface as SubjectInterface;
    use observr\Event\EventTrait as EventTrait;
    use observr\Event\EventAwareTrait as EventAwareTrait;
    use observr\Subject\SubjectTrait as SubjectTrait;
    
    class Event extends \ArrayObject implements EventInterface, EventAwareInterface, SubjectInterface {
        use EventTrait, EventAwareTrait, SubjectTrait;
        
        const FAILURE = 'fail';
        
        const COMPLETE = 'complete';
        const DONE = 'complete';
        
        const CANCEL = 'cancel';
        
        const SUCCESS = 'success';
    }
}