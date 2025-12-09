<?php
// api_manager.php
session_start();
include_once 'db_connect.php'; 

function send_json_response($status, $message, $data = []) {
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
    exit;
}

$dbconn = connect_main_db(); 

if (!$dbconn) {
    send_json_response('error', 'Error de conexión BD.');
}

if (!isset($_POST['action'])) {
    send_json_response('error', 'Acción no especificada.');
}

$action = $_POST['action'];
// Obtenemos la tabla vinculada a la sesión actual
$products_table = isset($_SESSION['products_table_name']) ? $_SESSION['products_table_name'] : null;

try {
    switch ($action) {
        
        // --- LOGICA DE COMPRA (NUEVO) ---
        case 'checkout':
            if (!$products_table) send_json_response('error', 'Sesión inválida.');
            
            // Recibimos el carrito como JSON string y lo convertimos
            $cart_items = json_decode($_POST['cart_data'], true);

            if (empty($cart_items)) {
                send_json_response('error', 'Carrito vacío.');
            }

            // Iniciar Transacción (Todo o nada)
            pg_query($dbconn, "BEGIN");

            foreach ($cart_items as $item) {
                $id = intval($item['id']);
                $qty_to_buy = intval($item['qty']);

                // 1. Verificar stock actual (Bloqueamos la fila 'FOR UPDATE' para evitar condiciones de carrera)
                $query_check = "SELECT stock, name FROM {$products_table} WHERE id = $1 FOR UPDATE";
                $res_check = pg_query_params($dbconn, $query_check, [$id]);
                $product_data = pg_fetch_assoc($res_check);

                if (!$product_data) {
                    pg_query($dbconn, "ROLLBACK");
                    send_json_response('error', "El producto ID $id no existe.");
                }

                if ($product_data['stock'] < $qty_to_buy) {
                    pg_query($dbconn, "ROLLBACK");
                    send_json_response('error', "Stock insuficiente para: " . $product_data['name']);
                }

                // 2. Descontar Inventario
                $query_update = "UPDATE {$products_table} SET stock = stock - $1 WHERE id = $2";
                if (!pg_query_params($dbconn, $query_update, [$qty_to_buy, $id])) {
                    pg_query($dbconn, "ROLLBACK");
                    send_json_response('error', "Error al actualizar DB.");
                }
            }

            pg_query($dbconn, "COMMIT"); // Confirmar cambios
            send_json_response('success', 'Venta realizada. Inventario actualizado.');
            break;

        // --- CRUD ESTÁNDAR (Actualizado con Stock) ---
        case 'create':
            if (!$products_table) send_json_response('error', 'Acceso denegado.');
            // Incluye 'stock'
            $query = "INSERT INTO {$products_table} (name, description, price, stock, image_url) VALUES ($1, $2, $3, $4, $5)";
            pg_query_params($dbconn, $query, [$_POST['name'], $_POST['description'], $_POST['price'], $_POST['stock'], $_POST['image_url']]);
            send_json_response('success', 'Producto creado.');
            break;

        case 'update':
            if (!$products_table) send_json_response('error', 'Acceso denegado.');
            // Incluye 'stock'
            $query = "UPDATE {$products_table} SET name = $1, description = $2, price = $3, stock = $4, image_url = $5 WHERE id = $6";
            pg_query_params($dbconn, $query, [$_POST['name'], $_POST['description'], $_POST['price'], $_POST['stock'], $_POST['image_url'], $_POST['id']]);
            send_json_response('success', 'Producto actualizado.');
            break;

        case 'delete':
            if (!$products_table) send_json_response('error', 'Acceso denegado.');
            pg_query_params($dbconn, "DELETE FROM {$products_table} WHERE id = $1", [$_POST['id']]);
            send_json_response('success', 'Producto eliminado.');
            break;

        // --- BÚSQUEDA (Sin cambios mayores, solo agrega stock al select) ---
        case 'search':
            $term = '%' . $_POST['query'] . '%';
            $sql = "
                (SELECT id, name, price, stock, 'tienda' as _source_db FROM products WHERE name ILIKE $1)
                UNION ALL
                (SELECT id, name, price, stock, 'tienda_sucursal_b' as _source_db FROM products_sucursal_b WHERE name ILIKE $1)
                ORDER BY name ASC
            ";
            $result = pg_query_params($dbconn, $sql, [$term]);
            if (pg_num_rows($result) > 0) {
                send_json_response('success', 'Encontrado', pg_fetch_all($result));
            } else {
                send_json_response('error', 'No encontrado.');
            }
            break;

        default:
            send_json_response('error', 'Acción no válida.');
    }

} catch (Exception $e) {
    if ($action === 'checkout') pg_query($dbconn, "ROLLBACK");
    send_json_response('error', 'Error Servidor: ' . $e->getMessage());
} finally {
    pg_close($dbconn);
}
?>