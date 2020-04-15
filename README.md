Shop: Categories Service
--
### Installation:

1. Clone the repository:
```
/path/to/project/:$ git clone git@gitlab.com:shop_ecommerce/shop_categories.git
```
2. Open Dockerfile ```/path/to/project/shop_categories/Dockerfile``` and check these arguments if they match your OS architecture (x86 or x64): ```PHALCON_EXT_PATH=php7/64bits```
3. Rename the file “config.example.php” under “app/config” to “config.php” then change the parameters to match your preferences, example:
```php
'database' => [
    'adapter' => 'Mysql',
    'host' => 'network-gateway-ip',
    'port' => 3306,
    'username' => 'mysql-username',
    'password' => 'mysql-password',
    'dbname' => 'shop_products',
    'charset' => 'utf8'
],
'mongodb' => [
    'host' => 'network-gateway-ip',
    'port' => 27017,
    'username' => null,
    'password' => null,
    'dbname' => 'shop_products'
]
```
4. Build image and start containers 
```bash
docker build --rm -t shop/categories:latest . && docker-compose up -d
```
This command will create new containers:
```
shop_categories_categories-sync_1: This will declare a new queue “categories_sync” in RabbitMQ queues list
shop_categories_categories-async_1: This will declare a new queue “categories_async” in RabbitMQ queues list
shop_categories_categories-api_1: This will start a new application server listening on a specific port
shop_categories_categories-unit-test_1: This will run the unit test for this micro-service
```
API service will listen to a specified port in docker-compose file, you can access it by going to this URL: [http://localhost:port](http://localhost:1000)
- The default port value is 1000.
- You can use Postman with the collections provided to test micro-service APIs.

If you want to scale up the workers (sync / async), you can simply run this command:
```bash
docker-compose up --scale categories-{sync/async}=num -d
```
Where “num” is the number of processes to run, {sync/async} is the service which you want to scale up, example:
```bash
docker-compose up --scale categories-async=3 -d
```

---
### Unit test
To run the unit test, just run this command:
```bash
docker-compose up categories-unit-test
```