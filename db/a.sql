

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 25/03/2025 às 23:51
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `controle_maquinas` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `controle_maquinas`;

-- Estrutura para tabela `contato`
CREATE TABLE `contato` (
  `id` int(11) NOT NULL,
  `maquina_id` int(11) NOT NULL,
  `contato_recente` datetime NOT NULL,
  `contato_anterior` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Estrutura para tabela `maquinas`
CREATE TABLE `maquinas` (
  `id` int(11) NOT NULL,
  `status` varchar(20) DEFAULT NULL,
  `nome` varchar(50) NOT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `mac` varchar(17) DEFAULT NULL,
  `comentario` varchar(100) DEFAULT NULL,
  `chamado` varchar(100) DEFAULT NULL,
  `data_cadastro` date DEFAULT current_timestamp(),
  `data_contato` datetime DEFAULT NULL,
  `mesh` enum('S','N') DEFAULT NULL,
  `wsus` enum('S','N') DEFAULT NULL,
  `av` enum('S','N') DEFAULT NULL,
  `ocs` enum('S','N') DEFAULT NULL,
  `regional` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Estrutura para tabela `regionais`
CREATE TABLE `regionais` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `comentario` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Estrutura para tabela `users`
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `login` varchar(50) NOT NULL,
  `senha` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Índices
ALTER TABLE `contato`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `maquina_id` (`maquina_id`),
  ADD KEY `idx_maquina_contato` (`maquina_id`);

ALTER TABLE `maquinas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `regional` (`regional`),
  ADD KEY `idx_maquina_regional` (`regional`);

ALTER TABLE `regionais`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

-- AUTO_INCREMENT
ALTER TABLE `contato`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `maquinas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `regionais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

-- Restrições (FK)
ALTER TABLE `contato`
  ADD CONSTRAINT `Contato_ibfk_1` FOREIGN KEY (`maquina_id`) REFERENCES `maquinas` (`id`) ON DELETE CASCADE;

ALTER TABLE `maquinas`
  ADD CONSTRAINT `Maquinas_ibfk_1` FOREIGN KEY (`regional`) REFERENCES `regionais` (`id`);

COMMIT;