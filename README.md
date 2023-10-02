# VarDump

> 🔔 Subscribe to the [newsletter](https://chv.to/chevere-newsletter) to don't miss any update regarding Chevere.

![Chevere](chevere.svg)

[![Build](https://img.shields.io/github/actions/workflow/status/chevere/var-dump/test.yml?branch=0.9&style=flat-square)](https://github.com/chevere/var-dump/actions)
![Code size](https://img.shields.io/github/languages/code-size/chevere/var-dump?style=flat-square)
[![Apache-2.0](https://img.shields.io/github/license/chevere/var-dump?style=flat-square)](LICENSE)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%209-blueviolet?style=flat-square)](https://phpstan.org/)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat-square&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fchevere%2Fvar-dump%2F0.9)](https://dashboard.stryker-mutator.io/reports/github.com/chevere/var-dump/0.9)

[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=chevere_var-dump&metric=alert_status)](https://sonarcloud.io/dashboard?id=chevere_var-dump)
[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=chevere_var-dump&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=chevere_var-dump)
[![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=chevere_var-dump&metric=reliability_rating)](https://sonarcloud.io/dashboard?id=chevere_var-dump)
[![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=chevere_var-dump&metric=security_rating)](https://sonarcloud.io/dashboard?id=chevere_var-dump)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=chevere_var-dump&metric=coverage)](https://sonarcloud.io/dashboard?id=chevere_var-dump)
[![Technical Debt](https://sonarcloud.io/api/project_badges/measure?project=chevere_var-dump&metric=sqale_index)](https://sonarcloud.io/dashboard?id=chevere_var-dump)
[![CodeFactor](https://www.codefactor.io/repository/github/chevere/var-dump/badge)](https://www.codefactor.io/repository/github/chevere/var-dump)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/1f8286c311934c45b96c0a6b3d33204f)](https://app.codacy.com/gh/chevere/var-dump/dashboard)

![VarDump](.github/banner/var-dump-logo.svg)

## Quick start

Install VarDump using [Composer](https://getcomposer.org).

```sh
composer require --dev chevere/var-dump
```

* Use `vd` to dump information about any variable (replaces `var_dump` and `dump`)
* Use `vdd` to do the same as `vd` and `die(0)` (replaces `dd`)

```php
vd($myVar); // var dump
vdd($myVar); // var dump and die
```

## Demo

![HTML demo](demo/demo.svg)

* [HTML](https://chevere.github.io/var-dump/demo/output/html.html)
* [Plain text](https://chevere.github.io/var-dump/demo/output/plain.txt)
* [Console (asciinema)](https://asciinema.org/a/496889)

## Documentation

Documentation is available at [chevere.org](https://chevere.org/packages/var-dump).

## License

Copyright 2022 [Rodolfo Berrios A.](https://rodolfoberrios.com/)

Chevere is licensed under the Apache License, Version 2.0. See [LICENSE](LICENSE) for the full license text.

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
