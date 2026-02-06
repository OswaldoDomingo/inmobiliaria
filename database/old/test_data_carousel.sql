-- Script para insertar propiedades de prueba en la base de datos
-- Esto permitirá verificar el funcionamiento del carrusel

-- Asegurarse de que existan clientes propietarios (usar IDs existentes del dump)
-- Ya existen clientes con id 1, 2, 3, 4 según el dump

-- Insertar 8 inmuebles de prueba (estado='activo', activo=1, archivado=0)
INSERT INTO inmuebles 
(ref, propietario_id, comercial_id, direccion, localidad, provincia, cp, tipo, operacion, precio, superficie, habitaciones, banos, estado, descripcion, activo, archivado, imagen) 
VALUES
('REF001', 1, 3, 'Calle Mayor 15, 3º A', 'Valencia', 'Valencia', '46001', 'piso', 'venta', 250000.00, 95, 3, 2, 'activo', 'Piso luminoso en pleno centro de Valencia, reformado recientemente', 1, 0, NULL),

('REF002', 2, 3, 'Avenida del Puerto 45', 'Valencia', 'Valencia', '46021', 'chalet', 'venta', 450000.00, 180, 4, 3, 'activo', 'Chalet adosado con jardín privado y piscina comunitaria', 1, 0, NULL),

('REF003', 1, 4, 'Calle Colón 88, 5º', 'Valencia', 'Valencia', '46004', 'piso', 'alquiler', 1200.00, 85, 2, 1, 'activo', 'Apartamento moderno ideal para parejas jóvenes', 1, 0, NULL),

('REF004', 3, 3, 'Urbanización Los Pinos', 'Paterna', 'Valencia', '46980', 'casa', 'venta', 320000.00, 150, 4, 2, 'activo', 'Casa unifamiliar con amplio jardín y garaje', 1, 0, NULL),

('REF005', 2, 4, 'Paseo Marítimo 22', 'Cullera', 'Valencia', '46400', 'piso', 'vacacional', 800.00, 70, 2, 1, 'activo', 'Apartamento frente al mar, ideal para vacaciones', 1, 0, NULL),

('REF006', 1, 3, 'Calle Poeta Querol 12', 'Valencia', 'Valencia', '46002', 'local', 'alquiler', 2500.00, 120, 0, 2, 'activo', 'Local comercial en zona de gran afluencia', 1, 0, NULL),

('REF007', 4, 4, 'Avenida Blasco Ibáñez 134', 'Valencia', 'Valencia', '46022', 'piso', 'venta', 195000.00, 75, 2, 1, 'activo', 'Piso cerca de la universidad, perfecto para inversión', 1, 0, NULL),

('REF008', 3, 3, 'Calle Russafa 51, 2º B', 'Valencia', 'Valencia', '46006', 'duplex', 'venta', 310000.00, 110, 3, 2, 'activo', 'Dúplex moderno en el barrio de Ruzafa', 1, 0, NULL);

-- Verificar la inserción
SELECT COUNT(*) as total_activos FROM inmuebles WHERE estado='activo' AND activo=1 AND archivado=0;

-- Mostrar los 6 primeros que aparecerían en el carrusel
SELECT id_inmueble, ref, tipo, operacion, localidad, precio 
FROM inmuebles 
WHERE estado='activo' AND activo=1 AND archivado=0 
ORDER BY RAND(TO_DAYS(CURDATE()))
LIMIT 6;
