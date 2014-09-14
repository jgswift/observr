<?php
namespace observr\Tests\Mock {
    use observr;
    
    class Button {
        use observr\Subject;
        
        public $value;
    }
}