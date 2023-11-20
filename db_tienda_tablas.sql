/* 
--------------------
Proyecto PHP MySQL
Álvaro Romero Campaña 2A DAW
Base de datos TIENDA 
--------------------
*/

CREATE schema db_tienda
USE db_tienda

CREATE TABLE  productos (
idProducto INT(8) AUTO_INCREMENT PRIMARY KEY,
nombreProducto VARCHAR(40) NOT NULL,
precio NUMERIC(7,2) NOT NULL,
descripcion VARCHAR(255) NOT NULL,
cantidad INT NOT NULL,
imagen VARCHAR(255) NOT NULL
);

CREATE TABLE productosCesta (
idProducto INT(8) NOT NULL,
idCesta INT(8) NOT NULL,
cantidad NUMERIC(2) NOT NULL,
CONSTRAINT pk_productosCestas
	PRIMARY KEY (idProducto, idCesta),
CONSTRAINT fk_productosCestas_productos
	FOREIGN KEY (idProducto)
    REFERENCES productos(idProducto),
CONSTRAINT fk_productosCestas_cestas
	FOREIGN KEY (idCesta)
    REFERENCES cestas(idCesta)
);

CREATE TABLE usuarios (
usuario VARCHAR(12) PRIMARY KEY,
contrasena VARCHAR(255) NOT NULL,
fechaNacimiento DATE NOT NULL,
rol VARCHAR(20) DEFAULT 'cliente',
saldo FLOAT DEFAULT '0'
);

CREATE TABLE cestas (
idCesta INT(8) AUTO_INCREMENT PRIMARY KEY,
usuario VARCHAR(12) NOT NULL,
precioTotal NUMERIC(7,2) NOT NULL DEFAULT '0',
FOREIGN KEY (usuario) REFERENCES usuarios(usuario)
);

CREATE TABLE LineasPedidos(
lineaPedido INT(2),
idProducto INT NOT NULL,
idPedido INT NOT NULL,
precioUnitario FLOAT NOT NULL,
cantidad INT NOT NULL
);

CREATE TABLE Pedidos(
idPedido INT(8) AUTO_INCREMENT PRIMARY KEY,
usuario VARCHAR(12) NOT NULL,
precioTotal FLOAT NOT NULL,
fechaPedido DATETIME DEFAULT NOW()
);
