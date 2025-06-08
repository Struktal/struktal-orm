# database-objects

This is a PHP library that provides helpful classes and methods for working with objects that are stored in a database.

# Installation

To install this library, include it in your project using Composer:

```bash
composer require struktal/database-objects
```

# Data access object (DAO) pattern

Using the data access object pattern allows you to easily access and manipulate data in a database with PHP objects.

There are so-called "model objects" that represent the data that's being stored in the database, and for each table there is an own model object.

There's also a data access object interface that defines the standard operations that can be performed on the model objects, such as creating, reading and updating entries.
For every model object, there is an own belonging data access object.

# Usage

Before you can use this library, you need to connect it to your database.
You can do this in the startup of your application:

```php
\struktal\DatabaseObjects\Database::connect(
    $host,
    $database,
    $username,
    $password
);
```

Then, you can use the library's features in your code.

## Inheriting from model and data access objects

To prevent you from having to write the same code over and over again, there are classes called `GenericObject` (model object) and `GenericObjectDAO` (data access object interface) that every custom object should extend from within the namespace `\struktal\DatabaseObjects`. The `GenericObject` class already implements the table columns

- `id` (integer) - The unique identifier of the object
- `created` (datetime) - The date and time when the object was created
- `updated` (datetime) - The date and time when the object was last updated

and the `GenericObjectDAO` the standard operations

- `save(GenericObject $object)` to upsert an object's database entry
- `delete(GenericObject $object)` to delete an object's database entry
- `getObject(...)` to get a single object from the database
- `getObjects(...)` to get multiple objects from the database

To set up a new object, you need to create a new class with the same name as the table in the database, and extend it from `\struktal\DatabaseObjects\GenericObject`.
For example, if you have a table called `User`, you would create a class like this:

```php
class User extends \struktal\DatabaseObjects\GenericObject {
    public string $username;
    public string $password;
    
    // Feel free to add getters, setters, and other methods as needed
}
```

Next, you'll also have to create a new DAO class that extends `\struktal\DatabaseObjects\GenericObjectDAO`.
The DAO's class name should be the same as the model object's class name, but with `DAO` appended to it.
For the `User` model object, the DAO class would look like this:

```php
class UserDAO extends \struktal\DatabaseObjects\GenericObjectDAO {
    // Basic DAO methods already implemented in GenericObjectDAO
}
```

If you need methods with custom queries or other non-standard operations for this specific object, you can add them to this DAO class.

The above code allows to access and manipulate the database table called `User` with the following structure:

| `id`  | `username` | `password` | `created`  | `updated`  |
|-------|------------|------------|------------|------------|
| `INT` | `VARCHAR`  | `VARCHAR`  | `DATETIME` | `DATETIME` |

Database tables have to be created manually.
To do so, orientate yourself on this example:

```sql
CREATE TABLE IF NOT EXISTS `User` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `created` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `updated` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## Saving an object to the database

Once you have created the model and DAO classes, crating and updating an object is easy by using the DAO's `save()` method.
It differs between inserting a new entry and updating an existing one by checking if the `id` attribute is set.

```php
$user = new User();
$user->username = "JohnDoe";
$user->password = "SecurePassword123"; // Make sure to hash passwords before saving them!

User::dao()->save($user); // Inserts a new entry into the database

$user->password = "NewSecurePassword456"; // Update the password
User::dao()->save($user); // Updates the existing entry in the database
```

The `save()` method will automatically set the `id`, `created`, and `updated` attributes of the object if a new database entry was inserted.

## Loading objects from the database

To load objects from the database, you can use the DAO's `getObject()` and `getObjects()` methods:

```php
// Get a single object by its ID
$user = User::dao()->getObject([
    "id" => 1
]);

// Get all objects
$users = User::dao()->getObjects();
```

For both methods, you can set the following parameters:

- `filters`: An associative array that contains requirements for the objects that should be returned with the column name as key and the value that the column should have as value
- `orderBy`: A column name that the returned objects should be ordered by
- `orderAsc`: A boolean that indicates whether the objects should be ordered ascending (default) or descending
- `limit`: An integer that limits the number of returned objects (-1 for no limit)
- `offset`: An integer that sets the offset for the returned objects

You can also write more detailed queries by using complex `DAOFilter`s.
They allow you to use other operators than the default `=` operator.
Use it as follows:

```php
$users = User::dao()->getObjects([
    new \struktal\DatabaseObjects\DAOFilter(
        \struktal\DatabaseObjects\DAOFilterType::LIKE,
        "username",
        "John%"
    ),
    new \struktal\DatabaseObjects\DAOFilter(
        \struktal\DatabaseObjects\DAOFilterType::GREATER_THAN,
        "id",
        10
    )
]);
```

This example will return all users whose username starts with "John" and whose ID is greater than 10.

## Deleting an object from the database

To delete an object from the database, you can use the DAO's `delete()` method:

```php
$user = User::dao()->getObject([
    "id" => 1
]);
User::dao()->delete($user);
```

# Dependencies

This library uses the following dependencies:

- **ext-pdo**
- **pest** - GitHub: [pestphp/pest](https://github.com/pestphp/pest), licensed under [MIT license](https://github.com/pestphp/pest/blob/3.x/LICENSE.md)

# License

This software is licensed under the MIT license.
See the [LICENSE](LICENSE) file for more information.
