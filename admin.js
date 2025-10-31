// admin.js

document.addEventListener('DOMContentLoaded', () => {

    const form = document.getElementById('admin-form');
    // (CAMBIO) Apuntamos al nuevo grid, no al <tbody>
    const productGrid = document.getElementById('product-grid-admin'); 
    const statusMessage = document.getElementById('status-message');

    // (NUEVO) --- Selectores para la Búsqueda y Modal ---
    const searchForm = document.getElementById('search-form');
    const searchInput = document.getElementById('search-input');
    const searchStatus = document.getElementById('search-status');
    const modalBackdrop = document.getElementById('product-modal-backdrop');
    const modalContent = document.getElementById('product-modal');
    const modalCloseBtn = document.getElementById('modal-close-btn');



    // 1. Manejador para CREAR y ACTUALIZAR (sin cambios)
    form.addEventListener('submit', async (e) => {
        e.preventDefault(); 
        const formData = new FormData(form);
        
        try {
            const response = await fetch('api_manager.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();

            if (result.status === 'success') {
                // Redirigir para ver los cambios (simple y efectivo)
                const action = formData.get('action');
                window.location.href = `admin.php?status=${action}d`;
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            console.error('Error en fetch:', error);
            alert('Error de conexión. Revisa la consola (F12).');
        }
    });

    // 2. Manejador para ELIMINAR (actualizado)
    // (CAMBIO) Escuchamos clics en el 'productGrid'
    productGrid.addEventListener('click', async (e) => {
        
        if (e.target.classList.contains('btn-delete')) {
            e.preventDefault(); 

            if (!confirm('¿Estás seguro de que quieres eliminar este artículo?')) {
                return;
            }
            
            const button = e.target;
            const productId = button.dataset.id;
            // (CAMBIO) Buscamos la 'tarjeta' contenedora, no la 'fila' <tr>
            const card = button.closest('.product-card-admin'); 

            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', productId);

            try {
                const response = await fetch('api_manager.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.status === 'success') {
                    // (CAMBIO) Aplicamos la animación a la 'card'
                    card.classList.add('removing'); 
                    setTimeout(() => {
                        card.remove(); // La quitamos del DOM
                        showStatusMessage('success', 'Artículo eliminado exitosamente.');
                    }, 500); 
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error en fetch:', error);
                alert('Error de conexión. Revisa la consola (F12).');
            }
        }
    });

    // (NUEVO) --- Listener para el Formulario de Búsqueda ---
    searchForm.addEventListener('submit', async (e) => {
        e.preventDefault(); // Evitar que la página se recargue
        const query = searchInput.value.trim();
        
        if (query.length === 0) return; // No buscar si está vacío

        const formData = new FormData();
        formData.append('action', 'search');
        formData.append('query', query);

        try {
            const response = await fetch('api_manager.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            // ...dentro de searchForm.addEventListener...
        if (result.status === 'success') {
            // Llama a la nueva función y le pasa el ARRAY COMPLETO
            showResultsModal(result.data); 
            showSearchStatus('success', `Mostrando ${result.data.length} resultados.`);
            searchInput.value = ''; // Limpiar campo
        } else {
// ...
                showSearchStatus('error', result.message);
            }
        } catch (error) {
            console.error('Error en búsqueda:', error);
            showSearchStatus('error', 'Error de conexión al buscar.');
        }
    });

    // (NUEVO) --- Funciones para manejar el Modal ---
   // ELIMINA la función 'showProductModal' entera y AÑADE ESTA:

/**
 * Recibe un ARRAY de productos y los muestra en una tabla dentro del modal.
 * @param {Array} products - El array de productos de la API.
 */
function showResultsModal(products) {
    
    // Apuntamos a los nuevos IDs del modal
    const resultsList = document.getElementById('modal-results-list');
    const modalTitle = document.getElementById('modal-search-title');

    // 1. Limpiar y titular
    resultsList.innerHTML = '';
    modalTitle.textContent = `Resultados de la Búsqueda (${products.length})`;

    // 2. Construir la tabla de resultados
    let htmlContent = '<table class="results-table">';
    htmlContent += '<thead><tr><th>Nombre</th><th>Precio</th><th>Origen</th></tr></thead>';
    htmlContent += '<tbody>';
    
    // 3. Recorrer el array y crear una fila por producto
    products.forEach(product => {
        const price = parseFloat(product.price).toFixed(2);
        
        // Usamos la columna '_source_db' que creaste en la API
        htmlContent += `
            <tr>
                <td>${product.name}</td>
                <td>$${price}</td>
                <td>
                    <span class="source-tag source-${product._source_db}">
                        ${product._source_db}
                    </span>
                </td>
            </tr>
        `;
    });
    
    htmlContent += '</tbody></table>';
    resultsList.innerHTML = htmlContent; // Insertar la tabla en el modal

    // 4. Mostrar el modal (esta lógica es la misma de antes)
    modalBackdrop.style.display = 'flex';
    setTimeout(() => {
        modalContent.classList.add('visible');
        modalBackdrop.classList.add('visible');
    }, 10);
}

// La función 'hideProductModal' puede quedarse con el mismo nombre,
// ya que solo se encarga de cerrar el modal.

    function hideProductModal() {
        // 1. Quitar clases de visibilidad para la animación
        modalContent.classList.remove('visible');
        modalBackdrop.classList.remove('visible');

        // 2. Ocultar el fondo después de que termine la animación
        setTimeout(() => {
            modalBackdrop.style.display = 'none';
        }, 300); // 300ms (debe coincidir con la transición en CSS)
    }

    // (NUEVO) --- Listeners para Cerrar el Modal ---
    modalCloseBtn.addEventListener('click', hideProductModal);
    modalBackdrop.addEventListener('click', (e) => {
        // Si se hace clic en el fondo (backdrop) y NO en el contenido
        if (e.target === modalBackdrop) {
            hideProductModal();
        }
    });

    // (NUEVO) --- Función para mensajes de estado de búsqueda ---
    function showSearchStatus(type, message) {
        searchStatus.className = type === 'success' ? 'success-message' : 'error-message';
        searchStatus.textContent = message;
        searchStatus.style.display = 'block';
        
        setTimeout(() => {
            searchStatus.style.display = 'none';
        }, 3000);
    }

    // Función para mostrar mensajes (sin cambios)
    function showStatusMessage(type, message) {
        statusMessage.className = '';
        statusMessage.classList.add(type === 'success' ? 'success-message' : 'error-message');
        statusMessage.textContent = message;
        statusMessage.style.display = 'block';
        
        setTimeout(() => {
            statusMessage.style.display = 'none';
        }, 3000);
    }
    
    // Mostrar mensajes de URL (sin cambios)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('status')) {
        const status = urlParams.get('status');
        if (status === 'created') {
            showStatusMessage('success', 'Artículo publicado exitosamente.');
        } else if (status === 'updated') {
            showStatusMessage('success', 'Artículo actualizado exitosamente.');
        }
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});