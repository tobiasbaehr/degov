# Robo scripts

Robo is a taskrunner, made in PHP. We are using it e.g. for running deGov updates easily. Learn more [about Robo](https://robo.li/getting-started/).

## Running in verbose mode

The verbose mode shows you, what happens in detail. E.g. Composer output. Otherwise the output it kept minimal.

```
composer degov-update -- -vvv
```

## Tell Robo its RoboFile.php

The `--load-from` parameter is useful, if you want to run Robo from a location where no `RoboFile.php` is present:

```
./bin/robo degov:update --load-from docroot/profiles/contrib/degov/scripts/Robo/RoboFile.php
```