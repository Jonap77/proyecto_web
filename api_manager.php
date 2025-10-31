<?php
// api_manager.php
// Este archivo SÓLO maneja la lógica y devuelve JSON.

include_once 'db_connect.php';

// Función para enviar una respuesta JSON estándar y salir
function send_json_response($status, $message, $data = []) {
    // Le decimos al cliente que estamos enviando JSON
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
    exit; // Detener el script
}

if (!$dbconn) {
    send_json_response('error', 'Error de conexión a la base de datos.');
}

// Asegurarse de que se envió una acción
if (!isset($_POST['action'])) {
    send_json_response('error', 'Acción no especificada.');
}

$action = $_POST['action'];

try {
    switch ($action) {
        
        // (C) CREAR
        case 'create':
            $query = "INSERT INTO products (name, description, price, image_url) VALUES ($1, $2, $3, $4) RETURNING id";
            pg_prepare($dbconn, "create_product", $query);
            $result = pg_execute($dbconn, "create_product", [
                $_POST['name'], $_POST['description'], $_POST['price'], $_POST['image_url']
            ]);
            $new_product = pg_fetch_assoc($result);
            send_json_response('success', 'Producto creado exitosamente.', ['new_id' => $new_product['id']]);
            break;

        // (U) ACTUALIZAR
        case 'update':
            $query = "UPDATE products SET name = $1, description = $2, price = $3, image_url = $4 WHERE id = $5";
            pg_prepare($dbconn, "update_product", $query);
            pg_execute($dbconn, "update_product", [
                $_POST['name'], $_POST['description'], $_POST['price'], $_POST['image_url'], $_POST['id']
            ]);
            send_json_response('success', 'Producto actualizado exitosamente.', ['updated_id' => $_POST['id']]);
            break;

        // (D) ELIMINAR
        case 'delete':
            $query = "DELETE FROM products WHERE id = $1";
            pg_prepare($dbconn, "delete_product", $query);
            pg_execute($dbconn, "delete_product", [$_POST['id']]);
            send_json_response('success', 'Producto eliminado exitosamente.', ['deleted_id' => $_POST['id']]);
            break;

            case 'search':
            // 1. Obtenemos el término de búsqueda del POST
            $query = $_POST['query'];
            
            // 2. Preparamos el término para una búsqueda 'LIKE' (con comodines %)
            $search_term = '%' . $query . '%';
            
            // 3. Definimos la consulta SQL (aquí está la magia)
            // Usamos UNION ALL para combinar los resultados de ambas tablas
            // Asumimos que 'products_sucursal_b' tiene la misma estructura
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

            // 4. Preparamos y ejecutamos la consulta
            pg_prepare($dbconn, "search_all_nodes", $sql);
            $result = pg_execute($dbconn, "search_all_nodes", [$search_term]);
            
            // 5. Verificamos si hubo resultados
            if (pg_num_rows($result) > 0) {
                // Si hay, los convertimos todos a un array
                $products = pg_fetch_all($result);
                send_json_response('success', 'Productos encontrados.', $products);
            } else {
                // Si no hay, enviamos un error amigable
                send_json_response('error', 'No se encontraron productos que coincidan.');
            }
            break;


            
        
        default:
            send_json_response('error', 'Acción no válida.');
    }

} catch (Exception $e) {
    // Si algo falla en la base de datos
    send_json_response('error', 'Error en la consulta: ' . $e->getMessage());
} finally {
    // Siempre cerrar la conexión
    pg_close($dbconn);
}
?>