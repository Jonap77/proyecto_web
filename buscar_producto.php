<?php
$conn = new mysqli('localhost', 'root', 'ulloa@123', 'barberia');
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$q = $_GET['q'] ?? '';

if ($q === '') {
    // Si no hay búsqueda, mostrar todos los productos con inventario
    $sql = "SELECT i.id_inventario, i.fecha_alta, i.fecha_compra, i.cantidad_producto, i.total_producto,
                   p.id_producto, p.nombre, p.precio, p.descripcion
            FROM inventario i
            INNER JOIN producto p ON i.id_producto = p.id_producto";
    
    $stmt = $conn->prepare($sql);
} else {
    // Búsqueda con filtro
    $sql = "SELECT i.id_inventario, i.fecha_alta, i.fecha_compra, i.cantidad_producto, i.total_producto,
                   p.id_producto, p.nombre, p.precio, p.descripcion
            FROM inventario i
            INNER JOIN producto p ON i.id_producto = p.id_producto
            WHERE p.nombre LIKE CONCAT('%', ?, '%') OR p.id_producto LIKE CONCAT('%', ?, '%')";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $q, $q);
}

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['id_producto']) . "</td>";
    echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
    echo "<td>" . htmlspecialchars($row['precio']) . "</td>";
    echo "<td>" . htmlspecialchars($row['descripcion']) . "</td>";
    echo "<td>" . htmlspecialchars($row['id_inventario']) . "</td>";
    echo "<td>" . htmlspecialchars($row['fecha_alta']) . "</td>";
    echo "<td>" . htmlspecialchars($row['fecha_compra']) . "</td>";
    echo "<td>" . htmlspecialchars($row['total_producto']) . "</td>";
    echo "<td>" . htmlspecialchars($row['cantidad_producto']) . "</td>";
    echo "</tr>";
}

$stmt->close();
$conn->close();
?>




