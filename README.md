# VarDump

> 🔔 Subscribe to the [newsletter](https://chv.to/chevere-newsletter) to don't miss any update regarding Chevere.

![Chevere](chevere.svg)

[![Build](https://img.shields.io/github/actions/workflow/status/chevere/var-dump/test.yml?branch=2.0&style=flat-square)](https://github.com/chevere/var-dump/actions)
![Code size](https://img.shields.io/github/languages/code-size/chevere/var-dump?style=flat-square)
[![Apache-2.0](https://img.shields.io/github/license/chevere/var-dump?style=flat-square)](LICENSE)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%209-blueviolet?style=flat-square)](https://phpstan.org/)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat-square&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fchevere%2Fvar-dump%2F2.0)](https://dashboard.stryker-mutator.io/reports/github.com/chevere/var-dump/2.0)

[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=chevere_var-dump&metric=alert_status)](https://sonarcloud.io/dashboard?id=chevere_var-dump)
[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=chevere_var-dump&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=chevere_var-dump)
[![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=chevere_var-dump&metric=reliability_rating)](https://sonarcloud.io/dashboard?id=chevere_var-dump)
[![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=chevere_var-dump&metric=security_rating)](https://sonarcloud.io/dashboard?id=chevere_var-dump)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=chevere_var-dump&metric=coverage)](https://sonarcloud.io/dashboard?id=chevere_var-dump)
[![Technical Debt](https://sonarcloud.io/api/project_badges/measure?project=chevere_var-dump&metric=sqale_index)](https://sonarcloud.io/dashboard?id=chevere_var-dump)
[![CodeFactor](https://www.codefactor.io/repository/github/chevere/var-dump/badge)](https://www.codefactor.io/repository/github/chevere/var-dump)

## Summary

Multi-purpose colorful modern alternative to [var_dump](https://www.php.net/manual/function.var-dump.php). It's a PHP library that provides an alternative to `var_dump` and `dump` functions. It's designed to be used in development environments to help you debug your code.

## Features

* Colorful output with automatic light/dark modes
* HTML, Console, and Plain text output
* No JavaScript required
* Displays modifiers, types, values, references, and more
* Recursive detection
* Foldable arrays and objects with indentation display
* Lightweight codebase

## Installing

VarDump is available through [Packagist](https://packagist.org/packages/chevere/var-dump) and the repository source is at [chevere/var-dump](https://github.com/chevere/var-dump).

```sh
composer require --dev chevere/var-dump
```

## Quick start

* Use `vd` to dump information about any variable (replaces `var_dump` and `dump`)
* Use `vdd` to do the same as `vd` and `die(0)` (replaces `dd`)

```php
vd($myVar); // var dump
vdd($myVar); // var dump and die
```

## Demo

![HTML demo dark](demo/demo-dark.png)

![HTML demo light](demo/demo-light.png)

* [HTML](https://chevere.github.io/var-dump/demo/output/html.html)
* [Plain text](https://chevere.github.io/var-dump/demo/output/plain.txt)
* [Console (asciinema)](https://asciinema.org/a/496889)

## Documentation

Documentation is available at [chevere.org](https://chevere.org/packages/var-dump).

## License

Copyright [Rodolfo Berrios A.](https://rodolfoberrios.com/)

Chevere is licensed under the Apache License, Version 2.0. See [LICENSE](LICENSE) for the full license text.

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
