<?php
// db_connect.php (Ahora solo contiene la función de conexión, usando tus credenciales)

// (IMPORTANTE): Define la configuración de la base de datos principal
define('DB_HOST', 'localhost');
define('DB_PORT', '5433'); 
define('DB_NAME_MAIN', 'tienda'); 
define('DB_USER', 'postgres');
define('DB_PASSWORD', 'ulloa123'); 

/**
 * Conecta a la base de datos principal ('tienda').
 * Se usa para: 1. Login (buscar admin_users). 2. CRUD (ambas tablas están en 'tienda').
 * @return resource|false La conexión de PostgreSQL o FALSE si falla.
 */
function connect_main_db() {
    $conn_string = "host=" . DB_HOST . " port=" . DB_PORT . 
                   " dbname=" . DB_NAME_MAIN . " user=" . DB_USER . 
                   " password=" . DB_PASSWORD;
    
    $dbconn = pg_connect($conn_string);

    if ($dbconn) {
        pg_set_client_encoding($dbconn, "UTF8");
    }
    
    return $dbconn;
}
?>