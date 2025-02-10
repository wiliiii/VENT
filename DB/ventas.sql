-- Estructura de la tabla `caja`
CREATE TABLE caja (
  caja_id SERIAL PRIMARY KEY,
  caja_numero INTEGER NOT NULL,
  caja_nombre VARCHAR(100) NOT NULL,
  caja_efectivo NUMERIC(30,2) NOT NULL
);

-- Volcado de datos para la tabla `caja`
INSERT INTO caja (caja_numero, caja_nombre, caja_efectivo) VALUES
(1, 'Caja Principal', 0.00);

-- Estructura de la tabla `categoria`
CREATE TABLE categoria (
  categoria_id SERIAL PRIMARY KEY,
  categoria_nombre VARCHAR(50) NOT NULL,
  categoria_ubicacion VARCHAR(150) NOT NULL
);

-- Estructura de la tabla `cliente`
CREATE TABLE cliente (
  cliente_id SERIAL PRIMARY KEY,
  cliente_tipo_documento VARCHAR(20) NOT NULL,
  cliente_numero_documento VARCHAR(35) NOT NULL,
  cliente_nombre VARCHAR(50) NOT NULL,
  cliente_apellido VARCHAR(50) NOT NULL,
  cliente_provincia VARCHAR(30) NOT NULL,
  cliente_ciudad VARCHAR(30) NOT NULL,
  cliente_direccion VARCHAR(70) NOT NULL,
  cliente_telefono VARCHAR(20) NOT NULL,
  cliente_email VARCHAR(50) NOT NULL
);

-- Volcado de datos para la tabla `cliente`
INSERT INTO cliente (cliente_tipo_documento, cliente_numero_documento, cliente_nombre, cliente_apellido, cliente_provincia, cliente_ciudad, cliente_direccion, cliente_telefono, cliente_email) VALUES
('Otro', 'N/A', 'Publico', 'General', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A');

-- Estructura de la tabla `empresa`
CREATE TABLE empresa (
  empresa_id SERIAL PRIMARY KEY,
  empresa_nombre VARCHAR(90) NOT NULL,
  empresa_telefono VARCHAR(20) NOT NULL,
  empresa_email VARCHAR(50) NOT NULL,
  empresa_direccion VARCHAR(100) NOT NULL
);

-- Estructura de la tabla `producto`
CREATE TABLE producto (
  producto_id SERIAL PRIMARY KEY,
  producto_codigo VARCHAR(77) NOT NULL,
  producto_nombre VARCHAR(100) NOT NULL,
  producto_stock_total INTEGER NOT NULL,
  producto_tipo_unidad VARCHAR(20) NOT NULL,
  producto_precio_compra NUMERIC(30,2) NOT NULL,
  producto_precio_venta NUMERIC(30,2) NOT NULL,
  producto_marca VARCHAR(35) NOT NULL,
  producto_modelo VARCHAR(35) NOT NULL,
  producto_estado VARCHAR(20) NOT NULL,
  producto_foto VARCHAR(500) NOT NULL,
  categoria_id INTEGER NOT NULL,
  FOREIGN KEY (categoria_id) REFERENCES categoria(categoria_id)
);

-- Estructura de la tabla `usuario`
CREATE TABLE usuario (
  usuario_id SERIAL PRIMARY KEY,
  usuario_nombre VARCHAR(50) NOT NULL,
  usuario_apellido VARCHAR(50) NOT NULL,
  usuario_email VARCHAR(50) NOT NULL,
  usuario_usuario VARCHAR(30) NOT NULL,
  usuario_clave VARCHAR(535) NOT NULL,
  usuario_foto VARCHAR(200) NOT NULL,
  caja_id INTEGER NOT NULL,
  FOREIGN KEY (caja_id) REFERENCES caja(caja_id)
);

-- Volcado de datos para la tabla `usuario`
INSERT INTO usuario (usuario_nombre, usuario_apellido, usuario_email, usuario_usuario, usuario_clave, usuario_foto, caja_id) VALUES
('Administrador', 'Principal', '', 'Administrador', '$2y$10$Jgm6xFb5Onz/BMdIkNK2Tur8yg/NYEMb/tdnhoV7kB1BwIG4R05D2', '', 1);

-- Estructura de la tabla `venta`
CREATE TABLE venta (
  venta_id SERIAL PRIMARY KEY,
  venta_codigo VARCHAR(200) UNIQUE NOT NULL,
  venta_fecha DATE NOT NULL,
  venta_hora VARCHAR(17) NOT NULL,
  venta_total NUMERIC(30,2) NOT NULL,
  venta_pagado NUMERIC(30,2) NOT NULL,
  venta_cambio NUMERIC(30,2) NOT NULL,
  usuario_id INTEGER NOT NULL,
  cliente_id INTEGER NOT NULL,
  caja_id INTEGER NOT NULL,
  FOREIGN KEY (usuario_id) REFERENCES usuario(usuario_id),
  FOREIGN KEY (cliente_id) REFERENCES cliente(cliente_id),
  FOREIGN KEY (caja_id) REFERENCES caja(caja_id)
);

-- Estructura de la tabla `venta_detalle`
CREATE TABLE venta_detalle (
  venta_detalle_id SERIAL PRIMARY KEY,
  venta_detalle_cantidad INTEGER NOT NULL,
  venta_detalle_precio_compra NUMERIC(30,2) NOT NULL,
  venta_detalle_precio_venta NUMERIC(30,2) NOT NULL,
  venta_detalle_total NUMERIC(30,2) NOT NULL,
  venta_detalle_descripcion VARCHAR(200) NOT NULL,
  venta_codigo VARCHAR(200) NOT NULL,
  producto_id INTEGER NOT NULL,
  FOREIGN KEY (producto_id) REFERENCES producto(producto_id),
  FOREIGN KEY (venta_codigo) REFERENCES venta(venta_codigo)
);
