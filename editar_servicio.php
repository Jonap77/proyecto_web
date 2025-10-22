<?php
$conn = mysqli_connect("localhost", "root", "ulloa@123", "barberia");

if (isset($_POST['id_servicio'])) {
    $id_servicio = $_POST['id_servicio'];
    $tipo = mysqli_real_escape_string($conn, $_POST['tipo']);
    $precio = (float) $_POST['precio'];
    $descripcion = mysqli_real_escape_string($conn, $_POST['descripcion']);

    $sql = "UPDATE servicio SET tipo='$tipo', precio=$precio, descripcion='$descripcion' WHERE id_servicio=$id_servicio";

    if (mysqli_query($conn, $sql)) {
        // Redirecciona con confirmación
        header("Location: agregar_servicio.php");
        exit();
    } else {
        echo "❌ Error al actualizar: " . mysqli_error($conn);
    }
}
?>

