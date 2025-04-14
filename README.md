-- Insertar un usuario administrador
INSERT INTO usuarios (nombre, numero_casa, rol, correo, contraseña)
VALUES ('Admin Principal', '00', 'administrador', 'admin@condominio.com', MD5('admin123'));

-- Insertar un residente
INSERT INTO usuarios (nombre, numero_casa, rol, correo, contraseña)
VALUES ('Juan Pérez', '01', 'residente', 'juan.perez@correo.com', MD5('residente123'));

-- Insertar un residente
INSERT INTO usuarios (nombre, numero_casa, rol, correo, contraseña)
VALUES ('Karol Ruiz', '12', 'residente', 'karolruiz123@correo.com', MD5('residente24'));

admin@condominio.com : admin123
juan.perez@correo.com : residente123
karolruiz123@correo.com : residente24

-- Insertar algunos recibos de prueba
INSERT INTO recibos (usuario_id, monto, fecha_limite, estado)
VALUES 
(2, 150000, '2025-04-30', 'Pendiente'),
(2, 150000, '2025-03-31', 'Pagado');

-- Insertar documentos de prueba
INSERT INTO documentos (usuario_id, tipo, url)
VALUES 
(2, 'obra', 'documentos/permiso_obra.pdf'),
(2, 'trasteo', 'documentos/permiso_trasteo.pdf');