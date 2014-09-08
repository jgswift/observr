observr
====
PHP 5.5+ observer pattern using traits

[![Build Status](https://travis-ci.org/jgswift/observr.png?branch=master)](https://travis-ci.org/jgswift/observr)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jgswift/observr/badges/quality-score.png?s=87a44242339b2b007df16d5847b06c0246500931)](https://scrutinizer-ci.com/g/jgswift/observr/)

## Installation

Install via [composer](https://getcomposer.org/):
```sh
php composer.phar require jgswift/observr:dev-master
```

## Usage

### Basic example

observr is a lightweight php trait that loosely implements the observer pattern

The following is a minimal example
```php
<?php
class Foo
{
    use observr\Subject;
}

$foo = new Foo;
$foo->attach("bar",function($sender,$e) {
    return "baz";
});

var_dump($foo->setState("bar")); // returns "baz"
```

### Pass-thru

Use observr\Event to define pass-thru variables or for event cancellation.  You can implement custom pass-thrus by inheriting observr\Event

```php
<?php
class Foo
{
    use observr\Subject;
}

$foo = new Foo;
$foo->attach("bar",function($sender,$e) {
    $e->cancel();
});

$event = new observr\Event($foo);
$foo->setState("bar",$event)

var_dump($event->canceled); // returns true
```

### Done, Fail, Always

observr\Event also implements the observer pattern itself and can be used to conveniently observe event result.  The built-in events are *DONE*, *FAIL*, & *ALWAYS*

* DONE is notified when all observers fire successfully
* FAIL is notified when any observer cancels the event using observr\Event->cancel()
* ALWAYS is notified every time the state is changed

```php
<?php
class Foo
{
    use observr\Subject;
}

$foo = new Foo;
$foo->attach("bar",function($sender,$e) {
    return 1;
});

$foo->attach("baz",function($sender,$e) {
    $e->cancel(); // cancels the event, therein firing FAIL
});

$event = new observr\Event($foo);
$event->attach(observr\Event::DONE,function() {
    echo 'DONE';
});
$event->attach(observr\Event::FAIL,function() {
    echo 'FAIL';
});
$event->attach(observr\Event::ALWAYS,function() {
    echo 'ALWAYS';
});

$foo->setState("bar",$event)
// returns ...
// DONE
// ALWAYS

$foo->setState("baz",$event)
// returns ...
// FAIL
// ALWAYS
```

In this example, observr\Event is assigned all 3 built-in observers

Setting $foo state to "bar" successfully completes and notifies both *DONE* and *ALWAYS*
Using the same observr\Event, setting $foo state to "baz" fails to complete and notifies *FAIL* and ALWAYS*

### Streaming

The stream provides an easy way to wrap around multiple subjects at once.

```php
$bob = new User
$john = new User;

$stream = new observr\Stream('login');

// instruct stream to watch our user login events
$stream->watch($bob);
$stream->watch($john);

$c = 0;
$stream->attach(function($sender,$e=null)use(&$c) {
    $c++; // called twice, this is where we intercept the event
});

// open stream
$stream->open();

// trigger some fake logins
$bob->setState('login');
$john->setState('login');

// close stream
$stream->close();

var_dump($c); // 2
```

