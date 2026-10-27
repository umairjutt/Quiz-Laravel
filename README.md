# Laravel Quiz - early learning project

An early Laravel project exploring quiz assignment, student management and role-based workflows.

**Status:** historical learning work. The quiz model/service contract and substantive workflow tests are incomplete. This repository is retained as learning history.

## Explore the code

- [API routes](routes/api.php)
- [Quiz service](app/Services/QuizService.php)
- [Request validation](app/Http/Requests/)
- [Models](app/Models/) and [migrations](database/migrations/)
- [Tests](tests/)

## Stack

PHP, Laravel 10, MySQL, Sanctum/JWT integration and Spatie permissions/activity logging. See [composer.json](composer.json) for the original dependency versions.

## Development status

Installation and end-to-end behavior have not been verified for a current environment. Environment setup, model relationships, quiz persistence, request-log redaction and meaningful tests need completion before reuse.

## Maintainer

[Umair Jutt](https://github.com/umairjutt)
