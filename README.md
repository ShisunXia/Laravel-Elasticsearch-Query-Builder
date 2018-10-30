# Laravel Elasticsearch Query Builder
> Query Elasticsearch by using Eloquent

This Laravel package is developed to simplify the process of querying Elasticsearch. Eloquent is a powerful tool to access and manipulate data in RDB. However, it is not designed to query no-sql DB like Elasticsearch. This package is made to fill the gap between the most popular ORM in Laravel and Elasticsearch.

The package only relies on the official [PHP Elasticsearch package(v6)](https://github.com/elastic/elasticsearch-php). Besides that, [elasticquent/elasticquent](https://github.com/elasticquent/Elasticquent) is highly recommended for query validation purpose. 
## Installation

* Add the following to your ``composer.json`` file:
    ```sh
    "shisun/laravel-elasticsearch-query-builder": "dev-master"
    ```
* Run ``composer update`` in terminal

* Add the package to your ``app.php`` file in the config folder:

    ```php
    'providers' => [
        ...
        Shisun\LaravelElasticsearchQueryBuilder\LaravelElasticsearchQueryBuilderServiceProvider::class,
    ]
    ```

* If your Elasticsearch is not accessible at ``localhost:9200`` then you have to publish the config file by running:
    ```sh
    $ php artisan vendor:publish --provider="Shisun\LaravelElasticsearchQueryBuilder\LaravelElasticsearchQueryBuilderServiceProvider"
    ```
* (Optional) Change the ``ES_HOSTS`` in config/laravel-elasticsearch-query-builder.php to the address of your Elasticsearch

* Add ``EsTrait`` to your model
    ```php
    use Shisun\LaravelElasticsearchQueryBuilder\EsTrait;
    
    class User extends Model
    {
        use EsTrait;
    }
    ```

## Usage example

## Release History


## Meta

Shisun(Leo) Xia - shisun.xia@hotmail.com

Distributed under the GNU V3 license. See ``LICENSE`` for more information.

[https://github.com/ShisunXia/Laravel-Elasticsearch-Query-Builder](https://github.com/ShisunXia/Laravel-Elasticsearch-Query-Builder)


<!-- Markdown link & img dfn's -->
