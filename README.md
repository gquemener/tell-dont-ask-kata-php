## Credits

Inspired by Gabriele Tondi

## Goal

A legacy refactor kata, focused on the violation of the [tell don’t ask](https://kata-log.rocks/tell-dont-ask) principle and the [anemic domain model](https://martinfowler.com/bliki/AnemicDomainModel.html).
Description

Here you find a simple order flow application. It’s able to create orders, do some calculation (totals and taxes), and manage them (approval/reject and shipment).

The old development team did not find the time to build a proper domain model but instead preferred to use a procedural style, building this anemic domain model. Fortunately, they did at least take the time to write unit tests for the code.

Your new CTO, after many bugs caused by this application, asked you to refactor this code to make it more maintainable and reliable.

## How to
### Install

```bash
composer install
```

### Run tests

```bash
composer tests
```

### Enforce CS

```bash
composer cs
```
