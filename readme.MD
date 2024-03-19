# Factory - "Backend Zadatak" - 15.3.2024. - Filip Horvat

## Set up project



vendor/bin/php-cs-fixer fix
vendor/bin/phpstan analyse --memory-limit=1G
vendor/bin/phpunit
php bin/console doctrine:schema:validate --skip-sync


php bin/console doctrine:schema:drop --force --full-database
php bin/console doctrine:migrations:migrate  --no-interaction
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate -n
php bin/console app:seed
APP_DEBUG=0 php -d memory_limit=2000M bin/console app:seed-speed


## Notes

Sustav kategorija se može izvesti na milijun načina, jaku puno ewb shopova namjerno ima 2 ili 3 level koji je obavezan za sve proizvode
a da se onda samo ta zadnja subkategorija dodaje na proizvod



 06 ŠTO JE POTREBNO OD RJEŠENJA ZADATKA
1. Napraviti rutu za izlistavanje svih proizvoda s Paginacijom.
ruta "/api/products", test "PaginateProductsTest.php" -> samo paginacija bez filtera i sortiranja

2. Napraviti rutu za izlistavanje proizvoda unutar kategorije s paginacijom.
ruta "/api/category/{category}/products", test "PaginateProductsTest.php" -> samo paginacija bez filtera i sortiranja

3. Napraviti rutu za jedan proizvod.
ruta "/api/products/{id}", test "PaginateProductsTest.php"

4. Napraviti rutu za filtriranje proizvoda po cijeni, nazivu i kategoriji te sortiranje proizvoda po cijeni i po nazivu silazno/uzlazno. (Pripaziti na contract i price liste.)
ruta "/api/v2/products", test "PaginateV2ProductsTest.php"

5. Napraviti rutu za dodavanje nove narudžbe koja prima array od proizvoda.


6. Rješenje mora dobro i brzo funkcionirati s 5 proizvoda te sa cca 20k proizvoda s n milijuna varijacija cijena za proizvode.

