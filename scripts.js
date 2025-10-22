// ==========================================================
// 1. Lógica para el Modal de 'Agregar Nuevo Servicio'
// ==========================================================

function abrirModalServicio() {
    document.getElementById('modal-servicio').style.display = 'block';
    document.getElementById('fondo-modal').style.display = 'block';
}

function cerrarModalServicio() {
    document.getElementById('modal-servicio').style.display = 'none';
    document.getElementById('fondo-modal').style.display = 'none';
}


// ==========================================================
// 2. Lógica para el Modal de 'Editar Servicio'
// ==========================================================

function mostrarEditar(id) {
    // 1. Obtener la tarjeta del servicio usando el ID
    const card = document.getElementById(`servicio-${id}`);

    // 2. Rellenar el formulario del modal de edición
    if (card) {
        // Asignar el ID al campo oculto del formulario de edición
        document.getElementById('edit-id').value = id;

        // Obtener los datos de la tarjeta para rellenar los campos
        const tipo = card.querySelector('h3').innerText;
        // El precio se extrae de la primera etiqueta <p> eliminando el texto y el símbolo '$'
        const precioText = card.querySelector('p').innerText;
        const precio = parseFloat(precioText.replace('Precio: $', '').trim());
        // La descripción es la segunda etiqueta <p>
        const descripcion = card.querySelectorAll('p')[1].innerText;

        document.getElementById('edit-tipo').value = tipo;
        document.getElementById('edit-precio').value = precio;
        document.getElementById('edit-descripcion').value = descripcion;

        // 3. Mostrar el modal de edición
        document.getElementById('modal-editar').style.display = 'block';
    }
}

function cerrarEditar() {
    document.getElementById('modal-editar').style.display = 'none';
}


// ==========================================================
// 3. Lógica para el Modal de 'Eliminar Servicio'
// ==========================================================

let idAEliminar = null;

// Esta función es llamada cuando se presiona "Eliminar" en una tarjeta de servicio
function mostrarEliminar(id) {
    idAEliminar = id;
    document.getElementById("delete-id").value = id;
    document.getElementById("modal-eliminar").style.display = "block";
}

function cerrarEliminar() {
    document.getElementById("modal-eliminar").style.display = "none";
    idAEliminar = null;
}

// Esta función es llamada cuando se hace submit en el formulario de confirmación
function confirmarEliminar() {
    // Si la función se llama desde el botón "Eliminar" de la tarjeta (aunque no está en el HTML)
    // El formulario de eliminación en tu HTML tiene el evento 'onsubmit="return confirmarEliminar()"'.
    // Usamos 'idAEliminar' para obtener el ID guardado.
    
    if (idAEliminar === null) {
        alert("Error: No se ha seleccionado ningún servicio para eliminar.");
        return false;
    }

    const formData = new FormData();
    formData.append("id_servicio", idAEliminar);

    // Enviar solicitud POST a eliminar_servicio.php usando Fetch API (AJAX)
    fetch("eliminar_servicio.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        if (data.trim() === "success") {
            // Eliminar la tarjeta del servicio del DOM si la eliminación fue exitosa
            const card = document.getElementById(`servicio-${idAEliminar}`);
            if (card) card.remove();
            alert("✅ Servicio eliminado correctamente.");
        } else {
            alert("❌ Error al eliminar el servicio. Respuesta del servidor: " + data);
        }
        cerrarEliminar(); // Cerrar el modal de confirmación
    })
    .catch(() => {
        alert("❌ Error de conexión al intentar eliminar el servicio.");
        cerrarEliminar(); // Cerrar el modal en caso de error de red
    });

    // Devolver false para evitar que el formulario se envíe de forma tradicional (ya lo manejamos con fetch)
    return false;
}