# Laravel Elasticsearch Query Builder
> Query Elasticsearch by using Eloquent

This Laravel package is developed to simplify the process of querying Elasticsearch. Eloquent is a powerful tool to access and manipulate data in RDB. However, it is not designed to query no-sql DB like Elasticsearch. This package is made to fill the gap between the most popular ORM in Laravel and Elasticsearch.

The package only relies on the official [PHP Elasticsearch package(v6)](https://github.com/elastic/elasticsearch-php).

## PHP version
The package is developed and tested under PHP ``v7.1``. It should be also compatible with ``v7.*``. Please email me if you find any compatibility issue.

## Elasticsearch version
The package is developed and tested under Elasticsearch ``v6.*``. It should be also compatible with ``v5.*``. Please email me if you find any compatibility issue. It is confirmed that the package does not support versions before ``v5.*``.

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
    * [orderBy](#orderby)
* [Pagination](#pagination)
    * [page](#page)
    * [limit](#limit)
    * [offset](#offset)
* [Aggregation](#aggregations)
    * [aggregate](#aggregate)
    * [aggregateAll](#aggregateall)
    * [aggregateOn](#aggregateon)
    * [groupBy](#groupby)
    * [min](#min)
    * [max](#max)
    * [avg](#avg)
    * [sum](#sum)
* [Other Filters](#other-filters)
    * [minScore](#minscore)
    * [with](#with)
    * [withOut](#without)
* [Execute Query](#execute-query)
    * [get](#get)
    * [count](#count)
    * [first](#first)
    * [find](#find)
    * [delete](#delete)
* [Results Manipulation](#results-manipulation)
    * [toArray](#toarray)
    * [toEloquent](#toeloquent)
    * [rawResults](#rawresults)
    * [aggregations](#aggregations)
    * [getAggregationBuckets](#getaggregationbuckets)
    * [paginate](#paginate)
* [Direct Query Output](#orwherebetween)
    * [getQuery](#orwherebetween)
    * [getBody](#orwherebetween)
    * [getAggs](#orwherebetween)

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
   1. basic example
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

### Pagination
#### page
* Parameters

   | Name     | Required | Type                    | Default   | Description                                           |
   |:--------:|:--------:|:-----------------------:|:---------:|:-----------------------------------------------------:|
   | page     | Y        | ``integer``             |           |                                                       |
   | per_page | Y        | ``integer``             |           |                                                       |
* Output
   
   ``self``

* Examples
   1. basic example
      ```php
      // get the first page. 25 users per page.
      User::es()->page(1, 25)->get()
      ```
      
#### limit
* Parameters

   | Name     | Required | Type                    | Default   | Description                                           |
   |:--------:|:--------:|:-----------------------:|:---------:|:-----------------------------------------------------:|
   | limit    | Y        | ``integer``             |           |                                                       |
  
* Output
   
   ``self``

* Examples
   1. basic example
      ```php
      // get 25 users.
      User::es()->limit(25)->get()
      ```
      
#### offset
* Parameters

   | Name     | Required | Type                    | Default   | Description                                           |
   |:--------:|:--------:|:-----------------------:|:---------:|:-----------------------------------------------------:|
   | limit    | Y        | ``integer``             |           |                                                       |
  
* Output
   
   ``self``

* Examples
   1. basic example
      ```php
      // skip first 25 users.
      User::es()->offset(25)->get()
      ```
      
### Aggregations
#### aggregate
* Parameters

   | Name     | Required | Type                    | Default   | Description                                           |
   |:--------:|:--------:|:-----------------------:|:---------:|:-----------------------------------------------------:|
   | name     | Y        | ``string``              |           |                                                       |
   | agg      |      Y   | ``callable``            |           |                                                       |
* Output
   
   ``self``

* Examples
   1. basic example
      ```php
      Item::es()->aggregate('group_items_by_categories', function($q) {
          $q->groupBy('category_id');
      })->get()->aggregations();
      ```
   2. with filters
         ```php
         // get all active and pending items. But only aggregate on the filtered items that are also red.
         // Note: aggregate clause is only applied on the filtered items.
         Item::es()->whereIn('status', ['active', 'pending'])->aggregate('categories', function($q) {
             $q->where('color', 'red')->aggregate('group_by', function($k) {
                 $k->groupBy('category_id');
             });
         })->get()->aggregations();
         // this returns
         // [
         //     'categories' => [
         //         'doc_count' => 50,
         //         'group_by'  => [
         //             'doc_count_error_upper_bound' => 0,
         //             'sum_other_doc_count'         => 8,
         //             'buckets' => [
         //                 [
         //                     'key' => 1,
         //                     'doc_count' => 15
         //                 ],
         //                 ...
         //             ]
         //         ]
         //     ]
         //  ]
         // the 'key' in buckets is one of the category_id
         ```
      
#### aggregateAll
* Parameters

   | Name     | Required | Type                    | Default   | Description                                           |
   |:--------:|:--------:|:-----------------------:|:---------:|:-----------------------------------------------------:|
   | name     | Y        | ``string``              |           |                                                       |
   | agg      |      Y   | ``callable``            |           |                                                       |
* Output
   
   ``self``

* Examples
   1. with filters
         ```php
         // get all active and pending items. And aggregate on all red items.
         // Note: aggregateAll clause is applied on all items regardless other queries.
         Item::es()->whereIn('status', ['active', 'pending'])->aggregateAll('categories', function($q) {
             $q->where('color', 'red')->aggregate('group_by', function($k) {
                 $k->groupBy('category_id');
             });
         })->get()->aggregations();
         // this returns
         // [
         //     'categories' => [
         //         'doc_count' => 50,
         //         'group_by'  => [
         //             'doc_count_error_upper_bound' => 0,
         //             'sum_other_doc_count'         => 8,
         //             'buckets' => [
         //                 [
         //                     'key' => 1,
         //                     'doc_count' => 15
         //                 ],
         //                 ...
         //             ]
         //         ]
         //     ]
         //  ]
         // the 'key' in buckets is one of the category_id
         ```
      
#### aggregateOn
* Parameters

   | Name     | Required | Type                    | Default   | Description                                           |
   |:--------:|:--------:|:-----------------------:|:---------:|:-----------------------------------------------------:|
   | relation | Y        | ``string``              |           | Must be capitalized                                   |
   | agg      |      Y   | ``callable``            |           |                                                       |
   |custom_name|         | ``string``              | ``null``  |                                                       |
* Output
   
   ``self``

* Examples
   1. basic example
      ```php
      Item::es()->aggregateOn('someRelations', function($q) {
          $q->min('id');
      })->get()->aggregations();
      // this returns
      // [
      //     'some_relations' => [
      //         'doc_count' => 50,
      //         'min_id'  => [
      //             'value' => 1
      //         ]
      //     ]
      //  ]
      // 'some_relations' will be replaced if custom_name is provided
      ```
         
#### groupBy
* Parameters

   | Name     | Required | Type                    | Default   | Description                                           |
   |:--------:|:--------:|:-----------------------:|:---------:|:-----------------------------------------------------:|
   | column   | Y        | ``string``              |           |                                                       |
   | size     |         | ``integer``              | ``10``    | The limit of number of groups. Default to 10 groups   |
* Output
   
   ``self``

* Check aggregate clause for examples

#### min
* Parameters

   | Name     | Required | Type                    | Default   | Description                                           |
   |:--------:|:--------:|:-----------------------:|:---------:|:-----------------------------------------------------:|
   | column   | Y        | ``string``              |           |                                                       |
   |custom_name|         | ``string``              | ``null``  |                                                       |
* Output
   
   ``self``

* Examples
   1. basic example
      ```php
      Item::es()->min('id')->get()->aggregations();
      // or
      Item::es()->aggregate('test', function($q) {
          $q->min('id');
      })->get()->aggregations();
      // They both return. Note: the 'test' in the second example is ignored.
      // [
      //     'min_id' => [
      //         'value' => 1
      //     ]
      //  ]
      // 'min_id' will be replaced if custom_name is provided. The format of the name is 'min_' + column
      ```
      
#### max
* Parameters

   | Name     | Required | Type                    | Default   | Description                                           |
   |:--------:|:--------:|:-----------------------:|:---------:|:-----------------------------------------------------:|
   | column   | Y        | ``string``              |           |                                                       |
   |custom_name|         | ``string``              | ``null``  |                                                       |
   |missing_value|       | ``numeric``             | ``null``  | This value will be used to replace null values        |
* Output
   
   ``self``

* Examples
   1. basic example
      ```php
      Item::es()->max('id')->get()->aggregations();
      // or
      Item::es()->aggregate('test', function($q) {
          $q->max('id');
      })->get()->aggregations();
      // They both return. Note: the 'test' in the second example is ignored.
      // [
      //     'max_id' => [
      //         'value' => 1
      //     ]
      //  ]
      // 'min_id' will be replaced if custom_name is provided. The format of the name is 'max_' + column
      ```
      
#### avg
* Parameters

   | Name     | Required | Type                    | Default   | Description                                           |
   |:--------:|:--------:|:-----------------------:|:---------:|:-----------------------------------------------------:|
   | column   | Y        | ``string``              |           |                                                       |
   |custom_name|         | ``string``              | ``null``  |                                                       |
   |missing_value|       | ``numeric``             | ``null``  | This value will be used to replace null values        |
* Output
   
   ``self``

* Examples
   1. basic example
      ```php
      Item::es()->avg('id')->get()->aggregations();
      // or
      Item::es()->aggregate('test', function($q) {
          $q->avg('id');
      })->get()->aggregations();
      // They both return. Note: the 'test' in the second example is ignored.
      // [
      //     'avg_id' => [
      //         'value' => 1
      //     ]
      //  ]
      // 'min_id' will be replaced if custom_name is provided. The format of the name is 'avg_' + column
      ```
      
#### sum
* Parameters

   | Name     | Required | Type                    | Default   | Description                                           |
   |:--------:|:--------:|:-----------------------:|:---------:|:-----------------------------------------------------:|
   | column   | Y        | ``string``              |           |                                                       |
   |custom_name|         | ``string``              | ``null``  |                                                       |
   |missing_value|       | ``numeric``             | ``null``  | This value will be used to replace null values        |
* Output
   
   ``self``

* Examples
   1. basic example
      ```php
      Item::es()->sum('id')->get()->aggregations();
      // or
      Item::es()->aggregate('test', function($q) {
          $q->sum('id');
      })->get()->aggregations();
      // They both return. Note: the 'test' in the second example is ignored.
      // [
      //     'sum_id' => [
      //         'value' => 1
      //     ]
      //  ]
      // 'min_id' will be replaced if custom_name is provided. The format of the name is 'sum_' + column
      ```
      
### Other Filters
#### minScore
* Parameters

   | Name     | Required | Type                    | Default   | Description                                           |
   |:--------:|:--------:|:-----------------------:|:---------:|:-----------------------------------------------------:|
   | score    | Y        | ``numeric``             |           | Only get items with _score greater than this value    |
  
* Output
   
   ``self``

* Examples
   1. basic example
      ```php
      User::es()->whereMatch('name', 'Leo', ['operator' => 'and'])->minScore(5)->get()
      ```
      
#### with
* Parameters

   | Name     | Required | Type                    | Default   | Description                                           |
   |:--------:|:--------:|:-----------------------:|:---------:|:-----------------------------------------------------:|
   | relations| Y        | ``list``                |           | include these relations in results. All relations are included by default.|
  
* Output
   
   ``self``

* Examples
   1. basic example
      ```php
      User::es()->with('Addresses', 'Company')->get()
      ```
      
#### withOut
* Parameters

   | Name     | Required | Type                    | Default   | Description                                           |
   |:--------:|:--------:|:-----------------------:|:---------:|:-----------------------------------------------------:|
   | relations| Y        | ``list``                |           | Exclude these relations in results. All relations are included by default.|
  
* Output
   
   ``self``

* Examples
   1. basic example
      ```php
      User::es()->withOut('Addresses', 'Company')->get()
      ```
      
### Execute Query
#### get
#### count
* Output
   
   ``integer``

* Examples
   1. basic example
      ```php
      User::es()->count()
      ```
      
#### first
* Parameters

   | Name     | Required | Type                    | Default   | Description                                           |
   |:--------:|:--------:|:-----------------------:|:---------:|:-----------------------------------------------------:|
   | eloquent |          | ``boolean``             |``false``  | get result in array or Eloquent. Returns array if not provided.|
  
* Output
   
   ``array``|``Eloquent``

* Examples
   1. basic example
      ```php
      User::es()->first()
      ```
      
#### find
* Parameters

   | Name     | Required | Type                    | Default   | Description                                           |
   |:--------:|:--------:|:-----------------------:|:---------:|:-----------------------------------------------------:|
   | key      |  Y       | ``mixed``               |           |                                                       |
  
* Output
   
   ``array``

* Examples
   1. basic example
      ```php
      // get the user with id = 5
      // Note: the key_name can be set by setKeyName or setOptions. The key_name is 'id' by default
      User::es()->find(5)
      ```
#### delete
* Parameters

   | Name     | Required | Type                    | Default   | Description                                           |
   |:--------:|:--------:|:-----------------------:|:---------:|:-----------------------------------------------------:|
   | key      |  Y       | ``mixed``               |           |                                                       |
  
* Output
   
   ``array``

* Examples
   1. basic example
      ```php
      // delete the user with id = 5
      // Note: the key_name can be set by setKeyName or setOptions. The key_name is 'id' by default
      User::es()->delete(5)
      ```

### Results Manipulation    
#### toArray
* Output
   
   ``array``

#### toEloquent
Warning: This function does not work if you don't use the package with Eloquent.
* Output
   
   ``Eloquent``
      
#### rawResults
Get raw results from Elasticsearch
* Output
   
   ``array``

#### aggregations
Get aggregation results
* Output
   
   ``array``
      
#### getAggregationBuckets
This is a helper function to get buckets from the aggregation specified by agg_name
* Parameters

   | Name     | Required | Type                    | Default   | Description                                           |
   |:--------:|:--------:|:-----------------------:|:---------:|:-----------------------------------------------------:|
   | agg_name |  Y       | ``mixed``               |           |                                                       |
   | agg      |          | ``mixed``               |  ``null`` |                                                       |
* Output
   
   ``array``

* Examples
   1. basic example
      ```php
      // if the aggregations are
      // this returns
      // [
      //     'categories' => [
      //         'doc_count' => 50,
      //         'group_by'  => [
      //             'doc_count_error_upper_bound' => 0,
      //             'sum_other_doc_count'         => 8,
      //             'buckets' => [
      //                 [
      //                     'key' => 1,
      //                     'doc_count' => 15
      //                 ],
      //                 ...
      //             ]
      //         ]
      //     ]
      //  ]
      // Then you can use getAggregationBuckets('categories') to get the buckets array
      ```
      
#### paginate
Returns pagination information
* Parameters

   | Name     | Required | Type                    | Default   | Description                                           |
   |:--------:|:--------:|:-----------------------:|:---------:|:-----------------------------------------------------:|
   | per_page |          | ``mixed``               |           | The value can be ignored if per_page is set by limit() or page(). Otherwise, it's required.|
* Output
  
   ``array``
* Example
   1. basic
   ```php
  [
     "pages" => [
       1,
       2,
       3,
       4,
       5,
     ],
     "rows" => 165,
     "active" => 1,
     "totalpages" => 7.0,
     "prev" => false,
     "next" => true,
     "per_page" => 25
  ]
  ```

### Direct Query Output    
#### getQuery
Returns the query part of the body
#### getBody
Returns the body
#### getAggs
Returns the aggregation part of the body 
    
 
## Release History


## Meta

Shisun(Leo) Xia - shisun.xia@hotmail.com

Distributed under the GNU V3 license. See ``LICENSE`` for more information.

[https://github.com/ShisunXia/Laravel-Elasticsearch-Query-Builder](https://github.com/ShisunXia/Laravel-Elasticsearch-Query-Builder)


<!-- Markdown link & img dfn's -->
