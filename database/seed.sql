INSERT INTO usuario(cpf, nome, senha) 
VALUES
("123", "luiz", "321"),
("002", "felipe", "abobrinha");

SELECT * FROM usuario;

UPDATE usuario SET senha = "muito forte" WHERE id = 1;