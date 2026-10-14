-- Estructura de REFERENCIA, deducida de MySQL Workbench (DESCRIBE + Indexes + Foreign Keys).
-- Sirve solo para pruebas locales si no tienes la base del docente.
-- La aplicación debe usarse contra la base real facilitada en clase.
CREATE DATABASE IF NOT EXISTS tienda_torrez CHARACTER SET utf8mb4;
USE tienda_torrez;

CREATE TABLE IF NOT EXISTS clientes (
  id_cliente INT AUTO_INCREMENT PRIMARY KEY,
  nombre     VARCHAR(100) NOT NULL,
  email      VARCHAR(100) NOT NULL,
  edad       INT NOT NULL,
  CONSTRAINT uq_cliente_email UNIQUE (email)
);

CREATE TABLE IF NOT EXISTS pedidos (
  id_pedido  INT AUTO_INCREMENT PRIMARY KEY,
  id_cliente INT NOT NULL,
  fecha      DATE NOT NULL,
  total      DECIMAL(10,2) NOT NULL,
  INDEX idx_pedido_cliente (id_cliente),
  CONSTRAINT fk_pedido_cliente FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente)
);

CREATE TABLE IF NOT EXISTS productos (
  id_producto INT AUTO_INCREMENT PRIMARY KEY,
  nombre      VARCHAR(100) NOT NULL,
  categoria   VARCHAR(50)  NOT NULL,
  precio      DECIMAL(10,2) NOT NULL,
  stock       INT NOT NULL,
  CONSTRAINT uq_producto_nombre UNIQUE (nombre),
  INDEX idx_producto_categoria (categoria),
  INDEX idx_producto_categoria_precio (categoria, precio)
);
