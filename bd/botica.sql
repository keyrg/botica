-- PostgreSQL database dump
-- Converted from MySQL to PostgreSQL

-- Database: botica

-- DROP DATABASE IF EXISTS botica;

CREATE DATABASE botica
    WITH 
    OWNER = postgres
    ENCODING = 'UTF8'
    LC_COLLATE = 'en_US.UTF-8'
    LC_CTYPE = 'en_US.UTF-8'
    TABLESPACE = pg_default
    CONNECTION LIMIT = -1;

\c botica

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

-- Table: bitacora

CREATE TABLE bitacora (
    bitacora_id serial PRIMARY KEY,
    bitacora_codigo varchar(70) NOT NULL,
    bitacora_fecha date NOT NULL,
    bitacora_horaInicio varchar(20) NOT NULL,
    bitacora_horaFin varchar(20) NOT NULL,
    bitacora_tipoUsuario varchar(20) NOT NULL,
    bitacora_ano integer NOT NULL,
    bitacora_id_usuario integer NOT NULL
);

-- Table: cliente

CREATE TABLE cliente (
    cliente_id serial PRIMARY KEY,
    cliente_nombre varchar(150) NOT NULL,
    cliente_dni varchar(8) NOT NULL,
    cliente_celular varchar(12) NOT NULL,
    cliente_direccion varchar(255) NOT NULL,
    cliente_correo varchar(70) NOT NULL
);

-- Table: compra

CREATE TABLE compra (
    compra_id serial PRIMARY KEY,
    compra_codigo varchar(70) NOT NULL,
    compra_tipoComprobante varchar(30) NOT NULL,
    compra_serie varchar(7) NOT NULL,
    compra_numComprobante varchar(30) NOT NULL,
    compra_fecha varchar(40) NOT NULL,
    compra_impuesto integer NOT NULL,
    compra_total numeric(10,2) NOT NULL DEFAULT 0.00,
    compra_id_proveedor integer NOT NULL,
    compra_id_usuario integer NOT NULL,
    compra_estado boolean NOT NULL DEFAULT true
);

-- Table: comprobante

CREATE TABLE comprobante (
    comprobante_id serial PRIMARY KEY,
    comprobante_nombre varchar(70) NOT NULL,
    comprobante_letraSerie varchar(20) NOT NULL,
    comprobante_serie varchar(30) NOT NULL,
    comprobante_numero varchar(50) NOT NULL,
    comprobante_estado boolean NOT NULL
);

-- Insert data into comprobante

INSERT INTO comprobante (comprobante_id, comprobante_nombre, comprobante_letraSerie, comprobante_serie, comprobante_numero, comprobante_estado) VALUES 
(1, 'Boleta', 'B', '001', '0000001', true),
(2, 'Factura', 'F', '001', '0000001', true);

-- Reset the sequence after manual inserts
SELECT setval('comprobante_comprobante_id_seq', (SELECT MAX(comprobante_id) FROM comprobante));

-- Table: detalle_compra

CREATE TABLE detalle_compra (
    detalleCompra_id serial PRIMARY KEY,
    detalleCompra_cantidad integer NOT NULL DEFAULT 1,
    detalleCompra_precioC numeric(11,2) NOT NULL DEFAULT 0.00,
    detalleCompra_precioV numeric(11,2) NOT NULL DEFAULT 0.00,
    detalleCompra_id_compra integer NOT NULL,
    detalleCompra_id_producto integer NOT NULL
);

-- Table: detalle_venta

CREATE TABLE detalle_venta (
    detalleVenta_id serial PRIMARY KEY,
    detalleVenta_cantidad integer NOT NULL DEFAULT 1,
    detalleVenta_precioV numeric(10,2) NOT NULL DEFAULT 0.00,
    detalleVenta_descuento numeric(10,2) NOT NULL DEFAULT 0.00,
    detalleVenta_id_venta integer NOT NULL,
    detalleVenta_id_producto integer NOT NULL
);

-- Table: empresa

CREATE TABLE empresa (
    empresa_id serial PRIMARY KEY,
    empresa_nombre varchar(100) NOT NULL,
    empresa_ruc varchar(20) NOT NULL,
    empresa_celular varchar(12) NOT NULL,
    empresa_direccion varchar(100) NOT NULL,
    empresa_correo varchar(70) NOT NULL,
    empresa_impuesto varchar(20) NOT NULL,
    empresa_impuestoValor integer NOT NULL,
    empresa_moneda varchar(30) NOT NULL,
    empresa_simbolo varchar(30) NOT NULL,
    empresa_logo varchar(255) NOT NULL
);

