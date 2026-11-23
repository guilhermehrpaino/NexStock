CREATE DATABASE IF NOT EXISTS nexstock
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE nexstock;

CREATE TABLE IF NOT EXISTS usuarios (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome VARCHAR(120) NOT NULL,
  usuario VARCHAR(80) NOT NULL,
  senha VARCHAR(255) NOT NULL,
  cargo VARCHAR(60) NOT NULL DEFAULT 'Administrador',
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_usuarios_usuario (usuario)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

CREATE OR REPLACE VIEW users AS
  SELECT id, usuario, senha
  FROM usuarios;

CREATE TABLE IF NOT EXISTS categorias (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome VARCHAR(120) NOT NULL,
  descricao TEXT NULL,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_categorias_nome (nome)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS produtos (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome VARCHAR(180) NOT NULL,
  codigo VARCHAR(80) NULL,
  categoria_id INT UNSIGNED NOT NULL,
  preco DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT 0.00,
  quantidade INT UNSIGNED NOT NULL DEFAULT 0,
  estoque_minimo INT UNSIGNED NOT NULL DEFAULT 0,
  descricao TEXT NULL,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_produtos_codigo (codigo),
  KEY idx_produtos_nome (nome),
  KEY idx_produtos_categoria_id (categoria_id),
  KEY idx_produtos_ativo (ativo),
  CONSTRAINT fk_produtos_categoria
    FOREIGN KEY (categoria_id) REFERENCES categorias (id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS movimentacoes (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  produto_id INT UNSIGNED NOT NULL,
  usuario_id INT UNSIGNED NOT NULL,
  tipo ENUM('entrada', 'saida') NOT NULL,
  quantidade INT UNSIGNED NOT NULL,
  observacao TEXT NULL,
  data_hora TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_movimentacoes_produto_id (produto_id),
  KEY idx_movimentacoes_usuario_id (usuario_id),
  KEY idx_movimentacoes_data_hora (data_hora),
  KEY idx_movimentacoes_tipo (tipo),
  CONSTRAINT fk_movimentacoes_produto
    FOREIGN KEY (produto_id) REFERENCES produtos (id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT fk_movimentacoes_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios (id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;
