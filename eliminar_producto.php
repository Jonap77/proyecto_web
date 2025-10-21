<?php
$conexion = new mysqli('localhost', 'root', 'ulloa@123', 'barberia');
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['eliminar'])) {
        // Datos para eliminar
        $id_producto = $_POST['id_producto'] ?? '';
        $id_inventario = $_POST['id_inventario'] ?? '';

        if ($id_producto === '') {
            die("El ID de producto es obligatorio para eliminar.");
        }
        if ($id_inventario === '') {
            die("El ID de inventario es obligatorio para eliminar.");
        }

        // Eliminar inventario primero
        $sqlInv = "DELETE FROM inventario WHERE id_inventario = ? AND id_producto = ?";
        $stmtInv = $conexion->prepare($sqlInv);
        $stmtInv->bind_param('ss', $id_inventario, $id_producto);

        if (!$stmtInv->execute()) {
            die("Error al eliminar inventario: " . $stmtInv->error);
        }
        $stmtInv->close();

        // Luego eliminar producto
        $sqlProd = "DELETE FROM producto WHERE id_producto = ?";
        $stmtProd = $conexion->prepare($sqlProd);
        $stmtProd->bind_param('s', $id_producto);

        if (!$stmtProd->execute()) {
            die("Error al eliminar producto: " . $stmtProd->error);
        }
        $stmtProd->close();

        header("Location: Producto.php");
        exit();
    }

    // Si no es eliminar, continúa con tu código original

    if (isset($_POST['agregar'])) {
        // Datos producto
        $id_producto = $_POST['id_producto'] ?? '';
        $nombre = $_POST['nombre_producto'] ?? '';
        $precio = $_POST['precio_producto'] ?? '';
        $descripcion = $_POST['descripcion_producto'] ?? '';

        // Datos inventario
        $id_inventario = $_POST['id_inventario'] ?? '';
        $fecha_alta = $_POST['fecha_alta'] ?? null;
        $fecha_compra = $_POST['fecha_compra'] ?? null;
        $cantidad_producto = $_POST['cantidad_producto'] ?? 0;
        $total_producto = $_POST['total_producto'] ?? 0;

        // Validaciones básicas
        if ($id_producto === '' || $nombre === '' || $precio === '' || $descripcion === '') {
            die("Por favor, completa todos los campos obligatorios del producto.");
        }
        if ($id_inventario === '') {
            die("El ID de inventario es obligatorio.");
        }

        // Verificar si el producto ya existe
        $stmtCheck = $conexion->prepare("SELECT COUNT(*) FROM producto WHERE id_producto = ?");
        $stmtCheck->bind_param("s", $id_producto);
        $stmtCheck->execute();
        $stmtCheck->bind_result($existeProducto);
        $stmtCheck->fetch();
        $stmtCheck->close();

        if ($existeProducto > 0) {
            // Ya existe → ACTUALIZAR
            $sqlUpdateProd = "UPDATE producto SET nombre = ?, precio = ?, descripcion = ? WHERE id_producto = ?";
            $stmtProd = $conexion->prepare($sqlUpdateProd);
            $stmtProd->bind_param('sdss', $nombre, $precio, $descripcion, $id_producto);

            if (!$stmtProd->execute()) {
                die("Error al actualizar producto: " . $stmtProd->error);
            }
            $stmtProd->close();

            $sqlUpdateInv = "UPDATE inventario SET fecha_alta = ?, fecha_compra = ?, cantidad_producto = ?, total_producto = ? 
                             WHERE id_inventario = ? AND id_producto = ?";
            $stmtInv = $conexion->prepare($sqlUpdateInv);
            $stmtInv->bind_param('ssisss', $fecha_alta, $fecha_compra, $cantidad_producto, $total_producto, $id_inventario, $id_producto);

            if (!$stmtInv->execute()) {
                die("Error al actualizar inventario: " . $stmtInv->error);
            }
            $stmtInv->close();
        } else {
            // NO existe → INSERTAR (usa tu código original sin tocarlo)
            $sqlProd = "INSERT INTO producto (id_producto, nombre, precio, descripcion) VALUES (?, ?, ?, ?)";
            $stmtProd = $conexion->prepare($sqlProd);
            $stmtProd->bind_param('ssds', $id_producto, $nombre, $precio, $descripcion);

            if (!$stmtProd->execute()) {
                die("Error al insertar producto: " . $stmtProd->error);
            }
            $stmtProd->close();

            $sqlInv = "INSERT INTO inventario (id_inventario, fecha_alta, fecha_compra, cantidad_producto, total_producto, id_producto) 
                       VALUES (?, ?, ?, ?, ?, ?)";
            $stmtInv = $conexion->prepare($sqlInv);
            $stmtInv->bind_param('sssdis', $id_inventario, $fecha_alta, $fecha_compra, $cantidad_producto, $total_producto, $id_producto);

            if (!$stmtInv->execute()) {
                die("Error al insertar inventario: " . $stmtInv->error);
            }
            $stmtInv->close();
        }

        header("Location: Producto.php");
        exit();
    }
}
?>












