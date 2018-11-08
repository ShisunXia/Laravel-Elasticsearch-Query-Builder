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

* Use with Eloquent Model
   * Add ``EsTrait`` to your model
        ```php
        use Shisun\LaravelElasticsearchQueryBuilder\EsTrait;
        
        class User extends Model
        {
            use EsTrait;
        }
        ```
   * Enable validation(Optional)
     ```php
     use Shisun\LaravelElasticsearchQueryBuilder\EsTrait;
     class User extends Model
     {
        use EsTrait;
        public $mappingProperties;
        public function __construct($attributes = []) {
            parent::__construct($attributes);
        
            $this->mappingProperties = [
                'id' => [
                    'type' => 'integer',
                ],
                'name' => [
                    'type' => 'text',
                        'index' => true,
                ],
                'email' => [
                    'type' => 'keyword'
                ],
                'address' => [
                    'type' => 'text'
                ],
                'float_example' => [
                    'type' => 'float'
                ],
                'multi_fields_example' => [
                    'type' => 'text',
                        'fields' => [
                            'raw' => [
                                'type' => 'keyword'
                            ]
                        ]
                    ],
                'created_at' => [
                    'type' => 'date',
                    'format' => 'yyyy-MM-dd HH:mm:ss',
                ],
                'some_relations' => [
                    'type' => 'nested',
                    'properties' => [
                        ....
                    ]
                ]
            ];
        }
     }   
     ```
   * Set model-level Index and Type name. Add follow two functions to your model.(Optional)
     ```php
     public function getIndexName() {
         return 'users';
     }
     // type name will be set to index name if this function is not defined.
     public function getTypeName() {
         return 'users';
     }
     ```
* Use without Eloquent
  ```php
  use Shisun\LaravelElasticsearchQueryBuilder\LaravelElasticsearchQueryBuilder as Builder;
  $query = (new Builder())->setOptions([
      'index_name' => 'users',
      'type_name'  => 'users'
  ])->where('name', 'Leo')->get()->toArray();
  // or if you only want to generate the query without getting results
  $query = (new Builder())->where('name', 'Leo')->getBody()
  ```
   

