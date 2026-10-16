-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 16/10/2026 às 18:40
-- Versão do servidor: 8.0.40
-- Versão do PHP: 8.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `saep_db`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `agendamentos`
--

CREATE TABLE `agendamentos` (
  `id` int NOT NULL,
  `cliente_id` int NOT NULL,
  `data` date NOT NULL,
  `horario` time NOT NULL,
  `servico` varchar(100) NOT NULL,
  `observacoes` varchar(255) DEFAULT NULL,
  `status` varchar(30) DEFAULT 'Agendado'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `carros`
--

CREATE TABLE `carros` (
  `id` int NOT NULL,
  `dono_id` int NOT NULL,
  `modelo` varchar(50) NOT NULL,
  `marca` varchar(50) NOT NULL,
  `placa` varchar(10) DEFAULT NULL,
  `cor` varchar(30) DEFAULT NULL,
  `ano` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `carros`
--

INSERT INTO `carros` (`id`, `dono_id`, `modelo`, `marca`, `placa`, `cor`, `ano`) VALUES
(1, 1, 'Gol', 'Volkswagen', 'ABC1D23', 'Prata', 2018),
(2, 1, 'Onix', 'Chevrolet', 'DEF4G56', 'Branco', 2021),
(3, 2, 'Corolla', 'Toyota', 'GHI7J89', 'Preto', 2020),
(4, 3, 'HB20', 'Hyundai', 'JKL0M12', 'Vermelho', 2019);

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `id` int NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cpf` varbinary(255) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `dono`
--

CREATE TABLE `dono` (
  `id` int NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cpf` varbinary(255) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `dono`
--

INSERT INTO `dono` (`id`, `nome`, `cpf`, `telefone`, `email`) VALUES
(1, 'Ana Souza', 0x693edeeb94abd227ad0021c1a1dedace, '(11) 99999-1111', 'ana@email.com'),
(2, 'Pedro Lima', 0xe18687b33403b877c44037378cc004da, '(11) 98888-2222', 'pedro@email.com'),
(3, 'Lucia Alves', 0xb4a8f534df1f80181ce8c480e5c29028, '(11) 97777-3333', 'lucia@email.com');

-- --------------------------------------------------------

--
-- Estrutura para tabela `procedimentos`
--

CREATE TABLE `procedimentos` (
  `id` int NOT NULL,
  `carro_id` int NOT NULL,
  `data` date NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `valor` decimal(8,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `procedimentos`
--

INSERT INTO `procedimentos` (`id`, `carro_id`, `data`, `descricao`, `valor`) VALUES
(1, 1, '2025-09-01', 'Troca de óleo e filtro', 180.00),
(2, 1, '2025-09-15', 'Alinhamento e balanceamento', 120.00),
(3, 2, '2025-09-02', 'Revisão dos freios', 350.00),
(4, 3, '2025-09-03', 'Troca da bateria', 450.00),
(5, 4, '2025-09-05', 'Troca das velas', 200.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `procedimentos_salao`
--

CREATE TABLE `procedimentos_salao` (
  `id` int NOT NULL,
  `agendamento_id` int NOT NULL,
  `data` date NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `valor` decimal(8,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `nome` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `senha`) VALUES
(1, 'João Silva', '$2y$10$zBl9XM/gvMWUFinE/Q7vDO8VFqDVENdhQVbKD1jiBdNvmtCwgpWPO'),
(2, 'Maria Santos', '$2y$10$FbjJfLOoCOd6NMuI1X4LIO/73fkC7vB.4R4m3LjldVY3Ab.DOqra2'),
(3, 'Carlos Ferreira', '$2y$10$XkD3Ox2URr2bPXP0uqVm8ul0p4EiF105EI/pse1hMqXLx/2R4VE4C');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`);

--
-- Índices de tabela `carros`
--
ALTER TABLE `carros`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dono_id` (`dono_id`);

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `dono`
--
ALTER TABLE `dono`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `procedimentos`
--
ALTER TABLE `procedimentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `carro_id` (`carro_id`);

--
-- Índices de tabela `procedimentos_salao`
--
ALTER TABLE `procedimentos_salao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `agendamento_id` (`agendamento_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `carros`
--
ALTER TABLE `carros`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `dono`
--
ALTER TABLE `dono`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `procedimentos`
--
ALTER TABLE `procedimentos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `procedimentos_salao`
--
ALTER TABLE `procedimentos_salao`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD CONSTRAINT `agendamentos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `carros`
--
ALTER TABLE `carros`
  ADD CONSTRAINT `carros_ibfk_1` FOREIGN KEY (`dono_id`) REFERENCES `dono` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `procedimentos`
--
ALTER TABLE `procedimentos`
  ADD CONSTRAINT `procedimentos_ibfk_1` FOREIGN KEY (`carro_id`) REFERENCES `carros` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `procedimentos_salao`
--
ALTER TABLE `procedimentos_salao`
  ADD CONSTRAINT `procedimentos_salao_ibfk_1` FOREIGN KEY (`agendamento_id`) REFERENCES `agendamentos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
