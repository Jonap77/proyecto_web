<?php
/* ==============================================
   BLOQUE DE VISTA (MANEJO DE GET)
   ============================================== */

// (CAMBIO) Incluimos la conexión ÚNICA Y SIMPLE
include_once 'db_connect.php'; 

$is_editing = false;
$product_to_edit = null;

// (U) Comprobar si estamos en modo "Editar" (vía URL)
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = $_GET['id'];
    // Esta consulta busca solo en la tabla 'products' principal
    $query = "SELECT * FROM products WHERE id = $1"; 
    pg_prepare($dbconn, "get_product", $query);
    $result = pg_execute($dbconn, "get_product", array($id));
    
    if ($product = pg_fetch_assoc($result)) {
        $is_editing = true;
        $product_to_edit = $product;
    }
}

// (R) LEER: Obtener todos los productos para la cuadrícula de admin
// (IMPORTANTE) Esto solo muestra productos de la tabla 'products' principal,
// lo cual es correcto, ya que solo esos puedes editar/eliminar.
$query_all = "SELECT id, name, price, description, image_url FROM products ORDER BY id ASC";
$result_all = pg_query($dbconn, $query_all);

// El resto de tu HTML (<body>, <header>, <main>, etc.) no cambia.
// El modal con el 'span id="modal-source"' TAMPOCO cambia.
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Tienda (AJAX)</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="admin-body">

    <header class="admin-header">
        <h1>🛒 Gestionar Mi Tienda</h1>
    </header>

    <main class="admin-main">

        <div class="admin-card form-card">
            <h2><?php echo $is_editing ? '✏️ Editando Artículo' : '➕ Publicar Nuevo Artículo'; ?></h2>
            
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
                
                <label for="price">Precio:</label>
                <input type="number" id="price" name="price" step="0.01" min="0" 
                       value="<?php echo $is_editing ? $product_to_edit['price'] : ''; ?>" required>
                
                <label for="image_url">URL de Imagen:</label>
                <input type="text" id="image_url" name="image_url" 
                       value="<?php echo $is_editing ? htmlspecialchars($product_to_edit['image_url']) : ''; ?>" placeholder="https://via.placeholder.com/200">
                
                <button type="submit" class="btn <?php echo $is_editing ? 'btn-update' : 'btn-create'; ?>">
                    <?php echo $is_editing ? 'Actualizar Artículo' : 'Publicar Artículo'; ?>
                </button>
                
                <?php if ($is_editing): ?>
                    <a href="admin.php" class="btn btn-cancel">Cancelar</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="admin-card list-card">
            <h2>Artículos Publicados</h2>

<div class="search-container">
    <form id="search-form">
        <input type="text" id="search-input" name="query" placeholder="Buscar por nombre...">
        <button type="submit" class="btn btn-search">🔍</button>
    </form>
</div>

<div id="search-status" style="display: none;"></div>
            <div id="status-message" style="display: none;"></div>

            <div id="product-grid-admin">
                <?php
                if (pg_num_rows($result_all) > 0):
                    while ($product = pg_fetch_assoc($result_all)):
                        // Usar una imagen placeholder si la URL está vacía
                        $image_url = !empty($product['image_url']) ? htmlspecialchars($product['image_url']) : 'https://via.placeholder.com/200?text=Sin+Imagen';
                ?>
                
                <div class="product-card-admin" id="product-card-<?php echo $product['id']; ?>">
                    <img src="<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p class="description"><?php echo htmlspecialchars($product['description']); ?></p>
                    <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                    
                    <div class="admin-actions">
                        <a href="admin.php?action=edit&id=<?php echo $product['id']; ?>" class="btn btn-edit">Editar</a>
                        <button class="btn btn-delete" data-id="<?php echo $product['id']; ?>">
                            Eliminar
                        </button>
                    </div>
                </div>

                <?php
                    endwhile;
                else:
                    echo "<p>Aún no has publicado ningún artículo.</p>";
                endif;
                ?>
            </div> </div>
    </main>
    
    <?php
    pg_close($dbconn);
    ?>

    
<div id="product-modal-backdrop" class="modal-backdrop">
   <div id="product-modal" class="modal-content">
    <span id="modal-close-btn" class="modal-close">&times;</span>
    
    <h2 id="modal-search-title" class="modal-search-title">Resultados de la Búsqueda</h2>
    
    <div id="modal-results-list" class="modal-results-list">
        </div>
</div>
        
        <div class="modal-body">
            <div class="modal-image-container">
            </div>
           
        </div>
    </div>
</div>

<script src="admin.js"></script>
</body>
</html>