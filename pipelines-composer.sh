#!/usr/bin/env bash

php /usr/local/bin/composer create-project degov/degov-project --dev
php /usr/local/bin/composer require phpunit/phpunit:~4.8 --dev