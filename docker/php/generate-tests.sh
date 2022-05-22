#!/usr/bin/env bash
php /app/bin/php-hephaestus populate:mysql
php /app/bin/php-hephaestus --config /app/example/php-hephaestus.json pipe mysqlIntegerTest phpSimpleClassIntegerTest
php /app/bin/php-hephaestus --config /app/example/php-hephaestus.json pipe mysqlIntegerTest phpSimplePHPUnitIntegerTest
