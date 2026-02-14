#!/bin/bash
set -e

php-fpm --fpm-config /opt/php-8.5.2/etc/php-fpm.conf -c /opt/php-8.5.2/etc/php.ini &

exec nginx -g "daemon off;"
