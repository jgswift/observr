<?php
namespace observr\Tests\Mock {
    use observr;
    
    class Button implements observr\Subject\SubjectInterface {
        use observr\Subject;
        
        public $value;
    }
}