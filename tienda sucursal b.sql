-- Asegúrate de que la pestaña de tu Query Tool
-- diga "tienda_sucursal_b/postgres@PostgreSQL..."
-- 1. Ejecuta ESTO PRIMERO:
CREATE TABLE products (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255)
);



INSERT INTO products (name, description, price, image_url) 
VALUES 
('Monitor Curvo', 'Monitor ultrawide de 34 pulgadas.', 550.00, 'https://via.placeholder.com/200?text=Monitor'), ('Teclado Mecánico RGB', 'Teclado mecánico con switches rojos e iluminación RGB.', 120.50, 'https://source.unsplash.com/600x400/?mechanical+keyboard'),
('Mouse Inalámbrico Ergonómico', 'Mouse óptico inalámbrico con diseño ergonómico y batería recargable.', 75.00, 'https://source.unsplash.com/600x400/?wireless+mouse'),
('Laptop Ultrabook 14"', 'Laptop delgada con 16GB RAM, 512GB SSD y procesador Core i7.', 1300.00, 'https://source.unsplash.com/600x400/?laptop'),
('Audífonos Bluetooth NC', 'Audífonos over-ear con cancelación de ruido y 30 horas de batería.', 250.99, 'https://source.unsplash.com/600x400/?headphones');