## Usage
### Table of Contents
* [Init & Configs](#init-&-configs)
    * [__construct](#__construct)
    * [setOptions](#setoptions)
    * getters
      * getIndexName
      * getTypeName
      * getValidation
      * getMappingProperties
    * setters
      * setIndexName
      * setTypeName
      * setValidation
      * setMappingProperties
* [Query Clauses](#query-clauses)
    * [where](#where)
    * [orWhere](#orwhere)
    * [whereMatch](#wherematch)
    * [orWhereMatch](#)
    * [whereHas](#wherehas)
    * [whereHasNull](#wherehasnull)
    * [orWhereHas](#orwherehas)
    * [whereIn](#wherein)
    * [orWhereIn](#orwherein)
    * [whereNotIn](#wherenotin)
    * [orWhereNotIn](#orwherenotin)
    * [whereBetween](#wherebetween)
    * [orWhereBetween](#orwherebetween)
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

### Init & Configs

#### __construct
* You do not need to use this function if you use Eloquent
* Parameters

   | Name     | Required | Type                    | Default   | Description                                           |
   |:--------:|:--------:|:-----------------------:|:---------:|:-----------------------------------------------------:|
   | model    |          | ``Eloquent``,``null``   | ``null``  |                                                       |
* Output
   
   ``self``

* Examples
   1. ``=`` can be ignored
      ```php
      use Shisun\LaravelElasticsearchQueryBuilder\LaravelElasticsearchQueryBuilder as Builder;
      $builder = new Builder();
      ```
      
#### setOptions
* You do not need to use this function if you use Eloquent
* Parameters

   | Name     | Required | Type                    | Default   | Description                                           |
   |:--------:|:--------:|:-----------------------:|:---------:|:-----------------------------------------------------:|
   | options  |    Y     | ``array``               |           |                                                       |
* Output
   
   ``self``

* Examples
   1. Basic
      ```php
      use Shisun\LaravelElasticsearchQueryBuilder\LaravelElasticsearchQueryBuilder as Builder;
      $builder = (new Builder())->setOptions([
          'index_name' => 'users',
          'type_name'  => 'users',
          'validation' => false,
          'mapping_properties' => [check the example in the installation section]
      ]);
      ```
      

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
   | column   | Y        | ``callable``,``string`` |           |                                                       |
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
   | column   | Y        | ``string``              |           |                                                       |
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
#### whereHas
* Parameters

   | Name     | Required | Type                    | Default   | Description                                           |
   |:--------:|:--------:|:-----------------------:|:---------:|:-----------------------------------------------------:|
   | relation | Y        | ``string``, ``callable``|           | Must be capitalized                                   |
   | closure  |          | ``callable``, ``null``  | ``null``  | ``from`` and ``to`` cannot be both ``null``           |
   | or       |          | ``boolean``             | ``null``  | for internal use. See orWhereHas                      |
   | boost    |          | ``boolean``,``integer`` | ``false`` | Used to adjust the weight of the condition            |
* Output
   
   ``self``

* Examples
   1. basic example
      ```php
      // find all users with active someRelations
      User::es()->whereHas('someRelations', function($q) {
          $q->where('status', 'active');
      })->first();
      ```
   2. first parameter can be a function
      ```php
      // find all users with either active someRelations or red someOtherRelations
      // Note: the parent 'whereHas' is interchangable with 'where' clause
      User::es()->whereHas(function($q) {
          $q->orWhereHas('someRelations', function($k) {
               $k->where('status', 'active');
          })->orWhereHas('someOtherRelations', function($k) {
               $k->where('color', 'red');
          });
      })->first();
      ```
      
#### orWhereHas
* Parameters

   | Name     | Required | Type                    | Default   | Description                                           |
   |:--------:|:--------:|:-----------------------:|:---------:|:-----------------------------------------------------:|
   | relation | Y        | ``string``, ``callable``|           | Must be capitalized                                   |
   | closure  |          | ``callable``, ``null``  | ``null``  | ``from`` and ``to`` cannot be both ``null``           |
   | boost    |          | ``boolean``,``integer`` | ``false`` | Used to adjust the weight of the condition            |
* Output
   
   ``self``

* Examples
   1. basic example
      ```php
      // find all users with either active someRelations or red someOtherRelations
      // Note: the parent 'whereHas' is interchangable with 'where' clause
      User::es()->whereHas(function($q) {
          $q->orWhereHas('someRelations', function($k) {
               $k->where('status', 'active');
          })->orWhereHas('someOtherRelations', function($k) {
               $k->where('color', 'red');
          });
      })->first();
      ```
      

#### whereHasNull
* Parameters

   | Name     | Required | Type                    | Default   | Description                                           |
   |:--------:|:--------:|:-----------------------:|:---------:|:-----------------------------------------------------:|
   | relation | Y        | ``string``, ``callable``|           | Must be capitalized                                   |

* Output
   
   ``self``

* Examples
   1. basic example
      ```php
      // find all users with no someRelation
      User::es()->whereHasNull('someRelations')->first();
      ```
#### orWhereHasNull
* Parameters

   | Name     | Required | Type                    | Default   | Description                                           |
   |:--------:|:--------:|:-----------------------:|:---------:|:-----------------------------------------------------:|
   | relation | Y        | ``string``, ``callable``|           | Must be capitalized                                   |

* Output
   
   ``self``

* Examples
   1. basic example
      ```php
      // find all users either with no someRelation or named as 'Leo'
      User::es()->where(function($q) {
          $q->orWhereHasNull('someRelations')->orWhere('name', 'Leo');
      })->first();
      ```
      
#### whereIn
* Parameters

   | Name     | Required | Type                    | Default   | Description                                           |
   |:--------:|:--------:|:-----------------------:|:---------:|:-----------------------------------------------------:|
   | column   | Y        | ``string``              |           |                                                       |
   | values   |    Y     | ``array``               |           |                                                       |
* Output
   
   ``self``

* Examples
   1. basic example
      ```php
      // find all users with pending or active status
      User::es()->whereIn('status', ['active', 'pending'])->get();
      ```
      
#### orWhereIn
* Parameters

   | Name     | Required | Type                    | Default   | Description                                           |
   |:--------:|:--------:|:-----------------------:|:---------:|:-----------------------------------------------------:|
   | column   | Y        | ``string``              |           |                                                       |
   | values   |    Y     | ``array``               |           |                                                       |
* Output
   
   ``self``

* Examples
   1. basic example
      ```php
      // find all users with either pending/active status or with name Leo
      User::es()->where(function($q) {
          $q->orWhereIn('status', ['active', 'pending'])->orWhere('name', 'Leo');
      })->get();
      ```

#### whereNotIn
* Parameters

   | Name     | Required | Type                    | Default   | Description                                           |
   |:--------:|:--------:|:-----------------------:|:---------:|:-----------------------------------------------------:|
   | column   | Y        | ``string``              |           |                                                       |
   | values   |    Y     | ``array``               |           |                                                       |
* Output
   
   ``self``

* Examples
   1. basic example
      ```php
      // find all users that are not in pending or active status
      User::es()->whereNotIn('status', ['active', 'pending'])->get();
      ```
      
#### orWhereNotIn
* Parameters

   | Name     | Required | Type                    | Default   | Description                                           |
   |:--------:|:--------:|:-----------------------:|:---------:|:-----------------------------------------------------:|
   | column   | Y        | ``string``              |           |                                                       |
   | values   |    Y     | ``array``               |           |                                                       |
* Output
   
   ``self``

* Examples
   1. basic example
      ```php
      // find all users with either not in pending/active status or with name Leo
      User::es()->where(function($q) {
          $q->orWhereNotIn('status', ['active', 'pending'])->orWhere('name', 'Leo');
      })->get();
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

#### orderBy
* Parameters

   | Name     | Required | Type                    | Default   | Description                                           |
   |:--------:|:--------:|:-----------------------:|:---------:|:-----------------------------------------------------:|
   | column   | Y        | ``string``              |           |                                                       |
   | order    |          | ``numeric``             | ``asc``   | either ``asc`` or ``desc``                            |
   | script   |          | ``array``,``boolean``   | ``false`` |                                                       |
* Output
   
   ``self``

* Examples
   1. basic example
      ```php
      User::es()->orderBy('id', 'desc')->get()
      ```
   2. script
      ```php
      // If the category of the item is Laptop then use discount_price for ordering. Otherwise, use listing_price.
      Item::es()->orderBy('id', 'desc', 
                  ['lang' => 'painless', 'source' => "if(doc['category'].value == 'Laptops') {return doc['discount_price'].value;} else {return doc['listing_price'].value;}"])
            ->get()
      ```


## Release History


## Meta

Shisun(Leo) Xia - shisun.xia@hotmail.com

Distributed under the GNU V3 license. See ``LICENSE`` for more information.

[https://github.com/ShisunXia/Laravel-Elasticsearch-Query-Builder](https://github.com/ShisunXia/Laravel-Elasticsearch-Query-Builder)


<!-- Markdown link & img dfn's -->
