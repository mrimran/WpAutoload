# A plugin to write better plugin code (Written for Developers by a Developer)

A Plugin that is going facilitate use of Namespaces and making it easier to load files using Namespaces in other plugins.
It makes possible to get rid of `include_once, require_once, include and require` as all of these methods make your code 
look uglier and it becomes difficult to maintain that code. As to include other classes or functions properly you need 
to append some paths and then have to mention the file name with the exact path.

This plugin needs you freedom of writing your code and organizing it based on your needed directory structure. 

## Guidlines
The only guidelines you need to follow are below:
 - Namespace your classes and functions which you are separating so anything placed in 
 `wp-content/plugins/TestPlugin/inc` should be namespaced as `TestPlugin\inc`. 
 Whereas anything placed in `wp-content/plugins/TestPlugin/Inc` would be namespaced as `TestPlugin\Inc`.
 - Filename should be same as of class name, but case doesn't matter because if Class name is 
 `Test` and the file name is `test.php`, the autoloader would still be able to load the correct file.

## Usage
Now it's time to see how it'll help you to maintain your code if you are following the above guidelines to maintain 
your code via `Namespaces`.

In your main plugin file add below line:
```
include_once(WP_PLUGIN_DIR . "/wp-autoload/wp-autoload.php" );
```

Code to include classes and functions
```
wpal_load(PluginNamespace\SubNamespace\ClassName);

//or in case calling from the code which comes in same namespace use the below code
wpal_load(ClassName::class);

//now after using the above code the class or fuction is now included and can be called or used
$classObj = new ClassName($param1, $param2);
```

Even without using the above code you can directly create instances of the classes, as the plugin would automatically 
include those files and will return the instance.

```
/* wpal_create_single_instance: will only create a single instance of the class and save it for later use so this will 
 * make sure only one instance of the class is created.
 */
wpal_create_single_instance(PluginNamespace\SubNamespace\ClassName);
//or depending on your namespace
wpal_create_single_instance(ClassName::class);

/* wpal_create_instance: will first check if the instance is already created by wpal_single_create_instance, 
 * if found would use that otherwise would create new one.
 */
wpal_create_instance(PluginNamespace\SubNamespace\ClassName);
//or depending on your namespace
wpal_create_instance(ClassName::class);

/* wpal_new_instance: will always create new instance of the class. 
 */
wpal_new_instance(PluginNamespace\SubNamespace\ClassName);
//or depending on your namespace
wpal_new_instance(ClassName::class);

//So now you don't need to write $classObj = new ClassName(), you can just use those methods to create it.
```
## Installation
- Download the plugin and rename it to `wp-autoload` or `WpAutoload` then place it in `wp-content/plugins/` folder.
- Login in to your admin and enable the plugin to use it.
