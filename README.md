observr
====
PHP 5.5+ event layer

[![Build Status](https://travis-ci.org/jgswift/observr.png?branch=master)](https://travis-ci.org/jgswift/observr)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jgswift/observr/badges/quality-score.png?s=87a44242339b2b007df16d5847b06c0246500931)](https://scrutinizer-ci.com/g/jgswift/observr/)
[![Latest Stable Version](https://poser.pugx.org/jgswift/observr/v/stable.svg)](https://packagist.org/packages/jgswift/observr)
[![License](https://poser.pugx.org/jgswift/observr/license.svg)](https://packagist.org/packages/jgswift/observr)
[![Coverage Status](https://coveralls.io/repos/jgswift/observr/badge.png?branch=master)](https://coveralls.io/r/jgswift/observr?branch=master)

## Description

observr is a generic event layer that provides a flexible foundation for event handling in a domain-agnostic and non-intrusive way.

## Installation

Install via cli using [composer](https://getcomposer.org/):
```sh
php composer.phar require jgswift/observr:0.2.*
```

Install via composer.json using [composer](https://getcomposer.org/):
```json
{
    "require": {
        "jgswift/observr": "0.2.*"
    }
}
```

## Dependency

* php 5.5+
* [react/promise](https://github.com/reactphp/promise)

## Usage

### Subject

The Subject layer is a flexible interface/trait combination that provides a generic observer pattern implementation.

#### Basic example

```php
class User implements observr\SubjectInterface
{
    use observr\Subject;
}

$user = new User;
$user->attach("login",function($sender,$e) {
    return "login successful";
});

var_dump($user->setState("login")); // returns "login successful"
```

#### State Change

```observr\Event``` delivers a combination ```EventInterface``` and ```EventAwareInterface``` to the observing callbacks
and can be used to define pass-thru state variables.

```php
$user = new User;
$user->attach("login",function($sender,$e) {
    $e->cancel();  // manual cancellation
});

$event = new observr\Event($user);
$user->setState("login",$event)

var_dump($event->isCanceled()); // returns true
```

To implement a custom event interface just inherit ```observr\Event``` or implement ```EventInterface``` and ```EventAwareInterface```

#### EventInterface (abbr.)

State status and exception container

```php
interface EventInterface {
    public function isComplete();

    public function isSuccess();

    public function isFailure();

    public function isCanceled();

    public function getException();

    public function setException(\Exception $exception);
}
```

#### EventAwareInterface (abbr.)

Performs state changes for ```EventInterface```

```php
interface EventAwareInterface {
    public function cancel(EventInterface $event = null);

    public function complete(EventInterface $event);

    public function fail(EventInterface $event);

    public function succeed(EventInterface $event);
}
```

### Event Namespacing

In order to handle events differently depending on package behavior it is possible to ```attach```/```detach``` with namespaces.

```php
$user = new User;
            
$user->attach('login',function($sender,$e) {
    echo 'default login';
});

$user->attach('login.myNamespace',function($sender,$e) {
    echo 'my custom login';
});

$user->setState('login'); // echo 'default login' & 'my custom login'

$user->detach('login.myNamespace'); // removes only the namespaced handler

$user->setState('login'); // echo 'default login'
```

#### Success, Failure, Complete, Cancel

```observr\Event``` also implements the observer pattern itself and can be used to
 validate event results.  The validation constants are ```COMPLETE```, ```FAILURE```, ```SUCCESS```, and ```CANCEL```

* ```COMPLETE``` is notified when all observers fire without failure
* ```FAILURE``` is notified when any observer throws an exception
* ```SUCCESS``` is notified every time the state is changed without failure or cancellation
* ```CANCEL``` is notifier when any observer invokes cancellation

```php
class Foo implements SubjectInterface
{
    use observr\Subject;
}

$foo = new Foo;
$foo->attach("bar",function($sender,$e) {
    // success with no cancellation or errors
});

$foo->attach("baz",function($sender,$e) {
    $e->cancel(); // cancels the event
});

$foo->attach("fizz",function($sender,$e) {
    throw new \Exception; // causes fault
});

$event = new observr\Event($foo);

$event->attach(observr\Event::COMPLETE,function() {
    echo 'COMPLETE';
});

$event->attach(observr\Event::FAILURE,function() {
    echo 'FAILURE'
});

$event->attach(observr\Event::CANCELED,function() {
    echo 'CANCELED';
});

$event->attach(observr\Event::SUCCESS,function() {
    echo 'SUCCESS';
});

$foo->setState("bar",$event); // everything worked!
// invokes ...
// COMPLETE
// SUCCESS


$foo->setState("baz",$event); // manual cancellation
// invokes ...
// COMPLETE
// CANCEL

$foo->setState("fizz",$event); // throws exception
// invokes ...
// FAILURE

```
### Emitter

Emitter is a Subject where events are exposed into individual objects

#### Basic Emitter

```php
class Button implements SubjectInterface {
    use observr\Subject;
}

$button = new Button;

$click = new observr\Emitter('click');

$button->setState($click, function($sender,$e) {
    echo 'CLICKED!';
});

$click($button); // prints 'CLICKED'!
```

#### EmitterInterface (abbr.)

```php
interface EmitterInterface extends SubjectInterface {
    public function getName();

    public function emit($e = null);

    public function on(callable $callable);

    public function bind(callable $callable);

    public function unbind(callable $callable);

    public function map(callable $callable);

    public function filter(callable $callable);

    public function __toString();
}
```

#### Combining Emitters

```php
class Button {
    function __construct() {
        $this->click = new observr\Emitter('click');
        $this->mouseup = new observr\Emitter('mouseup');
        $this->mousedown = new observr\Emitter('mousedown');
    }
}

$button = new Button;

$combinedClick = $button->click->map(function($sender, $e) {
    /* extra mapping */
})->merge($button->mousedown->map(function($sender,$e) {
    /* extra mapping */
}))->merge($button->mouseup->map(function($sender,$e) {
    /* extra mapping */
}));

$combinedClick($button); // performs click, mousedown & mouseup all together
```

#### Filtering

Filtering allows mapping procedures to be applied selectively

```php
$sending = $button->click
  ->filter(function($button,$e) {
    if($button instanceof Button) { // only changes Button to "Sending..."
        return true;
    }

    return false;
})->map(function($button,$e) {
    $button->value = 'Sending...';
});

$sending($button); // triggers click and changes button text to "Sending..."
```

### Streaming

A stream provides an easy way to wrap around multiple subjects at once and listen to many events.

#### Basic

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

#### StreamInterface (abbr.)

```php
interface StreamInterface {
    public function close();

    public function getSubjects();

    public function isOpen();

    public function open();

    public function watch($pointer);

    public function unwatch($pointer);

    public function isWatching($pointer);
}
```

## Related Package(s)

* [jgswift/detectr](http://github.com/jgswift/detectr) - complex event processor

