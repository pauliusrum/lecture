## Introduction
Project contains two systems:
* [Vulnerable app](vulnerable-app) - system that contains security issues.
* [Malicious app](malicious-app) - system that exploits security issues.
 
## Prerequisites
* PHP 7.3
* Xdebug 2.7

## Setup

### Vulnerable app
* Build: `vulnerable-app/build`
* Start: `(cd vulnerable-app && php bin/console server:start 8080)`

### Malicious app
* Build: `malicious-app/build`
* Start: `(cd malicious-app && php bin/console server:start 8040)`