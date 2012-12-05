trf4php
=======

This is a transaction manager facade library. It wraps and hides database handler libraries thus you can whenever switch to another one. The idea came from the Spring framwework.

Why is it useful?
-----------------

If you separate your project to layers, you probably have application layer with several service classes. Common requirement is that database libraries do not be used directly
in higher levels. This problem partially solved by ORM-s which follow datamapper pattern: you can use simple classes to persisting them. You can even use repositories through interfaces.
However, transaction handling is not hidden, you cannot switch your library to another one without modifying your service classes.

trf4php is a simple interface. You can use it in your code for transaction handling and you can change the current implementation. Perhaps you use simple PDO but you would like to switch to Doctrine. It requires just a configuration change.

Features
--------

Currently there is one 'real' binding implementation: [trf4php/trf4php-doctrine](https://github.com/szjani/trf4php-doctrine)

Feel free to implement a binding for your preferred database handling framework.
