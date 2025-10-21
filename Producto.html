


<script>
function validarFormulario() {
            var id = document.getElementsByName("id_producto")[0].value;
            var nombre = document.getElementsByName("nombre_producto")[0].value;
            var precio = document.getElementsByName("precio_producto")[0].value;
            var descripcion = document.getElementsByName("descripcion_producto")[0].value;

            if (id.trim() === "" || nombre.trim() === "" || precio.trim() === "" || descripcion.trim() === "") {
                alert("Todos los campos son obligatorios. Por favor, completa el formulario.");
                return false; // Evita que el formulario se envíe
            }
            return true;
        }




</script>

<script>
function limpiarFormulario() {
    const form = document.querySelector('.formulario-producto');
    const campos = form.querySelectorAll('input:not([type="button"]):not([type="submit"]), textarea');

    let camposVacios = true;

    campos.forEach(campo => {
        if (campo.value.trim() !== '') {
            camposVacios = false;
        }
    });

    if (camposVacios) {
        alert("El formulario ya está vacío.");
        return;
    }

    // Limpiar y desbloquear todos los campos
    campos.forEach(campo => {
        campo.value = '';
        campo.removeAttribute('readonly');
        campo.removeAttribute('disabled');
    });
}
</script>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="preload" href="normalize.css" as="style">
    <link rel="stylesheet" href="normalize.css"><!--aplicacion de normalize-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style_producto.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <title>Productos</title>

  
</head>

<header class = "header">

<div class = "contenedor-header">

<div class="icono-inicio">
        <a href="../Inicio/Inicio.php">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="24" height="24" stroke-width="2">
            <path d="M5 12l-2 0l9 -9l9 9l-2 0"></path>
            <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"></path>
            <path d="M10 12h4v4h-4z"></path>
            </svg>
        </a>
    </div>
</div>

<div class="buscador">
  <form method="POST" action="" class="busca_producto" onsubmit="return false;">
    <input type="text" id="buscador" placeholder="Busca el producto que desees">
    <input class="boton_buscar" type="button" name="buscar" value="Buscar" onclick="buscar()">
  </form>
</div>


</header>


<body>

<div class="contenedor-producto">
<form action="eliminar_producto.php" method="POST" class="formulario-producto" onsubmit="return validarFormulario()">
  <div class="form-grid">

    <div class="contenedor-input">
      <label>ID</label>
      <input type="text" name="id_producto" class="id" placeholder="Id prod">
    </div>

    <div class="contenedor-input">
      <label>Producto</label>
      <input type="text" name="nombre_producto" class="producto" placeholder="Nombre del producto">
    </div>

    <div class="contenedor-input">
      <label>Precio $</label>
      <input type="text" name="precio_producto" class="precio" placeholder="Precio">
    </div>

    <div class="contenedor-input">
      <label>Descripción</label>
      <textarea name="descripcion_producto" class="descripcion" placeholder="Describe tu producto"></textarea>
    </div>

    <div class="contenedor-input">
      <label>ID Inventario</label>
      <input type="text" name="id_inventario" placeholder="ID de inventario">
    </div>

    <div class="contenedor-input">
      <label>Fecha de Alta</label>
      <input type="date" name="fecha_alta">
    </div>

    <div class="contenedor-input">
      <label>Fecha de Compra</label>
      <input type="date" name="fecha_compra">
    </div>

    <div class="contenedor-input">
      <label>Total Producto</label>
      <input type="text" name="total_producto" placeholder="Total del producto">
    </div>

    <div class="contenedor-input">
      <label>Cantidad de Producto</label>
      <input type="number" name="cantidad_producto" placeholder="Cantidad">
    </div>

  </div> <!-- Cierra .form-grid -->

  <!-- Botones al final -->
  <div class="botones">
    <input type="submit" class="agregar" name="agregar" value="Agregar">
    <input type="submit" class="editar" name="editar" value="Editar">
    <input type="submit" class="eliminar" name="eliminar" value="Eliminar">
    <input type="button" class="limpiar" name="limpiar" value="Limpiar" onclick="limpiarFormulario()">



  </div>
</form>

</div><!--Cierra contenedor form-->


<br>
<!--Creacion de tabla -->



<?php
$conexion = mysqli_connect("localhost", "root", "ulloa@123", "barberia");

$consulta = "SELECT p.id_producto, p.nombre, p.precio, p.descripcion, 
                    i.id_inventario, i.cantidad_producto, i.fecha_alta, 
                    i.fecha_compra, i.total_producto
             FROM producto p
             INNER JOIN inventario i ON p.id_producto = i.id_producto";

$resul = mysqli_query($conexion, $consulta);
?>

