SELECT * FROM public.admin_users
ORDER BY id ASC 

DROP TABLE IF EXISTS admin_users;

-- 2. Crea la tabla con un campo de contraseña más corto (texto plano)
CREATE TABLE admin_users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    -- (CAMBIO CLAVE) Almacena el texto plano de la contraseña
    password VARCHAR(50) NOT NULL, 
    -- 'products' para Sucursal A, 'products_sucursal_b' para Sucursal B
    products_table_name VARCHAR(50) NOT NULL 
);

-- Usuario Sucursal A (Gestiona la tabla 'products')
INSERT INTO admin_users (username, password, products_table_name) VALUES (
    'admin_a', 
    '123456', -- Contraseña simple
    'products'
);

-- Usuario Sucursal B (Gestiona la tabla 'products_sucursal_b' - el FDW)
INSERT INTO admin_users (username, password, products_table_name) VALUES (
    'admin_b', 
    '654321', -- Contraseña simple
    'products_sucursal_b'
);