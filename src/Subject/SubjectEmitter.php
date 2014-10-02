<?php
namespace observr\Subject {
    use observr\Subject\Emitter\EmitterInterface as EmitterInterface;
    use observr\Subject\Emitter\EmitterTrait as EmitterTrait;
    use observr\Subject\Emitter\EmitterSubjectTrait as EmitterSubjectTrait;
    use observr\Subject\Emitter\EmitterSubjectInterface as EmitterSubjectInterface;
    
    abstract class SubjectEmitter implements EmitterSubjectInterface, EmitterInterface {
        use FixtureTrait, EmitterSubjectTrait, EmitterTrait;
        
        /**
         * Default emitter constructor
         * @param string $name
         */
        function __construct($name) {
            $this->setName($name);
        }
    }
}