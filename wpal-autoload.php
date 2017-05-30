<?php
/*
Plugin Name: Wp Autoload with Namespaces
Plugin URI: https://github.com/mrimran/WpAutoload
Description: Plugin to making loading PHP files easier via autoload instead of using include or require. It also supports to create instances of classes using fully-classified names of the class.
Version: 1.0.5
Author: Imran Zahoor
Author URI: http://imranzahoor.wordpress.com/
License: A "Slug" license name e.g. GPL2
*/

$wpalClassInstances = [];

/**
 * Dynamically loads the class attempting to be instantiated elsewhere in the
 * plugin.
 *
 * @package WpAutoload
 */
spl_autoload_register( 'wpal_load' );

/**
 * Dynamically loads the class attempting to be instantiated elsewhere in the
 * plugin by looking at the $class_name parameter being passed as an argument.
 *
 * The argument should be in the form: TestProduct\Namespace. The
 * function will then break the fully-qualified class name into its pieces and
 * will then build a file to the path based on the namespace.
 *
 * The namespaces in this plugin map to the paths in the directory structure.
 *
 * @param string $className The fully-qualified name of the class to load.
 */
function wpal_load($className ) {

    if ( false === strpos( $className, '\\' ) ) {//ensure namespace is requested
        return;
    }

    // Split the class name into an array to read the namespace and class.
    $fileParts = explode( '\\', $className );
    //print_r($fileParts);

    // Do a reverse loop through $file_parts to build the path to the file.
    $namespace = '';
    $fileName = '';
    $fileNameLower = '';
    for ( $i = count( $fileParts ) - 1; $i >= 0; $i-- ) {
        // Read the current component of the file part.
        $current = $fileParts[ $i ];

        // If we're at the first entry, then we're at the filename.
        if ( count( $fileParts ) - 1 === $i ) {
            $fileName = "$current.php";
            $fileNameLower = strtolower($fileName);
        } else {
            $namespace = '/' . $current . $namespace;
        }
    }

    // Now build a path to the file using mapping to the file location.
    $filepath = $filepathLower = trailingslashit( dirname( dirname( __FILE__ ) ) . $namespace );
    $filepath .= $fileName;

    $filepathLower .= $fileNameLower;


    // If the file exists in the specified path, then include it.
    if ( file_exists( $filepath ) ) {
        include_once( $filepath );
    } elseif( file_exists($filepathLower) ) {
        include_once( $filepathLower );
    } else {
        wp_die(
            esc_html( "The file attempting to be loaded at $filepath does not exist." )
        );
    }
}

/**
 * First checks if the instance already created if not creates instance of the class and saves it for later use,
 * it'll never create two instances of the same class.
 * @param string $className Fully qualified name of the class
 * @param array $args Arguments of the class Optional
 * @return mixed
 */
function wpal_create_single_instance( $className, $args=[] ) {
    global $wpalClassInstances;
    wpal_load( $className );

    if(!$wpalClassInstances[$className]) {
        return $wpalClassInstances[$className] = new $className(...$args);
    } else {
        return $wpalClassInstances[$className];
    }
}

/**
 * Always create new instance of the class without checking if exists or not.
 * @param string $className
 * @param array $args
 * @return mixed
 */
function wpal_create_new_instance( $className, $args=[] ) {
    global $wpalClassInstances;
    wpal_load( $className );

    return $wpalClassInstances[$className] = new $className(...$args);
}

/**
 * First checks if the instance already which may have been created via wpal_create_single_instance.
 * If not creates instance of the class but does not save it.
 * @param string $className Fully qualified name of the class
 * @param array $args Arguments of the class Optional
 * @return mixed
 */
function wpal_create_instance( $className, $args=[] ) {
    global $wpalClassInstances;
    wpal_load( $className );

    if(isset($wpalClassInstances[$className]) && !empty($wpalClassInstances[$className])) {
        return new $className(...$args);
    } else {
        return $wpalClassInstances[$className];
    }
}
