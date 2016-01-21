#!/bin/sh
php bin/vendors install
phpunit -c app
