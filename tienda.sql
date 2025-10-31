-- 1. (Opcional) Crea la base de datos si aún no existe
-- CREATE DATABASE tu_tienda_db;

-- 2. Conéctate a tu base de datos (en psql, usarías \c tu_tienda_db)
-- 3. Crea la tabla de productos
CREATE TABLE products (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255)
);

-- 4. Inserta algunos productos de ejemplo para probar
INSERT INTO products (name, description, price, image_url) VALUES
('Laptop Pro', 'Una laptop potente para profesionales.', 1200.00, 'https://via.placeholder.com/200?text=Laptop'),
('Mouse Óptico', 'Un mouse preciso y ergonómico.', 25.50, 'https://via.placeholder.com/200?text=Mouse'),
('Teclado Mecánico', 'Teclado con switches cherry mx.', 150.75, 'https://via.placeholder.com/200?text=Teclado'),
('Monitor 4K', 'Monitor de 27 pulgadas Ultra HD.', 450.00, 'https://via.placeholder.com/200?text=Monitor');