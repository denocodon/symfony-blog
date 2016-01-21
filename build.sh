#!/bin/sh
composer install
#php bin/vendors install
phpunit -c app
