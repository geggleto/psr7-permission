# psr7-permission

## Configuration

This Middleware checks to see if a User has permission to access a specific resource.

The Middleware makes the following assumptions:
- That you load the middleware with all of the routes the system is applied to.
- That the user's permission table is loaded into the Requests 'permission' attribute [by default]

If a user attempts to access a resource they are not allowed an Exception is raised.
   
## Usage

```php

//In Slim 3

$permission = new Geggleto\Service\Permission($container['system_routes']);

$app->add($permission);

```
 
 
 
