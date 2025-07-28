CREATE DATABASE IF NOT EXISTS contas_a_pagar 
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE contas_a_pagar;

CREATE TABLE usuario(
    id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cpf VARCHAR(11) NOT NULL,
    nome VARCHAR(45) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    data_criacao DATETIME NOT NULL DEFAULT NOW(),
    data_edicao DATETIME ON UPDATE NOW()
) ENGINE=INNODB;

CREATE TABLE conta(
    id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(45) NOT NULL,
    saldo_em_centavos INT NOT NULL,
    data_criacao DATETIME NOT NULL DEFAULT NOW(),
    data_edicao DATETIME ON UPDATE NOW()
) ENGINE=INNODB;

CREATE TABLE centro_de_custo(
    id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(45) NOT NULL,
    data_criacao DATETIME NOT NULL DEFAULT NOW(),
    data_edicao DATETIME ON UPDATE NOW()
) ENGINE=INNODB;

CREATE TABLE fornecedor(
    id MEDIUMINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(55) NOT NULL,
    telefone VARCHAR(45),
    data_criacao DATETIME NOT NULL DEFAULT NOW(),
    data_edicao DATETIME ON UPDATE NOW()
) ENGINE=INNODB;

CREATE TABLE status(
    id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(45) NOT NULL
) ENGINE=INNODB;

CREATE TABLE despesa(
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    descricao VARCHAR(500),
    valor_em_centavos INT UNSIGNED NOT NULL,
    total_parcelas SMALLINT UNSIGNED NOT NULL,
    data_criacao DATETIME NOT NULL DEFAULT NOW(),
    data_edicao DATETIME ON UPDATE NOW(),
    usuario_id SMALLINT UNSIGNED NOT NULL,
    conta_id SMALLINT UNSIGNED NOT NULL,
    centro_de_custo_id SMALLINT UNSIGNED NOT NULL,
    fornecedor_id MEDIUMINT UNSIGNED NOT NULL,
    status_id TINYINT UNSIGNED NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuario(id),
    FOREIGN KEY (conta_id) REFERENCES conta(id),
    FOREIGN KEY (centro_de_custo_id) REFERENCES centro_de_custo(id),
    FOREIGN KEY (fornecedor_id) REFERENCES fornecedor(id),
    FOREIGN KEY (status_id) REFERENCES status(id)
) ENGINE=INNODB;

CREATE TABLE parcela(
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    numero_parcela SMALLINT UNSIGNED NOT NULL,
    valor_em_centavos INT UNSIGNED NOT NULL,
    data_vencimento DATE NOT NULL,
    data_pagamento DATE,
    data_criacao DATETIME NOT NULL DEFAULT NOW(),
    data_edicao DATETIME ON UPDATE NOW(),
    despesa_id INT UNSIGNED NOT NULL,
    status_id TINYINT UNSIGNED NOT NULL,
    FOREIGN KEY (despesa_id) REFERENCES despesa(id),
    FOREIGN KEY (status_id) REFERENCES status(id)
) ENGINE=INNODB;

CREATE TABLE log_usuario(
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    campo VARCHAR(45) NOT NULL,
    valor_antigo VARCHAR(45) NOT NULL,
    valor_novo VARCHAR(45) NOT NULL,
    data_log DATETIME DEFAULT NOW(),
    usuario_id SMALLINT UNSIGNED NOT NULL,
    usuario_editado_id SMALLINT UNSIGNED NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuario(id),
    FOREIGN KEY (usuario_editado_id) REFERENCES usuario(id)
) ENGINE=INNODB;

CREATE TABLE log_conta(
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    campo VARCHAR(45) NOT NULL,
    valor_antigo VARCHAR(45) NOT NULL,
    valor_novo VARCHAR(45) NOT NULL,
    data_log DATETIME DEFAULT NOW(),
    usuario_id SMALLINT UNSIGNED NOT NULL,
    conta_id SMALLINT UNSIGNED NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuario(id),
    FOREIGN KEY (conta_id) REFERENCES conta(id)
) ENGINE=INNODB;

CREATE TABLE log_fornecedor(
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    campo VARCHAR(45) NOT NULL,
    valor_antigo VARCHAR(55) NOT NULL,
    valor_novo VARCHAR(55) NOT NULL,
    data_log DATETIME DEFAULT NOW(),
    usuario_id SMALLINT UNSIGNED NOT NULL,
    fornecedor_id MEDIUMINT UNSIGNED NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuario(id),
    FOREIGN KEY (fornecedor_id) REFERENCES fornecedor(id)
) ENGINE=INNODB;

CREATE TABLE log_centro_de_custo(
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    campo VARCHAR(45) NOT NULL,
    valor_antigo VARCHAR(45) NOT NULL,
    valor_novo VARCHAR(45) NOT NULL,
    data_log DATETIME DEFAULT NOW(),
    usuario_id SMALLINT UNSIGNED NOT NULL,
    centro_de_custo_id SMALLINT UNSIGNED NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuario(id),
    FOREIGN KEY (centro_de_custo_id) REFERENCES centro_de_custo(id)
) ENGINE=INNODB;

CREATE TABLE log_despesa(
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    campo VARCHAR(500) NOT NULL,
    valor_antigo VARCHAR(500) NOT NULL,
    valor_novo VARCHAR(500) NOT NULL,
    data_log DATETIME DEFAULT NOW(),
    usuario_id SMALLINT UNSIGNED NOT NULL,
    despesa_id INT UNSIGNED NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuario(id),
    FOREIGN KEY (despesa_id) REFERENCES despesa(id)
) ENGINE=INNODB;

CREATE TABLE log_parcela(
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    campo VARCHAR(45) NOT NULL,
    valor_antigo VARCHAR(45) NOT NULL,
    valor_novo VARCHAR(45) NOT NULL,
    data_log DATETIME DEFAULT NOW(),
    usuario_id SMALLINT UNSIGNED NOT NULL,
    parcela_id INT UNSIGNED NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuario(id),
    FOREIGN KEY (parcela_id) REFERENCES parcela(id)
) ENGINE=INNODB;