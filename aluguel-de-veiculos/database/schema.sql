CREATE DATABASE IF NOT EXISTS aluguel_veiculos
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE aluguel_veiculos;

DROP TABLE IF EXISTS alugueis;
DROP TABLE IF EXISTS veiculos;
DROP TABLE IF EXISTS usuarios;

CREATE TABLE usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome_completo VARCHAR(120) NOT NULL,
    cpf CHAR(11) NOT NULL,
    data_nascimento DATE NOT NULL,
    telefone VARCHAR(11) NOT NULL,
    email VARCHAR(120) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    endereco VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_usuarios_cpf (cpf),
    UNIQUE KEY uq_usuarios_email (email)
) ENGINE=InnoDB;

CREATE TABLE veiculos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    modelo VARCHAR(100) NOT NULL,
    cor VARCHAR(40) NOT NULL,
    fabricante VARCHAR(80) NOT NULL,
    placa VARCHAR(7) NOT NULL,
    disponivel TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_veiculos_placa (placa),
    KEY idx_veiculos_disponivel (disponivel)
) ENGINE=InnoDB;

CREATE TABLE alugueis (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    data_retirada DATE NOT NULL,
    data_entrega DATE NOT NULL,
    forma_pagamento ENUM('credito', 'debito', 'pix', 'dinheiro') NOT NULL,
    usuario_id INT UNSIGNED NOT NULL,
    veiculo_id INT UNSIGNED NOT NULL,
    status ENUM('ativo', 'finalizado') NOT NULL DEFAULT 'ativo',
    finalizado_em DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_alugueis_status (status),
    KEY idx_alugueis_usuario (usuario_id),
    KEY idx_alugueis_veiculo (veiculo_id),
    CONSTRAINT fk_alugueis_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_alugueis_veiculo
        FOREIGN KEY (veiculo_id) REFERENCES veiculos (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;
