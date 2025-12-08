<?php
// api_manager.php
// Este archivo SÓLO maneja la lógica y devuelve JSON.

// (NUEVO) Iniciar sesión para obtener la clave de acceso
session_start();

// (NUEVO) Incluimos la conexión y la función de conexión
include_once 'db_connect.php'; 

// Función para enviar una respuesta JSON estándar y salir
function send_json_response($status, $message, $data = []) {
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
    exit;
}

// (NUEVO) Conectar a la base de datos principal
// (NUEVO) Conectar a la base de datos principal
$dbconn = connect_main_db(); 

if (!$dbconn) {
    // CAMBIO TEMPORAL: Muestra el error exacto de PostgreSQL (si está disponible)
    $pg_error = pg_last_error();
    if ($pg_error) {
         send_json_response('error', 'Error de conexión (PostgreSQL): ' . $pg_error);
    } else {
        send_json_response('error', 'Error de conexión a la base de datos.');
    }
}

// Asegurarse de que se envió una acción
if (!isset($_POST['action'])) {
    send_json_response('error', 'Acción no especificada.');
}

$action = $_POST['action'];

// (NUEVO) Lógica para proteger el CRUD
$products_table = null;
if (isset($_SESSION['products_table_name'])) {
    $products_table = $_SESSION['products_table_name'];
}

try {
    switch ($action) {
        
        // (C) CREAR, (U) ACTUALIZAR, (D) ELIMINAR
        case 'create':
        case 'update':
        case 'delete':
            // (NUEVO) Bloquear acceso si no hay sesión
            if (!$products_table) {
                 send_json_response('error', 'Acceso denegado. Por favor, inicie sesión.');
            }
            
            if ($action === 'create') {
                // (CAMBIO CLAVE) Usar la tabla de productos del usuario
                $query = "INSERT INTO {$products_table} (name, description, price, image_url) VALUES ($1, $2, $3, $4) RETURNING id";
                pg_prepare($dbconn, "create_product", $query);
                $result = pg_execute($dbconn, "create_product", [
                    $_POST['name'], $_POST['description'], $_POST['price'], $_POST['image_url']
                ]);
                $new_product = pg_fetch_assoc($result);
                send_json_response('success', 'Producto creado exitosamente.', ['new_id' => $new_product['id']]);
                
            } elseif ($action === 'update') {
                // (CAMBIO CLAVE) Usar la tabla de productos del usuario
                $query = "UPDATE {$products_table} SET name = $1, description = $2, price = $3, image_url = $4 WHERE id = $5";
                pg_prepare($dbconn, "update_product", $query);
                pg_execute($dbconn, "update_product", [
                    $_POST['name'], $_POST['description'], $_POST['price'], $_POST['image_url'], $_POST['id']
                ]);
                send_json_response('success', 'Producto actualizado exitosamente.', ['updated_id' => $_POST['id']]);
                
            } elseif ($action === 'delete') {
                // (CAMBIO CLAVE) Usar la tabla de productos del usuario
                $query = "DELETE FROM {$products_table} WHERE id = $1";
                pg_prepare($dbconn, "delete_product", $query);
                pg_execute($dbconn, "delete_product", [$_POST['id']]);
                send_json_response('success', 'Producto eliminado exitosamente.', ['deleted_id' => $_POST['id']]);
            }
            break;


        case 'search':
            // Esta acción NO requiere estar logueado, solo busca en ambas tablas.
            $query = $_POST['query'];
            $search_term = '%' . $query . '%';
            
            // (SIN CAMBIOS) El 'search' sigue buscando en ambas tablas.
            $sql = "
                (SELECT id, name, description, price, image_url, 'tienda' as _source_db 
                 FROM products 
                 WHERE name ILIKE $1)
                
                UNION ALL
                
                (SELECT id, name, description, price, image_url, 'tienda_sucursal_b' as _source_db 
                 FROM products_sucursal_b
                 WHERE name ILIKE $1)
                
                ORDER BY name ASC
            ";

            pg_prepare($dbconn, "search_all_nodes", $sql);
            $result = pg_execute($dbconn, "search_all_nodes", [$search_term]);
            
            if (pg_num_rows($result) > 0) {
                $products = pg_fetch_all($result);
                send_json_response('success', 'Productos encontrados.', $products);
            } else {
                send_json_response('error', 'No se encontraron productos que coincidan.');
            }
            break;

        default:
            send_json_response('error', 'Acción no válida.');
    }

} catch (Exception $e) {
    send_json_response('error', 'Error en la consulta: ' . $e->getMessage());
} finally {
    pg_close($dbconn);
}
?>