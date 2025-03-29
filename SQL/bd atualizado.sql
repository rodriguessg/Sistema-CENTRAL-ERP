-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 29-Mar-2025 às 15:42
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de dados: `gm_sicbd`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `agendamentos`
--

CREATE TABLE `agendamentos` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `data_g` date DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `cor` varchar(7) NOT NULL DEFAULT '#ff0000'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `agendamentos`
--

INSERT INTO `agendamentos` (`id`, `nome`, `descricao`, `data_g`, `email`, `cor`) VALUES
(1, 'Computador Dell', 'e', '2025-01-29', 'gabrielsouza.workti@gmail.com', '#ff0000'),
(2, 'Computador Dell', 'e', '2025-01-29', 'gabrielsouza.workti@gmail.com', '#ff0000'),
(3, 'Computador Dell', 'e', '2025-01-29', 'gabrielsouza.workti@gmail.com', '#ff0000'),
(4, 'Computador Dell', 'e', '2025-01-29', 'gabrielsouza.workti@gmail.com', '#ff0000'),
(5, 'teste', 'teste de agendamento de lembrete de contrato', '2025-01-29', 'gabrielsouza.workti@gmail.com', '#ff0000'),
(6, 'teste', 'teste de agendamento de lembrete de contrato', '2025-01-29', 'gabrielsouza.workti@gmail.com', '#ff0000'),
(7, 'teste', 'teste de agendamento de tarefa', '2025-01-31', 'gabrielsouza.workti@gmail.com', '#ff0000'),
(8, 'teste 4', 'teste 4', '2025-01-29', 'gabrielsouza.workti@gmail.com', '#ff0000'),
(9, 'SOLICITAR NOVA GUIA ', 'SOLICITAR NOVA GUIA ', '2025-01-29', 'gabrielsouza.workti@gmail.com', '#ff0000'),
(10, 'TESTE', ' t5est', '2025-03-07', 'gabrielzsouzarodrigues@gmail.com', '#ff0000'),
(11, 'TESTE', ' t5est', '2025-03-07', 'gabrielzsouzarodrigues@gmail.com', '#ff0000'),
(12, 'TESTE', ' t5est', '2025-03-07', 'gabrielzsouzarodrigues@gmail.com', '#ff0000'),
(13, 'TESTE', ' t5est', '2025-03-07', 'gabrielzsouzarodrigues@gmail.com', '#ff0000'),
(14, 'TESTE', ' t5est', '2025-03-07', 'gabrielzsouzarodrigues@gmail.com', '#ff0000'),
(15, 'TESTE', ' t5est', '2025-03-07', 'gabrielzsouzarodrigues@gmail.com', '#ff0000'),
(16, 'TESTE', ' t5est', '2025-03-07', 'gabrielzsouzarodrigues@gmail.com', '#ff0000'),
(17, 'MONITOR', 'teste', '2025-03-22', 'gabrielzsouzarodrigues@gmail.com', '#ff0000'),
(18, 'MONITOR', 't', '2025-03-07', 'gabrielzsouzarodrigues@gmail.com', '#ff0000'),
(19, 'MONITOR', 't', '2025-03-07', 'gabrielzsouzarodrigues@gmail.com', '#ff0000'),
(20, 'TESTE', 'r', '2025-03-07', 'gabrielzsouzarodrigues@gmail.com', '#ff0000'),
(21, 'MONITOR', 'r', '2025-03-22', 'gabrielzsouzarodrigues@gmail.com', '#ff0000');

-- --------------------------------------------------------

--
-- Estrutura da tabela `codigo_atual`
--

