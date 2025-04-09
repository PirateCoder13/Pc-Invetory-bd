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


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `controle_maquinas`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `contato`
--

CREATE TABLE `contato` (
  `id` int(11) NOT NULL,
  `maquina_id` int(11) NOT NULL,
  `contato_recente` datetime NOT NULL,
  `contato_anterior` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `contato`
--

INSERT INTO `contato` (`id`, `maquina_id`, `contato_recente`, `contato_anterior`) VALUES
(1, 1, '2024-01-15 14:30:00', '2024-01-14 09:15:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `maquinas`
--

CREATE TABLE `maquinas` (
  `id` int(11) NOT NULL,
  `status` varchar(20) DEFAULT NULL,
  `nome` varchar(50) NOT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `mac` varchar(17) DEFAULT NULL,
  `comentario` varchar(100) DEFAULT NULL,
  `chamado` varchar(100) DEFAULT NULL,
  `data_cadastro` date DEFAULT current_timestamp(),
  `mesh` enum('S','N') DEFAULT NULL,
  `wsus` enum('S','N') DEFAULT NULL,
  `av` enum('S','N') DEFAULT NULL,
  `ocs` enum('S','N') DEFAULT NULL,
  `regional` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `maquinas`
--

INSERT INTO `maquinas` (`id`, `status`, `nome`, `ip`, `mac`, `comentario`, `chamado`, `data_cadastro`, `mesh`, `wsus`, `av`, `ocs`, `regional`) VALUES
(1, 'Ativa', 'MAQ-001', '192.168.1.10', '00:1A:2B:3C:4D:5E', 'Sala de servidores', 'CHAM-123', '2025-03-24', 'S', 'N', 'S', 'N', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `regionais`
--

CREATE TABLE `regionais` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `comentario` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `regionais`
--

INSERT INTO `regionais` (`id`, `nome`, `comentario`) VALUES
(1, 'R1-Curitiba', ''),
(2, 'Rio de Janeiro', 'Filial zona sul');

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `login` varchar(50) NOT NULL,
  `senha` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `nome`, `login`, `senha`) VALUES
(1, 'Admin', 'admin', '123456');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `contato`
--
ALTER TABLE `contato`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `maquina_id` (`maquina_id`),
  ADD KEY `idx_maquina_contato` (`maquina_id`);

--
-- Índices de tabela `maquinas`
--
ALTER TABLE `maquinas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `regional` (`regional`),
  ADD KEY `idx_maquina_regional` (`regional`);

--
-- Índices de tabela `regionais`
--
ALTER TABLE `regionais`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `contato`
--
ALTER TABLE `contato`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `maquinas`
--
ALTER TABLE `maquinas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `regionais`
--
ALTER TABLE `regionais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `contato`
--
ALTER TABLE `contato`
  ADD CONSTRAINT `Contato_ibfk_1` FOREIGN KEY (`maquina_id`) REFERENCES `maquinas` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `maquinas`
--
ALTER TABLE `maquinas`
  ADD CONSTRAINT `Maquinas_ibfk_1` FOREIGN KEY (`regional`) REFERENCES `regionais` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
