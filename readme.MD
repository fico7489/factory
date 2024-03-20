# Factory - "Backend Zadatak" - 15.3.2024. - Filip Horvat

## Set up project

1. git clone https://github.com/fico7489/factory
2. cd factory
3. docker compose up -d
4. docker compose exec php sh
5. composer install
6. php bin/console lexik:jwt:generate-keypair
7. php bin/console app:db:fresh
 - NOTE: on first docker up mysql needs 30-60 seconds to be ready
8. php bin/console app:db:seed

YOU ARE READY! CHECK POSTMAN COLLECTION IN THE ROOT OF THE PROJECT! -> Factory.postman_collection.json

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

### Seeding

By default:

```
php bin/console app:db:seed
```

will generate 100 products, 1000 users, 100 user groups, 10 000 contract lists and 10 000 price lists

If you want to test speed with more "products", "price_lists" and "contracts", run following after initial seeding, as many times as you want:

```
APP_DEBUG=0 php -d memory_limit=2G  bin/console app:db:seed --count-product=2000
```

In each call that command will add 2000 product, 200 000 price_lists  and 200 000 contract_lists

Each call is 1 min on my end.

### Fresh DB

After seeding with: 

```
php bin/console app:db:seed
```

you can recreate DB with:

```
php bin/console app:db:fresh
```

and then reseed again a fresh DB

### Debug queries

If you want to see how queries look like check App\Service\Util\Log\DoctrineSqlFormatter


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
pa da se ispiše točno koji tax je gdje primjenjen i kako itd. Negdje sam ušao malo dublje ne gdje minimalno, ali mislim da je ovo sasvim dovoljno.

Što se tiče riješenja za "proizvoda po cijeni, nazivu i kategoriji te sortiranje proizvoda po cijeni i po nazivu silazno/uzlazno"
to sam za potrebe ovog zadatka napravio pomoću sql upita, iako bi ja to rađe sa elasticom.

### Logika za ordere je sljedeća:

- Imamo 3 koncepta "određivanje cijene", "popusti" i "porezi" koji se izračunavaju po tom slijedu
- Sve se obračunava na ordere itemima, a na orderu se samo sve zbroji.

### Koraci izračuna na order itemu:

- Na order itemu se prvo sprema price i to je onaj sirovi price iz proizvoda
- zatim se detektira pravi price koji može biti iz samog "proizvoda", "contract_list" ili "price_list" i on se spremi u "price_adjusted"
- u istom koraku se spremi i "order_item_price" da kod ispisa stavki ordera i cijene na računu možemo prikazati kako smo došli do te prikazane cijene
- nakon toga se izračuna subtotal koji je "price_adjusted" * "quantity"
- sljedeći korak je izračun popusta, imamo dvije vrste popusta "per_item" i "global" koji se zapisuju u "discountItem" i "discountGlobal"
- Popusti su tako razdijeljeni jer ponekad se globalni popust prikazati posebno negdje na računu 
- Ako se primijeni globalni porez on se razdijeli na sve order iteme i tamo spremi, a ne na samom order 
- Isto tako se radi sa porezima, jer se može desiti da neki porez ide samo na pojedine kategorije proizvoda 
- U aplikaciji je dodan jedan globalni popust koji daje 10$ ako subtotal premašuje 100$ i jedan per item koji daje 10% na monitor kategorije proizvoda
- Oba dva poreza se zbrajaju u getteru getDiscount()
- Predzadnji korak je izračuna poreza, koji se računa iz taxBase koji je $this->getSubtotal() + $this->getDiscount();
- Zadnji korak je računanje totala koji customer plaća
- modifikatori za tax i discount mogu se dodati prema interfejsu.

Ako sam nešto zaboravio objasniti pitat ćete me na callu.

