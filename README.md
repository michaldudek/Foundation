MD\Foundation
============

A set of useful PHP classes.

[![Build Status](https://travis-ci.org/michaldudek/Foundation.svg?branch=master)](https://travis-ci.org/michaldudek/Foundation)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/5690ef76-b97c-4ae1-9d20-695b2840b8ae/mini.png)](https://insight.sensiolabs.com/projects/5690ef76-b97c-4ae1-9d20-695b2840b8ae)
[![HHVM Status](http://hhvm.h4cc.de/badge/michaldudek/foundation.png)](http://hhvm.h4cc.de/package/michaldudek/foundation)

`MD\Foundation` is a set of useful PHP classes and functions that ease your development and abstract away some very common code.

It is what [Lo-Dash](http://lodash.com/) is for JavaScript.

Visit [http://www.michaldudek.pl/Foundation/](http://www.michaldudek.pl/Foundation/) for full documentation.

## Installation

You can install `MD\Foundation` using [Composer](https://getcomposer.org/).

    $ composer require michaldudek/foundation dev-master

## Features / namespaces

`MD\Foundation` features are grouped into PHP namespaces.

### MD\Foundation\Utils

Namespace `MD\Foundation\Utils` contains several static classes that contain common functions that can e.g. change your array filtering code into a one-liner. Classes are split into `StringUtils`, `ArrayUtils`, `ObjectUtils` and `FilesystemUtils`.

### MD\Foundation\Crypto

Password hashing and cryptography is a solved problem, yet a lot of projects still reinvent the wheel with funky hashing algorithms. `MD\Foundation\Crypto` namespaces provides a standard and best-practice methods to deal with basic cryptography, especially if you are not running PHP 5.5 yet.

### MD\Foundation\Debug

Inside the `MD\Foundation\Debug` namespace you will find few classes that help debug and profile your code, especially fit for runtime. The `Debugger` can be especially useful for analyzing variables at runtime and the `Timer` will help you find bottlenecks in your code.

### MD\Foundation\Exceptions

We all love to throw exceptions and while PHP provides a lot of exceptions to throw, `MD\Foundation\Exceptions` adds a few more (like `NotImplementedException` with automatic method name resolution) or extends existing ones for easier use (`InvalidArgumentException`).

### Other

There is also a couple of uncategorized classes which occassionally might be of use and which range from creating `MagicObject`s with automatic magic getters and setters (e.g. for very quick data storage) to ease working with other vendors like very common `Psr\Log`.

## Contribute

Issues and pull requests are very welcome! When creating a pull request please include full test coverage to your changes.