-- Insert data into empresa

INSERT INTO empresa (empresa_id, empresa_nombre, empresa_ruc, empresa_celular, empresa_direccion, empresa_correo, empresa_impuesto, empresa_impuestoValor, empresa_moneda, empresa_simbolo, empresa_logo) VALUES 
(1, 'Sana-Plus', '206054132701', '963852741', 'sin direccion', 'corre@example.com', 'IGV', 18, 'PEN', 'S/.', '../Assets/images/iconos/SVerde.png');

-- Reset the sequence after manual inserts
SELECT setval('empresa_empresa_id_seq', (SELECT MAX(empresa_id) FROM empresa));

-- Table: laboratorio

CREATE TABLE laboratorio (
    lab_id serial PRIMARY KEY,
    lab_codigo varchar(40) NOT NULL,
    lab_nombre varchar(50) NOT NULL
);

-- Table: lote

CREATE TABLE lote (
    lote_id serial PRIMARY KEY,
    lote_codigo varchar(70) NOT NULL,
    lote_cantUnitario integer NOT NULL DEFAULT 1,
    lote_fechaVencimiento date NOT NULL,
    lote_id_producto integer NOT NULL,
    lote_id_proveedor integer NOT NULL,
    lote_id_compra integer NOT NULL
);

-- Table: pago

CREATE TABLE pago (
    pago_id serial PRIMARY KEY,
    pago_nombre varchar(70) NOT NULL,
    pago_descripcion varchar(255) NOT NULL,
    pago_estado boolean NOT NULL
);

-- Table: presentacion

CREATE TABLE presentacion (
    present_id serial PRIMARY KEY,
    present_codigo varchar(40) NOT NULL,
    present_nombre varchar(50) NOT NULL
);

-- Table: producto

CREATE TABLE producto (
    prod_id serial PRIMARY KEY,
    prod_codigo varchar(100) NOT NULL,
    prod_codigoin varchar(70) NOT NULL,
    prod_nombre varchar(50) NOT NULL,
    prod_concentracion varchar(250) NOT NULL,
    prod_adicional varchar(100) NOT NULL,
    prod_imagen varchar(250) NOT NULL,
    prod_precioC numeric(10,2) NOT NULL DEFAULT 0.00,
    prod_precioV numeric(10,2) NOT NULL DEFAULT 0.00,
    prod_id_lab integer NOT NULL,
    prod_id_tipo integer NOT NULL,
    prod_id_present integer NOT NULL
);

-- Table: proveedor

CREATE TABLE proveedor (
    proved_id serial PRIMARY KEY,
    proved_codigo varchar(70) NOT NULL,
    proved_nombre varchar(200) NOT NULL,
    proved_tipoDocumento varchar(50) NOT NULL,
    proved_numDocumento varchar(20) NOT NULL,
    proved_celular varchar(12) NOT NULL,
    proved_correo varchar(50) NOT NULL,
    proved_direccion varchar(250) NOT NULL
);

-- Table: tipo_producto

CREATE TABLE tipo_producto (
    tipo_id serial PRIMARY KEY,
    tipo_codigo varchar(40) NOT NULL,
    tipo_nombre varchar(50) NOT NULL
);

-- Table: usuario

CREATE TABLE usuario (
    usuario_id serial PRIMARY KEY,
    usuario_codigo varchar(70) NOT NULL,
    usuario_nombre varchar(50) NOT NULL,
    usuario_apellido varchar(100) NOT NULL,
    usuario_fechanacimiento varchar(100) NOT NULL,
    usuario_profesion varchar(50) NOT NULL,
    usuario_dni varchar(15) NOT NULL,
    usuario_celular varchar(12) NOT NULL,
    usuario_genero varchar(50) NOT NULL,
    usuario_cargo varchar(30) NOT NULL,
    usuario_descripcion varchar(255) NOT NULL,
    usuario_login varchar(50) NOT NULL,
    usuario_contrasena varchar(100) NOT NULL,
    usuario_perfil varchar(250) NOT NULL,
    usuario_estado boolean NOT NULL DEFAULT true
);

-- Insert data into usuario

