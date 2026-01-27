INSERT INTO usuario(cpf, nome, senha) 
VALUES
("00000000000", "Admin", "$2y$12$iO0uR70UbguPjcvtd7vCr.CDfPNZgHKAMho5QX/H/8j6g55JLimJK");

INSERT INTO centro_de_custo(nome) VALUES
("Embalagens"),
("Refrigeração"),
("Veículos"),
("Recursos Humanos"),
("Administração"),
("Logística"),
("Marketing");

-- cd ../../mysql/bin
-- mysql -u root
-- drop database contas_a_pagar;
-- source C:\Apache24\htdocs\database\schema.sql