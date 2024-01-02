# SQuery

SQL Syntax Generator for PHP

# Introduction

Orignally made for [User Synthetics](https://github.com/ucscode/user-synthetics), SQuery has progressed to the point of becoming an independent PHP library that simplifies the process of generating SQL syntax for your database queries. With sQuery, you can build SQL queries using an intuitive and fluent interface, reducing the need to write SQL directly. It supports various SQL operations like `SELECT`, `FROM`, `JOIN`, `WHERE`, `GROUP BY`, and more.

The SQuery class provides a simplified and efficient way to interact with databases in PHP, specifically by easing the CRUD (Create, Read, Update, Delete) operations.

## Features

- SQuery ensures the proper ordering of your SQL syntax, even if the methods are not called in order.
- SQuery supports nearly all functions related to SQL keywords.
- Build SQL queries in a structured and readable manner.
- Supports common SQL operations such as `SELECT`, `FROM`, `JOIN`, `WHERE`, `GROUP BY`, `ORDER BY`, and `LIMIT`.
- Generate complex queries with ease by chaining methods.
- Designed to improve code maintainability and reduce the risk of SQL injection.

## Requirements

- PHP 8.1 or higher.
- A compatible database (e.g., MySQL, MariaDB) and appropriate database extensions (e.g., MySQLi, PDO) to execute the generated queries.

## Installation

```php
composer require ucscode/squery
```
   
### SELECT SQL EXAMPLE

```php
use Ucscode\SQuery\SQuery;
use Ucscode\SQuery\Condition;

$squery = new SQuery();

$condition = new Condition();
$condition
   ->add("u.vendor", "ucscode")
   ->and("u.namespace", "SQuery")
   ->or("u.foundation", "Uss%", 'RLIKE')
   ;

$squery
   ->select("u.username")
   ->from("tablename", "u")
   ->where($condition)
   ->limit(2)
   ->groupBy('u.id', 'DESC')
   ;
   
echo $squery->build();
```

### INSERT SQL EXAMPLE

```php
$data = [
   'username' => 'Ucscode', 
   'password' => '12345',
   'role' => 'SUPER_ADMIN'
];

$squery = new SQuery();
$squery->insert('tablename', $data);
```

### UPDATE SQL EXAMPLE

```php
$data = [
   'username' => 'User Synthetics', 
   'password' => '54321',
   'role' => 'PROJECT'
];

$condition = new Condition();
$condition
   ->add("user_id", 1)
   ->and('role', 'SUPER_ADMIN')
   ->or('username', 'spider-man', 'NOT')
   ->and('finance', null, 'IS NOT')
   ;

$squery = new SQuery();
$squery
   ->update('tablename', $data)
   ->where($condition);
```

### DELETE SQL EXAMPLE

5. To generate a DELETE query, use the `delete()` method:

```php
$squery = new SQuery();

$condition = new Condition();
$condition->add("username", "trouble-maker");

$squery
   ->delete()
   ->from('tablename')
   ->where($condition);
```

## Warning!

It is important to note that the SQuery library does not automatically sanitize user input. When using the library, it is crucial to sanitize any user-supplied data before passing it as input to the SQuery methods.

## Note

Please note that the `SQuery` class only generates SQL query strings; it does not execute them against a database. To execute these queries, you would need to establish a database connection and use appropriate methods from the MySQLi or PDO libraries.

## License

This project is licensed under the [MIT License](LICENSE).
