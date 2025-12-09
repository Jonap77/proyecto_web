// admin.js - Versión Corregida y Depurada

document.addEventListener('DOMContentLoaded', () => {
    console.log("DOM Cargado. Iniciando Admin JS...");

    // ==========================================
    // 1. REFERENCIAS AL DOM (ELEMENTOS HTML)
    // ==========================================
    const form = document.getElementById('admin-form');
    const productGrid = document.getElementById('product-grid-admin'); 
    const statusMessage = document.getElementById('status-message');

    // Elementos del Carrito
    const cartFloatBtn = document.getElementById('cart-float-btn');
    const cartCountLabel = document.getElementById('cart-count');
    const cartModalBackdrop = document.getElementById('cart-modal-backdrop');
    const cartCloseBtn = document.getElementById('cart-close-btn');
    const cartTableBody = document.getElementById('cart-table-body');
    const cartGrandTotalLabel = document.getElementById('cart-grand-total');
    const btnCheckout = document.getElementById('btn-checkout');

    // Verificar si los elementos existen para evitar errores
    if (!cartFloatBtn || !productGrid) {
        console.error("Error Crítico: No se encontraron elementos ID en el HTML. Revisa admin.php");
        return; // Detener ejecución si falta algo vital
    }

    // Estado del Carrito
    let cart = []; 

    // ==========================================
    // 2. FUNCIONES DEL CARRITO
    // ==========================================

    function addToCart(id, name, price, maxStock) {
        console.log(`Intentando agregar: ${name} (ID: ${id})`);
        
        // Validar datos numéricos
        price = parseFloat(price);
        maxStock = parseInt(maxStock);

        const existingItem = cart.find(item => item.id === id);

        if (existingItem) {
            if (existingItem.qty >= maxStock) {
                alert(`No puedes agregar más. Solo hay ${maxStock} en inventario.`);
                return;
            }
            existingItem.qty++;
        } else {
            if (maxStock <= 0) {
                alert("Producto agotado.");
                return;
            }
            cart.push({ id, name, price, qty: 1, maxStock });
        }
        
        updateCartUI();
        showStatusMessage('success', `${name} agregado al carrito.`);
    }

    function updateCartUI() {
        const totalItems = cart.reduce((sum, item) => sum + item.qty, 0);
        
        // Actualizar contador
        if (cartCountLabel) cartCountLabel.textContent = totalItems;
        
        // Animación pequeña
        cartFloatBtn.style.transform = "scale(1.2)";
        setTimeout(() => cartFloatBtn.style.transform = "scale(1)", 200);
        
        console.log("Carrito actualizado:", cart);
    }

    function renderCartModal() {
        if (!cartTableBody) return;
        
        cartTableBody.innerHTML = '';
        let grandTotal = 0;

        if (cart.length === 0) {
            cartTableBody.innerHTML = '<tr><td colspan="4" style="text-align:center; padding: 20px;">El carrito está vacío.</td></tr>';
        } else {
            cart.forEach((item, index) => {
                const total = item.price * item.qty;
                grandTotal += total;
                
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${item.name}</td>
                    <td>
                        <button type="button" class="btn-qty" data-action="decrease" data-index="${index}">-</button>
                        <span style="margin:0 5px;">${item.qty}</span>
                        <button type="button" class="btn-qty" data-action="increase" data-index="${index}">+</button>
                    </td>
                    <td>$${total.toFixed(2)}</td>
                    <td><button type="button" class="btn-remove" data-index="${index}" style="color:red;border:none;background:none;cursor:pointer">🗑️</button></td>
                `;
                cartTableBody.appendChild(row);
            });
        }
        
        if (cartGrandTotalLabel) cartGrandTotalLabel.textContent = `$${grandTotal.toFixed(2)}`;
    }

    // ==========================================
    // 3. LISTENERS (MANEJADORES DE EVENTOS)
    // ==========================================

    // A. Click en la Cuadrícula de Productos (Delegación de eventos)
    productGrid.addEventListener('click', (e) => {
        // Buscamos si el clic fue dentro de un botón de agregar
        const btnAdd = e.target.closest('.btn-cart-add');
        
        if (btnAdd) {
            e.preventDefault(); // Evitar cualquier comportamiento raro
            e.stopPropagation(); // Evitar que el clic suba
            
            // Si está deshabilitado, no hacer nada
            if (btnAdd.disabled) return;

            const card = btnAdd.closest('.product-card-admin');
            
            // Obtener datos
            const id = card.dataset.id;
            const name = card.dataset.name;
            const price = card.dataset.price;
            const stock = card.dataset.stock;

            addToCart(id, name, price, stock);
            return;
        }

        // Buscamos si el clic fue en eliminar producto (CRUD)
        if (e.target.classList.contains('btn-delete')) {
            e.preventDefault();
            if (!confirm('¿Estás seguro de eliminar este producto?')) return;
            
            const btn = e.target;
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', btn.dataset.id);
            
            fetch('api_manager.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'success') {
                        btn.closest('.product-card-admin').remove();
                        showStatusMessage('success', 'Eliminado correctamente');
                    } else {
                        alert(data.message);
                    }
                })
                .catch(err => console.error(err));
        }
    });

    // B. Click en el Botón Flotante del Carrito (Abrir Modal)
    cartFloatBtn.addEventListener('click', (e) => {
        e.preventDefault();
        console.log("Abriendo carrito...");
        renderCartModal();
        cartModalBackdrop.style.display = 'flex'; // Mostrar modal
    });

    // C. Click en Cerrar Modal
    if (cartCloseBtn) {
        cartCloseBtn.addEventListener('click', () => {
            cartModalBackdrop.style.display = 'none';
        });
    }

    // D. Click DENTRO del Modal del Carrito (Botones + / - / Eliminar)
    if (cartTableBody) {
        cartTableBody.addEventListener('click', (e) => {
            const btn = e.target.closest('button');
            if (!btn) return;

            const index = parseInt(btn.dataset.index);
            
            if (btn.classList.contains('btn-remove')) {
                cart.splice(index, 1);
                updateCartUI();
                renderCartModal();
            } 
            else if (btn.dataset.action === 'increase') {
                const item = cart[index];
                if (item.qty < item.maxStock) {
                    item.qty++;
                    updateCartUI();
                    renderCartModal();
                } else {
                    alert(`Tope de stock alcanzado (${item.maxStock})`);
                }
            } 
            else if (btn.dataset.action === 'decrease') {
                const item = cart[index];
                item.qty--;
                if (item.qty <= 0) cart.splice(index, 1);
                updateCartUI();
                renderCartModal();
            }
        });
    }

    // E. Botón Pagar (Checkout)
    if (btnCheckout) {
        btnCheckout.addEventListener('click', async () => {
            if (cart.length === 0) return alert("El carrito está vacío.");
            
            if (!confirm("¿Confirmar compra? Se actualizará el inventario.")) return;

            const formData = new FormData();
            formData.append('action', 'checkout');
            formData.append('cart_data', JSON.stringify(cart));

            try {
                const response = await fetch('api_manager.php', { method: 'POST', body: formData });
                const result = await response.json();

                if (result.status === 'success') {
                    alert(result.message);
                    cart = [];
                    updateCartUI();
                    cartModalBackdrop.style.display = 'none';
                    window.location.reload(); // Recargar para ver stock actualizado
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error("Error en checkout:", error);
                alert("Error de conexión al pagar.");
            }
        });
    }

    // F. Formulario de Crear/Editar
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            try {
                const response = await fetch('api_manager.php', { method: 'POST', body: formData });
                const result = await response.json();
                if (result.status === 'success') {
                    // Recargar quitando parámetros URL para limpiar
                    window.location.href = window.location.pathname + "?status=" + formData.get('action') + "d";
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) { console.error(error); }
        });
    }

    // Utilidad: Mensajes
    function showStatusMessage(type, message) {
        if (!statusMessage) return;
        statusMessage.className = type === 'success' ? 'success-message' : 'error-message';
        statusMessage.textContent = message;
        statusMessage.style.display = 'block';
        setTimeout(() => { statusMessage.style.display = 'none'; }, 3000);
    }
});