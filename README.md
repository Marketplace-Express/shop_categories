Shop: Categories Service
--
### Installation:

1. Clone the repository:
```
git clone git@gitlab.com:shop_ecommerce/shop_categories.git
```

2. Rename the file “config.example.php” under “app/config” to “config.php” then change the parameters to match your preferences, example:
```php
'database' => [
    'adapter' => 'Mysql',
    'host' => 'network-gateway-ip from step 1.b',
    'port' => 3306,
    'username' => 'mysql-username',
    'password' => 'mysql-password',
    'dbname' => 'shop_products',
    'charset' => 'utf8'
],
'mongodb' => [
    'host' => 'network-gateway-ip from step 1.b',
    'port' => 27017,
    'username' => null,
    'password' => null,
    'dbname' => 'shop_products'
]
```
And so on for Redis and RabbitMQ ...
>Note: You can use network (marketplace-network) gateway ip instead of providing each container ip

3. Login to docker registry provider, in order to pull this micro service docker image:
```bash
docker login registry.gitlab.com
```
Provide your user name and password on gitlab, you should have access to the project, so you can pull the image.

4. Pull the docker image from container registry:
```bash
docker pull registry.gitlab.com/shop_ecommerce/shop_products
```
       
5- Run `docker-compose up -d`, This command will create new containers:

1. shop_categories_categories-sync_1:
- This will declare a new queue “categories_sync” in RabbitMQ queues list
2. shop_categories_categories-async_1:
- This will declare a new queue “categories_async” in RabbitMQ queues list
3. shop_categories_categories-api_1:
- This will start a new application server listening on a specific port specified in `docker-compose.yml` file, you can access it by going to this URL: [http://localhost:port](http://localhost:1000)
- As a default, the port value is 1000.
- You can use Postman with the collections provided to test micro service APIs.
4. shop_categories_categories-unit-test_1:
- This will run the unit test for this micro-service

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