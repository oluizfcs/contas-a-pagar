CREATE DATABASE IF NOT EXISTS contas_a_pagar 
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE contas_a_pagar;

CREATE TABLE usuario(
    id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cpf VARCHAR(11) UNIQUE NOT NULL,
    nome VARCHAR(45) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    data_criacao DATETIME NOT NULL DEFAULT NOW(),
    data_edicao DATETIME ON UPDATE NOW(),
    `enabled` TINYINT NOT NULL DEFAULT 1
) ENGINE=INNODB;

CREATE TABLE banco(
    id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(45) NOT NULL,
    saldo_em_centavos BIGINT NOT NULL,
    data_criacao DATETIME NOT NULL DEFAULT NOW(),
    data_edicao DATETIME ON UPDATE NOW(),
    `enabled` TINYINT NOT NULL DEFAULT 1
) ENGINE=INNODB;

CREATE TABLE centro_de_custo(
    id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(45) NOT NULL,
    data_criacao DATETIME NOT NULL DEFAULT NOW(),
    data_edicao DATETIME ON UPDATE NOW(),
    `enabled` TINYINT NOT NULL DEFAULT 1
) ENGINE=INNODB;

CREATE TABLE fornecedor(
    id MEDIUMINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(55) NOT NULL,
    telefone VARCHAR(45),
    data_criacao DATETIME NOT NULL DEFAULT NOW(),
    data_edicao DATETIME ON UPDATE NOW(),
    `enabled` TINYINT NOT NULL DEFAULT 1
) ENGINE=INNODB;

CREATE TABLE conta(
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    descricao VARCHAR(500),
    valor_em_centavos INT UNSIGNED NOT NULL,
    total_parcelas SMALLINT UNSIGNED NOT NULL,
    data_criacao DATETIME NOT NULL DEFAULT NOW(),
    data_edicao DATETIME ON UPDATE NOW(),
    usuario_id SMALLINT UNSIGNED NOT NULL,
    banco_id SMALLINT UNSIGNED NOT NULL,
    centro_de_custo_id SMALLINT UNSIGNED NOT NULL,
    fornecedor_id MEDIUMINT UNSIGNED NOT NULL,
    paid TINYINT NOT NULL DEFAULT 0,
    `enabled` TINYINT NOT NULL DEFAULT 1,
    FOREIGN KEY (usuario_id) REFERENCES usuario(id),
    FOREIGN KEY (banco_id) REFERENCES banco(id),
    FOREIGN KEY (centro_de_custo_id) REFERENCES centro_de_custo(id),
    FOREIGN KEY (fornecedor_id) REFERENCES fornecedor(id)
) ENGINE=INNODB;

CREATE TABLE parcela(
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    numero_parcela SMALLINT UNSIGNED NOT NULL,
    valor_em_centavos INT UNSIGNED NOT NULL,
    data_vencimento DATE NOT NULL,
    data_pagamento DATE,
    conta_id INT UNSIGNED NOT NULL,
    paid TINYINT NOT NULL DEFAULT 0,
    FOREIGN KEY (conta_id) REFERENCES conta(id)
) ENGINE=INNODB;

/*///////////////////////////////// LOGS /////////////////////////////////////// */

CREATE TABLE log_usuario(
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    campo VARCHAR(45) NOT NULL,
    valor_antigo VARCHAR(45),
    valor_novo VARCHAR(45),
    data_log DATETIME DEFAULT NOW(),
    usuario_id SMALLINT UNSIGNED NOT NULL,
    usuario_editado_id SMALLINT UNSIGNED NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuario(id),
    FOREIGN KEY (usuario_editado_id) REFERENCES usuario(id)
) ENGINE=INNODB;

CREATE TABLE log_banco(
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    campo VARCHAR(45) NOT NULL,
    valor_antigo VARCHAR(45),
    valor_novo VARCHAR(45),
    data_log DATETIME DEFAULT NOW(),
    usuario_id SMALLINT UNSIGNED NOT NULL,
    banco_id SMALLINT UNSIGNED NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuario(id),
    FOREIGN KEY (banco_id) REFERENCES banco(id)
) ENGINE=INNODB;

CREATE TABLE log_fornecedor(
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    campo VARCHAR(45) NOT NULL,
    valor_antigo VARCHAR(55),
    valor_novo VARCHAR(55),
    data_log DATETIME DEFAULT NOW(),
    usuario_id SMALLINT UNSIGNED NOT NULL,
    fornecedor_id MEDIUMINT UNSIGNED NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuario(id),
    FOREIGN KEY (fornecedor_id) REFERENCES fornecedor(id)
) ENGINE=INNODB;

CREATE TABLE log_centro_de_custo(
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    campo VARCHAR(45) NOT NULL,
    valor_antigo VARCHAR(45),
    valor_novo VARCHAR(45),
    data_log DATETIME DEFAULT NOW(),
    usuario_id SMALLINT UNSIGNED NOT NULL,
    centro_de_custo_id SMALLINT UNSIGNED NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuario(id),
    FOREIGN KEY (centro_de_custo_id) REFERENCES centro_de_custo(id)
) ENGINE=INNODB;

CREATE TABLE log_conta(
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    campo VARCHAR(500) NOT NULL,
    valor_antigo VARCHAR(500),
    valor_novo VARCHAR(500),
    data_log DATETIME DEFAULT NOW(),
    usuario_id SMALLINT UNSIGNED NOT NULL,
    conta_id INT UNSIGNED NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuario(id),
    FOREIGN KEY (conta_id) REFERENCES conta(id)
) ENGINE=INNODB;