<table id="tabla">
<thead>
    <tr>
        <td>ID Producto</td>
        <td>Nombre</td>
        <td>Precio $</td>
        <td>Descripción</td>
        <td>Código de Barras</td>
        <td>Fecha de Alta</td>
        <td>Fecha de Compra</td>
        <td>Total Producto Comprado</td>
        <td>Stock Disponible</td>
    </tr>
</thead>

<tbody id="resultados">
<?php while ($mostrar = mysqli_fetch_assoc($resul)): ?>

    <tr>
        <td><?php echo htmlspecialchars($mostrar['id_producto']); ?></td> <!-- ID Producto -->
        <td><strong><?php echo htmlspecialchars($mostrar['nombre']); ?></strong></td> <!-- Nombre -->
        <td><?php echo htmlspecialchars(number_format($mostrar['precio'], 2)); ?></td> <!-- Precio $ -->
        <td><?php echo htmlspecialchars($mostrar['descripcion']); ?></td> <!-- Descripción -->
        <td><strong><?php echo htmlspecialchars($mostrar['id_inventario']); ?></strong></td> <!-- Código de Barras -->
        <td><?php echo htmlspecialchars($mostrar['fecha_alta']); ?></td> <!-- Fecha de Compra -->
        <td><?php echo htmlspecialchars($mostrar['fecha_compra']); ?></td> <!-- Fecha de Alta -->
        <td><strong><?php echo htmlspecialchars($mostrar['total_producto']); ?></strong></td> <!-- Total Producto Comprado -->
        <td><?php echo htmlspecialchars($mostrar['cantidad_producto']); ?></td> <!-- Stock Disponible -->

    </tr>
<?php endwhile; ?>
</tbody>
</table>

</div>


</body>


<script>
const input = document.getElementById('buscador');
const tbody = document.getElementById('resultados');

function cargarDatos(query = '') {
    fetch(`buscar_producto.php?q=${encodeURIComponent(query)}`)
        .then(response => response.text())
        .then(html => {
            tbody.innerHTML = html;
        })
        .catch(error => {
            console.error('Error en búsqueda:', error);
        });
}

// Cargar todos los productos inicialmente
cargarDatos();

input.addEventListener('input', () => {
    const query = input.value.trim();
    cargarDatos(query);
});

</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.querySelector('#resultados');

    const campoID = document.getElementsByName('id_producto')[0];
    const campoNombre = document.getElementsByName('nombre_producto')[0];
    const campoPrecio = document.getElementsByName('precio_producto')[0];
    const campoDescripcion = document.getElementsByName('descripcion_producto')[0];
    const campoInventario = document.getElementsByName('id_inventario')[0];
    const campoFechaAlta = document.getElementsByName('fecha_alta')[0];
    const campoFechaCompra = document.getElementsByName('fecha_compra')[0];
    const campoTotal = document.getElementsByName('total_producto')[0];
    const campoCantidad = document.getElementsByName('cantidad_producto')[0];

    function bloquearCampos() {
        [campoID, campoInventario].forEach(campo => campo.setAttribute('readonly', 'readonly'));
        [campoNombre, campoPrecio, campoDescripcion, campoCantidad, campoFechaCompra, campoFechaAlta, campoTotal].forEach(campo => campo.setAttribute('readonly', 'readonly'));
    }

    function desbloquearCampos() {
        [campoID,campoNombre, campoPrecio, campoDescripcion, campoInventario, campoFechaAlta, campoFechaCompra, campoTotal, campoCantidad].forEach(campo => campo.removeAttribute('readonly'));
        [campoID, campoInventario].forEach(campo => campo.setAttribute('readonly', 'readonly'));
    }

    tbody.addEventListener('click', function (event) {
        const fila = event.target.closest('tr');
        if (!fila) return;

        const celdas = fila.querySelectorAll('td');
        if (celdas.length < 9) return;

        campoID.value = celdas[0].textContent.trim();
        campoNombre.value = celdas[1].textContent.trim();
        campoPrecio.value = celdas[2].textContent.trim();
        campoDescripcion.value = celdas[3].textContent.trim();
        campoInventario.value = celdas[4].textContent.trim();
        campoFechaAlta.value = celdas[5].textContent.trim();
        campoFechaCompra.value = celdas[6].textContent.trim();
        campoTotal.value = celdas[7].textContent.trim();
        campoCantidad.value = celdas[8].textContent.trim();

        bloquearCampos();
    });

    document.querySelector('.editar').addEventListener('click', function (e) {
        e.preventDefault();
        desbloquearCampos();
    });

    document.querySelector('.limpiar').addEventListener('click', function () {
        [campoID, campoNombre, campoPrecio, campoDescripcion, campoInventario, campoCantidad, campoFechaCompra, campoFechaAlta, campoTotal].forEach(campo => campo.value = '');
    });

});




</script>

</html>