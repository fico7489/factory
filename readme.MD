# Factory - "Backend Zadatak" - 15.3.2024. - Filip Horvat

## Set up project


php bin/console doctrine:schema:drop --force --full-database
php bin/console doctrine:migrations:migrate  --no-interaction


vendor/bin/php-cs-fixer fix
vendor/bin/phpstan analyse
vendor/bin/phpunit
php bin/console doctrine:schema:validate --skip-sync
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate -n
php bin/console app:seed



## Notes

Sustav kategorija se može izvesti na milijun načina, jaku puno ewb shopova namjerno ima 2 ili 3 level koji je obavezan za sve proizvode
a da se onda samo ta zadnja subkategorija dodaje na proizvod

