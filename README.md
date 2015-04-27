fizzbuzz-http
=============

A (silly) HTTP API for doing FizzBuzz stuff.

Copyright (c) 2014–2015 Scott Zeid.  
<http://code.s.zeid.me/fizzbuzz-http>

See it in action:

* PHP client:  <http://fizzbuzz-http.s.zeid.me/client.php>.
* HTML/JS client:  <http://fizzbuzz-http.s.zeid.me/client.html>.


Methods
-------

The notation used to denote in-URL parameters is `:param`, where `param` is
the name of the parameter.  All methods output a JSON literal upon success.

With the included PHP implementation, the method and parameters are passed
as the query string—for example, `server.php?/divisible-by/15/5`.


### GET `/start`

Returns the integer at which the counter should start counting
(currently `1`).


### GET `/end`

Returns the integer after which the counter should stop counting
(currently `100`).


### GET `/step`

Returns the integer by which the counter should increment after each iteration
(currently `1`).


### GET `/divisible-by/:a/:b`

Returns `true` if `a` is divisible by `b` and `false` otherwise.


### GET `/fizz/:what`

Returns information about `fizz`.  Acceptable values for `what` are:

* `number`:   
  Returns the number that corresponds to `fizz`
  (currently `3`).

* `text`:   
  Returns the output string that corresponds to `fizz`
  (currently `Fizz`).


### GET `/buzz/:what`

Returns information about `buzz`.  Acceptable values for `what` are:

* `number`:   
  Returns the number that corresponds to `buzz`
  (currently `5`).

* `text`:   
  Returns the output string that corresponds to `buzz`
  (currently `Buzz`).
