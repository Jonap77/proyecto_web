<?php
$conn = mysqli_connect("localhost", "root", "ulloa@123", "barberia");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id_servicio'])) {
    $id = (int) $_POST['id_servicio'];
    $query = "DELETE FROM servicio WHERE id_servicio = $id";

    if (mysqli_query($conn, $query)) {
        echo "success";
    } else {
        echo "error";
    }
}
?>

