# Factory - "Backend Zadatak" - 15.3.2024. - Filip Horvat

## Set up project

1. git clone https://github.com/fico7489/factory
2. cd factory
4. docker compose up -d
5. docker compose exec php sh
6. composer install
7. php bin/console doctrine:migrations:migrate  --no-interaction
 - NOTE: on first docker up mysql needs 30-60 seconds to be ready
8. php bin/console lexik:jwt:generate-keypair
9. php bin/console app:db:seed

YOUR ARE READY!

### Run tests

FRESH_DB=1 vendor/bin/phpunit

Tests can be run x times on the same database because all data deleted after each test, with FRESH_DB=1 database is recreated

Tests will generate documentation in "api_documentation" folder where you can see how api endpoints are called

### Using app

Curl can be imported into the POSTMAN

#### Login:

```
curl --location --request GET 'http://localhost:5004/api/login_check' \
    --header 'Content-Type: application/json' \
    --data-raw '{
    "username" : "admin@example.com",
    "password" : "secret"
}'
```

#### Paginate products with sorts and filters

```
curl --location 'http://localhost:5004/api/v2/products?itemsPerPage=10&page=28&sorts[][price_adjusted]=asc' \
    --header 'Content-Type: application/json' \
    --header 'Authorization: Bearer [TOKEN]' \
    --header 'Accept: application/vnd.api+json'
```

For other possible filters and sorts check "api_documentation"

#### Create order

```
curl --location 'http://localhost:5004/api/orders?include=orderItems' \
    --header 'Content-Type: application/json' \
    --header 'Authorization: Bearer [TOKEN]' \
    --header 'Accept: application/vnd.api+json' \
    --data '{
        "items":
        {
            "1": 1,
            "2": 2,
            "3": 3,
            "4": 4,
            "5": 5,
            "6": 6,
            "7": 7
        }
    }'
```

First 7 products are prepared in seeders to cover all possible cases, logged as first user with id=1 (admin@example.com)

In items filed are productId => quantity mappings

When order is created you can check data here: http://localhost:5004/orders/{ORDER_ID}








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

Što se tiče riješenja za "proizvoda po cijeni, nazivu i kategoriji te sortiranje proizvoda po cijeni i po nazivu silazno/uzlazno"
je za potrebe ovog zadatka napravljeno pomoću sql upita, iako bi ja to rađe sa elasticom.


Što se tiče riješenja za ordere TODO...



06.ŠTO JE POTREBNO OD RJEŠENJA ZADATKA
1. Napraviti rutu za izlistavanje svih proizvoda s Paginacijom.
ruta GET->"/api/products", test "PaginateProductsTest.php" -> samo paginacija bez filtera i sortiranja

2. Napraviti rutu za izlistavanje proizvoda unutar kategorije s paginacijom.
ruta GET->"/api/category/{category}/products", test "PaginateProductsTest.php" -> samo paginacija bez filtera i sortiranja

3. Napraviti rutu za jedan proizvod.
ruta GET->"/api/products/{id}", test "PaginateProductsTest.php"

4. Napraviti rutu za filtriranje proizvoda po cijeni, nazivu i kategoriji te sortiranje proizvoda po cijeni i po nazivu silazno/uzlazno. (Pripaziti na contract i price liste.)
ruta GET->"/api/v2/products", test "PaginateV2ProductsTest.php"

5. Napraviti rutu za dodavanje nove narudžbe koja prima array od proizvoda.
ruta POST->"/api/orders", test "CreateOrderActionTest.php"

6. Rješenje mora dobro i brzo funkcionirati s 5 proizvoda te sa cca 20k proizvoda s n milijuna varijacija cijena za proizvode.
RADI

07 GOTOVO RJEŠENJE

Kad je sve napravljeno izvršeno, poslati source-code, i izvezenu bazu podataka.
DONE

Bilo bi poželjno poslati i Postman kolekciju. Postman preporučamo kao alat za testiranje ruta. 
DOCUMENTACIJA kako pozivati rute se generira u folderu "api_documentation" iz testova

Poželjno je napisati testove za rute (ali nije nužno).
DONE