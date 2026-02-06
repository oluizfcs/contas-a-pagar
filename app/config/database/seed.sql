use contas_a_pagar;
SET NAMES 'utf8mb4';

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

INSERT INTO fornecedor(nome, telefone) VALUES
("Empresa de Embalagens", "(99) 99999-9999"),
("Empresa de Refrigeração", "(99) 99999-1234"),
("Empresa de Veículos", "(99) 12345-9999"),
("Posto de Combustível", null);

-- cd ../../mysql/bin
-- mysql -u root
-- drop database contas_a_pagar;
-- source C:\Apache24\htdocs\database\schema.sql