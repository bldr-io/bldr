# Bldr Contribution Guide

This page contains guidelines for contributing to the Bldr Task Runner. Please review these guidelines before submitting any pull requests to the framework.

## Which Branch?

**ALL** bug fixes should be made to the 4.x branch which they belong. Bug fixes should never be sent to the `master` branch unless they fix features that exist only in the upcoming release.

## Pull Requests

The pull request process differs for new features and bugs. Before sending a pull request for a new feature, you should first create an issue with `[Proposal]` in the title. The proposal should describe the new feature, as well as implementation ideas. The proposal will then be reviewed and either approved or denied. Once a proposal is approved, a pull request may be created implementing the new feature. Pull requests which do not follow this guideline will be asked to reformat.

Pull requests for bugs may be sent without creating any proposal issue. If you believe that you know of a solution for a bug that has been filed on Github, please leave a comment detailing your proposed fix.

### Feature Requests

If you have an idea for a new feature you would like to see added to Bldr, you may create an issue on Github with `[Request]` in the title. The feature request will then be reviewed by a core contributor.

## Coding Guidelines

Bldr follows all of the [PHP-FIG coding standards](https://github.com/php-fig/fig-standards/tree/master/accepted) coding standards. 

In addition to these standards, Bldr also follows the [naming conventions](https://github.com/php-fig/fig-standards/blob/master/bylaws/002-psr-naming-conventions.md#naming-conventions-for-code-released-by-php-fig) for PHP-FIG, minus the PSR specific naming.

Bldr also follows the following standards:

* Array's will follow the short syntax, and will not have a trailing slash
* Align variable values, when variables are grouped, including array values
* No spaces around concatenation periods