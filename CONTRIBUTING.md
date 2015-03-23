# Contributing

Please adhere to the following rules when contributing (because I try to):

* Follow PHP Standards Recommendations (PSR)
	* [PSR-1, Basic coding standard](http://www.php-fig.org/psr/psr-1/)
	* [PSR-2, Coding style guide](http://www.php-fig.org/psr/psr-2/)
* Document your code using [PHPDocumentor's](http://phpdoc.org) syntax
	* Methods should start with a [DocBlock](http://phpdoc.org/docs/latest/getting-started/your-first-set-of-documentation.html#writing-a-docblock).
	* A method's DocBlock should include a summary and a description, if a description is necessary.
	* A method's DocBlock should include, in order, the [`@param`](http://phpdoc.org/docs/latest/references/phpdoc/tags/param.html), [`@return`](http://phpdoc.org/docs/latest/references/phpdoc/tags/return.html), [`@throws`](http://phpdoc.org/docs/latest/references/phpdoc/tags/throws.html), [`@since`](http://phpdoc.org/docs/latest/references/phpdoc/tags/since.html), and [`@see`](http://phpdoc.org/docs/latest/references/phpdoc/tags/see.html) tags (if the `@see` is necessary).
* Write unit tests using [PHPUnit](https://phpunit.de).
	* Tests should test behavior, not return types. In other words, test inputs and outputs in different scenarios to make sure the function fulfills its promise to the rest of the code.
	* Tests should be named `test<methodName>_returns<Value>_if<testCase>` (e.g., `testCopy_returnsTrue_ifSourceIsEmpty`) or `test<methodName>_throws<exceptionName>_if<testCase>` (e.g., `testCopy_throwsInvalidArgumentException_ifSourceDoesNotExist`).
	* Tests should include only one assertion (although, of course, there are exceptions to this rule).

If those rules are palatable, here are the steps to follow:

1. Fork
2. Clone
3. PHPUnit
4. Branch
5. PHPUnit
6. Code
7. PHPUnit
8. Commit
9. Push
10. Pull request
11. Relax and eat a Paleo muffin

That's it! I'm looking forward to your pull request, and thank you for the help! If you have any questions, email me at [clayjs0@gmail.com](mailto:clayjs0@gmail.com).