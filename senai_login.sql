-- Banco de dados simplificado para CRUD de CLIENTE
CREATE DATABASE IF NOT EXISTS `senai_login` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `senai_login`;

-- Tabela PERFIL
DROP TABLE IF EXISTS `perfil`;
CREATE TABLE `perfil` (
  `id_perfil` int NOT NULL AUTO_INCREMENT,
  `nome_perfil` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_perfil`),
  UNIQUE KEY `nome_perfil` (`nome_perfil`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `perfil` (`id_perfil`, `nome_perfil`) VALUES
(1, 'Adm'),
(2, 'Secretaria'),
(4, 'Cliente');

-- Tabela USUARIO
DROP TABLE IF EXISTS `usuario`;
CREATE TABLE `usuario` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `senha` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_perfil` int DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `email` (`email`),
  KEY `id_perfil` (`id_perfil`),
  FOREIGN KEY (`id_perfil`) REFERENCES `perfil` (`id_perfil`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exemplo de usuários (senha: 123456)
INSERT INTO `usuario` (`id_usuario`, `nome`, `senha`, `email`, `id_perfil`) VALUES
(1, 'Administrador', '$2y$10$rIJhd7oXSRM1XbAdQCEsA.PF3n/rxNtIAUqCkcFybzE5J.mLBsq.q', 'admin@admin', 1),
(2, 'Maria Souza', '$2y$10$RRDyLe.N/SHniQ03fG3mnuRN84K/D4wVS3BkftU7nUUFEqyOhwFDu', 'maria@empresa.com', 2),
(3, 'Ana Pereira', '$2y$10$xaWdXzOzYETic/DhbeHV2OZCAgBaOJzqo9j38DeAEKV2.grcV.L3u', 'ana@empresa.com', 4);

-- Tabela CLIENTE (apenas para o CRUD da sua prova)
DROP TABLE IF EXISTS `cliente`;
CREATE TABLE `cliente` (
  `id_cliente` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `endereco` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_cliente`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `cliente` (`id_cliente`, `nome`, `endereco`, `telefone`, `email`) VALUES
(1, 'Teresa Lisbon', 'Rua California', '(47)1234-4568', 'teresa@teresa'),
(2, 'Chefe Bolden', 'Rua dos Bombeiros, 123', '(99)1234-4321', 'bolden@bolden'),
(3, 'Capitão Hermann', 'Rua do Molys, 123', '(21)6547-7854', 'hermann@hermann');