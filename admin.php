<?php
// admin.php
session_start();

// Verificar sesión
if (!isset($_SESSION['user_id']) || !isset($_SESSION['products_table_name'])) {
    header('Location: login.php');
    exit;
}

include_once 'db_connect.php'; 

// Obtener la tabla correspondiente al usuario (products o products_sucursal_b)
$products_table = $_SESSION['products_table_name'];
$dbconn = connect_main_db(); 

if (!$dbconn) {
    die("Error: No se pudo conectar a la base de datos.");
}

$is_editing = false;
$product_to_edit = null;

// (U) Editar: Cargar datos existentes
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM {$products_table} WHERE id = $1"; 
    pg_prepare($dbconn, "get_product", $query);
    $result = pg_execute($dbconn, "get_product", array($id));
    
    if ($product = pg_fetch_assoc($result)) {
        $is_editing = true;
        $product_to_edit = $product;
    }
}

// (R) LEER: Obtener productos incluyendo la columna STOCK
$query_all = "SELECT id, name, price, description, image_url, stock FROM {$products_table} ORDER BY id ASC";
$result_all = pg_query($dbconn, $query_all);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punto de Venta - <?php echo ($products_table === 'products') ? 'Sucursal A' : 'Sucursal B'; ?></title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="admin-body">

    <header class="admin-header">
        <h1>🛒 POS: <?php echo ($products_table === 'products') ? 'Sucursal A' : 'Sucursal B'; ?></h1>
        <a href="logout.php" class="btn btn-delete" style="float: right;">Cerrar Sesión</a>
    </header>

    <main class="admin-main">

        <div class="admin-card form-card">
            <h2><?php echo $is_editing ? '✏️ Editar Producto' : '➕ Nuevo Producto'; ?></h2>
            
            <form id="admin-form" method="POST">
                <?php if ($is_editing): ?>
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo $product_to_edit['id']; ?>">
                <?php else: ?>
                    <input type="hidden" name="action" value="create">
                <?php endif; ?>

                <label for="name">Nombre:</label>
                <input type="text" id="name" name="name" 
                       value="<?php echo $is_editing ? htmlspecialchars($product_to_edit['name']) : ''; ?>" required>
                
                <label for="description">Descripción:</label>
                <textarea id="description" name="description"><?php echo $is_editing ? htmlspecialchars($product_to_edit['description']) : ''; ?></textarea>
                
                <div style="display: flex; gap: 15px;">
                    <div style="flex: 1;">
                        <label for="price">Precio ($):</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" 
                               value="<?php echo $is_editing ? $product_to_edit['price'] : ''; ?>" required>
                    </div>
                    <div style="flex: 1;">
                        <label for="stock">Stock Inicial:</label>
                        <input type="number" id="stock" name="stock" step="1" min="0" 
                               value="<?php echo $is_editing ? $product_to_edit['stock'] : '10'; ?>" required>
                    </div>
                </div>

                <label for="image_url">URL de Imagen:</label>
                <input type="text" id="image_url" name="image_url" 
                       value="<?php echo $is_editing ? htmlspecialchars($product_to_edit['image_url']) : ''; ?>" placeholder="https://via.placeholder.com/200">
                
                <button type="submit" class="btn <?php echo $is_editing ? 'btn-update' : 'btn-create'; ?>">
                    <?php echo $is_editing ? 'Actualizar' : 'Guardar'; ?>
                </button>
                 <?php if ($is_editing): ?>
                    <a href="admin.php" class="btn btn-cancel">Cancelar</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="admin-card list-card">
            <h2>Inventario Disponible</h2>

            <div class="search-container">
                <form id="search-form">
                    <input type="text" id="search-input" name="query" placeholder="Buscar...">
                    <button type="submit" class="btn btn-search">🔍</button>
                </form>
            </div>
            
            <div id="status-message" style="display: none;"></div>

            <div id="product-grid-admin">
                <?php
                if (pg_num_rows($result_all) > 0):
                    while ($product = pg_fetch_assoc($result_all)):
                        $image_url = !empty($product['image_url']) ? htmlspecialchars($product['image_url']) : 'https://via.placeholder.com/200';
                        $stock = intval($product['stock']);
                        $isOutOfStock = $stock <= 0;
                ?>
                
                <div class="product-card-admin <?php echo $isOutOfStock ? 'out-of-stock' : ''; ?>" 
                     id="product-card-<?php echo $product['id']; ?>"
                     data-id="<?php echo $product['id']; ?>"
                     data-name="<?php echo htmlspecialchars($product['name']); ?>"
                     data-price="<?php echo $product['price']; ?>"
                     data-stock="<?php echo $stock; ?>">
                     
                    <img src="<?php echo $image_url; ?>" alt="Producto">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    
                    <p style="margin: 0 15px; color: #555;">
                        Stock: <strong class="stock-val"><?php echo $stock; ?></strong>
                    </p>
                    <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                    
                    <button class="btn btn-cart-add" <?php echo $isOutOfStock ? 'disabled' : ''; ?>>
                        <i class="fas fa-cart-plus"></i> <?php echo $isOutOfStock ? 'Agotado' : 'Agregar'; ?>
                    </button>

                    <div class="admin-actions">
                        <a href="admin.php?action=edit&id=<?php echo $product['id']; ?>" class="btn btn-edit">Editar</a>
                        <button class="btn btn-delete" data-id="<?php echo $product['id']; ?>">Eliminar</button>
                    </div>
                </div>

                <?php
                    endwhile;
                else:
                    echo "<p>No hay productos en inventario.</p>";
                endif;
                ?>
            </div> 
        </div>
    </main>

    <div id="cart-float-btn" class="cart-float-btn">
        <i class="fas fa-shopping-cart"></i>
        <span id="cart-count">0</span>
    </div>

    <div id="cart-modal-backdrop" class="modal-backdrop">
        <div class="modal-content" style="max-width: 600px;">
            <span id="cart-close-btn" class="modal-close">&times;</span>
            <h2 class="modal-search-title">🛒 Carrito de Compras</h2>
            
            <div style="padding: 20px;">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cant.</th>
                            <th>Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="cart-table-body">
                        </tbody>
                </table>
                
                <div class="cart-footer">
                    <h3>Total: <span id="cart-grand-total">$0.00</span></h3>
                    <button id="btn-checkout" class="btn btn-create btn-checkout">
                        ✅ Pagar y Descontar Stock
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="product-modal-backdrop" class="modal-backdrop">
       <div id="product-modal" class="modal-content">
            <span id="modal-close-btn" class="modal-close">&times;</span>
            <h2 id="modal-search-title" class="modal-search-title">Resultados</h2>
            <div id="modal-results-list" class="modal-results-list"></div>
        </div>
    </div>

    <?php pg_close($dbconn); ?>
    <script src="admin.js"></script>
</body>
</html>