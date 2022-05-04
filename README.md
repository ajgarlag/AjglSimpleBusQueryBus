AjglSimpleBusQueryBus
=====================

This package is an extension to the [MessageBus] package by @matthiasnoback intended to build query buses.

[![Build Status](https://github.com/ajgarlag/AjglSimpleBusQueryBus/actions/workflows/tests.yaml/badge.svg?branch=master)](https://github.com/ajgarlag/AjglSimpleBusQueryBusr/actions/workflows/tests.yaml)
[![Latest Stable Version](https://poser.pugx.org/ajgl/simple-bus-query-bus/v/stable.png)](https://packagist.org/packages/ajgl/simple-bus-query-bus)
[![Latest Unstable Version](https://poser.pugx.org/ajgl/simple-bus-query-bus/v/unstable.png)](https://packagist.org/packages/ajgl/simple-bus-query-bus)
[![Total Downloads](https://poser.pugx.org/ajgl/simple-bus-query-bus/downloads.png)](https://packagist.org/packages/ajgl/simple-bus-query-bus)
[![Montly Downloads](https://poser.pugx.org/ajgl/simple-bus-query-bus/d/monthly.png)](https://packagist.org/packages/ajgl/simple-bus-query-bus)
[![Daily Downloads](https://poser.pugx.org/ajgl/simple-bus-query-bus/d/daily.png)](https://packagist.org/packages/ajgl/simple-bus-query-bus)
[![License](https://poser.pugx.org/ajgl/simple-bus-query-bus/license.png)](https://packagist.org/packages/ajgl/simple-bus-query-bus)

It allows you to create a message bus that will catch the return value of the handling of the given message.


Installation
------------

To install the latest stable version of this component, open a console and execute the following command:
```
$ composer require ajgl/simple-bus-query-bus
```


Usage
-----

## Implementing a query bus

The classes and interfaces from this package can be used to set up a query bus. The characteristics of a query bus are:

* It handles queries, i.e. interrogative messages
* Queries are handled by exactly one query handler
* The behavior of the query bus is extensible: middlewares are allowed to do things before or after handling a query
* To get the query result back, a second parameter has to be passed to the query bus to fill it with the query result.

### Setting up the query bus

At least we need an instance of `CatchReturnMessageBusSupportingMiddleware`:

```php
use Ajgl\SimpleBus\Message\Bus\Middleware\CatchReturnMessageBusSupportingMiddleware;

$query = new CatchReturnMessageBusSupportingMiddleware();
```

### Defining the query handler map

Now we also want queries to be handled by exactly one query handler (which can be any [callable](http://php.net/manual/en/language.types.callable.php)). We first need to define the collection of
handlers that are available in the application. We should make this *query handler map* lazy-loading, or every
query handler will be fully loaded, even though it is not going to be used:

```php
use SimpleBus\Message\CallableResolver\CallableMap;
use SimpleBus\Message\CallableResolver\ServiceLocatorAwareCallableResolver;

// Provide a map of query names to callables. You can provide actual callables, or lazy-loading ones.
$queryHandlersByQueryName = [
    'Fully\Qualified\Class\Name\Of\Query' => ... // a "callable"
];
```

Each of the provided "callables" can be one of the following things:

- An actual [PHP callable](http://php.net/manual/en/language.types.callable.php),
- A service id (string) which the service locator (see below) can resolve to a PHP callable,
- An array of which the first value is a service id (string), which the service locator can resolve to a regular object, and the second value is a method name.

For backwards compatibility an object with a `handle()` method also counts as a "callable".

```php
// Provide a service locator callable. It will be used to instantiate a handler service whenever requested.
$serviceLocator = function ($serviceId) {
    $handler = ...;

    return $handler;
}

$queryHandlerMap = new CallableMap(
    $queryHandlersByQueryName,
    new ServiceLocatorAwareCallableResolver($serviceLocator)
);
```

### Resolving the query handler for a query

#### The name of a query

First we need a way to resolve the name of a query. You can use the fully-qualified class name (FQCN) of a
query object as its name:

```php
use SimpleBus\Message\Name\ClassBasedNameResolver;

$queryNameResolver = new ClassBasedNameResolver();
```

Or you can ask query objects what their name is:

```php
use SimpleBus\Message\Name\NamedMessageNameResolver;

$queryNameResolver = new NamedMessageNameResolver();
```

In that case your queries have to implement `NamedMessage`:

```php
use SimpleBus\Message\Name\NamedMessage;

class YourQuery implements NamedMessage
{
    public static function messageName()
    {
        return 'your_query';
    }
}
```

> #### Implementing your own `MessageNameResolver`
>
> If you want to use another rule to determine the name of a query, create a class that implements
> `SimpleBus\Message\Name\MessageNameResolver`.

### Resolving the query handler based on the name of the query

Using the `MessageNameResolver` of your choice, you can now let the *query handler resolver* find the right query
handler for a given query.

```php
use SimpleBus\Message\Handler\Resolver\NameBasedMessageHandlerResolver;

$queryHandlerResolver = new NameBasedMessageHandlerResolver(
    $queryNameResolver,
    $queryHandlerMap
);
```

Finally, we should add some middleware to the query bus that calls the resolved query handler and catchs the handler result:

```php
use Ajgl\SimpleBus\Message\Handler\DelegatesToMessageHandlerAndCatchReturnMiddleware;

$queryBus->appendMiddleware(
    new DelegatesToMessageHandlerAndCatchReturnMiddleware(
        $queryHandlerResolver
    )
);
```

## Using the query bus: an example

Consider the following query:

```php
class FindUserByEmailAddress
{
    private $emailAddress;

    public function __construct($emailAddress)
    {
        $this->emailAddress = $emailAddress;
    }

    public function emailAddress()
    {
        return $this->emailAddress;
    }
}
```

This query communicates the intention to "find an user by email address". The message data consists of an email address. This information is required to execute the desired behavior.

The handler for this query looks like this:

```php
class FindUserByEmailAddressQueryHandler
{
    ...

    public function handle(FindUserByEmailAddress $query)
    {
        $user = $this->userRepository->findOneByEmailAddress(
            $query->emailAddress()
        );

        return $user;
    }
}
```

We should register this handler as a service and add the service id to the query handler map.
Since we have already fully configured the query bus, we can just start creating a new query object and let the
query bus handle it. Eventually the query will be passed as a message to the `FindUserByEmailAddressQueryHandler`:

```php
$query = new FindUserByEmailAddress(
    'aj@garcialagar.es'
);

$queryBus->handle($query, $queryResult);
```

Once handled the query, the `$queryResult` variable will contain the query result (the user found).


License
-------

This component is under the MIT license. See the complete license in the [LICENSE] file.


Reporting an issue or a feature request
---------------------------------------

Issues and feature requests are tracked in the [Github issue tracker].


Author Information
------------------

Developed with ♥ by [Antonio J. García Lagar].

If you find this component useful, please add a ★ in the [GitHub repository page] and/or the [Packagist package page].

[MessageBus]: http://simplebus.github.io/MessageBus
[LICENSE]: LICENSE
[Github issue tracker]: https://github.com/ajgarlag/AjglSimpleBusQueryBus/issues
[Antonio J. García Lagar]: http://aj.garcialagar.es
[GitHub repository page]: https://github.com/ajgarlag/AjglSimpleBusQueryBus
[Packagist package page]: https://packagist.org/packages/ajgl/simple-bus-query-bus
