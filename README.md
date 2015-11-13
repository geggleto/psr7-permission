# psr7-permission

## How It Works

This middleware will help you maintain a set of URI's that your users can be registered to
This middleware will throw exceptions if there is a problem or the user does not have permission

## API

1. Permission:getPermissions($key)

    Returns a user's permitted routes
 
2. Permission:setPermissions($key, $routes)

    Update a user's permitted routes
 
3. Permission:addRoute($uri)

    Add a system level route
 
4. Permission:getRoutes()

    Get all system level routes
    
5. Permission:migrate()

    Setup the database tables
 
 
 
