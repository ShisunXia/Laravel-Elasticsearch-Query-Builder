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

## Usage
### Table of Contents
* [Query Clauses](#query-clauses)
    * where
    * orWhere
    * whereMatch
    * orWhereMatch
    * whereHas
    * whereHasNull
    * orWhereHas
    * whereIn
    * orWhereIn
    * whereNotIn
    * orWhereNotIn
    * whereBetween
    * orWhereBetween
* [Order](#order)
    * orderBy
* Pagination
    * page
    * limit
    * offset
* Aggregation
    * aggregate
    * aggregateAll
    * aggregateOn
    * groupBy
    * min
    * max
    * avg
    * sum
* Other Filters
    * minScore
    * with
    * withOut
* Execute Query
    * get
    * count
    * first
    * find
    * delete
* Results Manipulation
    * toArray
    * toEloquent
    * rawResults
    * aggregations
    * getAggregationBuckets
    * paginate
    * getTotal
* Direct Query Output
    * getQuery
    * getBody
    * getAggs
* Init & Config
    * __construct
    * setOptions
    * getters
      * getIndexName
      * getTypeName
      * getValidation
    * setters
      * setIndexName
      * setTypeName
      * setValidation

### Query Clauses

#### where
* Parameters

   | Name     | Required | Type                    | Default   | Description                                           |
   |:--------:|:--------:|:-----------------------:|:---------:|:-----------------------------------------------------:|
   | column   | Y        | ``callable``,``string`` |           |                                                       |
   | operator |          | ``string``              | ``null``  | ``=``,``>``,``<``,``<=``,``>=``,``like``,``!=``,``*`` |
   | value    |          | ``mixed``               | ``null``  |                                                       |
   | or       |          | ``bool``                | ``false`` |                                                       |
   | boost    |          | ``bool``,``int``        | ``false`` | The weight of the column                              |
* Output
   
   ``self``

* Examples
   1. ``=`` can be ignored
      ```php
      User::es()->where('id', 1)->first()
      ```
   2. ``column`` can be a function
      ```php
      User::es()->where(function($q) {
          $q->orWhere(...)->orWhere(...);
      })->get()->toArray()
      ```

#### orWhere

* Parameters

   | Name     | Required | Type                    | Default   | Description                                           |
   |:--------:|:--------:|:-----------------------:|:---------:|:-----------------------------------------------------:|
   | column   | - [x]    | ``callable``,``string`` |           |                                                       |
   | operator |          | ``string``              | ``null``  | ``=``,``>``,``<``,``<=``,``>=``,``like``,``!=``,``*`` |
   | value    |          | ``mixed``               | ``null``  |                                                       |
   | boost    |          | ``bool``,``int``        | ``false`` | The weight of the column                              |
* Output
   
   ``self``

* Examples
   1. ``=`` can be ignored
      ```php
      User::es()->orWhere('id', 1)->first()
      ```
   2. ``column`` can be a function
      ```php
      User::es()->orWhere(function($q) {
          $q->where(...)->where(...);
      })->limit(1)->get()->toArray()
      ```
      
#### whereMatch
* It is used to make fuzzy text search. This function should only be applied on text fields.
* Parameters

   | Name     | Required | Type                    | Default   | Description                                           |
   |:--------:|:--------:|:-----------------------:|:---------:|:-----------------------------------------------------:|
   | column   | - [x]    | ``string``              |           |                                                       |
   | value    |          | ``mixed``               | ``null``  |                                                       |
   | boost    |          | ``array``               | []        | ``match``query options. Check elasticsearch Docs for references |
* Output
   
   ``self``

* Examples
   1. without option
      ```php
      User::es()->whereMatch('email', 'shisun@')->first()
      ```
   2. ``column`` can be a function
      ```php
      User::es()->whereMatch('email', 'shisun@', [
             'query' => 'this will be overrided by $value',
             'operator' => 'and',
             'zero_terms_query' => 'all'
           ])->first()
      ```
#### whereBetween
* Parameters

   | Name     | Required | Type                    | Default   | Description                                           |
   |:--------:|:--------:|:-----------------------:|:---------:|:-----------------------------------------------------:|
   | column   | Y        | ``string``              |           |                                                       |
   | from     |          | ``numeric``             | ``null``  | ``from`` and ``to`` cannot be both ``null``           |
   | to       |          | ``numeric``             | ``null``  |                                                       |
* Output
   
   ``self``

* Examples
   1. basic example
      ```php
      User::es()->whereBetween('id', 1, 5)->first()
      ```

#### orWhereBetween
* Parameters

   | Name     | Required | Type                    | Default   | Description                                           |
   |:--------:|:--------:|:-----------------------:|:---------:|:-----------------------------------------------------:|
   | column   | Y        | ``string``              |           |                                                       |
   | from     |          | ``numeric``             | ``null``  | ``from`` and ``to`` cannot be both ``null``           |
   | to       |          | ``numeric``             | ``null``  |                                                       |
* Output
   
   ``self``

* Examples
   1. basic example
      ```php
      User::es()->orWhereBetween('id', 1, 5)->first()
      ```
### Order



## Release History


## Meta

Shisun(Leo) Xia - shisun.xia@hotmail.com

Distributed under the GNU V3 license. See ``LICENSE`` for more information.

[https://github.com/ShisunXia/Laravel-Elasticsearch-Query-Builder](https://github.com/ShisunXia/Laravel-Elasticsearch-Query-Builder)


<!-- Markdown link & img dfn's -->
