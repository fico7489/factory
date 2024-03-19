# Factory - "Backend Zadatak" - 15.3.2024. - Filip Horvat

## Set up project

1. git clone https://github.com/fico7489/factory
2. cd factory
3. docker compose up -d
4. docker compose exec php sh
5. composer install
6. php bin/console lexik:jwt:generate-keypair
7. php bin/console doctrine:migrations:migrate  --no-interaction
 - NOTE: on first docker up mysql needs 30-60 seconds to be ready
8. php bin/console app:db:seed

YOUR ARE READY! CHECK ROUTES SECTION!

### Run tests

```
FRESH_DB=1 vendor/bin/phpunit
```

FRESH_DB=1 - flag is for destroying and recreating fresh database for tests, once database is created 
tests can be run x times on the same database because all data is deleted after each test:

```
vendor/bin/phpunit
```

Tests will generate documentation in "api_documentation" folder where you can see how api endpoints are called

## DB Diagram

- ManyToMany for users<->user_groups
- ManyToMany for product<->categories
- order_item_price is entity which tells us how adjusted_price for each order_item is generated

[DB Diagram](docs/factory.png)

## Commands

- vendor/bin/php-cs-fixer fix
- vendor/bin/phpstan analyse --memory-limit=1G
- vendor/bin/phpunit
- php bin/console doctrine:schema:validate --skip-sync


- php bin/console doctrine:schema:drop --force --full-database
- php bin/console doctrine:migrations:migrate  --no-interaction
- php bin/console doctrine:migrations:diff
- php bin/console app:seed

## Notes

Napravljeno je sve iz uputa. 
Naravno tu bi sad mogao još danima raditi na svačemu, razno razne validacije, entitete za adrese, entitete i logiku za modifikatora
pa da se ispiše točno koji tax je gdje primjenjen i kako itd. Negdje sam ušao malo dublje ne gdje minimalno, ali mislim da je ovo sasvim dovoljno za pokazati.

Što se tiče riješenja za "proizvoda po cijeni, nazivu i kategoriji te sortiranje proizvoda po cijeni i po nazivu silazno/uzlazno"
to sam za potrebe ovog zadatka napravio pomoću sql upita, iako bi ja to rađe sa elasticom.

Logika za ordere je sljedeća:

- Imamo 3 koncepta "određivanje cijene", "popusti" i "porezi" koji se izračunavaju po tom slijedu
- Sve se obračunava na ordere itemima, a na orderu se samo sve zbroji.
- 
Koraci izračuna na order itemu:
- Na order itemu se prvo sprema price i to je onaj sirovi price iz proizvoda
- zatim se detektira pravi price koji može biti iz samog "proizvoda", "contract_list" ili "price_list" i on se spremi u "price_adjusted"
u istom koraku se spremi i "order_item_price" da kod ispisa stavki ordera i cijene na računu možemo prikazati kako smo došli do te prikazane cijene
- nakon toga se izračuna subtotal koji je "price_adjusted" * "quantity"
- sljedeći korak je izračun popusta, imamo dvije vrste popusta "per_item" i "global" koji se zapisuju u "discountItem" i "discountGlobal"
Popusti su tako razdijeljeni jer ponekad se globalni popust prikazati posebno negdje na računu

Ako se primijeni globalni porez on se razdijeli na sve order iteme i tamo spremi, a ne na samom order

Isto tako se radi sa porezima, jer se može desiti da neki porez ide samo na pojedine kategorije proizvoda

U aplikacijie je dodan jedan globalni popust koji daje 10$ ako subtotal premašuje 100$ i jedan per item koji daje 10% na monitor kategorije proizvoda
- Oba dva poreza se zbrajaju u getteru getDiscount()
- Predzadnji korak je izračuna poreza, koji se račune iz taxBase koji je $this->getSubtotal() + $this->getDiscount();
- Zadnji korak je računanje totala koji customer plaća

- modifikatori za tax i discount mogu se dodati prema interfejsu.


### Routes

#### 0. Login:

```
curl --location --request GET 'http://localhost:5004/api/login_check' \
    --header 'Content-Type: application/json' \
    --data-raw '{
    "username" : "admin@example.com",
    "password" : "secret"
}'
```

#### 1. Napraviti rutu za izlistavanje svih proizvoda s Paginacijom.

ruta GET->"/api/products", test "PaginateProductsTest.php" -> samo paginacija bez filtera i sortiranja

#### 2. Napraviti rutu za izlistavanje proizvoda unutar kategorije s paginacijom.
ruta GET->"/api/category/{category}/products", test "PaginateProductsTest.php" -> samo paginacija bez filtera i sortiranja

#### 3. Napraviti rutu za jedan proizvod.
ruta GET->"/api/products/{id}", test "PaginateProductsTest.php"

#### 4. Napraviti rutu za filtriranje proizvoda po cijeni, nazivu i kategoriji te sortiranje proizvoda po cijeni i po nazivu silazno/uzlazno. (Pripaziti na contract i price liste.)
ruta GET->"/api/v2/products", test "PaginateV2ProductsTest.php"

```
curl --location 'http://localhost:5004/api/v2/products?itemsPerPage=10&page=28&sorts[][price_adjusted]=asc' \
    --header 'Content-Type: application/json' \
    --header 'Authorization: Bearer [TOKEN]' \
    --header 'Accept: application/vnd.api+json'
```

For other possible filters and sorts check "api_documentation"

#### 5. Napraviti rutu za dodavanje nove narudžbe koja prima array od proizvoda.
ruta POST->"/api/orders", test "CreateOrderActionTest.php"


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

#### 6. Rješenje mora dobro i brzo funkcionirati s 5 proizvoda te sa cca 20k proizvoda s n milijuna varijacija cijena za proizvode.
RADI

-------------------


07 GOTOVO RJEŠENJE

Kad je sve napravljeno izvršeno, poslati source-code, i izvezenu bazu podataka.
DONE

Bilo bi poželjno poslati i Postman kolekciju. Postman preporučamo kao alat za testiranje ruta. 
DOCUMENTACIJA kako pozivati rute se generira u folderu "api_documentation" iz testova

Poželjno je napisati testove za rute (ali nije nužno).
DONE