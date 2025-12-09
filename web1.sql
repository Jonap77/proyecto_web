-- TABLA DE USUARIOS Y SU ASIGNACIÓN DE SUCURSAL
CREATE TABLE admin_users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    -- Almacena el hash seguro de la contraseña
    password_hash VARCHAR(255) NOT NULL, 
    -- 'products' para Sucursal A, 'products_sucursal_b' para Sucursal B
    products_table_name VARCHAR(50) NOT NULL 
);

-- NOTAS DE CONTRASEÑAS:
-- 'passwordA' está hasheada como: $2y$10$w60q4w0iMhG/E/vF7h6Oze0T.p1yH4o7l6LwN4e/C0e/U2m/X7K
-- 'passwordB' está hasheada como: $2y$10$o8c.k2/K5wA0.V8jM3wE.D5Z6vJ9l7X8zQ7k8D.T3e/N2g/P0x.K

-- Insertar el usuario de la Sucursal A (Gestiona la tabla 'products')
INSERT INTO admin_users (username, password_hash, products_table_name) VALUES (
    'admin_a', 
    '$2y$10$w60q4w0iMhG/E/vF7h6Oze0T.p1yH4o7l6LwN4e/C0e/U2m/X7K', 
    'products'
);

-- Insertar el usuario de la Sucursal B (Gestiona la tabla 'products_sucursal_b' - el FDW)
INSERT INTO admin_users (username, password_hash, products_table_name) VALUES (
    'admin_b', 
    '$2y$10$o8c.k2/K5wA0.V8jM3wE.D5Z6vJ9l7X8zQ7k8D.T3e/N2g/P0x.K', 
    'products_sucursal_b'
);