CREATE TABLE `codigo_atual` (
  `id` int(11) NOT NULL,
  `codigo` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `codigo_atual`
--

INSERT INTO `codigo_atual` (`id`, `codigo`) VALUES
(1, 600428000012478),
(2, 600428000012478);

-- --------------------------------------------------------

--
-- Estrutura da tabela `configuracoes`
--

CREATE TABLE `configuracoes` (
  `id` int(11) NOT NULL,
  `nome_sistema` varchar(255) NOT NULL,
  `email_sistema` varchar(255) NOT NULL,
  `logotipo_sistema` varchar(255) DEFAULT NULL,
  `tema_sistema` varchar(20) DEFAULT 'claro',
  `painelalmoxarifado` tinyint(1) DEFAULT 1,
  `painelfinanceiro` tinyint(1) DEFAULT 1,
  `painelrh` tinyint(1) DEFAULT 1,
  `descricao_sistema` text DEFAULT NULL,
  `configuracoes_adicionais` longtext DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `controle_transicao`
--

CREATE TABLE `controle_transicao` (
  `id` int(11) NOT NULL,
  `mes` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `controle_transicao`
--

INSERT INTO `controle_transicao` (`id`, `mes`) VALUES
(1, '2025-03');

-- --------------------------------------------------------

--
-- Estrutura da tabela `data_criacao`
--

CREATE TABLE `data_criacao` (
  `id` int(11) NOT NULL,
  `tabela` varchar(255) NOT NULL,
  `data_criacao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `data_criacao`
--

INSERT INTO `data_criacao` (`id`, `tabela`, `data_criacao`) VALUES
(1, 'exemplo_tabela', '2024-12-30 00:00:00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `fechamento`
--

CREATE TABLE `fechamento` (
  `id` int(11) NOT NULL,
  `data_fechamento` date NOT NULL,
  `natureza` varchar(255) NOT NULL,
  `total_entrada` decimal(10,2) NOT NULL,
  `total_saida` decimal(10,2) NOT NULL,
  `saldo_atual` decimal(10,2) NOT NULL,
  `custo` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `fechamento`
--

INSERT INTO `fechamento` (`id`, `data_fechamento`, `natureza`, `total_entrada`, `total_saida`, `saldo_atual`, `custo`) VALUES
(1, '0000-00-00', 'escritório', 0.00, 6.00, 132.00, 2.00),
(2, '0000-00-00', '333903003', 0.00, 105.05, 66574.17, 26.26),
(3, '0000-00-00', '333903005', 0.00, 6.17, 66.64, 1.23),
(4, '0000-00-00', '333903010', 6494.00, 0.00, 438968.43, 32.80),
(5, '0000-00-00', 'escritório', 0.00, 6.00, 396.00, 2.00),
(6, '0000-00-00', 'ADFSDF', 0.00, 12.00, -6.00, 6.00),
(7, '0000-00-00', '333903003', 0.00, 105.05, 1814940.56, 26.26),
(8, '0000-00-00', '333903005', 0.00, 6.17, 149.31, 1.23),
(9, '0000-00-00', '333903010', 6494.00, 0.00, 14836240.90, 32.80),
(10, '0000-00-00', 'escritório', 0.00, 6.00, 1188.00, 2.00),
(11, '0000-00-00', 'ADFSDF', 0.00, 12.00, -42.00, 6.00),
(12, '0000-00-00', '333903003', 0.00, 2468.63, 49474193.94, 26.26),
(13, '0000-00-00', '333903005', 0.00, 6.17, 333.18, 1.23),
(14, '0000-00-00', '333903010', 6494.00, 0.00, 99999999.99, 32.80),
(15, '0000-00-00', '333903003', 1115.13, 0.00, 2230.26, 32.80);

-- --------------------------------------------------------

--
-- Estrutura da tabela `funcionario`
--

CREATE TABLE `funcionario` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `cargo` varchar(50) NOT NULL,
  `data_admissao` date NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `funcionario`
--

INSERT INTO `funcionario` (`id`, `nome`, `email`, `cargo`, `data_admissao`, `criado_em`, `atualizado_em`) VALUES
(1, 'GABRIEL DE SOUZA RODRIGUES', 'gabriel@teste.com', 'ASSESSOR', '2024-12-04', '2024-12-04 19:55:37', '2024-12-04 19:55:37');

-- --------------------------------------------------------

--
-- Estrutura da tabela `gestao_contratos`
--

CREATE TABLE `gestao_contratos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `SEI` varchar(50) NOT NULL,
  `objeto` text NOT NULL,
  `gestor` varchar(255) NOT NULL,
  `gestorsb` varchar(255) NOT NULL,
  `fiscais` varchar(255) NOT NULL,
  `validade` date NOT NULL,
  `contatos` varchar(255) NOT NULL,
  `valor_contrato` decimal(15,2) NOT NULL,
  `valor_aditivo` decimal(15,2) DEFAULT NULL,
  `num_parcelas` int(11) DEFAULT NULL,
  `descricao` text NOT NULL,
  `situacao` enum('Ativo','Inativo','Encerrado') NOT NULL DEFAULT 'Ativo',
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `gestao_contratos`
--

INSERT INTO `gestao_contratos` (`id`, `titulo`, `SEI`, `objeto`, `gestor`, `gestorsb`, `fiscais`, `validade`, `contatos`, `valor_contrato`, `valor_aditivo`, `num_parcelas`, `descricao`, `situacao`, `data_cadastro`) VALUES
(1, 'CLARO', '20231600/889', 'TESTE', 'GABRIEL', 'MAIK', 'CELSO', '2025-03-15', '40028922', 5.00, 0.00, 10, 'TESTE', 'Ativo', '2025-03-07 13:18:45'),
(2, 'DELL', '20231600/890', 'TESTE', 'GABRIEL', 'MAIK', 'ALTAMIR', '2025-04-01', '40028922', 15.00, 0.00, 20, 'TESTE', 'Ativo', '2025-03-07 13:21:19'),
(3, 'UNIFORMES', '20231600/891', 'TESTE', 'GABRIEL', 'MAIK', 'ALEXANDRE', '2026-01-30', '40028922', 15.00, 0.00, 10, 'TESTE', 'Ativo', '2025-03-07 13:49:02'),
(4, 'CONTRATO DE PRESTAÇÃO DE  SERVIDORES', '20231600/892', 'TESTE', 'GABRIEL', 'MAIK', 'ALEXANDRE', '2025-03-29', '40028922', 15000.00, 0.00, 5, '12', 'Ativo', '2025-03-07 13:52:10'),
(5, 'CONTRATO DE PRESTAÇÃO DE  SERVIDORES', '20231600/893', 'TESTE', 'GABRIEL', 'MAIK', 'CARUSO', '2027-02-10', '40028922', 15000.00, 0.00, 360, 'TESTE', 'Ativo', '2025-03-10 11:36:52'),
(6, 'CONTRATO DE PRESTAÇÃO DE  SERVIDORES', '20231600/894', 'TESTE', 'GABRIEL', 'MAIK', 'CARUSO', '2027-09-10', '40028922', 500000.00, 0.00, 46, 'teste', 'Ativo', '2025-03-10 11:48:05'),
(7, 'CONTRATO DE PRESTAÇÃO DE  SERVIDORES', '20231600/895', 'TESTE', 'GABRIEL', 'MAIK', 'CARUSO', '2027-07-17', '40028922', 500000.00, 0.00, 34, '1', 'Ativo', '2025-03-10 12:02:42'),
(8, 'CONTRATO DE PRESTAÇÃO DE  SERVIDORES', '20231600/896', 'TESTE', 'GABRIEL', 'MAIK', 'ALEXANDRE', '2025-03-11', '40028922', 15000.00, 0.00, 0, 'te', 'Ativo', '2025-03-10 12:03:25'),
(9, 'CONTRATO DE PRESTAÇÃO DE  SERVIDORES', '20231600/889', 'TESTE', 'GABRIEL', 'MAIK', 'ALEXANDRE', '2025-03-11', '40028922', 15000.00, 0.00, 0, '3', 'Ativo', '2025-03-10 12:09:02'),
(10, 'CONTRATO DE PRESTAÇÃO DE  SERVIDORES', '20231600/894', 'TESTE', 'GABRIEL', 'MAIK', 'CELSO', '2025-03-10', '40028922', 15000.00, 0.00, 0, 'tetst', 'Ativo', '2025-03-10 12:19:26'),
(11, 'CONTRATO DE PRESTAÇÃO DE  SERVIDORES', '20231600/889', 'TESTE', 'GABRIEL', 'MAIK', 'ALEXANDRE', '2025-03-31', '40028922', 15000.00, 0.00, 0, 'test', 'Ativo', '2025-03-10 12:20:09'),
(12, 'CONTRATO DE PRESTAÇÃO DE  SERVIDORES', '20231600/889', 'TESTE', 'GABRIEL', 'MAIK', 'CARUSO', '2025-03-31', '40028922', 15000.00, 0.00, 0, '6y', 'Ativo', '2025-03-10 12:20:47');

-- --------------------------------------------------------

--
-- Estrutura da tabela `log_eventos`
--

CREATE TABLE `log_eventos` (
  `id` int(11) NOT NULL,
  `matricula` varchar(255) NOT NULL,
  `tipo_operacao` varchar(255) NOT NULL,
  `data_operacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `log_eventos`
--

INSERT INTO `log_eventos` (`id`, `matricula`, `tipo_operacao`, `data_operacao`) VALUES
(353, 'MASTER', 'Login bem-sucedido', '2025-01-17 18:00:55'),
(354, 'MASTER', 'Login bem-sucedido', '2025-01-17 19:08:07'),
(355, 'MASTER', 'Login bem-sucedido', '2025-01-21 12:07:57'),
(356, 'PAULO', 'Login bem-sucedido', '2025-01-21 12:08:16'),
(357, 'PAULO.S', 'Login bem-sucedido', '2025-01-21 12:09:47'),
(358, 'MASTER', 'Login bem-sucedido', '2025-01-27 14:14:02'),
(359, 'MASTER', 'Login bem-sucedido', '2025-01-28 13:15:46'),
(360, 'MASTER', 'Login bem-sucedido', '2025-01-29 23:14:30'),
(361, 'MASTER', 'Login bem-sucedido', '2025-01-29 23:21:18'),
(362, 'MASTER', 'Login bem-sucedido', '2025-01-29 23:21:31'),
(363, 'MASTER', 'Login bem-sucedido', '2025-01-30 00:53:24'),
(364, '990002025', 'foi cadastrado no sistema', '2025-01-30 00:54:13'),
(365, 'Rita', 'Login bem-sucedido', '2025-01-30 01:00:53'),
(366, 'PAULO', 'Login bem-sucedido', '2025-01-31 17:53:30'),
(367, 'PAULO', 'cadastrou  o produto no estoque', '2025-01-31 17:54:04'),
(368, 'PAULO', 'cadastrou  o produto no estoque', '2025-01-31 18:36:58'),
(369, 'MASTER', 'Login bem-sucedido', '2025-01-31 18:39:58'),
(370, 'PAULO', 'Login bem-sucedido', '2025-01-31 18:40:14'),
(371, 'PAULO', 'Login bem-sucedido', '2025-01-31 18:41:16'),
(372, 'MASTER', 'Login bem-sucedido', '2025-02-03 11:56:25'),
(373, 'MASTER', 'Login bem-sucedido', '2025-02-03 12:02:31'),
(374, 'MASTER', 'Login bem-sucedido', '2025-02-03 12:15:04'),
(375, 'MASTER', 'Login falhou: Senha inválida', '2025-02-03 12:25:11'),
(376, 'MASTER', 'Login bem-sucedido', '2025-02-03 12:25:22'),
(377, 'MASTER', 'Login bem-sucedido', '2025-02-12 17:06:12'),
(378, 'MASTER', 'atualizou o produto', '2025-02-12 18:38:07'),
(379, 'MASTER', 'Login bem-sucedido', '2025-02-25 14:01:45'),
(380, 'MASTER', 'Login falhou: Senha inválida', '2025-03-06 13:29:42'),
(381, 'MASTER', 'Login bem-sucedido', '2025-03-06 13:29:52'),
(382, 'MASTER', 'Login bem-sucedido', '2025-03-10 11:47:09'),
(383, 'MASTER', 'Login bem-sucedido', '2025-03-10 13:57:09'),
(384, 'Rita', 'Login falhou: Senha inválida', '2025-03-10 19:45:39'),
(385, 'Rita', 'Login falhou: Setor incorreto', '2025-03-10 19:45:45'),
(386, 'Rita', 'Login bem-sucedido', '2025-03-10 19:45:51'),
(387, '00', 'foi cadastrado no sistema', '2025-03-11 18:24:20'),
(388, 'CONTRATOS', 'Login bem-sucedido', '2025-03-11 18:24:54'),
(389, 'CONTRATOS', 'Login bem-sucedido', '2025-03-12 12:12:14'),
(390, 'CONTRATOS', 'Login bem-sucedido', '2025-03-12 12:14:42'),
(391, 'CONTRATOS', 'Login falhou: Setor incorreto', '2025-03-12 12:15:29'),
(392, 'CONTRATOS', 'Login bem-sucedido', '2025-03-12 12:15:36'),
(393, 'CONTRATOS', 'Login bem-sucedido', '2025-03-12 12:17:51'),
(394, 'CONTRATOS', 'Login bem-sucedido', '2025-03-12 12:30:39'),
(395, 'CONTRATOS', 'Login bem-sucedido', '2025-03-12 12:32:53'),
(396, 'CONTRATOS', 'Login bem-sucedido', '2025-03-12 12:33:23'),
(397, 'CONTRATOS', 'Login bem-sucedido', '2025-03-12 12:38:57'),
(398, 'CONTRATOS', 'Login bem-sucedido', '2025-03-12 12:40:34'),
(399, 'CONTRATOS', 'Login bem-sucedido', '2025-03-12 12:41:11'),
(400, 'CONTRATOS', 'Login bem-sucedido', '2025-03-12 12:42:49'),
(401, 'CONTRATOS', 'Login bem-sucedido', '2025-03-12 12:43:55'),
(402, 'CONTRATOS', 'Login bem-sucedido', '2025-03-12 12:45:13'),
(403, 'CONTRATOS', 'Login bem-sucedido', '2025-03-12 12:46:16'),
(404, 'CONTRATOS', 'Login bem-sucedido', '2025-03-12 12:46:34'),
(405, 'CONTRATOS', 'Login bem-sucedido', '2025-03-12 12:46:49'),
(406, 'CONTRATOS', 'Login bem-sucedido', '2025-03-12 12:48:15'),
(407, 'CONTRATOS', 'Login bem-sucedido', '2025-03-12 12:48:57'),
(408, 'CONTRATOS', 'Login bem-sucedido', '2025-03-12 12:49:20'),
(409, 'MASTER', 'Login bem-sucedido', '2025-03-12 14:38:56'),
(410, 'CONTRATOS', 'Login bem-sucedido', '2025-03-12 16:15:36'),
(411, 'CLAUDIA', 'Login falhou: Senha inválida', '2025-03-12 16:17:09'),
(412, 'CLAUDIA', 'Login falhou: Setor incorreto', '2025-03-12 16:17:17'),
(413, 'CLAUDIA', 'Login bem-sucedido', '2025-03-12 16:17:25'),
(414, 'CONTRATOS', 'Login bem-sucedido', '2025-03-12 16:18:40'),
(415, 'CONTRATOS', 'Login bem-sucedido', '2025-03-12 17:47:56'),
(416, 'MASTER', 'Login bem-sucedido', '2025-03-13 14:41:46'),
(417, 'CLAUDIA', 'Login bem-sucedido', '2025-03-13 14:42:44'),
(418, 'CONTRATOS', 'Login bem-sucedido', '2025-03-13 14:46:48'),
(419, 'MASTER', 'Login bem-sucedido', '2025-03-13 16:57:09'),
(420, 'CONTRATOS', 'Login bem-sucedido', '2025-03-13 17:01:06'),
(421, 'CONTRATOS', 'Login bem-sucedido', '2025-03-13 17:04:20'),
(422, 'MASTER', 'Login bem-sucedido', '2025-03-13 17:05:19'),
(423, 'MASTER', 'Login bem-sucedido', '2025-03-13 17:12:53'),
(424, 'CONTRATOS', 'Login bem-sucedido', '2025-03-13 17:13:56'),
(425, 'MASTER', 'Login bem-sucedido', '2025-03-13 17:15:23'),
(426, 'MASTER', 'Login bem-sucedido', '2025-03-13 17:15:57'),
(427, 'MASTER', 'Login bem-sucedido', '2025-03-13 17:16:45'),
(428, 'MASTER', 'Login bem-sucedido', '2025-03-13 17:18:13'),
(429, 'MASTER', 'Login falhou: Senha inválida', '2025-03-13 17:19:53'),
(430, 'MASTER', 'Login bem-sucedido', '2025-03-13 17:20:02'),
(431, 'CONTRATOS', 'Login bem-sucedido', '2025-03-13 17:20:13'),
(432, 'MASTER', 'Login bem-sucedido', '2025-03-13 17:21:08'),
(433, 'contratos', 'Login bem-sucedido', '2025-03-13 17:21:30'),
(434, 'CONTRATOS', 'Login bem-sucedido', '2025-03-13 17:22:16'),
(435, 'CONTRATOS', 'Login bem-sucedido', '2025-03-13 17:24:44'),
(436, 'MASTER', 'Login bem-sucedido', '2025-03-13 17:27:56'),
(437, 'CONTRATOS', 'Login bem-sucedido', '2025-03-13 17:28:07'),
(438, 'CONTRATOS', 'Login bem-sucedido', '2025-03-13 17:28:21'),
(439, 'CONTRATOS', 'Login bem-sucedido', '2025-03-13 17:28:42'),
(440, 'CONTRATOS', 'Login bem-sucedido', '2025-03-13 17:29:56'),
(441, 'CONTRATOS', 'Login bem-sucedido', '2025-03-13 17:37:39'),
(442, 'CONTRATOS', 'Login bem-sucedido', '2025-03-13 17:40:48'),
(443, 'CONTRATOS', 'Login bem-sucedido', '2025-03-13 17:42:26'),
(444, 'CONTRATOS', 'Login bem-sucedido', '2025-03-13 17:43:43'),
(445, 'CONTRATOS', 'Login falhou: Senha inválida', '2025-03-13 17:56:17'),
(446, 'CONTRATOS', 'Login bem-sucedido', '2025-03-13 17:56:26'),
(447, 'CONTRATOS', 'Login bem-sucedido', '2025-03-13 17:57:49'),
(448, 'CONTRATOS', 'Login bem-sucedido', '2025-03-13 17:58:03'),
(449, 'CONTRATOS', 'Login bem-sucedido', '2025-03-13 18:00:21'),
(450, 'CONTRATOS', 'Login bem-sucedido', '2025-03-13 18:00:39'),
(451, 'CONTRATOS', 'Login bem-sucedido', '2025-03-13 18:02:04'),
(452, 'CONTRATOS', 'Login bem-sucedido', '2025-03-13 18:04:16'),
(453, 'CONTRATOS', 'Login bem-sucedido', '2025-03-13 18:04:22'),
(454, 'CONTRATOS', 'Login bem-sucedido', '2025-03-13 18:04:55'),
(455, 'MASTER', 'Login bem-sucedido', '2025-03-13 18:05:04'),
(456, 'CONTRATOS', 'Login bem-sucedido', '2025-03-13 18:05:13'),
(457, 'CONTRATOS', 'Login bem-sucedido', '2025-03-13 18:06:34'),
(458, 'CONTRATOS', 'Login bem-sucedido', '2025-03-13 18:10:50'),
(459, 'MASTER', 'Login bem-sucedido', '2025-03-13 18:10:57'),
(460, 'MASTER', 'Login bem-sucedido', '2025-03-13 18:11:39'),
(461, 'MASTER', 'Login bem-sucedido', '2025-03-13 18:12:13'),
(462, 'MASTER', 'Login bem-sucedido', '2025-03-13 18:12:54'),
(463, 'MASTER', 'Login bem-sucedido', '2025-03-13 18:13:40'),
(464, 'MASTER', 'Login bem-sucedido', '2025-03-13 18:13:56'),
(465, 'MASTER', 'Login bem-sucedido', '2025-03-13 18:15:23'),
(466, 'CONTRATOS', 'Login bem-sucedido', '2025-03-13 18:15:33'),
(467, 'CONTRATOS', 'Login bem-sucedido', '2025-03-13 18:15:50'),
(468, 'CONTRATOS', 'Login falhou: Setor incorreto', '2025-03-13 18:16:35'),
(469, 'MASTER', 'Login bem-sucedido', '2025-03-13 18:16:41'),
(470, 'CLAUDIA', 'Login falhou: Senha inválida', '2025-03-14 12:22:49'),
(471, 'CLAUDIA', 'Login bem-sucedido', '2025-03-14 12:22:58'),
(472, 'CONTRATOS', 'Login bem-sucedido', '2025-03-14 12:24:22'),
(473, 'CLAUDIA', 'Login falhou: Senha inválida', '2025-03-14 12:35:00'),
(474, 'CLAUDIA', 'Login bem-sucedido', '2025-03-14 12:35:13'),
(475, 'PAULO', 'Login bem-sucedido', '2025-03-14 14:18:27'),
(476, 'CLAUDIA', 'Login bem-sucedido', '2025-03-14 14:21:22'),
(477, 'MASTER', 'Login bem-sucedido', '2025-03-14 14:31:59'),
(478, '919', 'foi cadastrado no sistema', '2025-03-14 14:53:17'),
(479, 'MARCO', 'Login falhou: Setor incorreto', '2025-03-14 14:53:36'),
(480, 'MASTER', 'Login bem-sucedido', '2025-03-14 16:44:38'),
(481, 'MASTER', 'Login bem-sucedido', '2025-03-14 16:49:06'),
(482, 'MASTER', 'Login bem-sucedido', '2025-03-14 17:49:31'),
(483, 'MASTER', 'Login bem-sucedido', '2025-03-14 17:57:29'),
(484, 'MASTER', 'Login bem-sucedido', '2025-03-14 18:01:59'),
(485, 'CONTRATOS', 'Login falhou: Senha inválida', '2025-03-14 18:16:22'),
(486, 'CONTRATOS', 'Login falhou: Senha inválida', '2025-03-14 18:16:38'),
(487, 'MASTER', 'Login bem-sucedido', '2025-03-14 18:24:17'),
(488, 'PAULO', 'Login bem-sucedido', '2025-03-14 18:27:00'),
(489, 'MASTER', 'Login falhou: Senha inválida', '2025-03-17 14:43:09'),
(490, 'MASTER', 'Login bem-sucedido', '2025-03-17 14:43:17'),
(491, 'PAULO', 'Login bem-sucedido', '2025-03-17 14:49:14'),
(492, 'PAULO', 'Login bem-sucedido', '2025-03-17 14:50:44'),
(493, 'PAULO', 'Login bem-sucedido', '2025-03-17 15:51:34'),
(494, 'MASTER', 'Login bem-sucedido', '2025-03-17 16:23:56'),
(495, 'MASTER', 'Login bem-sucedido', '2025-03-17 16:59:16'),
(496, 'MASTER', 'atualizou o produto', '2025-03-17 17:31:02'),
(497, 'MASTER', 'atualizou o produto', '2025-03-17 17:31:52'),
(498, 'PAULO', 'atualizou o produto', '2025-03-17 17:34:37'),
(499, 'PAULO', 'atualizou o produto', '2025-03-17 17:38:28'),
(500, 'PAULO', 'Login bem-sucedido', '2025-03-17 17:47:56'),
(501, 'PAULO', 'Login bem-sucedido', '2025-03-17 17:55:10'),
(502, 'PAULO', 'cadastrou  o produto no estoque', '2025-03-17 17:58:43'),
(503, 'PAULO', 'cadastrou  o produto no estoque', '2025-03-17 18:06:40'),
(504, 'MASTER', 'Login bem-sucedido', '2025-03-17 18:09:10'),
(505, 'PAULO', 'cadastrou  o produto no estoque', '2025-03-17 18:11:43'),
(506, 'PAULO', 'atualizou o produto', '2025-03-17 18:18:57'),
(507, 'PAULO', 'atualizou o produto', '2025-03-17 18:19:00'),
(508, 'PAULO', 'atualizou o produto', '2025-03-17 18:20:31'),
(509, 'MASTER', 'atualizou o produto', '2025-03-17 18:21:57'),
(510, 'MASTER', 'atualizou o produto', '2025-03-17 18:25:02'),
(511, 'MASTER', 'atualizou o produto', '2025-03-17 18:27:43'),
(512, 'CONTRATOS', 'Login falhou: Senha inválida', '2025-03-17 19:38:34'),
(513, 'CONTRATOS', 'Login bem-sucedido', '2025-03-17 19:38:44'),
(514, 'CLAUDIA', 'Login bem-sucedido', '2025-03-17 19:39:37'),
(515, 'PAULO', 'Login bem-sucedido', '2025-03-17 19:41:12'),
(516, 'MASTER', 'Login bem-sucedido', '2025-03-18 12:14:54'),
(517, 'PAULO', 'Login bem-sucedido', '2025-03-18 12:18:42'),
(518, 'MASTER', 'Login bem-sucedido', '2025-03-18 12:57:23'),
(519, 'PAULO', 'Login falhou: Senha inválida', '2025-03-18 12:59:44'),
(520, 'MASTER', 'Login bem-sucedido', '2025-03-18 12:59:52'),
(521, 'CLAUDIA', 'Login bem-sucedido', '2025-03-18 13:35:31'),
(522, 'CLAUDIA', 'Login bem-sucedido', '2025-03-18 13:44:26'),
(523, 'PAULO', 'Login bem-sucedido', '2025-03-18 13:47:23'),
(524, 'CLAUDIA', 'Login bem-sucedido', '2025-03-18 13:47:56'),
(525, 'CLAUDIA', 'Login bem-sucedido', '2025-03-18 13:52:03'),
(526, 'PAULO', 'Login bem-sucedido', '2025-03-18 13:54:39'),
(527, 'PAULO', 'atualizou o produto', '2025-03-18 13:55:01'),
(528, 'PAULO', 'atualizou o produto', '2025-03-18 14:00:35'),
(529, 'MASTER', 'Login bem-sucedido', '2025-03-18 14:22:18'),
(530, 'MASTER', 'Login bem-sucedido', '2025-03-18 14:33:42'),
(531, 'MASTER', 'Login bem-sucedido', '2025-03-18 17:05:55'),
(532, 'PAULO', 'Login bem-sucedido', '2025-03-18 17:12:51'),
(533, 'PAULO', 'Login falhou: Setor incorreto', '2025-03-18 17:19:46'),
(534, 'PAULO', 'Login bem-sucedido', '2025-03-18 17:19:55'),
(535, 'MASTER', 'Login falhou: Senha inválida', '2025-03-18 17:33:21'),
(536, 'MASTER', 'Login bem-sucedido', '2025-03-18 17:33:28'),
(537, 'MASTER', 'Login bem-sucedido', '2025-03-18 17:33:31'),
(538, 'MASTER', 'Login bem-sucedido', '2025-03-19 18:00:58'),
(539, 'MASTER', 'atualizou o produto', '2025-03-19 18:13:16'),
(540, 'MASTER', 'atualizou o produto', '2025-03-19 18:14:48'),
(541, 'MASTER', 'atualizou o produto', '2025-03-19 18:47:49'),
(542, 'PAULO', 'Login bem-sucedido', '2025-03-19 19:32:54'),
(543, 'MASTER', 'Login bem-sucedido', '2025-03-20 18:19:26'),
(544, 'MASTER', 'Login falhou: Senha inválida', '2025-03-20 18:19:49'),
(545, 'MASTER', 'Login bem-sucedido', '2025-03-20 18:19:58'),
(546, 'PAULO', 'Login bem-sucedido', '2025-03-20 18:22:53'),
(547, 'PAULO', 'atualizou o produto', '2025-03-20 19:20:34'),
(548, 'PAULO', 'atualizou o produto', '2025-03-20 19:21:08'),
(549, 'PAULO', 'cadastrou  o produto no estoque', '2025-03-20 19:21:34'),
(550, 'MASTER', 'Login bem-sucedido', '2025-03-20 19:26:12'),
(551, 'MASTER', 'Login bem-sucedido', '2025-03-20 19:57:24'),
(552, 'MASTER', 'Login bem-sucedido', '2025-03-22 10:33:44'),
(553, 'MASTER', 'Login bem-sucedido', '2025-03-22 10:57:46'),
(554, 'MASTER', 'Login bem-sucedido', '2025-03-22 10:58:21'),
(555, 'MASTER', 'Login bem-sucedido', '2025-03-22 12:00:59'),
(556, 'MASTER', 'cadastrou  o produto no estoque', '2025-03-22 21:17:34'),
(557, 'MASTER', 'cadastrou  o produto no estoque', '2025-03-22 21:39:41'),
(558, 'MASTER', 'cadastrou  o produto no estoque', '2025-03-22 22:00:17'),
(559, 'MASTER', 'cadastrou  o produto no estoque', '2025-03-22 22:04:14'),
(560, 'MASTER', 'cadastrou  o produto no estoque', '2025-03-22 22:10:31'),
(561, 'PAULO', 'Login falhou: Senha inválida', '2025-03-24 13:09:02'),
(562, 'PAULO', 'Login bem-sucedido', '2025-03-24 13:09:12'),
(563, 'PAULO', 'Login bem-sucedido', '2025-03-24 13:16:29'),
(564, 'CONTRATOS', 'Login falhou: Senha inválida', '2025-03-24 13:26:51'),
(565, 'CONTRATOS', 'Login falhou: Senha inválida', '2025-03-24 13:26:57'),
(566, 'CONTRATOS', 'Login falhou: Senha inválida', '2025-03-24 13:27:03'),
(567, 'CONTRATOS', 'Login bem-sucedido', '2025-03-24 13:27:12'),
(568, 'MASTER', 'Login bem-sucedido', '2025-03-24 13:30:11'),
(569, 'CLAUDIA', 'Login bem-sucedido', '2025-03-24 13:30:49'),
(570, 'MASTER', 'Login bem-sucedido', '2025-03-24 13:37:02'),
(571, 'MASTER', 'Login bem-sucedido', '2025-03-24 17:35:53'),
(572, 'PAULO', 'Login bem-sucedido', '2025-03-24 17:49:04'),
(573, 'PAULO', 'Login bem-sucedido', '2025-03-24 18:12:13'),
(574, 'MASTER', 'Login bem-sucedido', '2025-03-25 12:59:22'),
(575, 'MASTER', 'Login bem-sucedido', '2025-03-25 13:47:00'),
(576, 'MASTER', 'Login falhou: Senha inválida', '2025-03-25 13:50:27'),
(577, 'MASTER', 'Login bem-sucedido', '2025-03-25 13:50:34'),
(578, 'PAULO', 'Login bem-sucedido', '2025-03-25 13:53:51'),
(579, 'MASTER', 'Login bem-sucedido', '2025-03-25 14:29:51'),
(580, 'MASTER', 'atualizou o produto', '2025-03-26 12:34:37'),
(581, 'MASTER', 'cadastrou  o produto no estoque', '2025-03-26 12:35:33'),
(582, 'PAULO', 'cadastrou  o produto no estoque', '2025-03-26 16:43:21'),
(583, 'MASTER', 'Login bem-sucedido', '2025-03-28 11:56:00'),
(584, 'MASTER', 'cadastrou  o produto no estoque', '2025-03-28 20:40:18'),
(585, 'MASTER', 'cadastrou  o produto no estoque', '2025-03-28 21:01:05'),
(586, 'MASTER', 'Login bem-sucedido', '2025-03-29 12:34:33'),
(587, 'MASTER', 'cadastrou o produto no estoque', '2025-03-29 14:01:13'),
(588, 'PAULO', 'Login bem-sucedido', '2025-03-29 14:01:50'),
(589, 'PAULO', 'cadastrou o produto no estoque', '2025-03-29 14:40:57');

-- --------------------------------------------------------

--
-- Estrutura da tabela `materiais`
--

CREATE TABLE `materiais` (
  `codigo` varchar(20) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `classificacao` varchar(255) DEFAULT NULL,
  `natureza` varchar(255) DEFAULT NULL,
  `contabil` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `materiais`
--

INSERT INTO `materiais` (`codigo`, `descricao`, `classificacao`, `natureza`, `contabil`) VALUES
('42400050002', 'RESPIRADOR DESCARTAVEL TIPO CONCHA', 'Uniformes Tecidos e Aviamentos', '333903003', '2423.03'),
('42400050015', 'MASCARA RESPIRATORIA TIPO SEMI-FACIAL', 'Uniformes Tecidos e Aviamentos', '333903003', '2423.03'),
('42400062685', 'PROTETOR FACIAL VISOR 1mm INCOLOR ID 0020004', 'Uniformes Tecidos e Aviamentos', '333903003', '2423.03'),
('43400110010', 'FILTRO AR COMPRESSOR', 'Material para manutenção e conservação de Bens móveis', '333903011', '2425.11'),
('47100016581', 'TUBO NAO METALICO AGUA 40mm 3M SIGA 0040041', 'Material Eletrico,material para conservação e manutenção de Bens', '333903010', '2424.10'),
('47100040037', 'TUBO ESGOTO 100MM', 'Material Eletrico,material para conservação e manutenção de Bens', '333903010', '2424.10'),
('47100040108', 'TUBO NAO METALICO AGUA 20mm 3M ID 0061400', 'Material Eletrico,material para conservação e manutenção de Bens', '333903010', '2424.10'),
('47300010047', 'ABRAÇADEIRA', 'Material Eletrico e Eletrônico', '333903042', '5570.42'),
('51330020022', 'BROCA DE VIDEA 6MM', 'Maquinas Ferramentas e Utensilios de Oficina', '344905220', '3273.20'),
('53250066709', 'DISPOSITIVO FIXAÇÃO TIPO GRAMPO 7 MM SIGA 0040009', 'Material Eletrico,material para conservação e manutenção de Bens', '333903010', '2424.10'),
('53250090945', 'DISPOSITIVO FIXAÇÃO TIPO GRAMPO 4 MM SIGA 0040017', 'Material Eletrico,material para conservação e manutenção de Bens', '333903010', '2424.10'),
('53500009505', 'LIXA ACABAMENTO MADEIRA GRANA 120 SIGA', 'Material Eletrico,material para conservação e manutenção de Bens', '333903010', '2424.10'),
('53500020089', 'LIXA 100', 'Material Eletrico,material para conservação e manutenção de Bens', '333903010', '2424.10'),
('54400010022', 'ESCADA DOMESTICA ALUMINIO 06 DEGRAUS', 'Outros Equipamentos', '344905206', '3886.06'),
('55100040061', 'DORMENTE DE MADEIRA 1,60 M', 'Matérias Primas', '333903021', '2424.10'),
('55100040062', 'DORMENTE DE MADEIRA 2,00 M', 'Matérias Primas', '333903021', '2424.10'),
('59300060010', 'INTERRUPTOR COM TOMADA PREDIAL 10A 250V ID 0124286', 'Material Eletrico e Eletrônico', '333903042', '5570.42'),
('59300060012', 'INTERRUPTOR C/TOM PREDIAL 2 TECLAS 20A 250V ID 0124', 'Material Eletrico e Eletrônico', '333903042', '5570.42'),
('59750002873', 'CANALETA LISA BRANCO', 'Mat. Eletr. Mat. P/ Conserv. e Manut. de Bens Imoveis; Sinaliz. e Demarc', '344903010', '2424.10'),
('59750080061', 'CANALETA NÃO METALICA BRC FECHADA 40X16X200mm ID951', 'Material Eletrico e Eletrônico', '333903042', '5570.42'),
('62300004522', 'SOQUETE ADAPTADOR ROSCA PARA LAMPADA E40 x E27', 'Material Eletrico e Eletrônico', '333903042', '5570.42'),
('63500095901', 'CONE BARRIL SINALIZACAO ZEBRADO 75CM', 'Material para Sinalização Visual e Outros', '333903033', '2444.33'),
('68500117125', 'BROCA MADEIRA HELICOIDAL 3mm 1/8\" SIGA 0040035', 'Material para Sinalização Visual e Outros', '333903033', '2444.33'),
('75100003798', 'COLCHETE Nº 07', 'Artigos em Geral e Impressos para Expediente, Escritorio', '333903005', '3437.05'),
('75100004164', 'LAPIS DE ESCRITORIO', 'Artigos em Geral e Impressos para Expediente, Escritorio', '333903005', '3437.05'),
('75100010011', 'ALMOFADA VERMELHA Nº 04', 'Artigos em Geral e Impressos para Expediente, Escritorio', '333903005', '3437.05'),
('75200060035', 'CANETA VERMELHA PONTA ARREDONDADA', 'Artigos em Geral e Impressos para Expediente, Escritorio', '333903005', '3437.05'),
('75200060089', 'CANETA VERMELHA PONTA FINA', 'Artigos em Geral e Impressos para Expediente, Escritorio', '333903005', '3437.05'),
('75400010019', 'IMPRESSO CAPA DE PROCESSO', 'Artigos em Geral e Impressos para Expediente, Escritorio', '333903005', '3437.05'),
('79200006809', 'ESPONJA - LÃ DE AÇO', 'Artigos para Limpeza, Higiêne e Toalete', '333903002', '2422.02'),
('84150060027', 'CAPACETE CINZA', 'Uniformes Tecidos e Aviamentos', '333903003', '2423.03'),
('84150060028', 'CAPACETE ABA TOTAL', 'Uniformes Tecidos e Aviamentos', '333903003', '2423.03'),
('84150078394', 'AVENTAL DE SEGURANÇA TIPO SOLDADOR EM RASPA ID 0010', 'Uniformes Tecidos e Aviamentos', '333903003', '2423.03'),
('8465001632', 'CAPACETE DE SEGURANÇA VERDE', 'Uniformes Tecidos e Aviamentos', '333903003', '2423.03');

-- --------------------------------------------------------

--
-- Estrutura da tabela `notificacoes`
--

CREATE TABLE `notificacoes` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `setor` varchar(255) NOT NULL,
  `mensagem` text NOT NULL,
  `situacao` enum('nao lida','lida') DEFAULT 'nao lida',
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `notificacoes`
--

INSERT INTO `notificacoes` (`id`, `username`, `setor`, `mensagem`, `situacao`, `data_criacao`) VALUES
(5, 'PAULO', 'estoque', 'O produto \'PROJETOR\' chegou ao limite mínimo de 5 unidades.', 'lida', '2025-03-17 18:04:45'),
(9, 'MASTER', 'administrador', 'O produto \'PROJETOR\' atingiu o limite mínimo de 5 unidades. Precisa comprar mais.', 'lida', '2025-03-17 18:28:10'),
(18, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10L212\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'lida', '2025-03-17 18:44:32'),
(102, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10L212\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'lida', '2025-03-18 12:18:45'),
(103, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'lida', '2025-03-18 12:18:45'),
(242, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'lida', '2025-03-25 14:21:06'),
(243, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'lida', '2025-03-25 14:25:14'),
(244, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'lida', '2025-03-25 14:29:14'),
(245, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'lida', '2025-03-25 14:41:57'),
(246, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'lida', '2025-03-25 16:54:20'),
(247, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'lida', '2025-03-25 16:55:31'),
(248, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'lida', '2025-03-25 16:55:36'),
(249, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'lida', '2025-03-25 16:55:38'),
(250, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'lida', '2025-03-25 16:55:52'),
(251, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'lida', '2025-03-25 16:57:58'),
(252, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'lida', '2025-03-25 16:58:00'),
(253, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'lida', '2025-03-25 16:58:06'),
(254, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'lida', '2025-03-25 16:58:15'),
(255, 'PAULO', 'estoque', 'O produto \'78\' chegou ao limite mínimo de 5 unidades.', 'lida', '2025-03-25 17:19:08'),
(256, 'PAULO', 'estoque', 'O produto \'70\' atingiu o limite mínimo de 5 unidades. Precisa comprar mais.', 'lida', '2025-03-25 17:28:15'),
(257, 'PAULO', 'estoque', 'O produto \'70\' atingiu o limite mínimo de 5 unidades. Precisa comprar mais.', 'lida', '2025-03-25 17:28:30'),
(258, 'PAULO', 'estoque', 'O produto \'70\' atingiu o limite mínimo de 5 unidades. Precisa comprar mais.', 'lida', '2025-03-25 17:29:53'),
(259, 'PAULO', 'estoque', 'O produto \'78\' chegou ao limite mínimo de 5 unidades.', 'lida', '2025-03-25 17:30:11'),
(260, 'PAULO', 'estoque', 'O produto \'78\' chegou ao limite mínimo de 5 unidades.', 'lida', '2025-03-25 17:33:13'),
(261, 'PAULO', 'estoque', 'O produto \'75\' chegou ao limite mínimo de 5 unidades.', 'lida', '2025-03-25 17:45:44'),
(262, 'PAULO', 'estoque', 'O produto \'75\' chegou ao limite mínimo de 5 unidades.', 'lida', '2025-03-25 17:46:15'),
(263, 'MASTER', 'administrador', 'O produto \'69\' atingiu o limite mínimo de 5 unidades. Precisa comprar mais.', 'lida', '2025-03-28 20:31:38'),
(264, 'MASTER', 'administrador', 'O produto \'69\' atingiu o limite mínimo de 5 unidades. Precisa comprar mais.', 'lida', '2025-03-28 20:31:45'),
(265, 'MASTER', 'administrador', 'O produto \'70\' atingiu o limite mínimo de 5 unidades. Precisa comprar mais.', 'lida', '2025-03-28 20:32:08'),
(266, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-29 14:01:53'),
(267, 'PAULO', 'estoque', 'O produto \'PROJETOR\' está com 4 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-29 14:01:53'),
(268, 'PAULO', 'estoque', 'O produto \'82\' chegou ao limite mínimo de 5 unidades.', 'nao lida', '2025-03-29 14:39:21');

-- --------------------------------------------------------

--
-- Estrutura da tabela `pagamentos`
--

CREATE TABLE `pagamentos` (
  `id` int(11) NOT NULL,
  `id_contrato` int(11) NOT NULL,
  `parcela` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `data_pagamento` date NOT NULL,
  `data_lancamento` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `painel_config`
--

CREATE TABLE `painel_config` (
  `id` int(11) NOT NULL,
  `painelalmoxarifado` tinyint(1) DEFAULT 1,
  `painelfinanceiro` tinyint(1) DEFAULT 1,
  `painelrh` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `patrimonio`
--

CREATE TABLE `patrimonio` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `data_aquisicao` date DEFAULT NULL,
  `situacao` varchar(100) DEFAULT NULL,
  `localizacao` varchar(255) DEFAULT NULL,
  `data_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `codigo` varchar(50) NOT NULL,
  `categoria` varchar(100) DEFAULT NULL,
  `cadastrado_por` varchar(255) DEFAULT NULL,
  `destino` varchar(255) DEFAULT NULL,
  `responsavel` varchar(255) DEFAULT NULL,
  `tipo_operacao` varchar(255) NOT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `patrimonio`
--

INSERT INTO `patrimonio` (`id`, `nome`, `descricao`, `valor`, `data_aquisicao`, `situacao`, `localizacao`, `data_registro`, `codigo`, `categoria`, `cadastrado_por`, `destino`, `responsavel`, `tipo_operacao`, `foto`) VALUES
(1, 'teste12', 'teste', 120.00, '2024-11-21', 'inativo', 'sala 703', '2024-11-21 05:08:43', '600428000012477', 'equipamentos_informatica', 'CLAUDIA', NULL, NULL, 'Transferido', NULL),
(2, 'NOTEBOOK ', 'teste', 2.00, '2024-11-23', 'inativo', 'SALA - 404', '2024-11-24 00:18:39', '600428000012478', 'equipamentos_informatica', NULL, NULL, NULL, '', NULL),
(3, 'NOTEBOOK ', 'teste', 235.00, '2024-11-23', 'Em Processo de baixa', 'SALA - 404', '2024-11-24 00:20:17', '600428000012479', 'equipamentos_informatica', NULL, NULL, NULL, '', 'GABRIEL'),
(4, 'teste', 'teste', 12.00, '2024-11-23', 'ativo', '202', '2024-11-24 00:21:54', '705100000000196', 'bens_achados', 'GABRIEL', NULL, NULL, '', NULL),
(5, 'teste', 'teste', 780.00, '2024-11-23', 'ativo', 'sala 703', '2024-11-24 00:26:27', '450518000002335', 'moveis_utensilios', 'GABRIEL', NULL, NULL, '', NULL),
(6, 'NOTEBOOK ', 'teste', 90.00, '2024-11-23', 'ativo', 'sala 703', '2024-11-24 00:30:13', '460000000000000', 'reserva_bens_moveis', 'GABRIEL', NULL, NULL, '', NULL),
(7, 'teste120', 'teste', 6.00, '2024-11-23', 'ativo', 'sala 703', '2024-11-24 00:35:53', '1', 'bens_com_baixo_valor', 'GABRIEL', NULL, NULL, '', NULL),
(8, 'NOTEBOOK ', 'teste', 123.00, '2024-11-24', 'ativo', '202', '2024-11-24 10:47:44', '600428000012480', 'equipamentos_informatica', 'MASTER', NULL, NULL, '', NULL),
(9, 'NOTEBOOK ', 't', 3.00, '2024-11-24', 'ativo', 'sala 703', '2024-11-24 10:51:59', '600428000012481', 'equipamentos_informatica', 'MASTER', NULL, NULL, '', 'default.png'),
(10, 'Computador Dell', 'teste', 500.00, '2024-11-24', 'ativo', '202', '2024-11-24 10:54:00', '600428000012482', 'equipamentos_informatica', 'MASTER', NULL, NULL, '', 'default.png'),
(11, 'Computador Dell', 'teste', 1450.00, '2024-11-24', 'ativo', 'SALA - 404', '2024-11-24 10:56:09', '600428000012483', 'equipamentos_informatica', 'MASTER', NULL, NULL, '', NULL),
(12, 'teste12', 'test', 23412.00, '2024-11-24', 'ativo', 'sala 703', '2024-11-24 10:57:29', '600428000012484', 'equipamentos_informatica', 'MASTER', NULL, NULL, '', 'default.png'),
(13, 'NOTEBOOK ', 'teste', 450.00, '2024-11-24', 'ativo', 'SALA - 404', '2024-11-24 11:09:46', '600428000012485', 'equipamentos_informatica', 'MASTER', NULL, NULL, '', 'patrimonio-6743097a59877.jpg'),
(14, 'teste', 'teste', 123.00, '2024-11-24', 'ativo', 'SALA 501', '2024-11-24 11:12:35', '450518000002336', 'moveis_utensilios', 'MASTER', NULL, NULL, '', 'patrimonio-67430a237e743.jpg'),
(15, 'testea20', 'test', 1235.00, '2024-11-24', 'ativo', '202', '2024-11-24 14:13:58', '600428000012486', 'equipamentos_informatica', 'MASTER', NULL, NULL, '', 'default.png'),
(16, 'teste40', 'teste', 1.23, '2024-11-24', 'Em Processo de baixa', 'SALA - 404', '2024-11-24 14:14:28', '705100000000197', 'bens_achados', 'MASTER', NULL, NULL, '', 'default.png'),
(17, 'NOTEBOOK ', 'DELL LATITUDE 5440', 5600.00, '2024-11-25', 'em uso', 'SALA - 404', '2024-11-25 14:18:41', '600428000012487', 'equipamentos_informatica', 'GABRIEL', NULL, NULL, '', 'patrimonio-6744874180aee.jpeg'),
(18, 'GABRIEL DE SOUZA RODRIGUES', 'er', 23.00, '2024-12-10', 'ativo', '102', '2024-12-10 19:20:36', '600428000012488', 'equipamentos_informatica', 'MASTER', NULL, NULL, '', 'default.png'),
(19, 'MONITOR', 'teste', 10.00, '2024-12-30', 'ativo', 'SALA 501', '2024-12-30 13:51:06', '600428000012489', 'equipamentos_informatica', 'MASTER', NULL, NULL, '', 'patrimonio-6772a54a85261.jpg'),
(20, 'Monitor', 'Monitor DELL', 1.20, '2025-01-08', 'Em Processo de baixa', 'CENTRAL', '2025-01-08 13:41:03', '600428000012490', 'equipamentos_informatica', 'MASTER', NULL, NULL, '', 'default.png');

-- --------------------------------------------------------

--
-- Estrutura da tabela `permissoes`
--

CREATE TABLE `permissoes` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `setor` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `setor_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `permissoes`
--

INSERT INTO `permissoes` (`id`, `usuario_id`, `setor`, `created_at`, `setor_id`) VALUES
(1, 47, 1, '2024-11-18 01:59:53', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `produto` varchar(255) NOT NULL,
  `classificacao` varchar(100) DEFAULT NULL,
  `natureza` varchar(100) DEFAULT NULL,
  `contabil` varchar(100) DEFAULT NULL,
  `descricao` varchar(50) DEFAULT NULL,
  `unidade` varchar(50) DEFAULT NULL,
  `localizacao` varchar(100) DEFAULT NULL,
  `custo` decimal(10,5) DEFAULT NULL,
  `quantidade` int(11) DEFAULT NULL,
  `nf` varchar(50) DEFAULT NULL,
  `preco_medio` decimal(10,2) DEFAULT NULL,
  `tipo_operacao` varchar(50) DEFAULT 'entrada',
  `data_cadastro` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `produtos`
--

INSERT INTO `produtos` (`id`, `produto`, `classificacao`, `natureza`, `contabil`, `descricao`, `unidade`, `localizacao`, `custo`, `quantidade`, `nf`, `preco_medio`, `tipo_operacao`, `data_cadastro`) VALUES
(69, 'PAPEL A4w', 'TESTE', 'TESTE', '20.2200', '24716081', 'UM', 'xm2', 12.00000, 5, '', 6.00, 'retirado', '2023-01-10 15:12:03'),
(70, 'SACO LIXO 10L212', 'TESTE', '1', '2', '24716081', 'UM', 'xm2', 21.00000, 10, '', 10.50, 'retirado', '2024-01-10 15:13:45'),
(71, 'SACO LIXO 10LD', 'TESTE', 'W2', '20.2200', '22', '2', 'xm2', 2.00000, 2, '', 1.00, 'cadastrado', '2025-01-31 14:54:04'),
(75, 'PROJETOR', 'TESTE', 'TESTE', '20.2200', '24452005', '123', 'xm1', 2.00000, 4, '', 0.01, 'retirado', '2025-03-17 15:11:43'),
(76, 'CANETA AZUL', 'ESCRITÓRIO', 'escritório', '2', '3', '2', 'xm2', 2.00000, 1785, '3', 0.06, 'retirado', '2025-03-20 16:21:34'),
(77, 'MONITOR 24 POLEGADAS', 'INFORMÁTICA', 'ESCRITÓRIO', '2025.20', '500', 'UM', 'xm1', 32.80000, 138, '1', 237.67, 'cadastrado', '2025-03-22 18:17:34'),
(79, 'teste de custo', '3', '3', '3', '3', '3', 'xm1', 4.43000, 0, '1', 492.67, 'retirado', '2025-03-22 19:00:17'),
(80, 'SACO LIXO 10L90', 'ESCRITÓRIO', 'TESTE123', '2', '1', 'um', 'xm2', 4.43400, 23, '1', 192.78, 'cadastrado', '2025-03-22 19:04:14'),
(81, 'PAPEL A3', 'TESTE', '23', '1', '23q', 'um', 'xm2', 89340.00000, 198, '1', 451.21, 'cadastrado', '2025-03-22 19:10:31'),
(82, 'PROJETOR54', 'SDFASDFAF', 'ADFSDF', 'ASDFASDF', 'ASDFASD', 'FASADF', 'xm1', 6.00000, -47, '234', 0.00, 'retirado', '2025-03-26 09:35:33'),
(83, '42400050015', 'Uniformes Tecidos e Aviamentos', '333903003', '2423.03', '42400050015', 'UM', 'xm1', 26.26200, 51360388, '3', 250.12, 'retirado', '2025-03-26 13:43:21'),
(84, '42400050002', 'Uniformes Tecidos e Aviamentos', '333903003', '2423.03', 'RESPIRADOR DESCARTAVEL TIPO CONCHA', 'UM3', 'xm1', 90.00000, 34, '2', 2.00, 'cadastrado', '2025-03-28 17:40:18'),
(85, '75200060035', 'Artigos em Geral e Impressos para Expediente, Escritorio', '333903005', '3437.05', 'CANETA VERMELHA PONTA ARREDONDADA', 'UM', 'xm1', 1.23400, 608, '1', 36.00, 'retirado', '2025-03-28 18:01:05'),
(86, '47100040108', 'Material Eletrico,material para conservação e manutenção de Bens', '333903010', '2424.10', 'TUBO NAO METALICO AGUA 20mm 3M ID 0061400', 'UM', 'xm1', 32.79800, 516723668, '2', 165.00, 'cadastrado', '2025-03-29 11:01:13'),
(87, '84150078394', 'Uniformes Tecidos e Aviamentos', '333903003', '2423.03', 'AVENTAL DE SEGURANÇA TIPO SOLDADOR EM RASPA ID 001', 'UM', 'xm1', 32.79800, 49476458, '2', 964.00, 'cadastrado', '2025-03-29 11:40:57');

-- --------------------------------------------------------

--
-- Estrutura da tabela `setores`
--

CREATE TABLE `setores` (
  `id` int(11) NOT NULL,
  `nome_setor` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `setores`
--

INSERT INTO `setores` (`id`, `nome_setor`) VALUES
(1, 'Financeiro'),
(2, 'Patrimonio'),
(3, 'estoque'),
(4, 'administrador'),
(5, 'contratos');

-- --------------------------------------------------------

--
-- Estrutura da tabela `transferencias`
--

CREATE TABLE `transferencias` (
  `id` int(11) NOT NULL,
  `patrimonio_id` int(11) NOT NULL,
  `destino` varchar(255) NOT NULL,
  `data_transferencia` date NOT NULL,
  `observacao` text DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `responsavel` varchar(255) NOT NULL,
  `tipo_operacao` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `transferencias`
--

INSERT INTO `transferencias` (`id`, `patrimonio_id`, `destino`, `data_transferencia`, `observacao`, `criado_em`, `atualizado_em`, `responsavel`, `tipo_operacao`) VALUES
(1, 1, 'DIRAF', '2024-11-21', NULL, '2024-11-21 07:07:51', '2024-11-21 07:07:51', 'Claudia', ''),
(4, 1, 'GERCOM', '2024-11-24', NULL, '2024-11-23 23:43:46', '2024-11-23 23:43:46', 'Claudia', ''),
(5, 1, 'ASSPRIN', '2024-11-24', NULL, '2024-11-23 23:51:43', '2024-11-23 23:51:43', 'Claudia', 'Transferido'),
(6, 1, 'SUPLAN', '2024-11-24', NULL, '2024-11-23 23:57:44', '2024-11-23 23:57:44', 'Claudia', 'Transferido');

-- --------------------------------------------------------

--
-- Estrutura da tabela `transferencia_historico`
--

CREATE TABLE `transferencia_historico` (
  `id` int(11) NOT NULL,
  `patrimonio_id` int(11) NOT NULL,
  `origem` varchar(100) NOT NULL,
  `destino` varchar(100) NOT NULL,
  `data_transferencia` date NOT NULL,
  `responsavel` varchar(100) NOT NULL,
  `observacao` text DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `transicao`
--

CREATE TABLE `transicao` (
  `id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `data` date NOT NULL,
  `tipo` enum('Entrada','Saída') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `transicao`
--

INSERT INTO `transicao` (`id`, `material_id`, `quantidade`, `data`, `tipo`) VALUES
(1, 83, 2, '2025-03-28', 'Saída'),
(2, 83, 2, '2025-03-28', 'Saída'),
(3, 76, 3, '2025-03-28', 'Saída'),
(4, 85, 5, '2025-03-29', 'Saída'),
(6, 86, 198, '2025-03-29', 'Entrada'),
(7, 82, 2, '2025-03-29', 'Saída'),
(8, 87, 34, '2025-03-29', 'Entrada'),
(9, 83, 90, '2025-03-29', 'Saída');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `matricula` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `setor` enum('administrador','estoque','patrimonio','financeiro','contratos') NOT NULL,
  `cargo` varchar(50) NOT NULL,
  `situacao` enum('ativo','inativo') DEFAULT 'ativo',
  `foto` varchar(255) DEFAULT 'default.png',
  `primeira_vez` tinyint(1) DEFAULT 1,
  `tempo_registro` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `usuario`
--

INSERT INTO `usuario` (`id`, `username`, `senha`, `matricula`, `email`, `setor`, `cargo`, `situacao`, `foto`, `primeira_vez`, `tempo_registro`) VALUES
(1, 'MASTER', '$2y$10$7P1saBTiDCJSR1vseg4hI.S2kIIrLXlOsiHjOu0gRShRynbyKir0.', '000000', 'master@example.com', 'administrador', 'Supervisor', 'ativo', 'default.png', 1, '2024-11-21 02:54:34'),
(2, 'CLAUDIA', '$2y$10$hKZax9b6jgLxxg0UC3VN4OY7ErQVELisOsbfKN8TKdV/PnfQgQxQe', '2', 'claudia@gmail.com', 'patrimonio', 'ASSESSORA', 'ativo', 'default.png', 1, '2024-11-21 02:54:34'),
(3, 'PAULO', '$2y$10$VpFjjjRo.J8QX6JFXBMa5.VsNlIkcZdxXxDGy2KjQgmcJIhvRFto6', '3', 'paulo@gmail.com', 'estoque', 'ASSESSOR', 'ativo', 'default.png', 1, '2024-11-21 02:54:34'),
(4, 'PATRICIA', '$2y$10$J/b3Kn6/nP0XgmjxDmxKueOztTlZ0jNeeRrgywzZJwNxjKMvrsn.m', '4', 'patricia@gmail.com', 'estoque', 'GERENTE', 'ativo', 'default.png', 1, '2024-11-21 02:54:34'),
(5, 'PAULO.S', '$2y$10$Qlj.UVDk7vNLhcn1ySlqxetP6wGr6EgJO3kWyBptVflKZiuXeplYq', '5', 'paulos@gmail.com', 'financeiro', 'SUPERINTENDENTE', 'ativo', 'default.png', 1, '2024-11-21 02:54:34'),
(6, 'GABRIEL', '$2y$10$DEFqa14RkeKQvCzPfLOsQuHtbpD3TOFXJV/sEFJOaM2qhMhZaNoO2', '99000889', 'grodrigues@central.rj.gov.br', 'administrador', 'Analista de Suporte de Sistemas', 'ativo', 'perfil-user-673f82c52395e.jpg', 1, NULL),
(9, 'Rita', '$2y$10$b1rZmXkpaeueSD.fC1jtt.aRWzP9zSiwKcA8mrblToKKmcWkZGuyS', '990002025', 'rita@central.rj.gov.br', 'contratos', 'ASSESSORA', 'ativo', 'default.png', 1, NULL),
(10, 'CONTRATOS', '$2y$10$QG5qwt6JfD48s1.YqqS.M.7SUz/p5hBt4SiKnABpKiMN1cGLctEiW', '00', 'asscon@central.rj.gov.br', 'contratos', 'contratos', 'ativo', 'default.png', 1, '2025-03-25 11:43:45'),
(11, 'MARCO', '$2y$10$V/PS4I1wx9viy9zkpuUMKO7B4AUC/PIRBI.ZZI6LYSvbSBDlQhAIi', '919', 'GERGEP@CENTRAL.RJ.GOV.BR', 'financeiro', 'SUPERINTENDENTE', 'ativo', 'default.png', 1, NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `codigo_atual`
--
ALTER TABLE `codigo_atual`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `controle_transicao`
--
ALTER TABLE `controle_transicao`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mes` (`mes`);

--
-- Índices para tabela `data_criacao`
--
ALTER TABLE `data_criacao`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `fechamento`
--
ALTER TABLE `fechamento`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `funcionario`
--
ALTER TABLE `funcionario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices para tabela `gestao_contratos`
--
ALTER TABLE `gestao_contratos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `log_eventos`
--
ALTER TABLE `log_eventos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `materiais`
--
ALTER TABLE `materiais`
  ADD PRIMARY KEY (`codigo`);

--
-- Índices para tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_contrato` (`id_contrato`);

--
-- Índices para tabela `painel_config`
--
ALTER TABLE `painel_config`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `patrimonio`
--
ALTER TABLE `patrimonio`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Índices para tabela `permissoes`
--
ALTER TABLE `permissoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `setor_id` (`setor`);

--
-- Índices para tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `setores`
--
ALTER TABLE `setores`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `transferencias`
--
ALTER TABLE `transferencias`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `transferencia_historico`
--
ALTER TABLE `transferencia_historico`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patrimonio_id` (`patrimonio_id`);

--
-- Índices para tabela `transicao`
--
ALTER TABLE `transicao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `material_id` (`material_id`);

--
-- Índices para tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `matricula` (`matricula`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de tabela `codigo_atual`
--
ALTER TABLE `codigo_atual`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `controle_transicao`
--
ALTER TABLE `controle_transicao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `data_criacao`
--
ALTER TABLE `data_criacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `fechamento`
--
ALTER TABLE `fechamento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `funcionario`
--
ALTER TABLE `funcionario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `gestao_contratos`
--
ALTER TABLE `gestao_contratos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `log_eventos`
--
ALTER TABLE `log_eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=590;

--
-- AUTO_INCREMENT de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=269;

--
-- AUTO_INCREMENT de tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `painel_config`
--
ALTER TABLE `painel_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `patrimonio`
--
ALTER TABLE `patrimonio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de tabela `permissoes`
--
ALTER TABLE `permissoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT de tabela `setores`
--
ALTER TABLE `setores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `transferencias`
--
ALTER TABLE `transferencias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `transferencia_historico`
--
ALTER TABLE `transferencia_historico`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `transicao`
--
ALTER TABLE `transicao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  ADD CONSTRAINT `fk_contrato` FOREIGN KEY (`id_contrato`) REFERENCES `gestao_contratos` (`id`);

--
-- Limitadores para a tabela `transicao`
--
ALTER TABLE `transicao`
  ADD CONSTRAINT `transicao_ibfk_1` FOREIGN KEY (`material_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
