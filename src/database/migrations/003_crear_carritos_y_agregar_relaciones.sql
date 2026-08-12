-- En esta migracion se crea la tabla sin las claves foraneas

CREATE TABLE carritos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL
);


-- Relacionar carrito con usuarios

ALTER TABLE carritos
ADD FOREIGN KEY (usuario_id)
REFERENCES usuarios(id);

-- Relacionar carrito con productos

ALTER TABLE carritos
ADD FOREIGN KEY (producto_id)
REFERENCES productos(id);