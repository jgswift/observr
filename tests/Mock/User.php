<?php
namespace observr\Tests\Mock {
    use observr;
    class User implements observr\Subject\SubjectInterface {
        use observr\Subject;
    }
}