INSERT INTO usuario (usuario_id, usuario_codigo, usuario_nombre, usuario_apellido, usuario_fechanacimiento, usuario_profesion, usuario_dni, usuario_celular, usuario_genero, usuario_cargo, usuario_descripcion, usuario_login, usuario_contrasena, usuario_perfil, usuario_estado) VALUES 
(1, 'USU-8458881', 'Administrador', 'Administrador', '2000-11-09', 'Administrador', '4567891', '963852741', 'Masculino', 'Administrador', 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout', 'admin', 'STEzZWowVG9UaFZFQU5mMXhVcGx5QT09', '../Assets/images/avatar/masculino.png', true);

-- Reset the sequence after manual inserts
SELECT setval('usuario_usuario_id_seq', (SELECT MAX(usuario_id) FROM usuario));

-- Table: venta

CREATE TABLE venta (
    venta_id serial PRIMARY KEY,
    venta_codigo varchar(70) NOT NULL,
    venta_id_comprobante integer NOT NULL,
    venta_serie varchar(70) NOT NULL,
    venta_numComprobante varchar(30) NOT NULL,
    venta_fecha timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    venta_impuesto integer NOT NULL,
    venta_total numeric(10,2) NOT NULL DEFAULT 0.00,
    venta_id_usuario integer NOT NULL,
    venta_id_cliente integer NOT NULL,
    venta_estado boolean NOT NULL DEFAULT true
);

-- Create indexes

CREATE INDEX idx_bitacora_usuario ON bitacora (bitacora_id_usuario);
CREATE INDEX idx_compra_proveedor ON compra (compra_id_proveedor);
CREATE INDEX idx_compra_usuario ON compra (compra_id_usuario);
CREATE INDEX idx_detalle_compra_compra ON detalle_compra (detalleCompra_id_compra);
CREATE INDEX idx_detalle_compra_producto ON detalle_compra (detalleCompra_id_producto);
CREATE INDEX idx_detalle_venta_venta ON detalle_venta (detalleVenta_id_venta);
CREATE INDEX idx_detalle_venta_producto ON detalle_venta (detalleVenta_id_producto);
CREATE INDEX idx_lote_producto ON lote (lote_id_producto);
CREATE INDEX idx_lote_proveedor ON lote (lote_id_proveedor);
CREATE INDEX idx_producto_lab ON producto (prod_id_lab);
CREATE INDEX idx_producto_tipo ON producto (prod_id_tipo);
CREATE INDEX idx_producto_present ON producto (prod_id_present);
CREATE INDEX idx_venta_usuario ON venta (venta_id_usuario);
CREATE INDEX idx_venta_cliente ON venta (venta_id_cliente);
CREATE INDEX idx_venta_comprobante ON venta (venta_id_comprobante);

-- Create foreign key constraints

ALTER TABLE bitacora
    ADD CONSTRAINT fk_bitacora_usuario FOREIGN KEY (bitacora_id_usuario) REFERENCES usuario (usuario_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE compra
    ADD CONSTRAINT fk_compra_proveedor FOREIGN KEY (compra_id_proveedor) REFERENCES proveedor (proved_id) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT fk_compra_usuario FOREIGN KEY (compra_id_usuario) REFERENCES usuario (usuario_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE detalle_compra
    ADD CONSTRAINT fk_detalle_compra_compra FOREIGN KEY (detalleCompra_id_compra) REFERENCES compra (compra_id) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT fk_detalle_compra_producto FOREIGN KEY (detalleCompra_id_producto) REFERENCES producto (prod_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE detalle_venta
    ADD CONSTRAINT fk_detalle_venta_venta FOREIGN KEY (detalleVenta_id_venta) REFERENCES venta (venta_id) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT fk_detalle_venta_producto FOREIGN KEY (detalleVenta_id_producto) REFERENCES producto (prod_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE lote
    ADD CONSTRAINT fk_lote_proveedor FOREIGN KEY (lote_id_proveedor) REFERENCES proveedor (proved_id) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT fk_lote_producto FOREIGN KEY (lote_id_producto) REFERENCES producto (prod_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE producto
    ADD CONSTRAINT fk_producto_lab FOREIGN KEY (prod_id_lab) REFERENCES laboratorio (lab_id) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT fk_producto_tipo FOREIGN KEY (prod_id_tipo) REFERENCES tipo_producto (tipo_id) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT fk_producto_present FOREIGN KEY (prod_id_present) REFERENCES presentacion (present_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE venta
    ADD CONSTRAINT fk_venta_usuario FOREIGN KEY (venta_id_usuario) REFERENCES usuario (usuario_id) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT fk_venta_cliente FOREIGN KEY (venta_id_cliente) REFERENCES cliente (cliente_id) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT fk_venta_comprobante FOREIGN KEY (venta_id_comprobante) REFERENCES comprobante (comprobante_id) ON DELETE CASCADE ON UPDATE CASCADE;