-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 18/03/2025 às 12:53
-- Versão do servidor: 10.4.28-MariaDB
-- Versão do PHP: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `supat`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `agendamentos`
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
-- Despejando dados para a tabela `agendamentos`
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
-- Estrutura para tabela `codigo_atual`
--

CREATE TABLE `codigo_atual` (
  `id` int(11) NOT NULL,
  `codigo` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `codigo_atual`
--

INSERT INTO `codigo_atual` (`id`, `codigo`) VALUES
(1, 600428000012478),
(2, 600428000012478);

-- --------------------------------------------------------

--
-- Estrutura para tabela `data_criacao`
--

CREATE TABLE `data_criacao` (
  `id` int(11) NOT NULL,
  `tabela` varchar(255) NOT NULL,
  `data_criacao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `data_criacao`
--

INSERT INTO `data_criacao` (`id`, `tabela`, `data_criacao`) VALUES
(1, 'exemplo_tabela', '2024-12-30 00:00:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `funcionario`
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
-- Despejando dados para a tabela `funcionario`
--

INSERT INTO `funcionario` (`id`, `nome`, `email`, `cargo`, `data_admissao`, `criado_em`, `atualizado_em`) VALUES
(1, 'GABRIEL DE SOUZA RODRIGUES', 'gabriel@teste.com', 'ASSESSOR', '2024-12-04', '2024-12-04 19:55:37', '2024-12-04 19:55:37');

-- --------------------------------------------------------

--
-- Estrutura para tabela `gestao_contratos`
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
-- Despejando dados para a tabela `gestao_contratos`
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
-- Estrutura para tabela `log_eventos`
--

CREATE TABLE `log_eventos` (
  `id` int(11) NOT NULL,
  `matricula` varchar(255) NOT NULL,
  `tipo_operacao` varchar(255) NOT NULL,
  `data_operacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `log_eventos`
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
(515, 'PAULO', 'Login bem-sucedido', '2025-03-17 19:41:12');

-- --------------------------------------------------------

--
-- Estrutura para tabela `notificacoes`
--

CREATE TABLE `notificacoes` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `setor` varchar(255) NOT NULL,
  `mensagem` text NOT NULL,
  `status` enum('nao lida','lida') DEFAULT 'nao lida',
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `notificacoes`
--

INSERT INTO `notificacoes` (`id`, `username`, `setor`, `mensagem`, `status`, `data_criacao`) VALUES
(1, 'PAULO', 'estoque', 'O produto \'PROJETOR\' foi retirado do estoque. Quantidade retirada: 5.', 'lida', '2025-03-17 17:47:33'),
(2, 'PAULO', 'estoque', 'O produto \'PROJETOR\' chegou ao limite mínimo de 5 unidades.', 'lida', '2025-03-17 17:59:22'),
(3, 'PAULO', 'estoque', 'O produto \'PROJETOR\' chegou ao limite mínimo de 5 unidades.', 'lida', '2025-03-17 18:01:05'),
(4, 'PAULO', 'estoque', 'O produto \'PROJETOR\' chegou ao limite mínimo de 5 unidades.', 'lida', '2025-03-17 18:04:05'),
(5, 'PAULO', 'estoque', 'O produto \'PROJETOR\' chegou ao limite mínimo de 5 unidades.', 'lida', '2025-03-17 18:04:45'),
(7, 'MASTER', 'administrador', 'O produto \'PROJETOR\' atingiu o limite mínimo de 5 unidades. Precisa comprar mais.', 'nao lida', '2025-03-17 18:21:24'),
(8, 'MASTER', 'administrador', 'O produto \'PROJETOR\' chegou ao limite mínimo de 5 unidades.', 'nao lida', '2025-03-17 18:22:48'),
(9, 'MASTER', 'administrador', 'O produto \'PROJETOR\' atingiu o limite mínimo de 5 unidades. Precisa comprar mais.', 'nao lida', '2025-03-17 18:28:10'),
(10, 'MASTER', 'administrador', 'O produto \'PROJETOR\' atingiu o limite mínimo de 5 unidades. Precisa comprar mais.', 'nao lida', '2025-03-17 18:28:29'),
(11, 'PAULO', 'estoque', 'O produto \'PAPEL A4w\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:43:53'),
(12, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10L212\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:43:53'),
(13, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:43:53'),
(14, 'PAULO', 'estoque', 'O produto \'PAPEL A4w\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:44:05'),
(15, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10L212\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:44:05'),
(16, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:44:05'),
(17, 'PAULO', 'estoque', 'O produto \'PAPEL A4w\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:44:32'),
(18, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10L212\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:44:32'),
(19, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:44:32'),
(20, 'PAULO', 'estoque', 'O produto \'PAPEL A4w\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:44:33'),
(21, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10L212\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:44:33'),
(22, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:44:33'),
(23, 'PAULO', 'estoque', 'O produto \'PAPEL A4w\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:44:33'),
(24, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10L212\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:44:33'),
(25, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:44:33'),
(26, 'PAULO', 'estoque', 'O produto \'PAPEL A4w\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:44:48'),
(27, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10L212\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:44:48'),
(28, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:44:48'),
(29, 'PAULO', 'estoque', 'O produto \'PAPEL A4w\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:45:37'),
(30, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10L212\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:45:37'),
(31, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:45:37'),
(32, 'PAULO', 'estoque', 'O produto \'PAPEL A4w\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:46:25'),
(33, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10L212\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:46:25'),
(34, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:46:25'),
(35, 'PAULO', 'estoque', 'O produto \'PAPEL A4w\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:46:26'),
(36, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10L212\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:46:26'),
(37, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:46:26'),
(38, 'PAULO', 'estoque', 'O produto \'PAPEL A4w\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:46:34'),
(39, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10L212\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:46:34'),
(40, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:46:34'),
(41, 'PAULO', 'estoque', 'O produto \'PAPEL A4w\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:46:38'),
(42, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10L212\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:46:38'),
(43, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:46:38'),
(44, 'PAULO', 'estoque', 'O produto \'PAPEL A4w\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:46:39'),
(45, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10L212\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:46:39'),
(46, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:46:39'),
(47, 'PAULO', 'estoque', 'O produto \'PAPEL A4w\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:46:39'),
(48, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10L212\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:46:39'),
(49, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:46:39'),
(50, 'PAULO', 'estoque', 'O produto \'PAPEL A4w\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:46:39'),
(51, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10L212\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:46:39'),
(52, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:46:39'),
(53, 'PAULO', 'estoque', 'O produto \'PAPEL A4w\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:46:39'),
(54, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10L212\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:46:39'),
(55, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:46:39'),
(56, 'PAULO', 'estoque', 'O produto \'PAPEL A4w\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:46:57'),
(57, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10L212\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:46:57'),
(58, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:46:57'),
(59, 'PAULO', 'estoque', 'O produto \'PAPEL A4w\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:47:00'),
(60, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10L212\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:47:00'),
(61, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 18:47:00'),
(62, 'PAULO', 'estoque', 'O produto \'PAPEL A4w\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 19:40:53'),
(63, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10L212\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 19:40:53'),
(64, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 19:40:53'),
(65, 'PAULO', 'estoque', 'O produto \'PAPEL A4w\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 19:41:16'),
(66, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10L212\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 19:41:16'),
(67, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 19:41:16'),
(68, 'PAULO', 'estoque', 'O produto \'PAPEL A4w\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 19:42:53'),
(69, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10L212\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 19:42:53'),
(70, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 19:42:53'),
(71, 'PAULO', 'estoque', 'O produto \'PAPEL A4w\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 19:44:13'),
(72, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10L212\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 19:44:13'),
(73, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 19:44:13'),
(74, 'PAULO', 'estoque', 'O produto \'PAPEL A4w\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 19:46:39'),
(75, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10L212\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 19:46:39'),
(76, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 19:46:39'),
(77, 'PAULO', 'estoque', 'O produto \'PAPEL A4w\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 19:46:39'),
(78, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10L212\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 19:46:39'),
(79, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 19:46:39'),
(80, 'PAULO', 'estoque', 'O produto \'PAPEL A4w\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 19:46:40'),
(81, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10L212\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 19:46:40'),
(82, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 19:46:40'),
(83, 'PAULO', 'estoque', 'O produto \'PAPEL A4w\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 19:46:40'),
(84, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10L212\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 19:46:40'),
(85, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 19:46:40'),
(86, 'PAULO', 'estoque', 'O produto \'PAPEL A4w\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 19:46:40'),
(87, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10L212\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 19:46:40'),
(88, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 19:46:40'),
(89, 'PAULO', 'estoque', 'O produto \'PAPEL A4w\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 19:46:41'),
(90, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10L212\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 19:46:41'),
(91, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 19:46:41'),
(92, 'PAULO', 'estoque', 'O produto \'PAPEL A4w\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 19:46:47'),
(93, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10L212\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 19:46:48'),
(94, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 19:46:48'),
(95, 'PAULO', 'estoque', 'O produto \'PAPEL A4w\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 19:46:50'),
(96, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10L212\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 19:46:50'),
(97, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-17 19:46:51'),
(98, 'PAULO', 'estoque', 'O produto \'PAPEL A4w\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-18 11:17:12'),
(99, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10L212\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-18 11:17:12'),
(100, 'PAULO', 'estoque', 'O produto \'SACO LIXO 10LD\' está com 2 unidades. Estoque abaixo de 5 unidades.', 'nao lida', '2025-03-18 11:17:12');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pagamentos`
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
-- Estrutura para tabela `patrimonio`
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
-- Despejando dados para a tabela `patrimonio`
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
-- Estrutura para tabela `permissoes`
--

CREATE TABLE `permissoes` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `setor` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `setor_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `permissoes`
--

INSERT INTO `permissoes` (`id`, `usuario_id`, `setor`, `created_at`, `setor_id`) VALUES
(1, 47, 1, '2024-11-18 01:59:53', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `produto` varchar(255) NOT NULL,
  `classificacao` varchar(100) DEFAULT NULL,
  `natureza` varchar(100) DEFAULT NULL,
  `contabil` varchar(100) DEFAULT NULL,
  `codigo` varchar(50) DEFAULT NULL,
  `unidade` varchar(50) DEFAULT NULL,
  `localizacao` varchar(100) DEFAULT NULL,
  `custo` double(10,2) DEFAULT NULL,
  `quantidade` int(11) DEFAULT NULL,
  `nf` varchar(50) DEFAULT NULL,
  `preco_medio` decimal(10,2) DEFAULT NULL,
  `tipo_operacao` varchar(50) DEFAULT 'entrada',
  `data_cadastro` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `produto`, `classificacao`, `natureza`, `contabil`, `codigo`, `unidade`, `localizacao`, `custo`, `quantidade`, `nf`, `preco_medio`, `tipo_operacao`, `data_cadastro`) VALUES
(69, 'PAPEL A4w', 'TESTE', 'TESTE', '20.2200', '24716081', 'UM', 'xm2', 12.00, 2, '', 6.00, 'cadastrado', '2023-01-10 15:12:03'),
(70, 'SACO LIXO 10L212', 'TESTE', '1', '2', '24716081', 'UM', 'xm2', 21.00, 2, '', 10.50, 'cadastrado', '2024-01-10 15:13:45'),
(71, 'SACO LIXO 10LD', 'TESTE', 'W2', '20.2200', '22', '2', 'xm2', 2.00, 2, '', 1.00, 'cadastrado', '2025-01-31 14:54:04'),
(75, 'PROJETOR', 'TESTE', 'TESTE', '20.2200', '24452005', '123', 'xm1', 2.00, 5, '', 0.01, 'retirado', '2025-03-17 15:11:43');

-- --------------------------------------------------------

--
-- Estrutura para tabela `setores`
--

CREATE TABLE `setores` (
  `id` int(11) NOT NULL,
  `nome_setor` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `setores`
--

INSERT INTO `setores` (`id`, `nome_setor`) VALUES
(1, 'Financeiro'),
(2, 'Patrimonio'),
(3, 'estoque'),
(4, 'administrador'),
(5, 'contratos');

-- --------------------------------------------------------

--
-- Estrutura para tabela `transferencias`
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
-- Despejando dados para a tabela `transferencias`
--

INSERT INTO `transferencias` (`id`, `patrimonio_id`, `destino`, `data_transferencia`, `observacao`, `criado_em`, `atualizado_em`, `responsavel`, `tipo_operacao`) VALUES
(1, 1, 'DIRAF', '2024-11-21', NULL, '2024-11-21 07:07:51', '2024-11-21 07:07:51', 'Claudia', ''),
(4, 1, 'GERCOM', '2024-11-24', NULL, '2024-11-23 23:43:46', '2024-11-23 23:43:46', 'Claudia', ''),
(5, 1, 'ASSPRIN', '2024-11-24', NULL, '2024-11-23 23:51:43', '2024-11-23 23:51:43', 'Claudia', 'Transferido'),
(6, 1, 'SUPLAN', '2024-11-24', NULL, '2024-11-23 23:57:44', '2024-11-23 23:57:44', 'Claudia', 'Transferido');

-- --------------------------------------------------------

--
-- Estrutura para tabela `transferencia_historico`
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
-- Estrutura para tabela `usuario`
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
-- Despejando dados para a tabela `usuario`
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
(11, 'MARCO', '$2y$10$V/PS4I1wx9viy9zkpuUMKO7B4AUC/PIRBI.ZZI6LYSvbSBDlQhAIi', '919', 'GERGEP@CENTRAL.RJ.GOV.BR', '', 'SUPERINTENDENTE', 'ativo', 'default.png', 1, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `matricula` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `cargo` varchar(50) NOT NULL,
  `dados_status` varchar(50) DEFAULT 'ativo',
  `senha_provisoria` tinyint(1) DEFAULT 1,
  `setor` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `senha`, `matricula`, `email`, `cargo`, `dados_status`, `senha_provisoria`, `setor`) VALUES
(1, 'master', '$2y$10$BmLl2i5/Qi32kugMwXByNeynLE8wE6.LCret1eevK0.h9LEy7IA0i', '', '', '', 'ativo', 1, 'administrativo'),
(44, 'maik', '$2y$10$IOVyl0SfDjmZVZgSOOpMoetcMe6MzF3e43X7bzYj.0Nootjur5zLW', '', '', '', 'ativo', 1, 'Estoque'),
(46, 'gabriel', '$2y$10$y/dR02ny07xkxXbmDMygG.o4qPrlwebk9pYd2AjtpSup5DHPaR87a', '99000889', '', '', 'ativo', 0, 'Patrimônio'),
(47, 'Alex', '$2y$10$YbJ45ee.j9NY0Cbfno/CQu0wuDGfvW6VkIhSo3KC6CiSTCHeQrN7G', '9900057', 'gfg@central.rj.gov.br', 'teste', 'ativo', 1, 'Patrimônio'),
(48, 'novo', '$2y$10$uoWtOtMLJP4UOm6Td.oNsu.tSIF5uFxE7rLiCEAkGxS5yIF9RzXOy', '120', '10@gmail.com', 'teste', 'ativo', 1, 'Financeiro'),
(49, 'Ivan Santos rodrigues', '$2y$10$jfOqMYFcsEZ.uERUD7zIsuiVr6cchbOx51deyAtVO4DXllkhbyrCm', '99000889', 'gabrielsouza.workti@gmail.com', 'sdf', 'ativo', 1, 'Financeiro'),
(51, 'patrimonio_user', '$2y$10$kamCnI2sdqyNV.AcoZfx3OjtGbmf6nqZZdMvirut3EJ3/64GOTipu', '99000889', 'gfg@central.rj.gov.br', 'dsf', 'ativo', 1, 'Patrimônio');

--
-- Acionadores `usuarios`
--
DELIMITER $$
CREATE TRIGGER `after_user_insert` AFTER INSERT ON `usuarios` FOR EACH ROW BEGIN
    -- Insere uma entrada na tabela permissoes com o novo usuario_id
    INSERT INTO permissoes (usuario_id, setor_id) 
    VALUES (NEW.id, NULL); -- Use NULL para o setor_id se não houver valor padrão
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario_setores`
--

CREATE TABLE `usuario_setores` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `setor_id` int(11) NOT NULL,
  `data_atribuicao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `codigo_atual`
--
ALTER TABLE `codigo_atual`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `data_criacao`
--
ALTER TABLE `data_criacao`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `funcionario`
--
ALTER TABLE `funcionario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `gestao_contratos`
--
ALTER TABLE `gestao_contratos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `log_eventos`
--
ALTER TABLE `log_eventos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_contrato` (`id_contrato`);

--
-- Índices de tabela `patrimonio`
--
ALTER TABLE `patrimonio`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Índices de tabela `permissoes`
--
ALTER TABLE `permissoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `setor_id` (`setor`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `setores`
--
ALTER TABLE `setores`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `transferencias`
--
ALTER TABLE `transferencias`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `transferencia_historico`
--
ALTER TABLE `transferencia_historico`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patrimonio_id` (`patrimonio_id`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `matricula` (`matricula`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Índices de tabela `usuario_setores`
--
ALTER TABLE `usuario_setores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_usuario` (`usuario_id`),
  ADD KEY `fk_setor` (`setor_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
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
-- AUTO_INCREMENT de tabela `data_criacao`
--
ALTER TABLE `data_criacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=516;

--
-- AUTO_INCREMENT de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT de tabela `pagamentos`
--
ALTER TABLE `pagamentos`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

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
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT de tabela `usuario_setores`
--
ALTER TABLE `usuario_setores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `pagamentos`
--
ALTER TABLE `pagamentos`
  ADD CONSTRAINT `fk_contrato` FOREIGN KEY (`id_contrato`) REFERENCES `gestao_contratos` (`id`);

--
-- Restrições para tabelas `permissoes`
--
ALTER TABLE `permissoes`
  ADD CONSTRAINT `permissoes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `permissoes_ibfk_2` FOREIGN KEY (`setor`) REFERENCES `setores` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `usuario_setores`
--
ALTER TABLE `usuario_setores`
  ADD CONSTRAINT `fk_setor` FOREIGN KEY (`setor_id`) REFERENCES `setores` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
