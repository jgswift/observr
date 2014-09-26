<?php
namespace observr\Subject {
    use observr\Subject\Emitter\EmitterInterface as EmitterInterface;
    use observr\Subject\Emitter\EmitterTrait as EmitterTrait;
    use observr\Subject\SubjectTrait as SubjectTrait;
    use observr\Subject\SubjectInterface as SubjectInterface;
    
    abstract class SubjectEmitter implements SubjectInterface, EmitterInterface {
        use SubjectTrait, EmitterTrait;
        
        /**
         * Default emitter constructor
         * @param string $name
         */
        function __construct($name) {
            $this->setName($name);
        }
    }
}