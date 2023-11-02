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
cantidad INT NOT NULL
);

CREATE TABLE productosCesta (
idProducto INT(8) NOT NULL,
idCesta INT(8) NOT NULL,
FOREIGN KEY (idProducto) REFERENCES productos (idProducto),
FOREIGN KEY (idCesta) REFERENCES cestas (idCesta),
CONSTRAINT pk_ProductoCesta PRIMARY KEY (idProducto, idCesta)
);

CREATE TABLE usuarios (
usuario VARCHAR(12) PRIMARY KEY,
contrasena VARCHAR(255) NOT NULL,
fechaNacimiento DATE NOT NULL
);

CREATE TABLE cestas (
idCesta INT(8) AUTO_INCREMENT PRIMARY KEY,
usuario VARCHAR(12) NOT NULL,
precioTotal NUMERIC(7,2) NOT NULL DEFAULT '0',
FOREIGN KEY (usuario) REFERENCES usuarios(usuario)
);
