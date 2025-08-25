-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 25/08/2025 às 21:44
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
-- Banco de dados: `gm_sicbd`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `acidentes`
--

CREATE TABLE `acidentes` (
  `id` int(11) NOT NULL,
  `data` date NOT NULL,
  `descricao` text NOT NULL,
  `localizacao` varchar(255) DEFAULT NULL,
  `usuario` varchar(100) NOT NULL,
  `severidade` varchar(50) NOT NULL,
  `categoria` varchar(100) NOT NULL,
  `data_registro` datetime NOT NULL DEFAULT current_timestamp(),
  `cor` varchar(50) NOT NULL DEFAULT '',
  `status` varchar(20) NOT NULL DEFAULT 'em andamento',
  `policia` varchar(220) NOT NULL,
  `bombeiros` varchar(50) NOT NULL,
  `samu` varchar(110) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `acidentes`
--

INSERT INTO `acidentes` (`id`, `data`, `descricao`, `localizacao`, `usuario`, `severidade`, `categoria`, `data_registro`, `cor`, `status`, `policia`, `bombeiros`, `samu`) VALUES
(1, '2025-06-23', 'teste', 'SALA 501', 'controle', 'Leve', 'Outros', '2025-06-23 12:29:39', '', 'resolvido', '1', '0', '0'),
(2, '2025-06-23', 'teste 2 ', 'sala 703', 'controle', 'Moderado', 'Descarrilamento', '2025-06-23 13:50:39', '', 'resolvido', '0', '0', '0'),
(3, '2025-08-20', 'asdf', 'SALA 501', 'master', 'Leve', 'Colisão', '2025-08-20 15:29:29', '', 'resolvido', '0', '0', '0'),
(4, '2025-08-20', 'sdfg', 'sdfg', 'master', 'Leve', 'Obstrução na via', '2025-08-20 15:31:10', 'Verde', 'resolvido', '0', '0', '0'),
(5, '2025-08-20', 'asdf', 'sala 703', 'master', 'Moderado', 'Carro estacionado no trilho', '2025-08-20 15:31:32', 'Amarelo', 'resolvido', '1', '0', '0'),
(6, '2025-08-20', 'teste', 'TESTE', 'master', 'Grave', 'Agressão entre passageiros', '2025-08-20 15:32:48', 'Vermelho', 'resolvido', '', '', ''),
(7, '2025-08-21', 'Descarrilamento', 'Rua Almirante Alexandrino', 'admin', 'Grave', 'Descarrilamento', '2025-08-21 14:00:00', 'Vermelho', 'em andamento', '', '', ''),
(8, '2025-08-21', 'sf', 'teste 23', 'master', 'Grave', 'Descarrilamento sem vítimas', '2025-08-21 15:36:38', 'Vermelho', 'em andamento', '', '', ''),
(9, '2025-08-22', 'hgc', 'h', 'master', 'Moderado', 'Pane elétrica', '2025-08-21 19:07:46', 'Amarelo', 'em andamento', '', '', ''),
(10, '2025-08-22', 'xcvgb', 'SALA 501', 'master', 'Moderado', 'Ato de vandalismo no bonde', '2025-08-22 11:03:44', 'Amarelo', 'em andamento', '', '', ''),
(11, '2025-08-22', 'teste', '1', 'master', 'Grave', 'Incêndio em área próxima à via', '2025-08-22 11:11:33', 'Vermelho', 'em andamento', '', '', ''),
(12, '2025-08-22', 'eteseeeeeee', 'testeeee', 'master', 'Leve', 'Obstrução na via', '2025-08-22 11:15:05', 'Verde', 'em andamento', '', '', ''),
(13, '2025-08-22', 'asdfadfasdf', 'sdaf', 'master', 'Grave', 'Colisão com veículo', '2025-08-22 11:15:31', 'Vermelho', 'em andamento', '', '', ''),
(14, '2025-08-22', 'jklhjkhkjghjkhjkhjk', 'santa', 'master', 'Moderado', 'Carro estacionado no trilho', '2025-08-22 11:17:27', 'Amarelo', 'resolvido', '', '', ''),
(15, '2025-08-22', 'asdf', 'asdf', 'master', 'Grave', 'Passageiro passando mal (grave)', '2025-08-22 16:53:23', 'Vermelho', 'resolvido', '0', '0', '0'),
(16, '2025-08-22', 'asdf', 'asdf', 'master', 'Grave', 'Passageiro passando mal (grave)', '2025-08-22 16:54:02', 'Vermelho', 'resolvido', '0', '0', '0'),
(17, '2025-08-22', 'asdf', 'sadf', 'master', 'Grave', 'Acidente interno com vítima grave', '2025-08-22 16:54:26', 'Vermelho', 'resolvido', '0', '0', '0'),
(18, '2025-08-22', 'asdf', 'sadf', 'master', 'Grave', 'Acidente interno com vítima grave', '2025-08-22 16:54:55', 'Vermelho', 'resolvido', '', '', '');

-- --------------------------------------------------------

--
-- Estrutura para tabela `blocos`
--

CREATE TABLE `blocos` (
  `id` int(11) NOT NULL,
  `projeto_id` int(11) DEFAULT NULL,
  `numero` int(11) DEFAULT NULL,
  `titulo` varchar(255) DEFAULT NULL,
  `descricao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `bondes`
--

CREATE TABLE `bondes` (
  `id` int(11) NOT NULL,
  `ativo` tinyint(1) DEFAULT 0,
  `modelo` varchar(50) NOT NULL,
  `capacidade` int(11) NOT NULL,
  `ano_fabricacao` int(11) NOT NULL,
  `descricao` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `bondes`
--

INSERT INTO `bondes` (`id`, `ativo`, `modelo`, `capacidade`, `ano_fabricacao`, `descricao`) VALUES
(1, 1, 'BONDE 10', 32, 2023, 'dell'),
(2, 1, 'BONDE 17', 32, 2025, 'TURISMO'),
(3, 1, 'BONDE 22', 32, 1900, 'TURISMO');

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `cor` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `certidoes`
--

CREATE TABLE `certidoes` (
  `id` int(11) NOT NULL,
  `documento` varchar(50) NOT NULL COMMENT 'Tipo de certidão (ex.: CND, CRF, FGTS, INSS, Outros)',
  `data_vencimento` date NOT NULL COMMENT 'Data de vencimento da certidão',
  `nome` varchar(255) NOT NULL COMMENT 'Nome ou descrição da certidão',
  `fornecedor` varchar(255) NOT NULL COMMENT 'Empresa ou entidade fornecedora',
  `responsavel` varchar(255) NOT NULL COMMENT 'Pessoa responsável pela certidão',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Data de criação do registro',
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Data de última atualização',
  `arquivo` varchar(255) DEFAULT NULL,
  `contrato_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tabela para armazenar informações de certidões';

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
-- Estrutura para tabela `conferencias`
--

CREATE TABLE `conferencias` (
  `id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `mes_conferencia` date NOT NULL,
  `conferido` tinyint(1) NOT NULL,
  `quantidade_fisica` int(11) DEFAULT NULL,
  `data_conferencia` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `conferencias`
--

INSERT INTO `conferencias` (`id`, `produto_id`, `mes_conferencia`, `conferido`, `quantidade_fisica`, `data_conferencia`) VALUES
(1, 1, '2025-06-01', 1, 165, '2025-06-05 16:14:59'),
(3, 1, '2025-07-01', 1, 0, '2025-06-06 09:28:54'),
(13, 9, '2025-06-01', 1, 27, '2025-06-05 16:16:04'),
(28, 2, '2025-06-01', 0, 17, '2025-06-05 16:15:24'),
(32, 8, '2025-06-01', 1, 416, '2025-06-05 16:16:29'),
(34, 2, '2025-09-01', 1, 0, '2025-06-06 09:28:51');

-- --------------------------------------------------------

--
-- Estrutura para tabela `configuracoes`
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
-- Estrutura para tabela `contratos_parcelas`
--

CREATE TABLE `contratos_parcelas` (
  `id` int(11) NOT NULL,
  `contrato_id` int(11) DEFAULT NULL,
  `mes` int(11) DEFAULT NULL,
  `ano` int(11) DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `contratos_parcelas`
--

INSERT INTO `contratos_parcelas` (`id`, `contrato_id`, `mes`, `ano`, `valor`) VALUES
(255, 20, 7, 2025, 41666.67),
(256, 20, 8, 2025, 41666.67),
(257, 20, 9, 2025, 41666.67),
(258, 20, 10, 2025, 41666.67),
(259, 20, 11, 2025, 41666.67),
(260, 20, 12, 2025, 41666.67),
(261, 20, 1, 2026, 41666.67),
(262, 20, 2, 2026, 41666.67),
(263, 20, 3, 2026, 41666.67),
(264, 20, 4, 2026, 41666.67),
(265, 20, 5, 2026, 41666.67),
(266, 20, 6, 2026, 41666.67),
(267, 38, 2, 2025, 28029.04),
(268, 38, 3, 2025, 28029.04),
(269, 38, 4, 2025, 28029.04),
(270, 38, 5, 2025, 28029.04),
(271, 38, 6, 2025, 28029.04),
(272, 38, 7, 2025, 28029.04),
(273, 38, 8, 2025, 28029.04),
(274, 38, 9, 2025, 28029.04),
(275, 38, 10, 2025, 28029.04),
(276, 38, 11, 2025, 28029.04),
(277, 38, 12, 2025, 28029.04),
(278, 38, 1, 2026, 28029.04),
(279, 39, 4, 2025, 10365.32),
(280, 39, 5, 2025, 10365.32),
(281, 39, 6, 2025, 10365.32),
(282, 39, 7, 2025, 10365.32),
(283, 39, 8, 2025, 10365.32),
(284, 39, 9, 2025, 10365.32),
(285, 39, 10, 2025, 10365.32),
(286, 39, 11, 2025, 10365.32),
(287, 39, 12, 2025, 10365.32),
(288, 39, 1, 2026, 10365.32),
(289, 39, 2, 2026, 10365.32),
(290, 39, 3, 2026, 10365.32),
(291, 39, 4, 2026, 10365.32),
(292, 39, 5, 2026, 10365.32),
(293, 39, 6, 2026, 10365.32),
(294, 39, 7, 2026, 10365.32),
(295, 39, 8, 2026, 10365.32),
(296, 39, 9, 2026, 10365.32),
(297, 39, 10, 2026, 10365.32),
(298, 39, 11, 2026, 10365.32),
(299, 39, 12, 2026, 10365.32),
(300, 39, 1, 2027, 10365.32),
(301, 39, 2, 2027, 10365.32),
(302, 40, 4, 2025, 36072.42),
(303, 40, 5, 2025, 36072.42),
(304, 40, 6, 2025, 36072.42),
(305, 40, 7, 2025, 36072.42),
(306, 40, 8, 2025, 36072.42),
(307, 40, 9, 2025, 36072.42),
(308, 40, 10, 2025, 36072.42),
(309, 40, 11, 2025, 36072.42),
(310, 40, 12, 2025, 36072.42),
(311, 40, 1, 2026, 36072.42),
(312, 40, 2, 2026, 36072.42),
(313, 40, 3, 2026, 36072.42);

-- --------------------------------------------------------

--
-- Estrutura para tabela `controle_transicao`
--

CREATE TABLE `controle_transicao` (
  `id` int(11) NOT NULL,
  `mes` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Estrutura para tabela `emails_salvos`
--

CREATE TABLE `emails_salvos` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `criado_em` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `emails_salvos`
--

INSERT INTO `emails_salvos` (`id`, `email`, `username`, `criado_em`) VALUES
(1, 'grodrigues@central.rj.gov.br', 'CONTRATOS', '2025-04-25 02:30:34'),
(4, 'maikgtx2@gmail.com', 'CONTRATOS', '2025-04-25 02:54:18'),
(8, 'gabrielzsouzarodrigues@gmail.com', 'CONTRATOS', '2025-04-26 00:40:45'),
(9, 'gabrielzsouzarodrigues23@gmail.com', 'MASTER', '2025-04-26 00:42:33');

-- --------------------------------------------------------

--
-- Estrutura para tabela `etapas_contratos`
--

CREATE TABLE `etapas_contratos` (
  `id` int(11) NOT NULL,
  `contract_id` int(11) NOT NULL,
  `etapa` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `data` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Em Andamento',
  `order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `etapas_contratos`
--

INSERT INTO `etapas_contratos` (`id`, `contract_id`, `etapa`, `descricao`, `data`, `hora`, `status`, `order`) VALUES
(53, 20, 'Pagamentos', 'Atualização de status', '2025-05-27', '14:11:00', 'Completo', 0),
(192, 22, 'Execução do Contrato', 'Atualização de status', '2025-06-04', '19:27:00', 'Em Andamento', 0),
(193, 22, 'Finalização do Contrato', 'Atualização de status', '2025-06-04', '19:27:00', 'Em Andamento', 0),
(197, 22, 'Prestação de Contas', 'Atualização de status', '2025-06-04', '19:44:00', 'Em Andamento', 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `eventos`
--

CREATE TABLE `eventos` (
  `id` int(11) NOT NULL,
  `username` varchar(220) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `data` date NOT NULL,
  `hora` time NOT NULL,
  `categoria` varchar(50) NOT NULL,
  `cor` varchar(7) DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `certidao_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `eventos`
--

INSERT INTO `eventos` (`id`, `username`, `titulo`, `descricao`, `data`, `hora`, `categoria`, `cor`, `criado_em`, `certidao_id`) VALUES
(2, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a June/2025. Valor: R$ 90.055,00', '2025-06-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(3, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a July/2025. Valor: R$ 90.055,00', '2025-07-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(4, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a August/2025. Valor: R$ 90.055,00', '2025-08-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(5, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a September/2025. Valor: R$ 90.055,00', '2025-09-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(6, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a October/2025. Valor: R$ 90.055,00', '2025-10-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(7, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a November/2025. Valor: R$ 90.055,00', '2025-11-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(8, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a December/2025. Valor: R$ 90.055,00', '2025-12-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(9, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a January/2026. Valor: R$ 90.055,00', '2026-01-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(10, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a February/2026. Valor: R$ 90.055,00', '2026-02-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(11, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a March/2026. Valor: R$ 90.055,00', '2026-03-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(12, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a April/2026. Valor: R$ 90.055,00', '2026-04-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(13, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a May/2026. Valor: R$ 90.055,00', '2026-05-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(14, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a June/2026. Valor: R$ 90.055,00', '2026-06-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(15, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a July/2026. Valor: R$ 90.055,00', '2026-07-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(16, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a August/2026. Valor: R$ 90.055,00', '2026-08-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(17, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a September/2026. Valor: R$ 90.055,00', '2026-09-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(18, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a October/2026. Valor: R$ 90.055,00', '2026-10-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(19, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a November/2026. Valor: R$ 90.055,00', '2026-11-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(20, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a December/2026. Valor: R$ 90.055,00', '2026-12-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(21, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a January/2027. Valor: R$ 90.055,00', '2027-01-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(22, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a February/2027. Valor: R$ 90.055,00', '2027-02-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(23, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a March/2027. Valor: R$ 90.055,00', '2027-03-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(24, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a April/2027. Valor: R$ 90.055,00', '2027-04-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(25, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a May/2027. Valor: R$ 90.055,00', '2027-05-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(26, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a June/2027. Valor: R$ 90.055,00', '2027-06-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(27, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a July/2027. Valor: R$ 90.055,00', '2027-07-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(28, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a August/2027. Valor: R$ 90.055,00', '2027-08-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(29, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a September/2027. Valor: R$ 90.055,00', '2027-09-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(30, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a October/2027. Valor: R$ 90.055,00', '2027-10-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(31, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a November/2027. Valor: R$ 90.055,00', '2027-11-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(32, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a December/2027. Valor: R$ 90.055,00', '2027-12-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(33, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a January/2028. Valor: R$ 90.055,00', '2028-01-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(34, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a February/2028. Valor: R$ 90.055,00', '2028-02-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(35, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a March/2028. Valor: R$ 90.055,00', '2028-03-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(36, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a April/2028. Valor: R$ 90.055,00', '2028-04-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(37, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a May/2028. Valor: R$ 90.055,00', '2028-05-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(38, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a June/2028. Valor: R$ 90.055,00', '2028-06-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(39, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a July/2028. Valor: R$ 90.055,00', '2028-07-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(40, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a August/2028. Valor: R$ 90.055,00', '2028-08-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(41, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a September/2028. Valor: R$ 90.055,00', '2028-09-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(42, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a October/2028. Valor: R$ 90.055,00', '2028-10-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(43, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a November/2028. Valor: R$ 90.055,00', '2028-11-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(44, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a December/2028. Valor: R$ 90.055,00', '2028-12-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(45, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a January/2029. Valor: R$ 90.055,00', '2029-01-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(46, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a February/2029. Valor: R$ 90.055,00', '2029-02-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(47, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a March/2029. Valor: R$ 90.055,00', '2029-03-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(48, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a April/2029. Valor: R$ 90.055,00', '2029-04-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(49, '', 'Vencimento de Parcela: CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA40', 'Parcela referente a May/2029. Valor: R$ 90.055,00', '2029-05-17', '09:00:00', 'Pagamento', '#FF9900', '2025-05-15 13:25:01', 0),
(50, '', 'TESTE', 'TESTE', '2025-05-15', '10:34:00', 'Geral', '#7e4e4e', '2025-05-15 18:32:28', 0),
(51, '', 'Vencimento de Parcela: andamento 2', 'Parcela referente a July/2025. Valor: R$ 41.666,67', '2025-07-09', '09:00:00', 'Pagamento', '#FF9900', '2025-05-19 17:46:36', 0),
(52, '', 'Vencimento de Parcela: andamento 2', 'Parcela referente a August/2025. Valor: R$ 41.666,67', '2025-08-09', '09:00:00', 'Pagamento', '#FF9900', '2025-05-19 17:46:36', 0),
(53, '', 'Vencimento de Parcela: andamento 2', 'Parcela referente a September/2025. Valor: R$ 41.666,67', '2025-09-09', '09:00:00', 'Pagamento', '#FF9900', '2025-05-19 17:46:36', 0),
(54, '', 'Vencimento de Parcela: andamento 2', 'Parcela referente a October/2025. Valor: R$ 41.666,67', '2025-10-09', '09:00:00', 'Pagamento', '#FF9900', '2025-05-19 17:46:36', 0),
(55, '', 'Vencimento de Parcela: andamento 2', 'Parcela referente a November/2025. Valor: R$ 41.666,67', '2025-11-09', '09:00:00', 'Pagamento', '#FF9900', '2025-05-19 17:46:36', 0),
(56, '', 'Vencimento de Parcela: andamento 2', 'Parcela referente a December/2025. Valor: R$ 41.666,67', '2025-12-09', '09:00:00', 'Pagamento', '#FF9900', '2025-05-19 17:46:36', 0),
(57, '', 'Vencimento de Parcela: andamento 2', 'Parcela referente a January/2026. Valor: R$ 41.666,67', '2026-01-09', '09:00:00', 'Pagamento', '#FF9900', '2025-05-19 17:46:36', 0),
(58, '', 'Vencimento de Parcela: andamento 2', 'Parcela referente a February/2026. Valor: R$ 41.666,67', '2026-02-09', '09:00:00', 'Pagamento', '#FF9900', '2025-05-19 17:46:36', 0),
(59, '', 'Vencimento de Parcela: andamento 2', 'Parcela referente a March/2026. Valor: R$ 41.666,67', '2026-03-09', '09:00:00', 'Pagamento', '#FF9900', '2025-05-19 17:46:36', 0),
(60, '', 'Vencimento de Parcela: andamento 2', 'Parcela referente a April/2026. Valor: R$ 41.666,67', '2026-04-09', '09:00:00', 'Pagamento', '#FF9900', '2025-05-19 17:46:36', 0),
(61, '', 'Vencimento de Parcela: andamento 2', 'Parcela referente a May/2026. Valor: R$ 41.666,67', '2026-05-09', '09:00:00', 'Pagamento', '#FF9900', '2025-05-19 17:46:36', 0),
(62, '', 'Vencimento de Parcela: andamento 2', 'Parcela referente a June/2026. Valor: R$ 41.666,67', '2026-06-09', '09:00:00', 'Pagamento', '#FF9900', '2025-05-19 17:46:36', 0),
(63, '', 'REUNIÃO FINANCEIRO', 'TESTE MASTER', '2025-06-02', '08:52:00', 'Geral', '#ff0000', '2025-06-02 16:46:28', 0),
(64, '', 'Vencimento de Parcela: PRODERJ VPS', 'Parcela referente a February/2025. Valor: R$ 28.029,04', '2025-02-16', '09:00:00', 'Pagamento', '#FF9900', '2025-06-05 18:04:11', 0),
(65, '', 'Vencimento de Parcela: PRODERJ VPS', 'Parcela referente a March/2025. Valor: R$ 28.029,04', '2025-03-16', '09:00:00', 'Pagamento', '#FF9900', '2025-06-05 18:04:11', 0),
(66, '', 'Vencimento de Parcela: PRODERJ VPS', 'Parcela referente a April/2025. Valor: R$ 28.029,04', '2025-04-16', '09:00:00', 'Pagamento', '#FF9900', '2025-06-05 18:04:11', 0),
(67, '', 'Vencimento de Parcela: PRODERJ VPS', 'Parcela referente a May/2025. Valor: R$ 28.029,04', '2025-05-16', '09:00:00', 'Pagamento', '#FF9900', '2025-06-05 18:04:11', 0),
(68, '', 'Vencimento de Parcela: PRODERJ VPS', 'Parcela referente a June/2025. Valor: R$ 28.029,04', '2025-06-16', '09:00:00', 'Pagamento', '#FF9900', '2025-06-05 18:04:11', 0),
(69, '', 'Vencimento de Parcela: PRODERJ VPS', 'Parcela referente a July/2025. Valor: R$ 28.029,04', '2025-07-16', '09:00:00', 'Pagamento', '#FF9900', '2025-06-05 18:04:11', 0),
(70, '', 'Vencimento de Parcela: PRODERJ VPS', 'Parcela referente a August/2025. Valor: R$ 28.029,04', '2025-08-16', '09:00:00', 'Pagamento', '#FF9900', '2025-06-05 18:04:11', 0),
(71, '', 'Vencimento de Parcela: PRODERJ VPS', 'Parcela referente a September/2025. Valor: R$ 28.029,04', '2025-09-16', '09:00:00', 'Pagamento', '#FF9900', '2025-06-05 18:04:11', 0),
(72, '', 'Vencimento de Parcela: PRODERJ VPS', 'Parcela referente a October/2025. Valor: R$ 28.029,04', '2025-10-16', '09:00:00', 'Pagamento', '#FF9900', '2025-06-05 18:04:11', 0),
(73, '', 'Vencimento de Parcela: PRODERJ VPS', 'Parcela referente a November/2025. Valor: R$ 28.029,04', '2025-11-16', '09:00:00', 'Pagamento', '#FF9900', '2025-06-05 18:04:11', 0),
(74, '', 'Vencimento de Parcela: PRODERJ VPS', 'Parcela referente a December/2025. Valor: R$ 28.029,04', '2025-12-16', '09:00:00', 'Pagamento', '#FF9900', '2025-06-05 18:04:11', 0),
(75, '', 'Vencimento de Parcela: PRODERJ VPS', 'Parcela referente a January/2026. Valor: R$ 28.029,04', '2026-01-16', '09:00:00', 'Pagamento', '#FF9900', '2025-06-05 18:04:11', 0),
(76, '', 'Vencimento de Parcela: CLARO', 'Parcela referente a April/2025. Valor: R$ 10.365,32', '2025-03-29', '09:00:00', 'Pagamento', '#FF9900', '2025-06-12 15:07:42', 0),
(77, '', 'Vencimento de Parcela: CLARO', 'Parcela referente a May/2025. Valor: R$ 10.365,32', '2025-04-28', '09:00:00', 'Pagamento', '#FF9900', '2025-06-12 15:07:42', 0),
(78, '', 'Vencimento de Parcela: CLARO', 'Parcela referente a June/2025. Valor: R$ 10.365,32', '2025-05-29', '09:00:00', 'Pagamento', '#FF9900', '2025-06-12 15:07:42', 0),
(79, '', 'Vencimento de Parcela: CLARO', 'Parcela referente a July/2025. Valor: R$ 10.365,32', '2025-06-28', '09:00:00', 'Pagamento', '#FF9900', '2025-06-12 15:07:42', 0),
(80, '', 'Vencimento de Parcela: CLARO', 'Parcela referente a August/2025. Valor: R$ 10.365,32', '2025-07-29', '09:00:00', 'Pagamento', '#FF9900', '2025-06-12 15:07:42', 0),
(81, '', 'Vencimento de Parcela: CLARO', 'Parcela referente a September/2025. Valor: R$ 10.365,32', '2025-08-29', '09:00:00', 'Pagamento', '#FF9900', '2025-06-12 15:07:42', 0),
(82, '', 'Vencimento de Parcela: CLARO', 'Parcela referente a October/2025. Valor: R$ 10.365,32', '2025-09-28', '09:00:00', 'Pagamento', '#FF9900', '2025-06-12 15:07:42', 0),
(83, '', 'Vencimento de Parcela: CLARO', 'Parcela referente a November/2025. Valor: R$ 10.365,32', '2025-10-29', '09:00:00', 'Pagamento', '#FF9900', '2025-06-12 15:07:42', 0),
(84, '', 'Vencimento de Parcela: CLARO', 'Parcela referente a December/2025. Valor: R$ 10.365,32', '2025-11-28', '09:00:00', 'Pagamento', '#FF9900', '2025-06-12 15:07:42', 0),
(85, '', 'Vencimento de Parcela: CLARO', 'Parcela referente a January/2026. Valor: R$ 10.365,32', '2025-12-29', '09:00:00', 'Pagamento', '#FF9900', '2025-06-12 15:07:42', 0),
(86, '', 'Vencimento de Parcela: CLARO', 'Parcela referente a February/2026. Valor: R$ 10.365,32', '2026-01-29', '09:00:00', 'Pagamento', '#FF9900', '2025-06-12 15:07:42', 0),
(87, '', 'Vencimento de Parcela: CLARO', 'Parcela referente a March/2026. Valor: R$ 10.365,32', '2026-02-26', '09:00:00', 'Pagamento', '#FF9900', '2025-06-12 15:07:42', 0),
(88, '', 'Vencimento de Parcela: CLARO', 'Parcela referente a April/2026. Valor: R$ 10.365,32', '2026-03-29', '09:00:00', 'Pagamento', '#FF9900', '2025-06-12 15:07:42', 0),
(89, '', 'Vencimento de Parcela: CLARO', 'Parcela referente a May/2026. Valor: R$ 10.365,32', '2026-04-28', '09:00:00', 'Pagamento', '#FF9900', '2025-06-12 15:07:42', 0),
(90, '', 'Vencimento de Parcela: CLARO', 'Parcela referente a June/2026. Valor: R$ 10.365,32', '2026-05-29', '09:00:00', 'Pagamento', '#FF9900', '2025-06-12 15:07:42', 0),
(91, '', 'Vencimento de Parcela: CLARO', 'Parcela referente a July/2026. Valor: R$ 10.365,32', '2026-06-28', '09:00:00', 'Pagamento', '#FF9900', '2025-06-12 15:07:42', 0),
(92, '', 'Vencimento de Parcela: CLARO', 'Parcela referente a August/2026. Valor: R$ 10.365,32', '2026-07-29', '09:00:00', 'Pagamento', '#FF9900', '2025-06-12 15:07:42', 0),
(93, '', 'Vencimento de Parcela: CLARO', 'Parcela referente a September/2026. Valor: R$ 10.365,32', '2026-08-29', '09:00:00', 'Pagamento', '#FF9900', '2025-06-12 15:07:42', 0),
(94, '', 'Vencimento de Parcela: CLARO', 'Parcela referente a October/2026. Valor: R$ 10.365,32', '2026-09-28', '09:00:00', 'Pagamento', '#FF9900', '2025-06-12 15:07:42', 0),
(95, '', 'Vencimento de Parcela: CLARO', 'Parcela referente a November/2026. Valor: R$ 10.365,32', '2026-10-29', '09:00:00', 'Pagamento', '#FF9900', '2025-06-12 15:07:42', 0),
(96, '', 'Vencimento de Parcela: CLARO', 'Parcela referente a December/2026. Valor: R$ 10.365,32', '2026-11-28', '09:00:00', 'Pagamento', '#FF9900', '2025-06-12 15:07:42', 0),
(97, '', 'Vencimento de Parcela: CLARO', 'Parcela referente a January/2027. Valor: R$ 10.365,32', '2026-12-29', '09:00:00', 'Pagamento', '#FF9900', '2025-06-12 15:07:42', 0),
(98, '', 'Vencimento de Parcela: CLARO', 'Parcela referente a February/2027. Valor: R$ 10.365,32', '2027-01-29', '09:00:00', 'Pagamento', '#FF9900', '2025-06-12 15:07:42', 0),
(99, '', 'Vencimento de Parcela: OI S/A', 'Parcela referente a April/2025. Valor: R$ 36.072,42', '2025-03-29', '09:00:00', 'Pagamento', '#FF9900', '2025-06-12 15:52:11', 0),
(100, '', 'Vencimento de Parcela: OI S/A', 'Parcela referente a May/2025. Valor: R$ 36.072,42', '2025-04-28', '09:00:00', 'Pagamento', '#FF9900', '2025-06-12 15:52:11', 0),
(101, '', 'Vencimento de Parcela: OI S/A', 'Parcela referente a June/2025. Valor: R$ 36.072,42', '2025-05-29', '09:00:00', 'Pagamento', '#FF9900', '2025-06-12 15:52:11', 0),
(102, '', 'Vencimento de Parcela: OI S/A', 'Parcela referente a July/2025. Valor: R$ 36.072,42', '2025-06-28', '09:00:00', 'Pagamento', '#FF9900', '2025-06-12 15:52:11', 0),
(103, '', 'Vencimento de Parcela: OI S/A', 'Parcela referente a August/2025. Valor: R$ 36.072,42', '2025-07-29', '09:00:00', 'Pagamento', '#FF9900', '2025-06-12 15:52:11', 0),
(104, '', 'Vencimento de Parcela: OI S/A', 'Parcela referente a September/2025. Valor: R$ 36.072,42', '2025-08-29', '09:00:00', 'Pagamento', '#FF9900', '2025-06-12 15:52:11', 0),
(105, '', 'Vencimento de Parcela: OI S/A', 'Parcela referente a October/2025. Valor: R$ 36.072,42', '2025-09-28', '09:00:00', 'Pagamento', '#FF9900', '2025-06-12 15:52:11', 0),
(106, '', 'Vencimento de Parcela: OI S/A', 'Parcela referente a November/2025. Valor: R$ 36.072,42', '2025-10-29', '09:00:00', 'Pagamento', '#FF9900', '2025-06-12 15:52:11', 0),
(107, '', 'Vencimento de Parcela: OI S/A', 'Parcela referente a December/2025. Valor: R$ 36.072,42', '2025-11-28', '09:00:00', 'Pagamento', '#FF9900', '2025-06-12 15:52:11', 0),
(108, '', 'Vencimento de Parcela: OI S/A', 'Parcela referente a January/2026. Valor: R$ 36.072,42', '2025-12-29', '09:00:00', 'Pagamento', '#FF9900', '2025-06-12 15:52:11', 0),
(109, '', 'Vencimento de Parcela: OI S/A', 'Parcela referente a February/2026. Valor: R$ 36.072,42', '2026-01-29', '09:00:00', 'Pagamento', '#FF9900', '2025-06-12 15:52:11', 0),
(110, '', 'Vencimento de Parcela: OI S/A', 'Parcela referente a March/2026. Valor: R$ 36.072,42', '2026-02-26', '09:00:00', 'Pagamento', '#FF9900', '2025-06-12 15:52:11', 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `fechamento`
--

CREATE TABLE `fechamento` (
  `id` int(11) NOT NULL,
  `data_fechamento` date NOT NULL,
  `natureza` varchar(255) NOT NULL,
  `classificacao` varchar(220) NOT NULL,
  `saldo_anterior` decimal(10,2) NOT NULL,
  `total_entrada` decimal(10,2) NOT NULL,
  `total_saida` decimal(10,2) NOT NULL,
  `saldo_atual` decimal(10,2) NOT NULL,
  `custo` decimal(10,2) NOT NULL,
  `status` varchar(220) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `fechamento`
--

INSERT INTO `fechamento` (`id`, `data_fechamento`, `natureza`, `classificacao`, `saldo_anterior`, `total_entrada`, `total_saida`, `saldo_atual`, `custo`, `status`) VALUES
(1, '2025-04-02', '333903001', 'Material cama mesa Banho/Copa e Cozinha', 0.00, 0.00, 0.00, 0.00, 0.00, 'Pendente'),
(2, '2025-04-02', '333903002', 'Artigos para Limpeza, Higiêne e Toalete', 0.00, 0.00, 0.00, 0.00, 0.00, 'Pendente'),
(4, '2025-04-02', '333903005', 'Artigos em Geral e Impressos para Expediente, Escritorio', 0.00, 0.00, 0.00, 0.00, 0.00, 'Pendente'),
(5, '2025-04-02', '333903008', 'Material Radiológico Fotografico,Cinematográfico, de Gravação e Comunicação', 0.00, 0.00, 0.00, 0.00, 0.00, 'Pendente'),
(6, '2025-04-02', '333903010', 'Material Eletrico,material para conservação e manutenção de Bens', 283929.86, 52728946.02, 5124.80, 99967201.19, 0.00, 'Pendente'),
(7, '2025-04-02', '333903011', 'Material para manutenção e conservação de Bens móveis', 105952.82, 0.00, 0.00, 0.00, 0.00, 'Pendente'),
(8, '2025-04-02', '333903020', 'Produtos Alimentícios e Bebidas', 0.00, 0.00, 0.00, 0.00, 0.00, 'Pendente'),
(9, '2025-04-02', '333903021', 'Matérias Primas', 32760.00, 0.00, 0.00, 0.00, 0.00, 'Pendente'),
(10, '2025-04-02', '333903023', 'Material de Informatica', 0.00, 0.00, 0.00, 0.00, 0.00, 'Pendente'),
(11, '2025-04-02', '333903030', 'Material para manutenção de Veículo', 0.00, 0.00, 0.00, 0.00, 0.00, 'Pendente'),
(12, '2025-04-02', '333903033', 'Material para Sinalização Visual e Outros', 0.00, 0.00, 0.00, 0.00, 0.00, 'Pendente'),
(13, '2025-04-02', '333903042', 'Material Eletrico e Eletrônico', 0.00, 0.00, 0.00, 0.00, 0.00, 'Pendente'),
(14, '2025-04-02', '344903010', 'Mat. Eletr. Mat. P/ Conserv. e Manut. de Bens Imoveis; Sinaliz. e Demarc', 0.00, 0.00, 0.00, 0.00, 0.00, 'Pendente'),
(15, '2025-04-02', '344903011', 'Material para manutenção e conservação de Bens móveis', 0.00, 0.00, 0.00, 0.00, 0.00, 'Pendente'),
(16, '2025-04-02', '344905206', 'Outros Equipamentos', 0.00, 0.00, 0.00, 0.00, 0.00, 'Pendente'),
(17, '2025-04-02', '344905212', 'Utensilios de Copa, Cozinha, Dormitorio e Enfermaria', 0.00, 0.00, 0.00, 0.00, 0.00, 'Pendente'),
(18, '2025-04-02', '344905217', 'Equipamento para áudio, Vídeo e Foto', 0.00, 0.00, 0.00, 0.00, 0.00, 'Pendente'),
(19, '2025-04-02', '344905220', 'Maquinas, Ferramentas e Utensilios de Oficina', 0.00, 0.00, 0.00, 0.00, 0.00, 'Pendente'),
(20, '2025-04-02', '344905238', 'Equipamento e Material Permanente ( Material de T.I.C )', 0.00, 0.00, 0.00, 0.00, 0.00, 'Pendente'),
(23, '2025-05-27', '333903003', '', -7693.22, -99783187.58, -40274558.26, -99922256.24, 0.00, '');

-- --------------------------------------------------------

--
-- Estrutura para tabela `fechamentos`
--

CREATE TABLE `fechamentos` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `natureza` varchar(220) NOT NULL,
  `saldo_anterior` decimal(10,2) NOT NULL,
  `total_entrada` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_saida` decimal(10,2) NOT NULL DEFAULT 0.00,
  `saldo_atual` decimal(10,2) NOT NULL,
  `data_fechamento` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `fechamentos`
--

INSERT INTO `fechamentos` (`id`, `username`, `natureza`, `saldo_anterior`, `total_entrada`, `total_saida`, `saldo_atual`, `data_fechamento`) VALUES
(0, 'PAULO', '333903001', 6537.00, 0.00, 1097.84, 5439.46, '2025-04-07 14:25:09'),
(0, 'PAULO', '333903002', 19167.00, 0.00, 1663.80, 17503.12, '2025-04-07 14:25:09'),
(0, 'PAULO', '333903003', 62944.00, 327.99, 1814.37, 61458.06, '2025-04-07 14:25:09'),
(0, 'PAULO', '333903005', 14678.00, 0.00, 779.28, 13899.11, '2025-04-07 14:25:09'),
(0, 'PAULO', '333903008', 0.00, 0.00, 0.00, 0.00, '2025-04-07 14:25:09'),
(0, 'PAULO', '333903010', 7066.00, 0.00, 794.00, 6272.06, '2025-04-07 14:25:09'),
(0, 'PAULO', '333903011', 157765.00, 30.00, 429.00, 157365.90, '2025-04-07 14:25:09'),
(0, 'PAULO', '333903020', 0.00, 0.00, 0.00, 0.00, '2025-04-07 14:25:09'),
(0, 'PAULO', '333903021', 59061.00, 0.00, 0.00, 59061.49, '2025-04-07 14:25:09'),
(0, 'PAULO', '333903023', 913.00, 0.00, 65.19, 847.82, '2025-04-07 14:25:09'),
(0, 'PAULO', '333903030', 795.00, 0.00, 0.00, 795.34, '2025-04-07 14:25:09'),
(0, 'PAULO', '333903033', 681.00, 0.00, 0.00, 680.65, '2025-04-07 14:25:09'),
(0, 'PAULO', '333903042', 114463.00, 0.00, 1393.32, 113069.61, '2025-04-07 14:25:09'),
(0, 'PAULO', '344903010', 6555.00, 0.00, 0.00, 6554.64, '2025-04-07 14:25:09'),
(0, 'PAULO', '344903011', 7836.00, 0.00, 0.00, 7836.20, '2025-04-07 14:25:09'),
(0, 'PAULO', '344905206', 148.00, 0.00, 0.00, 147.80, '2025-04-07 14:25:09'),
(0, 'PAULO', '344905212', 320.00, 0.00, 0.00, 320.00, '2025-04-07 14:25:09'),
(0, 'PAULO', '344905217', 25444.00, 0.00, 0.00, 25443.80, '2025-04-07 14:25:09'),
(0, 'PAULO', '344905220', 34.00, 0.00, 0.00, 34.45, '2025-04-07 14:25:09'),
(0, 'PAULO', '344905238', 0.00, 0.00, 0.00, 0.00, '2025-04-07 14:25:09'),
(0, 'PAULO', '333903001', 6537.00, 0.00, 1097.84, 5439.46, '2025-05-06 13:16:55'),
(0, 'PAULO', '333903002', 19167.00, 0.00, 1663.80, 17503.12, '2025-05-06 13:16:55'),
(0, 'PAULO', '333903005', 14678.00, 0.00, 779.28, 13899.11, '2025-05-06 13:16:55'),
(0, 'PAULO', '333903008', 0.00, 0.00, 0.00, 0.00, '2025-05-06 13:16:55'),
(0, 'PAULO', '333903010', 7066.00, 432.71, 1660.84, 5837.93, '2025-05-06 13:16:55'),
(0, 'PAULO', '333903011', 157765.00, 0.00, 429.00, 157335.90, '2025-05-06 13:16:55'),
(0, 'PAULO', '333903020', 0.00, 0.00, 0.00, 0.00, '2025-05-06 13:16:55'),
(0, 'PAULO', '333903021', 59061.00, 0.00, 0.00, 59061.49, '2025-05-06 13:16:55'),
(0, 'PAULO', '333903023', 913.00, 0.00, 65.19, 847.82, '2025-05-06 13:16:55'),
(0, 'PAULO', '333903030', 795.00, 0.00, 0.00, 795.34, '2025-05-06 13:16:55'),
(0, 'PAULO', '333903033', 681.00, 0.00, 0.00, 680.65, '2025-05-06 13:16:55'),
(0, 'PAULO', '333903042', 114463.00, 0.00, 1393.32, 113069.61, '2025-05-06 13:16:55'),
(0, 'PAULO', '344903010', 6555.00, 0.00, 0.00, 6554.64, '2025-05-06 13:16:55'),
(0, 'PAULO', '344903011', 7836.00, 0.00, 0.00, 7836.20, '2025-05-06 13:16:55'),
(0, 'PAULO', '344905206', 148.00, 0.00, 0.00, 147.80, '2025-05-06 13:16:55'),
(0, 'PAULO', '344905212', 320.00, 0.00, 0.00, 320.00, '2025-05-06 13:16:55'),
(0, 'PAULO', '344905217', 25444.00, 0.00, 0.00, 25443.80, '2025-05-06 13:16:55'),
(0, 'PAULO', '344905220', 34.00, 0.00, 0.00, 34.45, '2025-05-06 13:16:55'),
(0, 'PAULO', '344905238', 0.00, 0.00, 0.00, 0.00, '2025-05-06 13:16:55'),
(0, 'MASTER', '333903001', 5439.46, 0.00, 0.00, 0.00, '2025-06-05 10:48:28'),
(0, 'MASTER', '333903002', 17503.12, 0.00, 0.00, 0.00, '2025-06-05 10:48:28'),
(0, 'MASTER', '333903005', 13899.11, 0.00, 0.00, 0.00, '2025-06-05 10:48:28'),
(0, 'MASTER', '333903008', 0.00, 0.00, 0.00, 0.00, '2025-06-05 10:48:28'),
(0, 'MASTER', '333903010', 5837.93, 130716.46, 9831.86, 283929.86, '2025-06-05 10:48:28'),
(0, 'MASTER', '333903011', 157335.90, 53016.82, 0.00, 105952.82, '2025-06-05 10:48:28'),
(0, 'MASTER', '333903020', 0.00, 0.00, 0.00, 0.00, '2025-06-05 10:48:28'),
(0, 'MASTER', '333903021', 59061.49, 32760.00, 0.00, 32760.00, '2025-06-05 10:48:28'),
(0, 'MASTER', '333903023', 847.82, 0.00, 0.00, 0.00, '2025-06-05 10:48:28'),
(0, 'MASTER', '333903030', 795.34, 0.00, 0.00, 0.00, '2025-06-05 10:48:28'),
(0, 'MASTER', '333903033', 680.65, 0.00, 0.00, 0.00, '2025-06-05 10:48:28'),
(0, 'MASTER', '333903042', 113069.61, 0.00, 0.00, 0.00, '2025-06-05 10:48:28'),
(0, 'MASTER', '344903010', 6554.64, 0.00, 0.00, 0.00, '2025-06-05 10:48:28'),
(0, 'MASTER', '344903011', 7836.20, 0.00, 0.00, 0.00, '2025-06-05 10:48:28'),
(0, 'MASTER', '344905206', 147.80, 0.00, 0.00, 0.00, '2025-06-05 10:48:28'),
(0, 'MASTER', '344905212', 320.00, 0.00, 0.00, 0.00, '2025-06-05 10:48:28'),
(0, 'MASTER', '344905217', 25443.80, 0.00, 0.00, 0.00, '2025-06-05 10:48:28'),
(0, 'MASTER', '344905220', 34.45, 0.00, 0.00, 0.00, '2025-06-05 10:48:28'),
(0, 'MASTER', '344905238', 0.00, 0.00, 0.00, 0.00, '2025-06-05 10:48:28'),
(0, 'MASTER', '333903003', 0.00, -3845.65, 0.00, -7693.22, '2025-06-05 10:48:28');

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
  `SEI` varchar(220) NOT NULL,
  `objeto` text NOT NULL,
  `gestor` varchar(255) NOT NULL,
  `gestorsb` varchar(255) NOT NULL,
  `fiscais` varchar(255) NOT NULL,
  `validade` date NOT NULL,
  `agencia_bancaria` varchar(11) NOT NULL,
  `fonte` varchar(11) NOT NULL,
  `publicacao` date DEFAULT NULL,
  `date_service` date DEFAULT NULL,
  `contatos` varchar(255) NOT NULL,
  `n_despesas` varchar(255) NOT NULL,
  `outros` enum('Sim','Não') DEFAULT 'Não',
  `servicos` varchar(255) DEFAULT NULL,
  `valor_nf` decimal(15,2) NOT NULL,
  `valor_contrato` decimal(15,2) NOT NULL,
  `valor_aditivo1` decimal(15,2) DEFAULT NULL,
  `num_parcelas` int(11) DEFAULT NULL,
  `descricao` text NOT NULL,
  `situacao` enum('Ativo','Inativo','Encerrado','Renovado') NOT NULL DEFAULT 'Ativo',
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  `setor` enum('administrador','estoque','patrimonio','financeiro','contratos') NOT NULL DEFAULT 'contratos',
  `parcelamento` enum('Sim','Não') DEFAULT 'Não',
  `valor_aditivo2` decimal(15,2) DEFAULT NULL,
  `valor_aditivo3` decimal(15,2) DEFAULT NULL,
  `valor_aditivo4` decimal(15,2) DEFAULT NULL,
  `valor_aditivo5` decimal(15,2) DEFAULT NULL,
  `etapa_atual` enum('Criação do Contrato','Aprovação Interna','Assinatura do Contrato','Execução do Contrato','Pagamentos','Finalização do Contrato','Prestação de Contas') DEFAULT 'Criação do Contrato',
  `categoria` varchar(50) NOT NULL,
  `garantia` decimal(5,2) DEFAULT 0.00,
  `gestor_portaria` varchar(50) DEFAULT NULL,
  `fiscal_portaria` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `gestao_contratos`
--

INSERT INTO `gestao_contratos` (`id`, `titulo`, `SEI`, `objeto`, `gestor`, `gestorsb`, `fiscais`, `validade`, `agencia_bancaria`, `fonte`, `publicacao`, `date_service`, `contatos`, `n_despesas`, `outros`, `servicos`, `valor_nf`, `valor_contrato`, `valor_aditivo1`, `num_parcelas`, `descricao`, `situacao`, `data_cadastro`, `setor`, `parcelamento`, `valor_aditivo2`, `valor_aditivo3`, `valor_aditivo4`, `valor_aditivo5`, `etapa_atual`, `categoria`, `garantia`, `gestor_portaria`, `fiscal_portaria`) VALUES
(22, 'PRODERJ', '100006/000680/2024', 'Prestação de serviços de Mensageira Eletrônica (e-mail)', 'JOÃO FREITAS BRAGA CARUSO', 'RAPHAELA BATISTA SALDANHA', 'ALEXANDRE MENDES DA ROCHA // GABRIEL DE SOUZA RODRIGUES // EMILLY MARTINS DOS SANTOS //', '2026-01-18', 'Bradesco- 2', '100', '2024-12-30', '2025-04-29', '33914009', 'Sem Despesas', 'Não', 'servico1', 0.00, 58500.00, NULL, 0, '.', 'Ativo', '2025-06-03 17:39:18', 'contratos', 'Sim', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'servico', 0.00, NULL, NULL),
(38, 'PRODERJ VPS', 'SEI-100006/000203/2021	', 'Hospedagem de Serviços virtuais privados (VPS)', ' JOÃO FREITAS BRAGA CARUSO', 'SEM GESTOR ', 'ALEXANDRE MENDES DA ROCHA // RENATO MACHADO DA SILVA', '2025-01-21', 'Bradesco- 2', '100', '2025-01-21', '2025-01-21', '33914009', 'Sem Despesas', 'Não', 'servico1', 0.00, 336348.48, NULL, 12, '..', 'Ativo', '2025-06-05 18:04:11', 'contratos', 'Sim', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'servico', 0.00, NULL, NULL),
(39, 'CLARO', 'SEI-100006/000595/2021		', 'Prestação de serviços de comunicação de dados de longa distância (WAN), conexão internet para rede governo e serviços complementares de tecnologia da informação e comunicação 		Prestação de serviços de comunicação de dados de longa distância (WAN), conexão internet para rede governo e serviços complementares de tecnologia da informação e comunicação 																						', 'DAVI VIANNA DE MACEDO OLIVEIRA', 'SEM GESTOR ', 'EMILLY MARTINS DOS SANTOS,', '2025-03-03', '\"BRADESCO A', '100', '2025-03-06', '2025-03-03', '33904012', 'Sem Despesas', 'Não', 'servico1', 0.00, 238402.42, NULL, 23, '.', 'Ativo', '2025-06-12 15:07:42', 'contratos', 'Sim', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'servico', 0.00, NULL, NULL),
(40, 'OI S/A', 'SEI-100006/001705/2022		', 'Prestação de serviço telefônico fixo comutado - STFC (fixo-fixo e fixo-móvel), nas modalidades Local, Longa Distância Nacional (LDN) e Longa Distância Internacional (LDI), a ser executado de forma contínua, com fornecimento de aparelhos novos ou seminovos por comodato												', 'DAVI VIANNA DE MACEDO OLIVEIRA ', 'SEM GESTOR ', 'PATRÍCIA MATOS DA SILVA -- PAULO VITOR LIMA GOMES', '2025-03-03', '\"BRADESCO C', '100', '2025-03-07', '2025-03-03', '33903906', 'Sem Despesas', 'Não', 'servico1', 0.00, 432868.98, NULL, 12, 'R$ 432.868,98 (Contrato + 1 TA) Termo Aditivo o valor de R$ 232.209,80 \r\n', 'Ativo', '2025-06-12 15:52:11', 'contratos', 'Sim', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'servico', 0.00, NULL, NULL),
(41, 'RAAC', 'SEI-100006/000184/2020		', 'Contratação de pessoa jurídica especializada na prestação de serviços técnicos de Auditoria Independente													', 'DAVI VIANNA DE MACEDO OLIVEIRA', 'SEM GESTOR ', 'DANILLO CUNHA PAULA--JOÃO LUIZ FREITAS BRAGA CARUSO--GRAZIELA SPÓSITO', '2021-01-08', '\"BRADESCO C', '100', '2021-01-13', '2021-01-13', '33903501', 'Sem Despesas', 'Não', 'servico1', 0.00, 298953.09, NULL, 0, 'R$ 298.953,09 (Contrato + 1, 2 e 3 TA)	\r\n', 'Ativo', '2025-06-12 17:01:13', 'contratos', 'Sim', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'servico', 0.00, NULL, NULL),
(42, 'SANTA CABRINI', 'SEI-100006/000928/2024		', 'Prestação de serviços de gerenciamento de mão de obra de 08 (oito) gerenciados em cumprimento de pena sob os regimes semiaberto, aberto, em prisão albergue domiciliar – PAD e livramento condicional													', 'RAPHAELA BATISTA SALDANHA', 'DAVI VIANNA DE MACEDO OLIVEIRA', 'BRUNO ALMEIDA DOS SANTOS//LUCIMAR STRAUB//RITA DE CÁSSIA CUSTODIO DA SILVA//EMILLY MARTINS DOS SANTOS//HUMBERTO HAUILA JUNIOR', '2024-11-22', '\"BRADESCO C', '100', '2024-11-25', '2024-11-22', '33.91.39.29', 'Sem Despesas', 'Não', 'servico1', 0.00, 246783.60, NULL, NULL, '.', 'Ativo', '2025-06-12 17:43:27', 'contratos', 'Não', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'servico', 0.00, NULL, NULL),
(43, 'CIEE', 'SEI-100006/000066/2024		', 'Contratação de estagiários												', 'DAVI VIANNA DE MACEDO OLIVEIRA', 'RAPHAELA BATISTA SALDANHA', 'ISABELLA DE SOUZA CHRYSOSTOMO//EMILLY MARTINS DOS SANTOS//ESTHEFANI RODRIGUES SANTOS', '2025-01-29', '\"BRADESCO A', '100', '2025-01-28', '2025-01-29', '33.90.34.01', 'Sem Despesas', 'Não', 'servico1', 0.00, 996570.00, NULL, NULL, '.', 'Ativo', '2025-06-12 18:54:52', 'contratos', 'Não', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'servico', 0.00, NULL, NULL),
(44, 'RAAC', 'SEI-100006/000184/2020		', 'Contratação de pessoa jurídica especializada na prestação de serviços técnicos de Auditoria Independente													', 'DAVI VIANNA DE MACEDO OLIVEIRA', 'SEM GESTOR ', 'DANILLO CUNHA PAULA// JOÃO LUIZ FREITAS BRAGA CARUSO// GRAZIELA SPÓSITO', '2021-01-08', '\"BRADESCO C', '100', '2021-01-13', '2021-01-08', '33903501', 'Sem Despesas', 'Não', 'servico1', 0.00, 298953.09, NULL, NULL, 'R$ 298.953,09 (Contrato + 1, 2 e 3 TA)	', 'Ativo', '2025-06-16 16:03:01', 'contratos', 'Não', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'servico', 0.00, NULL, NULL),
(45, 'AGUAS DO RIO', 'SEI-100006/001986/2021		', 'Fornecimento de agua e manutenção de esgoto												', 'DAVI VIANNA DE MACEDO OLIVEIRA', 'RAPHAELA BATISTA SALDANHA', 'Fiscais Técnicos  FÁBIO CORRÊA BARBOSA// Fiscais Técnicos  THIAGO NETO DE OLIVEIRA', '0001-01-01', '\"BRADESCO C', '100', '0001-01-01', '0001-01-01', '33903950', 'Sem Despesas', 'Não', 'servico1', 0.00, 0.00, NULL, NULL, 'EMILLY MARTINS DOS SANTOS FISCAL ADM. PORTARIA 71587147\r\n\r\n. Valor não foi estipulado no termo, sendo o faturamento a medida das faturas emitidas mensalmente. Vigência do termo é de 35 anos.', 'Ativo', '2025-06-16 17:29:25', 'contratos', 'Não', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'servico', 0.00, NULL, NULL),
(46, 'DADY ILHA ', 'SEI-100006/000561/2022', 'Prestação de serviços de solução continuada de impressão, cópia e digitalização corporativa - Estações Digitais de Serviço (EDS) Departamentais, integrada a sistemas corporativos e à rede de dados, compreendendo a cessão de direito de uso de equipamentos, incluindo a prestação de serviços de manutenção preventiva e corretiva, fornecimento de peças e consumíveis necessários (exceto papel), assim como serviços de gestão, controle e operacionalização da solução e treinamento.', 'DAVI VIANNA DE MACEDO OLIVEIRA', 'RAPHAELA BATISTA SALDANHA', 'JOÃO FREITAS BRAGA CARUSO // GABRIEL DE SOUZA RODRIGUES', '2024-08-29', '\"BRADESCO C', '100', '2024-08-27', '2024-08-29', '33904006', 'Sem Despesas', 'Não', 'servico1', 0.00, 140026.23, NULL, NULL, '.', 'Ativo', '2025-06-16 17:48:25', 'contratos', 'Não', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'obra', 0.00, NULL, NULL),
(47, 'WEBTRIP', 'SEI-100006/000422/2024		', 'Prestação de serviços de agência de viagens, consistindo em: reserva, cancelamento, marcação, remarcação, emissão e entrega de bilhetes de passagens aéreas no âmbito nacional e internacional; emissão de seguro de assistência em viagem internacional', '	 RAPHAELA BATISTA SALDANHA', 'DAVI VIANNA DE MACEDO OLIVEIRA', 'EMILLY MARTINS DOS SANTOS // ANDRE MIRANDA LOBÃO TAVARES MENDONÇA // ISABELLA DE SOUZA CHRYSOSTOMO', '2024-07-25', '\"BRADESCO A', '100', '2024-07-25', '2024-07-25', '3390.33.01', 'Sem Despesas', 'Não', 'servico1', 0.00, 100000.00, NULL, NULL, '.', 'Ativo', '2025-06-16 18:31:55', 'contratos', 'Não', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'servico', 0.00, NULL, NULL),
(48, 'MOBILESAT', ' 	SEI-100006/001573/2021		', 'Serviços de licença de uso de software monitoramento via satélite com tecnologia GPS/GSM/GPRS, contemplando hardware e software																							', 'DAVI VIANNA DE MACEDO OLIVEIRA', 'RAPHAELA BATISTA SALDANHA', 'FISCAIS TECNICICOS - FÁBIO CORRÊA BARBOSA / THIAGO NETO DE OLIVIERA-  FISCAL ADM- ISABELLA DE SOUZA CHRYSOSTOMO', '2024-11-08', '\"BRADESCO  ', '100', '2024-11-07', '2024-11-08', '33903039', 'Sem Despesas', 'Não', 'servico1', 0.00, 7680.00, NULL, NULL, '.', 'Ativo', '2025-06-16 19:04:11', 'contratos', 'Não', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'obra', 0.00, NULL, NULL),
(49, 'PRIME ', 'SEI-100006/000479/2023		', ' Contratação de empresa especializada para prestação de serviços de gerenciamento de abastecimento da frota de veículos oficiais do CENTRAL, com implantação, intermediação e administração de um sistema informatizado e integrado, com utilização de tag/etiqueta com tecnologia RFID ou similar de gerenciamento de frota em estabelecimentos credenciados, compreendendo a distribuição de etanol,  gasolina (comum/aditivada) e diesel(comum s/10).												', 'DAVI VIANNA DE MACEDO OLIVEIRA', 'SEM GESTOR ', 'EMILLY MARTINS DOS SANTOS, //  ISABELLA DE SOUZA CHRYSOSTOMO', '2025-05-08', '\"BRADESCO C', '100', '2025-05-12', '2025-05-08', '33903039', 'Sem Despesas', 'Não', 'servico1', 0.00, 188403.29, NULL, NULL, '188.403,29 (Contrato + 1 TA + 2 TA + 3 TA)\r\n', 'Ativo', '2025-06-18 18:40:07', 'contratos', 'Não', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'servico', 0.00, NULL, NULL),
(50, 'LIBEX', 'SEI-100006/000346/2023		', 'Prestação de serviços de LOCAÇÃO DE 5 VEÍCULOS ESPECIAIS', 'DAVI VIANNA DE MACEDO OLIVEIRA', 'RAPHAELA BATISTA SALDANHA', 'EMILLY MARTINS DOS SANTOS, //  ISABELLA DE SOUZA CHRYSOSTOMO', '2023-05-19', '\"BRADESCO C', '100', '2023-05-19', '2023-05-19', '33903913', 'Sem Despesas', 'Não', 'servico1', 0.00, 433279.35, NULL, NULL, '.', 'Ativo', '2025-06-18 20:02:16', 'contratos', 'Não', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'servico', 0.00, NULL, NULL),
(51, 'MRS', ' E-10/300.102/2010.		', 'Regulamentação do direito de passagem												', 'MARCIO RODRIGUES', 'SEM GESTOR ', 'DAVI VIANNA DE MACEDO OLIVEIRA-RAPHAELA BATISTA SALDANHA-RODRIGO PAES OSÓRIO TOSTA-DANILLO CUNHA PAULA', '0001-01-01', '0000', '0000', '0001-01-01', '0001-01-01', '00000', 'Sem Despesas', 'Não', 'servico1', 0.00, 0.00, NULL, NULL, '..', 'Ativo', '2025-06-23 13:41:24', 'contratos', 'Não', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'servico', 0.00, NULL, NULL),
(52, 'LIGTH', 'SEI-100006/000244/2020		', 'Fornecimento de energia - Carlos Brandt - Código de instalação: 0400046264												', '	 DAVI VIANNA DE MACEDO OLIVEIRA ', 'SEM GESTOR ', 'EMILLY MARTINS DOS SANTOS', '0001-01-01', '\"BRADESCO  ', '100', '0001-01-01', '0001-01-01', '33903911', 'Sem Despesas', 'Não', 'servico1', 0.00, 260000.00, NULL, NULL, 'Contrato renovado automaticamente. ', 'Ativo', '2025-06-23 16:53:59', 'contratos', 'Não', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'servico', 0.00, NULL, NULL),
(53, 'FIEL', 'SEI-100006/001021/2023		', 'Contratação de prestação de serviços de bondes elétricos de transporte de passageiros no Sistema de Bondes de Santa Teresa/RJ, por meio de disponibilização de mão de obra qualificada												', 'DAVI VIANNA DE MACEDO OLIVEIRA', 'RAPHAELA BATISTA SALDANHA', 'EMILLY MARTINS DOS SANTOS', '2023-10-20', '\"BRADESCO  ', '100', '2023-10-16', '2023-10-20', '3390.39.38', 'Sem Despesas', 'Não', 'servico1', 0.00, 4742540.77, NULL, NULL, '.', 'Ativo', '2025-06-23 17:35:42', 'contratos', 'Não', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'obra', 0.00, NULL, NULL),
(54, 'DELTA FLORENSE ', 'SEI-100006/000973/2023		', 'Contratação de pessoa jurídica para prestação de serviço de monitoramento (compreendendo-se captação, leitura e envio via e-mail) de publicações das decisões judiciais nos Diários Oficiais, a fim de auxiliar a defesa das Companhias em juízo, quais sejam: Companhia Fluminense de Trens Urbanos – Flumitrens; e Companhia Estadual de Engenharia de Transportes e Logística – Central												', 'RAPHAELA BATISTA SALDANHA', 'SEM GESTOR ', 'DAVI VIANNA DE MACEDO - EMILLY MARTINS DOS SANTOS', '2023-11-01', '\"BRADESCO  ', '100', '2023-11-01', '2023-11-01', '3390.39.82', 'Sem Despesas', 'Não', 'servico1', 0.00, 0.00, NULL, NULL, '.', 'Ativo', '2025-06-23 18:01:28', 'contratos', 'Não', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'obra', 0.00, NULL, NULL),
(55, 'MPE ENGENHARIA', 'SEI-100006/001175/2023		', 'Projeto Executivo e execução de serviços complementares de recuperação da Via Permanente e da Rede Aérea no Sistema de Bondes de Santa Teresa da Cidade do Rio de Janeiro - \"Ramal Dois Irmãos - Silvestre\" e \"Ramal Largo do Guimarães - Largo das Neves													', 'DAVI VIANNA DE MACEDO OLIVEIRA', 'SEM GESTOR ', 'RENATO FELIX NEVES// RAPHAELA BATISTA SALDANHA//JORGE CHAVES JUNIOR', '2023-09-22', '\"BRADESCO  ', '151 (FECAM)', '2023-09-22', '2023-09-22', '4490.51.07', 'Sem Despesas', 'Não', 'servico1', 0.00, 69800000.00, NULL, NULL, '.', 'Ativo', '2025-06-23 18:53:05', 'contratos', 'Não', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'obra', 0.00, NULL, NULL),
(56, 'GALATAS', 'SEI-100006/000254/2024		', 'Locação de 18 (dezoito) transceptores portáteis para o Sistema de Bondes de Santa Teresa												', '	 RAPHAELA BATISTA SALDANHA', 'DAVI VIANNA DE MACEDO OLIVEIRA', 'EMILLY MARTINS DOS SANTOS//FÁBIO CORRÊA BARBOSA//BRUNO ALMEIDA DOS SANTOS', '2024-06-09', '\"BRADESCO  ', '100', '2024-06-12', '2024-06-12', '33.90.39.14', 'Sem Despesas', 'Não', 'servico1', 0.00, 47088.00, NULL, NULL, '.\r\n', 'Ativo', '2025-06-23 19:04:44', 'contratos', 'Não', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'servico', 0.00, NULL, NULL),
(57, 'GREEN CARD', 'SEI-100006/000589/2023		', 'Prestação de serviço de administração e emissão de CARTÕES magnéticos com chip, conforme tecnologia disponível, para concessão dos benefícios AUXÍLIO-ALIMENTAÇÃO, em REFEIÇÕES e/ou GÊNEROS ALIMENTÍCIOS (ALIMENTAÇÃO)												', 'DAVI VIANNA DE MACEDO OLIVEIRA', 'RAPHAELA BATISTA SALDANHA', '	EMILLY MARTINS DOS SANTOS//VALDENICE ALVES DA SILVA DE PAULA', '2022-09-19', '\"BRADESCO  ', '100', '2025-01-01', '2025-01-01', '33.903.941', 'Sem Despesas', 'Não', 'servico1', 0.00, 4322640.00, NULL, NULL, '.', 'Ativo', '2025-06-23 19:26:47', 'contratos', 'Não', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'servico', 0.00, NULL, NULL),
(58, 'MPE ENGENHARIA ', 'SEI-100006/001175/2023		', 'Projeto Executivo e execução de serviços complementares de recuperação da Via Permanente e da Rede Aérea no Sistema de Bondes de Santa Teresa da Cidade do Rio de Janeiro - \"Ramal Dois Irmãos - Silvestre\" e \"Ramal Largo do Guimarães - Largo das Neves													', 'DAVI VIANNA DE MACEDO OLIVEIRA', 'SEM GESTOR ', 'RENATO FELIX NEVES // JOSE CARLOS DE ALMEIDA// FABRÍCIO ANTONIO RIBEIRO LOBO//	 JORGE CHAVES JUNIOR//  FISCAL ADM 	 RAPHAELA BATISTA SALDANHA', '2023-11-22', '\"BRADESCO  ', '151 (FECAM)', '2023-11-22', '2023-11-22', '4490.51.07', 'Sem Despesas', 'Não', 'servico1', 0.00, 69800000.00, NULL, NULL, '.', 'Ativo', '2025-06-24 14:08:02', 'contratos', 'Não', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'obra', 0.00, NULL, NULL),
(59, 'ALT & RAILTECO', 'SEI-100006/000030/2024		', 'Reperfilamento de 20 (vinte) Aros de Rodas												', 'DAVI VIANNA DE MACEDO OLIVEIRA', 'RAPHAELA BATISTA SALDANHA', '	 THIAGO NETO DE OLIVEIRA//BRUNO ALMEIDA DOS SANTOS//	 EMILLY MARTINS DOS SANTOS', '2024-12-13', '\"BRADESCO  ', '100', '2024-12-13', '2024-12-13', '33.90.39.17', 'Sem Despesas', 'Não', 'servico1', 0.00, 75900.00, NULL, NULL, '.', 'Ativo', '2025-06-24 14:32:32', 'contratos', 'Não', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'servico', 0.00, NULL, NULL),
(60, 'CTPM', 'SEI-100006/001008/2024		', 'Prestação de serviços de ELABORAÇÃO DE ESTIMATIVA ORÇAMENTÁRIA PARA OPERAÇÃO E MANUTENÇÃO DO SISTEMA FERROVIÁRIO DE TRANSPORTE DE PASSAGEIROS DA REGIÃO METROPOLITANA DO ESTADO DO RIO DE JANEIRO												', 'DAVI VIANNA DE MACEDO OLIVEIRA', 'MARCIO RODRIGUES ', 'JORGE CHAVES JUNIOR// ANDRE ASSUNÇÃO DE CASTRO// RENATO FELIX NEVES//RAPHAELA BATISTA SALDANHA', '2024-11-06', '\"BRADESCO A', '100', '2024-11-06', '2024-11-06', 'R$ 3.076.397,16', 'Sem Despesas', 'Não', 'servico1', 0.00, 3076397.16, NULL, NULL, 'TERMO ADTIVO 92590146\r\nTERMO ADITIVO 99522131', 'Ativo', '2025-06-30 14:04:42', 'contratos', 'Não', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'servico', 0.00, NULL, NULL),
(61, 'NOTRE DAME', 'SEI-100006/000837/2024		', 'Plano de saúde												', 'RAPHAELA BATISTA SALDANHA', 'DAVI VIANNA DE MACEDO OLIVEIRA', 'ISABELLA DE SOUZA CHRYSOSTOMO//VALDENICE ALVES DA SILVA DE PAULA//PAULO SERGIO SILVA SALES', '2024-11-21', '\"BRADESCO A', '100', '2024-11-27', '2024-11-21', '3390.39.63', 'Sem Despesas', 'Não', 'servico1', 0.00, 7654920.00, NULL, NULL, '.', 'Ativo', '2025-06-30 14:50:02', 'contratos', 'Não', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'servico', 0.00, NULL, NULL),
(62, 'MJS DUARTE LTDA', 'SEI-100006/000733/2024', 'Aquisição de FILTRO COADOR DE CAFÉ, GARRAFA TÉRMICA', 'RAPHAELA BATISTA SALDANHA', 'SEM GESTOR ', 'EMILLY MARTINS DOS SANTOS//CLAUDIA BIANCA SANTOS DE LUCA//HUMBERTO HAUILA JUNIOR', '2024-11-28', '\"BRADESCO A', '100/2023', '2024-11-28', '2024-11-28', '33.90.30.01', 'Sem Despesas', 'Não', 'servico1', 0.00, 3363.00, NULL, NULL, '.', 'Ativo', '2025-06-30 17:29:45', 'contratos', 'Não', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'servico', 0.00, NULL, NULL),
(63, 'GRANÁ 298 DISTRIBUIDORA ', 'SEI-100006/000733/2024		', 'Açúcar, Adoçante e Café												', 'RAPHAELA BATISTA SALDANHA', 'SEM GESTOR ', 'EMILLY MARTINS DOS SANTOS, HUMBERTO HAUILA JUNIOR//CLAUDIA BIANCA SANTOS DE LUCA', '2024-11-26', '\"BRADESCO A', '100/2023', '2024-11-28', '2024-11-28', '33.90.30.20', 'Sem Despesas', 'Não', 'servico1', 0.00, 24460.80, NULL, NULL, '.', 'Ativo', '2025-06-30 17:51:26', 'contratos', 'Não', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'obra', 0.00, NULL, NULL),
(64, 'RC CARD', 'SEI-100006/001414/2024		', 'Cesta natalina												', 'RAPHAELA BATISTA SALDANHA', 'DAVI VIANNA DE MACEDO OLIVEIRA', 'ANDRÉ MIRANDA LOBÃO TAVARES MENDONÇA//THAÍS SIMÕES DE ASSIS//VALDENICE ALVES DA SILVA DE PAULA', '2024-12-13', '\"BRADESCO A', '230', '2024-12-13', '2024-12-13', '33.90.39.41', 'Sem Despesas', 'Não', 'servico1', 0.00, 300960.00, NULL, NULL, '.', 'Ativo', '2025-06-30 18:31:30', 'contratos', 'Não', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'obra', 0.00, NULL, NULL),
(65, 'IVM TELECOMUNICAÇÃO', 'SEI-100006/000491/2024		', 'Telefonia móvel', 'RAPHAELA BATISTA SALDANHA', 'DAVI VIANNA DE MACEDO OLIVEIRA', 'EMILLY MARTINS DOS SANTOS// PATRÍCIA MATOS DA SILVA//HUMBERTO HAUILA JUNIOR', '2025-01-01', '\"BRADESCO A', '100', '2025-12-18', '2025-01-01', '33.90.39.44', 'Sem Despesas', 'Não', 'servico1', 0.00, 49598.40, NULL, NULL, '.', 'Ativo', '2025-06-30 18:52:12', 'contratos', 'Não', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'servico', 0.00, NULL, NULL),
(66, 'UCHOA SEGURANÇA', 'SEI-100006/000912/2024		', 'Prestação de serviços de continuados de vigilância desarmada, diurna e noturna												', 'DAVI VIANNA DE MACEDO OLIVEIRA', 'RAPHAELA BATISTA SALDANHA', 'ISABELLA DE SOUZA CHRYSOSTOMO//	 EMILLY MARTINS DOS SANTOS//BRUNO ALMEIDA DOS SANTOS//FÁBIO CORRÊA BARBOSA//	 HUMBERTO HAUILA JUNIOR//THIAGO NETO DE OLIVEIRA', '0005-01-14', '\"BRADESCO A', '100', '2025-01-14', '2025-01-14', '33.90.39.23', 'Sem Despesas', 'Não', 'servico1', 0.00, 2461756.08, NULL, NULL, '.termo aditivo 99329587', 'Ativo', '2025-06-30 19:06:32', 'contratos', 'Não', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'servico', 0.00, NULL, NULL),
(67, 'METALPRONTO INDÚSTRIA E COMÉRCIO LTDA', 'SEI-100006/000277/2025		', 'Desfazimento dos Trilhos modelo TR37 (aço carbono) - Lote 1								', 'DAVI VIANNA DE MACEDO OLIVEIRA', 'RAPHAELA BATISTA SALDANHA', 'HUMBERTO HAUILA JUNIOR//	 RICARDO SEBASTIAO CARRUPT DA CUNHA//	 99.000.945  ASSCON   ', '2025-06-12', '.', '.', '2025-06-11', '2025-06-12', '.', 'Sem Despesas', 'Não', 'servico1', 0.00, 112210.00, NULL, NULL, '.', 'Ativo', '2025-06-30 19:43:59', 'contratos', 'Não', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'servico', 0.00, NULL, NULL),
(68, 'SM GERENCIAMENTO DE RESÍDUOS SÓLIDOS E EMPREENDIMENTOS LTDA', 'SEI-100006/000277/2025', 'desfazimento dos bens abaixo relacionados Trilhos modelo 57R1', 'DAVI VIANNA DE MACEDO OLIVEIRA', 'RAPHAELA BATISTA SALDANHA', 'HUMBERTO HAUILA JUNIOR//	 RICARDO SEBASTIAO CARRUPT DA CUNHA//	 ISABELLA DE SOUZA CHRYSOSTOMO', '2025-06-11', '.', '.', '2025-06-11', '2025-06-11', '.', 'Sem Despesas', 'Não', 'servico1', 0.00, 79420.00, NULL, NULL, '.', 'Ativo', '2025-06-30 19:50:59', 'contratos', 'Não', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'obra', 0.00, NULL, NULL),
(69, 'F MAIA DEMOLIÇÕES LTDA', 'SEI-100006/000277/2025', 'desfazimento Trilhos modelo 57R1', 'DAVI VIANNA DE MACEDO OLIVEIRA', 'RAPHAELA BATISTA SALDANHA', 'HUMBERTO HAUILA JUNIOR//	 RICARDO SEBASTIAO CARRUPT DA CUNHA//	 	 ISABELLA DE SOUZA CHRYSOSTOMO', '2025-06-11', '.', '.', '2025-06-11', '2025-06-11', '.', 'Sem Despesas', 'Não', 'servico1', 0.00, 163910.00, NULL, NULL, '.', 'Ativo', '2025-06-30 19:56:59', 'contratos', 'Não', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'obra', 0.00, NULL, NULL),
(70, 'TECNOTRENS AUTOMAÇÃO LTDA-ME', '100006/002001/2021', 'aquisição de materiais eletro/eletrônicos de aplicação nos sistemas de controle e tração do material rodante (bondes),', 'DAVI VIANNA DE MACEDO OLIVEIRA', 'SEM GESTOR ', 'FÁBIO CORRÊA BARBOSA -99.000.559 - SUPTRA / CLÁUDIO LUIZ LOPES DO NASCIMENTO - 94.027.229 - SUPTRA', '2022-07-30', '.', '.', '2022-07-21', '2022-07-30', '.', 'Sem Despesas', 'Não', 'servico1', 0.00, 24293.00, NULL, NULL, '.', 'Inativo', '2025-07-01 14:06:24', 'contratos', 'Não', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'servico', 0.00, NULL, NULL),
(71, 'TECNOBRE COMÉRCIO', '100006/002001/2021', 'Aquisição de materiais eletro/eletrônicos de aplicação nos sistemas de controle e tração do material rodante (bondes)', 'DAVI VIANNA DE MACEDO OLIVEIRA', 'SEM GESTOR ', 'FÁBIO CORRÊA BARBOSA -99.000.559 - SUPTRA / CLÁUDIO LUIZ LOPES DO NASCIMENTO - 94.027.229 - SUPTRA', '2022-07-30', '.', '.', '2022-06-21', '2022-07-30', '.', 'Sem Despesas', 'Não', 'servico1', 0.00, 24293.00, NULL, 0, '.', 'Inativo', '2025-07-01 14:32:51', 'contratos', 'Sim', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'servico', 0.00, NULL, NULL),
(72, 'TECNOTRENS AUTOMAÇÃO LTDA-ME', 'SEI-100006/002001/2021', 'Peças para o Bonde de Santa Tereza', 'DAVI VIANNA DE MACEDO OLIVEIRA', 'SEM GESTOR ', 'FÁBIO CORRÊA BARBOSA -99.000.559 - SUPTRA / CLÁUDIO LUIZ LOPES DO NASCIMENTO - 94.027.229 - SUPTRA', '2022-07-30', '.', '.', '2022-06-21', '2022-07-30', '.', 'Sem Despesas', 'Não', 'servico1', 0.00, 13200.00, NULL, NULL, '.', 'Inativo', '2025-07-01 15:17:33', 'contratos', 'Não', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'obra', 0.00, NULL, NULL),
(73, 'PRIME TRANSLATIONS', '100006/001774/2021', 'Tradução', 'DAVI VIANNA DE MACEDO OLIVEIRA', 'SEM GESTOR ', 'Luis Claudio Alves de Mello - 09.002.064 - GERMAR / Carlos André Ferreira - 03.007.216 - DIRAF', '2022-06-13', 'corrente nº', '.111', '2022-06-13', '2022-06-13', '.44.90.35', 'Sem Despesas', 'Não', 'servico1', 0.00, 64000.00, NULL, NULL, '.', 'Inativo', '2025-07-01 16:19:59', 'contratos', 'Não', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'servico', 0.00, NULL, NULL),
(74, 'WEG DRIVES & CONTROLS - AUTOMAÇÃO LTDA', '100006/001217/2022', 'Aquisição de Inversor de tração', 'DAVI VIANNA DE MACEDO OLIVEIRA', 'SEM GESTOR ', 'CLÁUDIO LUIZ LOPES DO NASCIMENTO  - 94.027.229 - SUPTRA  /  LUIS CLAUDIO ALVES DE MELLO - 09.002.064 - GERMAR', '2022-11-28', ' conta corr', '100', '2022-11-28', '2022-11-28', '33903042', 'Sem Despesas', 'Não', 'servico1', 0.00, 92221.53, NULL, NULL, '.', 'Inativo', '2025-07-01 16:59:34', 'contratos', 'Não', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'obra', 0.00, NULL, NULL),
(75, 'HARPIA EMBALAGENS (BILHETE ÚNICO)', '100006/000001/2023', 'Prestação de Serviços gráficos: confeção de 240.000(duzentos e quarenta ) mil bilhetes personalizados', 'DAVI VIANNA DE MACEDO OLIVEIRA', 'SEM GESTOR ', 'FÁBIO CORRÊA BARBOSA   99.000.559 - SUPTRA   / CLAUDIO LUIZ LOPES DO NASCIMENTO 94.027.229 - SUPTRA', '2023-03-27', 'corrente nº', '230', '2023-03-27', '2023-03-27', '26.785.0469.6099', 'Sem Despesas', 'Não', 'servico1', 0.00, 33600.00, NULL, NULL, '.', 'Inativo', '2025-07-01 17:13:50', 'contratos', 'Não', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'servico', 0.00, NULL, NULL),
(76, 'SANTA CABRINI ina', '100006/001335/2021', 'Prestação de serviços de gerenciamento de mão de obra de 06 (seis) internos, para a realização de serviços gerais no prédio da CENTRAL.', 'DAVI VIANNA DE MACEDO OLIVEIRA', 'SEM GESTOR ', 'RITA DE CÁSSIA CUSTÓDIO DA SILVA  //LUCIMAR STRAUB //', '2021-11-22', 'corrente nº', '100', '2021-11-22', '2021-11-22', '3390.39', 'Sem Despesas', 'Não', 'servico1', 0.00, 135748.80, NULL, NULL, '.', 'Inativo', '2025-07-01 18:09:17', 'contratos', 'Não', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'servico', 0.00, NULL, NULL),
(77, 'CENTRO DE INTEGRAÇÃO EMPRESA ESCOLA DO ESTADO DO RIO DE JANEIRO', '100006/001572/2021', 'Estagiários', 'DAVI VIANNA DE MACEDO OLIVEIRA', 'SEM GESTOR ', 'Ana Caroline de Matos Cezario  - Graziela Spósito -', '2022-01-28', 'BRADESCO  c', '100', '2022-02-01', '2022-02-01', '3390', 'Sem Despesas', 'Não', 'servico1', 0.00, 1545600.00, NULL, NULL, '.', 'Inativo', '2025-07-01 18:33:41', 'contratos', 'Não', NULL, NULL, NULL, NULL, 'Criação do Contrato', 'obra', 0.00, NULL, NULL);

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
(589, 'PAULO', 'cadastrou o produto no estoque', '2025-03-29 14:40:57'),
(590, 'MASTER', 'atualizou o produto', '2025-04-05 16:30:04'),
(591, 'MASTER', 'atualizou o produto', '2025-04-05 16:30:22'),
(592, 'MASTER', 'cadastrou o produto no estoque', '2025-04-05 16:30:42'),
(593, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-04-06 10:08:31'),
(594, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-04-06 10:12:57'),
(595, 'CONTRATOS', 'Login bem-sucedido', '2025-04-06 11:01:04'),
(596, 'MASTER', 'Login bem-sucedido', '2025-04-06 11:16:23'),
(597, 'PAULO', 'Login bem-sucedido', '2025-04-06 11:48:23'),
(598, 'MASTER', 'Login bem-sucedido', '2025-04-06 11:53:21'),
(599, 'PAULO', 'Login bem-sucedido', '2025-04-06 12:12:57'),
(600, 'CONTRATOS', 'Login bem-sucedido', '2025-04-06 12:24:06'),
(601, 'PAULO', 'Login bem-sucedido', '2025-04-07 17:22:38'),
(602, 'CONTRATOS', 'Login bem-sucedido', '2025-04-07 17:25:51'),
(603, 'PAULO', 'Login bem-sucedido', '2025-04-08 11:49:38'),
(604, 'PAULO', 'cadastrou ou atualizou o produto no estoque', '2025-04-08 11:52:20'),
(605, 'MASTER', 'Login bem-sucedido', '2025-04-08 13:23:09'),
(606, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-04-08 13:52:57'),
(607, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-04-08 14:03:01'),
(608, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-04-08 14:10:39'),
(609, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-04-08 14:13:47'),
(610, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-04-08 14:41:55'),
(611, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-04-08 14:53:34'),
(612, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-04-08 14:56:27'),
(613, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-04-08 15:02:11'),
(614, 'MASTER', 'Login falhou: Senha inválida', '2025-04-08 15:03:33'),
(615, 'MASTER', 'Login bem-sucedido', '2025-04-08 15:03:47'),
(616, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-04-08 15:42:02'),
(617, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-04-08 20:04:42'),
(618, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-04-08 20:22:26'),
(619, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-04-08 20:28:39'),
(620, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-04-08 20:29:46'),
(621, 'MASTER', 'Login bem-sucedido', '2025-04-13 04:44:59'),
(622, 'CONTRATOS', 'Login bem-sucedido', '2025-04-13 04:46:24'),
(623, 'MASTER', 'Login bem-sucedido', '2025-04-18 10:35:12'),
(624, 'CONTRATOS', 'Login bem-sucedido', '2025-04-21 10:22:03'),
(625, 'MASTER', 'Login bem-sucedido', '2025-04-22 11:12:01'),
(626, 'CONTRATOS', 'Login bem-sucedido', '2025-04-22 11:12:22'),
(627, 'MASTER', 'Login bem-sucedido', '2025-04-23 16:50:09'),
(628, 'PAULO', 'Login bem-sucedido', '2025-04-23 16:51:29'),
(629, 'CONTRATOS', 'Login bem-sucedido', '2025-04-23 16:52:56'),
(630, 'CONTRATOS', 'Login bem-sucedido', '2025-04-24 23:24:15'),
(631, 'MASTER', 'Login bem-sucedido', '2025-04-25 00:57:29'),
(632, 'MASTER', 'Login bem-sucedido', '2025-04-26 01:16:45'),
(633, 'PAULO', 'Login bem-sucedido', '2025-04-26 01:32:33'),
(634, 'CONTRATOS', 'Login bem-sucedido', '2025-04-26 02:54:06'),
(635, 'MASTER', 'Login bem-sucedido', '2025-04-26 03:41:16'),
(636, 'CONTRATOS', 'Login bem-sucedido', '2025-04-26 03:42:59'),
(637, 'MASTER', 'Login bem-sucedido', '2025-04-27 15:39:05'),
(638, 'CONTRATOS', 'Login bem-sucedido', '2025-04-27 15:41:00'),
(639, 'MASTER', 'Login bem-sucedido', '2025-04-27 15:49:17'),
(640, 'CONTRATOS', 'Login bem-sucedido', '2025-04-27 15:49:36'),
(641, 'CONTRATOS', 'Login bem-sucedido', '2025-05-05 13:44:38'),
(642, 'PAULO', 'Login bem-sucedido', '2025-05-06 16:15:16'),
(643, 'contratos', 'Login falhou: Senha inválida', '2025-05-07 11:53:01'),
(644, 'contratos', 'Login bem-sucedido', '2025-05-07 11:53:11'),
(645, 'PAULO', 'Login bem-sucedido', '2025-05-07 18:13:04'),
(646, 'CONTRATOS', 'Login bem-sucedido', '2025-05-07 18:14:20'),
(647, 'CONTRATOS', 'Login bem-sucedido', '2025-05-07 18:16:42'),
(648, 'CONTRATOS', 'Login bem-sucedido', '2025-05-08 17:06:03'),
(649, 'contratos', 'Login bem-sucedido', '2025-05-09 12:18:14'),
(650, 'CONTRATOS', 'Login bem-sucedido', '2025-05-12 11:21:44'),
(651, 'CONTRATOS', 'Login bem-sucedido', '2025-05-12 16:35:36'),
(652, 'CONTRATOS', 'Login bem-sucedido', '2025-05-12 19:15:10'),
(653, 'CONTRATOS', 'Login bem-sucedido', '2025-05-12 19:50:06'),
(654, 'CONTRATOS', 'Login bem-sucedido', '2025-05-13 19:17:06'),
(655, 'CONTRATOS', 'Login bem-sucedido', '2025-05-13 19:18:10'),
(656, 'CONTRATOS', 'Login bem-sucedido', '2025-05-13 19:19:40'),
(657, 'MASTER', 'Login bem-sucedido', '2025-05-27 12:12:52'),
(658, 'contratos', 'Login bem-sucedido', '2025-05-27 13:29:54'),
(659, 'MASTER', 'Cadastro de Patrimônio', '2025-05-27 13:51:52'),
(660, 'contratos', 'Login bem-sucedido', '2025-05-27 13:59:17'),
(661, 'CLAUDIA', 'Login falhou: Senha inválida', '2025-05-27 16:58:53'),
(662, 'CLAUDIA', 'Login bem-sucedido', '2025-05-27 16:59:01'),
(663, 'MASTER', 'Login bem-sucedido', '2025-05-27 17:15:37'),
(664, 'MASTER', 'Login bem-sucedido', '2025-05-27 17:31:11'),
(665, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-05-27 17:41:11'),
(666, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-05-27 17:46:41'),
(667, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-05-27 17:49:58'),
(668, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-05-27 17:55:40'),
(669, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-05-27 17:59:17'),
(670, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-05-27 18:00:51'),
(671, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-05-27 18:01:07'),
(672, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-05-27 18:06:07'),
(673, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-05-27 18:08:10'),
(674, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-05-27 18:15:06'),
(675, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-05-27 18:15:21'),
(676, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-05-27 18:15:57'),
(677, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-05-27 18:18:13'),
(678, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-05-27 18:19:37'),
(679, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-05-27 18:20:17'),
(680, 'contratos', 'Login bem-sucedido', '2025-05-27 18:53:58'),
(681, 'MASTER', 'Login bem-sucedido', '2025-05-27 18:59:59'),
(682, 'contratos', 'Login bem-sucedido', '2025-05-29 12:21:01'),
(683, 'CLAUDIA', 'Login falhou: Senha inválida', '2025-05-29 13:14:21'),
(684, 'CLAUDIA', 'Login bem-sucedido', '2025-05-29 13:14:29'),
(685, 'PATRICIA', 'Login falhou: Senha inválida', '2025-05-29 13:22:38'),
(686, 'PATRICIA', 'Login bem-sucedido', '2025-05-29 13:22:42'),
(687, 'MASTER', 'Login bem-sucedido', '2025-05-29 13:26:41'),
(688, 'contratos', 'Login falhou: Senha inválida', '2025-05-29 13:33:37'),
(689, 'contratos', 'Login bem-sucedido', '2025-05-29 13:33:44'),
(690, 'MASTER', 'Login bem-sucedido', '2025-05-29 14:24:57'),
(691, 'contratos', 'Login bem-sucedido', '2025-05-29 17:49:22'),
(692, 'MASTER', 'Login falhou: Senha inválida', '2025-05-29 18:26:40'),
(693, '12', 'Login falhou: Usuário não encontrado', '2025-05-29 18:26:49'),
(694, 'contratos', 'Login falhou: Setor incorreto', '2025-05-29 18:28:12'),
(695, 'contratos', 'Login falhou: Setor incorreto', '2025-05-29 18:28:20'),
(696, 'MASTER', 'Login falhou: Senha inválida', '2025-05-29 18:29:51'),
(697, 'MASTER', 'Login falhou: Senha inválida', '2025-05-29 18:29:51'),
(698, 'contratos', 'Login falhou: Setor incorreto', '2025-05-29 18:30:01'),
(699, 'contratos', 'Login falhou: Setor incorreto', '2025-05-29 18:31:44'),
(700, 'contratos', 'Login bem-sucedido', '2025-05-29 18:32:27'),
(701, 'MASTER', 'Login bem-sucedido', '2025-05-29 19:02:11'),
(702, 'MASTER', 'Login bem-sucedido', '2025-05-29 19:33:42'),
(703, 'MASTER', 'Login bem-sucedido', '2025-05-29 19:36:21'),
(704, 'MASTER', 'Login bem-sucedido', '2025-06-02 11:46:50'),
(705, 'contratos', 'Login bem-sucedido', '2025-06-02 11:47:31'),
(706, 'contratos', 'Login bem-sucedido', '2025-06-03 15:57:06'),
(707, 'CONTRATOS', 'Login bem-sucedido', '2025-06-04 12:07:51'),
(708, 'MASTER', 'Login bem-sucedido', '2025-06-04 12:21:00'),
(709, 'contratos@central.rj.gov.br', 'Login falhou: Usuário não encontrado', '2025-06-04 13:13:03'),
(710, 'contratos', 'Login falhou: Senha inválida', '2025-06-04 13:13:18'),
(711, 'contratos@central.rj.gov.br', 'Login falhou: Usuário não encontrado', '2025-06-04 13:13:31'),
(712, 'contratos', 'Login bem-sucedido', '2025-06-04 13:14:14'),
(713, 'MASTER', 'Login bem-sucedido', '2025-06-04 17:22:18'),
(714, 'contratos', 'Login bem-sucedido', '2025-06-04 17:32:24'),
(715, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-04 18:43:49'),
(716, 'camilafirmino@central.rj.gov.br', 'Login falhou: Usuário não encontrado', '2025-06-05 17:19:21'),
(717, 'contratos@central.rj.gov.br', 'Login falhou: Usuário não encontrado', '2025-06-05 17:19:40'),
(718, 'contratos', 'Login bem-sucedido', '2025-06-05 17:20:19'),
(719, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-05 17:48:44'),
(720, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-05 19:06:47'),
(721, 'contratos', 'Login bem-sucedido', '2025-06-05 19:41:03'),
(722, 'PAULO', 'Login bem-sucedido', '2025-06-05 19:42:54'),
(723, 'contratos', 'Login bem-sucedido', '2025-06-06 15:08:20'),
(724, 'MASTER', 'Login bem-sucedido', '2025-06-06 17:54:42'),
(725, 'CLAUDIA', 'Login falhou: Senha inválida', '2025-06-06 17:55:14'),
(726, 'CLAUDIA', 'Login bem-sucedido', '2025-06-06 17:55:24'),
(727, 'contratos', 'Login bem-sucedido', '2025-06-06 17:55:40'),
(728, 'CLAUDIA', 'Login bem-sucedido', '2025-06-06 17:56:24'),
(729, 'paulo', 'Login bem-sucedido', '2025-06-06 17:58:00'),
(730, 'CLAUDIA', 'Login bem-sucedido', '2025-06-06 18:05:05'),
(731, 'MASTER', 'Login bem-sucedido', '2025-06-09 11:55:29'),
(732, 'contratos', 'Login falhou: Senha inválida', '2025-06-09 13:13:17'),
(733, 'contratos', 'Login bem-sucedido', '2025-06-09 13:13:34'),
(734, 'MASTER', 'Transferência de Patrimônio', '2025-06-09 17:00:27'),
(735, 'matricula_usuario_aqui', 'Patrimônio teste12 atualizado', '2025-06-09 17:05:45'),
(736, 'CLAUDIA', 'Login falhou: Senha inválida', '2025-06-09 17:19:08'),
(737, 'CLAUDIA', 'Login bem-sucedido', '2025-06-09 17:19:25'),
(738, 'CLAUDIA', 'Login bem-sucedido', '2025-06-09 17:42:42'),
(739, 'CLAUDIA', 'Patrimônio teste12 atualizado', '2025-06-09 18:26:09'),
(740, 'CLAUDIA', 'Patrimônio teste atualizado', '2025-06-09 18:39:24'),
(741, 'MASTER', 'Login bem-sucedido', '2025-06-09 19:08:18'),
(742, 'MASTER', 'Patrimônio 600428000012477 atualizado', '2025-06-09 19:39:54'),
(743, 'MASTER', 'Patrimônio 600428000012478 atualizado', '2025-06-09 19:41:05'),
(744, 'CLAUDIA', 'Login bem-sucedido', '2025-06-09 19:41:34'),
(745, 'CLAUDIA', 'Patrimônio 600428000012479 atualizado', '2025-06-09 19:41:54'),
(746, 'contratos', 'Login bem-sucedido', '2025-06-10 14:46:40'),
(747, 'MASTER', 'Login bem-sucedido', '2025-06-11 17:34:26'),
(748, 'MASTER', 'Login bem-sucedido', '2025-06-11 18:07:13'),
(749, 'MASTER', 'Cadastro de Patrimônio', '2025-06-11 19:00:42'),
(750, 'MASTER', 'Cadastro de Patrimônio', '2025-06-11 19:03:28'),
(751, 'CLAUDIA', 'Login falhou: Senha inválida', '2025-06-12 12:13:32'),
(752, 'CLAUDIA', 'Login bem-sucedido', '2025-06-12 12:13:45'),
(753, 'GABRIEL', 'Login falhou: Senha inválida', '2025-06-12 12:29:43'),
(754, 'GABRIEL', 'Login falhou: Senha inválida', '2025-06-12 12:29:57'),
(755, 'MASTER', 'Login bem-sucedido', '2025-06-12 12:30:05'),
(756, 'GABRIEL', 'Login bem-sucedido', '2025-06-12 12:31:04'),
(757, 'GABRIEL', 'Cadastro de Patrimônio', '2025-06-12 13:15:01'),
(758, 'GABRIEL', 'Cadastro de Patrimônio', '2025-06-12 13:15:41'),
(759, 'CLAUDIA', 'Login bem-sucedido', '2025-06-12 13:17:56'),
(760, 'CLAUDIA', 'Cadastro de Patrimônio', '2025-06-12 13:20:36'),
(761, 'camilafirmino@central.rj.gov.br', 'Login falhou: Usuário não encontrado', '2025-06-12 14:31:45'),
(762, 'contratos', 'Login bem-sucedido', '2025-06-12 14:32:00'),
(763, 'contratos', 'Login bem-sucedido', '2025-06-12 14:34:14'),
(764, 'contratos', 'Login bem-sucedido', '2025-06-12 14:34:38'),
(765, 'contratos', 'Login bem-sucedido', '2025-06-16 15:39:25'),
(766, 'contratos', 'Login bem-sucedido', '2025-06-16 15:39:50'),
(767, 'MASTER', 'Login bem-sucedido', '2025-06-17 12:46:11'),
(768, '990078', 'foi cadastrado no sistema', '2025-06-17 12:46:59'),
(769, 'bonde', 'Login falhou: Setor incorreto', '2025-06-17 12:47:11'),
(770, 'bonde', 'Login falhou: Setor incorreto', '2025-06-17 12:47:21'),
(771, 'bonde', 'Login bem-sucedido', '2025-06-17 12:49:45'),
(772, 'bonde', 'Login bem-sucedido', '2025-06-17 12:50:11'),
(773, 'bonde', 'Login bem-sucedido', '2025-06-17 13:00:46'),
(774, 'bonde', 'Login bem-sucedido', '2025-06-17 13:02:29'),
(775, 'bonde', 'Login bem-sucedido', '2025-06-17 14:40:31'),
(776, 'BONDE', 'Login bem-sucedido', '2025-06-17 14:41:33'),
(777, 'BONDE', 'Login bem-sucedido', '2025-06-17 14:42:03'),
(778, 'bonde', 'Login bem-sucedido', '2025-06-17 15:00:50'),
(779, 'BONDE', 'Login bem-sucedido', '2025-06-17 15:03:01'),
(780, 'bonde', 'Login bem-sucedido', '2025-06-17 15:05:49'),
(781, 'bonde', 'Login bem-sucedido', '2025-06-17 15:05:56'),
(782, 'bonde', 'Login bem-sucedido', '2025-06-17 15:07:57'),
(783, 'BONDE', 'Login bem-sucedido', '2025-06-17 15:08:37'),
(784, 'bonde', 'Login falhou: Setor incorreto', '2025-06-17 15:09:05'),
(785, 'bonde', 'Login falhou: Setor incorreto', '2025-06-17 15:09:47'),
(786, 'bonde', 'Login falhou: Setor incorreto', '2025-06-17 15:10:03'),
(787, 'bonde', 'Login falhou: Setor incorreto', '2025-06-17 15:10:52'),
(788, 'MASTER', 'Login bem-sucedido', '2025-06-17 15:11:02'),
(789, 'MASTER', 'Login bem-sucedido', '2025-06-17 15:11:22'),
(790, 'bonde', 'Login falhou: Setor incorreto', '2025-06-17 15:11:37'),
(791, 'BONDE', 'Login falhou: Setor incorreto', '2025-06-17 15:11:45'),
(792, 'bonde', 'Login bem-sucedido', '2025-06-17 15:12:46'),
(793, 'bonde', 'Login bem-sucedido', '2025-06-17 15:16:17'),
(794, 'bonde', 'Login bem-sucedido', '2025-06-17 16:58:53'),
(795, 'bonde', 'Login bem-sucedido', '2025-06-17 17:06:27'),
(796, 'MASTER', 'Login bem-sucedido', '2025-06-17 17:17:33'),
(797, 'contr50', 'foi cadastrado no sistema', '2025-06-17 17:21:14'),
(798, 'controle', 'Login bem-sucedido', '2025-06-17 17:21:45'),
(799, 'MASTER', 'Login bem-sucedido', '2025-06-17 18:19:37'),
(800, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-17 18:26:07'),
(801, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-17 18:37:09'),
(802, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-17 18:40:57'),
(803, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-17 18:46:30'),
(804, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-17 18:49:34'),
(805, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-17 19:00:49'),
(806, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-17 19:07:30'),
(807, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-17 19:13:24'),
(808, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-17 19:14:19'),
(809, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-17 19:17:13'),
(810, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-17 19:18:16'),
(811, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-18 12:02:46'),
(812, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-18 12:03:37'),
(813, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-18 12:05:40'),
(814, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-18 12:06:13'),
(815, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-18 12:07:16'),
(816, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-18 12:08:09'),
(817, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-18 12:08:09'),
(818, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-18 12:09:08'),
(819, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-18 12:17:48'),
(820, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-18 12:18:22'),
(821, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-18 12:19:46'),
(822, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-18 12:54:52'),
(823, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-18 12:55:44'),
(824, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-18 12:56:31'),
(825, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-18 13:42:49'),
(826, 'contratos', 'Login bem-sucedido', '2025-06-18 14:32:10'),
(827, 'contratos', 'Login bem-sucedido', '2025-06-18 14:53:43'),
(828, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-18 15:01:46'),
(829, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-18 15:01:50'),
(830, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-18 15:01:57'),
(831, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-18 15:02:01'),
(832, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-18 15:09:13'),
(833, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-18 15:13:54'),
(834, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-18 17:11:25'),
(835, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-18 17:12:42'),
(836, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-18 18:25:09'),
(837, 'MASTER', 'cadastrou ou atualizou o produto no estoque', '2025-06-18 18:37:41'),
(838, 'BONDE', 'Login falhou: Setor incorreto', '2025-06-23 13:22:37'),
(839, 'contratos', 'Login falhou: Senha inválida', '2025-06-23 13:23:07'),
(840, 'contratos', 'Login bem-sucedido', '2025-06-23 13:23:22'),
(841, 'controle', 'Login bem-sucedido', '2025-06-23 13:24:45'),
(842, 'bonde', 'Login bem-sucedido', '2025-06-23 19:05:08'),
(843, 'contratos', 'Login bem-sucedido', '2025-06-24 13:03:29'),
(844, 'MASTER', 'Login bem-sucedido', '2025-06-25 12:11:24'),
(845, 'contratos', 'Login bem-sucedido', '2025-06-25 12:12:57'),
(846, 'master', 'Login falhou: Setor incorreto', '2025-06-25 12:13:04'),
(847, 'master', 'Login falhou: Senha inválida', '2025-06-25 12:13:14'),
(848, 'contratos', 'Login bem-sucedido', '2025-06-25 12:13:24'),
(849, 'BONDE', 'Login bem-sucedido', '2025-06-25 12:17:29'),
(850, 'contratos', 'Login bem-sucedido', '2025-06-25 13:04:04'),
(851, 'contratos', 'Login bem-sucedido', '2025-06-30 13:11:44'),
(852, 'contratos', 'Login bem-sucedido', '2025-06-30 16:56:02'),
(853, 'contratos', 'Login bem-sucedido', '2025-07-01 13:00:03'),
(854, 'contratos', 'Login bem-sucedido', '2025-07-01 19:17:46'),
(855, 'contratos', 'Login bem-sucedido', '2025-07-02 13:21:27'),
(856, 'contratos', 'Login bem-sucedido', '2025-07-03 13:10:33'),
(857, 'planejamento', 'Login falhou: Usuário não encontrado', '2025-07-21 11:56:24'),
(858, 'planejamento', 'Login falhou: Usuário não encontrado', '2025-07-21 11:56:49'),
(859, 'planejamento', 'Login falhou: Setor incorreto', '2025-07-21 12:01:24'),
(860, 'planejamento', 'Login bem-sucedido', '2025-07-21 12:03:48'),
(861, 'planejamento', 'Login bem-sucedido', '2025-07-28 14:36:03'),
(862, 'BONDE', 'Login falhou: Setor incorreto', '2025-08-05 18:56:00'),
(863, 'BONDE', 'Login falhou: Setor incorreto', '2025-08-05 18:56:08'),
(864, 'controle', 'Login bem-sucedido', '2025-08-05 18:56:19'),
(865, 'bonde', 'Login bem-sucedido', '2025-08-05 19:00:57'),
(866, 'planejamento', 'Login bem-sucedido', '2025-08-08 13:51:42'),
(867, 'master', 'Login bem-sucedido', '2025-08-12 13:54:14'),
(868, 'planejamento', 'Login bem-sucedido', '2025-08-12 13:55:16'),
(869, 'contratos', 'Login bem-sucedido', '2025-08-12 23:05:21'),
(870, 'contratos', 'Login bem-sucedido', '2025-08-15 13:26:08'),
(871, 'master', 'Login bem-sucedido', '2025-08-18 12:49:03'),
(872, 'PATRICIA', 'Login falhou: Senha inválida', '2025-08-18 12:56:30'),
(873, 'PAULO', 'Login bem-sucedido', '2025-08-18 12:56:45'),
(874, 'PAULO', 'Login bem-sucedido', '2025-08-18 12:58:15'),
(875, 'master', 'Login bem-sucedido', '2025-08-18 12:59:38'),
(876, 'master', 'Login falhou: Senha inválida', '2025-08-18 13:52:30'),
(877, 'master', 'Login bem-sucedido', '2025-08-18 13:52:42'),
(878, 'contratos', 'Login falhou: Senha inválida', '2025-08-18 14:21:43'),
(879, 'contratos', 'Login bem-sucedido', '2025-08-18 14:22:03'),
(880, 'master', 'Login bem-sucedido', '2025-08-18 17:34:13'),
(881, 'master', 'Login bem-sucedido', '2025-08-21 21:32:29'),
(882, 'contratos', 'Login bem-sucedido', '2025-08-22 16:25:20'),
(883, 'master', 'Login bem-sucedido', '2025-08-22 16:31:59'),
(884, 'master', 'Login bem-sucedido', '2025-08-22 16:50:55'),
(885, 'camilafirmino@central.rj.gov.br', 'Login falhou: Usuário não encontrado', '2025-08-22 17:39:44'),
(886, 'camilafirmino@central.rj.gov.br', 'Login falhou: Usuário não encontrado', '2025-08-22 17:40:52'),
(887, 'contratos', 'Login falhou: Senha inválida', '2025-08-22 17:41:27'),
(888, 'contratos', 'Login falhou: Senha inválida', '2025-08-22 17:41:43'),
(889, 'contratos@central.rj.gov.br', 'Login falhou: Usuário não encontrado', '2025-08-22 17:43:04'),
(890, 'contratos', 'Login bem-sucedido', '2025-08-22 17:43:26'),
(891, 'master', 'Login bem-sucedido', '2025-08-22 18:38:05'),
(892, 'master', 'Login bem-sucedido', '2025-08-25 12:26:41');

-- --------------------------------------------------------

--
-- Estrutura para tabela `macroetapas`
--

CREATE TABLE `macroetapas` (
  `id` bigint(20) NOT NULL,
  `planejamento_id` bigint(20) NOT NULL,
  `setor` varchar(220) NOT NULL,
  `nome_macroetapa` varchar(255) NOT NULL,
  `responsavel` varchar(255) DEFAULT NULL,
  `etapa_nome` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `etapa_concluida` enum('sim','não') DEFAULT 'não',
  `data_conclusao` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `macroetapas`
--

INSERT INTO `macroetapas` (`id`, `planejamento_id`, `setor`, `nome_macroetapa`, `responsavel`, `etapa_nome`, `etapa_concluida`, `data_conclusao`) VALUES
(1, 68, 'operacionalizacao_bonde', 'PLANEJAMENTO PCA', '', 'DFD (Doc.de Formalização de Demanda)', 'sim', '0000-00-00'),
(2, 68, 'operacionalizacao_bonde', 'PLANEJAMENTO PCA', '', 'Sistema PCA (Plano de Contratações Anual)', 'sim', '0000-00-00'),
(3, 38, 'ti', 'Modernização', '', 'Modernização do parque computacional (desktops e notebooks)', 'não', '0000-00-00'),
(4, 68, 'operacionalizacao_bonde', 'Fase Preparatória', '', 'Estudo Técnico Preliminar / Matriz de Risco', 'sim', '0000-00-00'),
(5, 55, 'capacitacao', 'Treinamentos Específicos', '', 'Treinamentos para área de Segurança do Trabalho', 'sim', '0000-00-00'),
(6, 17, 'bondes', 'Operacionalização do Sistema de Bondes', '', 'Reativação de Linhas', 'sim', '0000-00-00'),
(7, 17, 'bondes', 'Recuperação do Sistema de Bondes', '', 'Operação do Bonde', 'sim', '0000-00-00'),
(8, 14, 'bondes', 'Operacionalização do Sistema de Bondes', '', 'Reativação de Linhas', 'sim', '0000-00-00'),
(9, 36, 'ti', 'Modernização', '', 'Modernização do parque computacional (desktops e notebooks)', 'sim', '0000-00-00'),
(10, 25, 'ferrovia', 'Projetos de Engenharia', '', 'Planejamento e Execução', 'sim', '0000-00-00'),
(11, 70, 'bondes', 'FASE PREPARATÓRIA', '', 'Termo de Referência', 'não', '0000-00-00'),
(12, 70, 'bondes', 'PLANEJAMENTO PCA', '', 'Desenvolvimento de novas soluções tecnológicas', 'não', '0000-00-00'),
(13, 21, 'bondes', 'Recuperação do Sistema de Bondes', '', 'Operação do Bonde', 'não', '0000-00-00'),
(14, 70, 'bondes', 'FASE EXTERNA', '', 'Abertura do Pregão / Dispensa de Licitação', 'não', '0000-00-00'),
(15, 70, 'bondes', 'FASE PREPARATÓRIA', '', 'Estudo Técnico Preliminar / Matriz de Risco', 'não', '0000-00-00'),
(16, 70, 'bondes', 'FASE PREPARATÓRIA', '', 'DDO - Declaração de Dotação Orçamentária', 'não', '0000-00-00'),
(17, 68, 'operacionalizacao_bonde', 'Fase Preparatória', '', 'Pesquisa de Preços / Relatório Analítico', 'sim', '0000-00-00'),
(18, 15, 'bondes', 'Operacionalização do Sistema de Bondes', '', 'Reativação de Linhas', 'sim', '0000-00-00'),
(19, 24, 'ferrovia', 'Projetos de Engenharia', '', 'Planejamento e Execução', 'sim', '0000-00-00'),
(20, 34, 'teleferico', 'Reativação e Operacionalização', '', 'Reativação do Sistema de Transporte do Teleférico do Alemão', 'sim', '0000-00-00'),
(21, 35, 'teleferico', 'Reativação e Operacionalização', '', 'Reativação do Sistema de Transporte do Teleférico do Alemão', 'não', '0000-00-00'),
(22, 26, 'ferrovia', 'Projetos de Engenharia', '', 'Planejamento e Execução', 'sim', '0000-00-00'),
(23, 27, 'ferrovia', 'Projetos de Engenharia', '', 'Planejamento e Execução', 'não', '0000-00-00'),
(24, 33, 'ferrovia', 'Projetos de Engenharia', '', 'Planejamento e Execução', 'não', '0000-00-00'),
(25, 44, 'ti', 'Infraestrutura de TI', '', 'Implantação da tecnologia BIM na CENTRAL e capacitar seus usuários', 'sim', '0000-00-00'),
(26, 44, 'ti', 'Infraestrutura de TI', '', 'Aquisição de equipamentos e softwares', 'sim', '0000-00-00'),
(27, 44, 'ti', 'Infraestrutura de TI', '', 'Estruturar o banco de dados dos projetos de engenharia', 'sim', '0000-00-00'),
(28, 44, 'ti', 'Infraestrutura de TI', '', 'Reestruturação de toda Infraestrutura de Rede com Segurança', 'sim', '0000-00-00'),
(29, 44, 'ti', 'Modernização', '', 'Modernização do parque computacional (desktops e notebooks)', 'não', '0000-00-00'),
(30, 44, 'ti', 'Soluções de Atendimento', '', 'Solução de Atendimento ao Usuário – ITSM', 'não', '0000-00-00'),
(31, 44, 'ti', 'Soluções de Atendimento', '', 'Wi-Fi corporativo com segurança e gerenciamento', 'não', '0000-00-00'),
(32, 44, 'ti', 'Monitoramento', '', 'Monitoramento e Observabilidade da Infraestrutura', 'não', '0000-00-00'),
(33, 44, 'ti', 'Conectividade', '', 'Contratação de link de internet dedicado', 'não', '0000-00-00'),
(34, 44, 'ti', 'Conectividade', '', 'Aquisição de softwares diversos', 'não', '0000-00-00'),
(35, 44, 'ti', 'Conectividade', '', 'Serviço de cabeamento estruturado', 'não', '0000-00-00'),
(36, 56, 'capacitacao', 'Treinamentos Específicos', '', 'Treinamentos no Sistema e-Social', 'não', '0000-00-00'),
(37, 56, 'capacitacao', 'Treinamentos Específicos', '', 'Treinamentos para área de Segurança do Trabalho', 'sim', '0000-00-00'),
(38, 56, 'capacitacao', 'Treinamentos Específicos', '', 'Treinamentos visando à Educação Ambiental', 'sim', '0000-00-00'),
(39, 57, 'capacitacao', 'Capacitação Geral', '', 'Capacitação dos empregados da GERGEP nos softwares de edição de texto e planilhas', 'sim', '0000-00-00'),
(40, 58, 'capacitacao', 'Capacitação Geral', '', 'Capacitação dos empregados da GERGEP nos softwares de edição de texto e planilhas', 'sim', '0000-00-00'),
(41, 88, 'solucoes_tecnologicas', 'PLANEJAMENTO PCA', '', 'Desenvolvimento de novas soluções tecnológicas', 'sim', '0000-00-00'),
(42, 89, 'solucoes_tecnologicas', 'PLANEJAMENTO PCA', '', 'Desenvolvimento de novas soluções tecnológicas', 'sim', '0000-00-00'),
(43, 88, 'solucoes_tecnologicas', 'FASE DE CONTRATAÇÃO', '', 'Garantia Contratual', 'sim', '0000-00-00'),
(44, 88, 'solucoes_tecnologicas', 'FASE PREPARATÓRIA', '', 'Pesquisa de Preços / Relatório Analítico', 'sim', '0000-00-00'),
(45, 90, 'solucoes_tecnologicas', 'PLANEJAMENTO PCA', '', 'Desenvolvimento de novas soluções tecnológicas', 'sim', '0000-00-00'),
(46, 90, 'solucoes_tecnologicas', 'FASE DE CONTRATAÇÃO', '', 'NAD / Nota de Empenho', 'sim', '0000-00-00'),
(47, 91, 'ferrovia', 'PLANEJAMENTO PCA', '', 'Desenvolvimento de novas soluções tecnológicas', 'sim', '0000-00-00'),
(48, 91, 'ferrovia', 'FASE PREPARATÓRIA', '', 'Estudo Técnico Preliminar / Matriz de Risco', 'sim', '0000-00-00'),
(49, 88, 'solucoes_tecnologicas', 'FASE EXTERNA', '', 'Homologação e Publicação no D.O', 'sim', '0000-00-00'),
(50, 42, 'ti', 'Conectividade', '', 'Contratação de link de internet dedicado', 'sim', '0000-00-00'),
(51, 42, 'ti', 'Conectividade', '', 'Aquisição de softwares diversos', 'sim', '0000-00-00'),
(52, 42, 'ti', 'Conectividade', '', 'Serviço de cabeamento estruturado', 'sim', '0000-00-00'),
(53, 43, 'ti', 'Modernização', '', 'Modernização do parque computacional (desktops e notebooks)', 'sim', '0000-00-00'),
(54, 42, 'ti', 'Modernização', '', 'Modernização do parque computacional (desktops e notebooks)', 'sim', '0000-00-00'),
(55, 16, 'bondes', 'Recuperação do Sistema de Bondes', '', 'Operação do Bonde', 'sim', '0000-00-00'),
(56, 16, 'bondes', 'Operacionalização do Sistema de Bondes', '', 'Reativação de Linhas', 'sim', '0000-00-00'),
(57, 18, 'bondes', 'Operacionalização do Sistema de Bondes', '', 'Reativação de Linhas', 'sim', '0000-00-00'),
(58, 18, 'bondes', 'Recuperação do Sistema de Bondes', '', 'Operação do Bonde', 'sim', '0000-00-00'),
(59, 19, 'bondes', 'Recuperação do Sistema de Bondes', '', 'Operação do Bonde', 'sim', '0000-00-00'),
(60, 19, 'bondes', 'Operacionalização do Sistema de Bondes', '', 'Reativação de Linhas', 'sim', '0000-00-00'),
(61, 20, 'bondes', 'Operacionalização do Sistema de Bondes', '', 'Reativação de Linhas', 'sim', '0000-00-00'),
(62, 20, 'bondes', 'Recuperação do Sistema de Bondes', '', 'Operação do Bonde', 'sim', '0000-00-00'),
(63, 22, 'bondes', 'Recuperação do Sistema de Bondes', '', 'Operação do Bonde', 'sim', '0000-00-00'),
(64, 23, 'bondes', 'Operacionalização do Sistema de Bondes', '', 'Reativação de Linhas', 'sim', '0000-00-00'),
(65, 29, 'ferrovia', 'Projetos de Engenharia', '', 'Planejamento e Execução', 'sim', '0000-00-00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `manutencoes`
--

CREATE TABLE `manutencoes` (
  `id` int(11) NOT NULL,
  `data` date NOT NULL,
  `tipo` varchar(50) NOT NULL CHECK (`tipo` in ('Preventiva','Realizada')),
  `descricao` text NOT NULL,
  `bonde_afetado` varchar(100) NOT NULL,
  `localidade` varchar(255) DEFAULT NULL,
  `status` varchar(50) NOT NULL CHECK (`status` in ('Pendente','Em Andamento','Concluída')),
  `usuario` varchar(255) NOT NULL,
  `data_registro` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `manutencoes`
--

INSERT INTO `manutencoes` (`id`, `data`, `tipo`, `descricao`, `bonde_afetado`, `localidade`, `status`, `usuario`, `data_registro`) VALUES
(1, '2025-06-23', 'Preventiva', 'teste', 'BO2', 'oficina', 'Pendente', 'controle', '2025-06-23 14:59:27');

-- --------------------------------------------------------

--
-- Estrutura para tabela `materiais`
--

CREATE TABLE `materiais` (
  `codigo` varchar(20) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `classificacao` varchar(255) DEFAULT NULL,
  `natureza` varchar(255) DEFAULT NULL,
  `contabil` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `materiais`
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
-- Estrutura para tabela `notificacoes`
--

CREATE TABLE `notificacoes` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `setor` varchar(255) NOT NULL,
  `mensagem` text NOT NULL,
  `situacao` enum('nao lida','lida') DEFAULT 'nao lida',
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `certidao_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `notificacoes`
--

INSERT INTO `notificacoes` (`id`, `username`, `setor`, `mensagem`, `situacao`, `data_criacao`, `certidao_id`) VALUES
(1, 'MASTER', 'administrador', 'Contrato \'	PRODERJ\' com validade em 2025-06-04 prestes a expirar.', 'lida', '2025-06-04 23:36:18', 0),
(2, 'MASTER', 'administrador', 'Contrato \'CONTRATO DE PRESTAÇÃO DE  SERVIDORES\' com validade em 2025-06-04 prestes a expirar.', 'lida', '2025-06-05 00:17:54', 0),
(3, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"PRODERJ VPS\" em 21/02/2025', '', '2025-06-05 18:04:11', 0),
(4, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"PRODERJ VPS\" em 21/03/2025', '', '2025-06-05 18:04:11', 0),
(5, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"PRODERJ VPS\" em 21/04/2025', '', '2025-06-05 18:04:11', 0),
(6, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"PRODERJ VPS\" em 21/05/2025', '', '2025-06-05 18:04:11', 0),
(7, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"PRODERJ VPS\" em 21/06/2025', '', '2025-06-05 18:04:11', 0),
(8, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"PRODERJ VPS\" em 21/07/2025', '', '2025-06-05 18:04:11', 0),
(9, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"PRODERJ VPS\" em 21/08/2025', '', '2025-06-05 18:04:11', 0),
(10, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"PRODERJ VPS\" em 21/09/2025', '', '2025-06-05 18:04:11', 0),
(11, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"PRODERJ VPS\" em 21/10/2025', '', '2025-06-05 18:04:11', 0),
(12, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"PRODERJ VPS\" em 21/11/2025', '', '2025-06-05 18:04:11', 0),
(13, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"PRODERJ VPS\" em 21/12/2025', '', '2025-06-05 18:04:11', 0),
(14, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"PRODERJ VPS\" em 21/01/2026', '', '2025-06-05 18:04:11', 0),
(15, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"CLARO\" em 03/04/2025', '', '2025-06-12 15:07:42', 0),
(16, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"CLARO\" em 03/05/2025', '', '2025-06-12 15:07:42', 0),
(17, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"CLARO\" em 03/06/2025', '', '2025-06-12 15:07:42', 0),
(18, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"CLARO\" em 03/07/2025', '', '2025-06-12 15:07:42', 0),
(19, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"CLARO\" em 03/08/2025', '', '2025-06-12 15:07:42', 0),
(20, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"CLARO\" em 03/09/2025', '', '2025-06-12 15:07:42', 0),
(21, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"CLARO\" em 03/10/2025', '', '2025-06-12 15:07:42', 0),
(22, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"CLARO\" em 03/11/2025', '', '2025-06-12 15:07:42', 0),
(23, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"CLARO\" em 03/12/2025', '', '2025-06-12 15:07:42', 0),
(24, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"CLARO\" em 03/01/2026', '', '2025-06-12 15:07:42', 0),
(25, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"CLARO\" em 03/02/2026', '', '2025-06-12 15:07:42', 0),
(26, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"CLARO\" em 03/03/2026', '', '2025-06-12 15:07:42', 0),
(27, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"CLARO\" em 03/04/2026', '', '2025-06-12 15:07:42', 0),
(28, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"CLARO\" em 03/05/2026', '', '2025-06-12 15:07:42', 0),
(29, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"CLARO\" em 03/06/2026', '', '2025-06-12 15:07:42', 0),
(30, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"CLARO\" em 03/07/2026', '', '2025-06-12 15:07:42', 0),
(31, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"CLARO\" em 03/08/2026', '', '2025-06-12 15:07:42', 0),
(32, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"CLARO\" em 03/09/2026', '', '2025-06-12 15:07:42', 0),
(33, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"CLARO\" em 03/10/2026', '', '2025-06-12 15:07:42', 0),
(34, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"CLARO\" em 03/11/2026', '', '2025-06-12 15:07:42', 0),
(35, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"CLARO\" em 03/12/2026', '', '2025-06-12 15:07:42', 0),
(36, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"CLARO\" em 03/01/2027', '', '2025-06-12 15:07:42', 0),
(37, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"CLARO\" em 03/02/2027', '', '2025-06-12 15:07:42', 0),
(38, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"OI S/A\" em 03/04/2025', '', '2025-06-12 15:52:11', 0),
(39, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"OI S/A\" em 03/05/2025', '', '2025-06-12 15:52:11', 0),
(40, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"OI S/A\" em 03/06/2025', '', '2025-06-12 15:52:11', 0),
(41, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"OI S/A\" em 03/07/2025', '', '2025-06-12 15:52:11', 0),
(42, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"OI S/A\" em 03/08/2025', '', '2025-06-12 15:52:11', 0),
(43, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"OI S/A\" em 03/09/2025', '', '2025-06-12 15:52:11', 0),
(44, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"OI S/A\" em 03/10/2025', '', '2025-06-12 15:52:11', 0),
(45, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"OI S/A\" em 03/11/2025', '', '2025-06-12 15:52:11', 0),
(46, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"OI S/A\" em 03/12/2025', '', '2025-06-12 15:52:11', 0),
(47, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"OI S/A\" em 03/01/2026', '', '2025-06-12 15:52:11', 0),
(48, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"OI S/A\" em 03/02/2026', '', '2025-06-12 15:52:11', 0),
(49, 'contratos', 'contratos', 'Lembrete: Vencimento da parcela do contrato \"OI S/A\" em 03/03/2026', '', '2025-06-12 15:52:11', 0),
(50, 'Gabriel', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-17 16:41:00', 0),
(51, 'N/A', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-21 15:43:53', 0),
(52, 'N/A', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-21 15:43:55', 0),
(53, 'N/A', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-21 15:44:01', 0),
(54, 'N/A', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-21 15:44:05', 0),
(55, 'N/A', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-21 15:44:32', 0),
(56, 'WA', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-21 15:44:49', 0),
(57, 'WA', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-21 15:44:49', 0),
(58, 'WA', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-21 15:44:50', 0),
(59, 'WA', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-21 16:08:26', 0),
(60, 'N/A', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-17 16:41:00', 0),
(61, 'Gabriel', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-17 16:41:00', 0),
(62, 'Gabriel', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-17 16:41:00', 0),
(63, 'N/A', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-17 16:41:00', 0),
(64, 'N/A', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-17 16:41:00', 0),
(65, 'N/A', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-17 16:41:00', 0),
(66, 'N/A', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-17 16:41:00', 0),
(67, 'N/A', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-17 16:41:00', 0),
(68, 'N/A', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-17 16:41:00', 0),
(69, 'Gabriel', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-17 16:41:00', 0),
(70, 'Gabriel', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-17 16:41:00', 0),
(71, '23', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-17 16:41:00', 0),
(72, 'N/A', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-17 16:41:00', 0),
(73, 'N/A', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-17 16:41:00', 0),
(74, 'N/A', 'bondes', 'Reativação de Linhas', 'nao lida', '2025-07-17 16:41:00', 0),
(75, 'N/A', 'bondes', 'Operação do Bonde', 'nao lida', '2025-07-17 16:41:00', 0),
(76, 'N/A', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-21 17:55:23', 0),
(77, 'N/A', 'bondes', 'Operação do Bonde', 'nao lida', '2025-07-21 18:14:11', 0),
(78, 'N/A', 'bondes', 'Reativação de Linhas', 'nao lida', '2025-07-21 18:14:11', 0),
(79, 'N/A', 'bondes', 'Reativação de Linhas', 'nao lida', '2025-07-21 18:28:47', 0),
(80, 'N/A', 'bondes', 'Operação do Bonde', 'nao lida', '2025-07-21 18:28:47', 0),
(81, 'N/A', 'bondes', 'Reativação de Linhas', 'nao lida', '2025-07-21 18:41:53', 0),
(82, 'N/A', 'bondes', 'Operação do Bonde', 'nao lida', '2025-07-21 18:41:53', 0),
(83, 'N/A', 'bondes', 'Operação do Bonde', 'nao lida', '2025-07-21 18:51:20', 0),
(84, 'N/A', 'bondes', 'Reativação de Linhas', 'nao lida', '2025-07-21 18:51:20', 0),
(85, 'N/A', 'bondes', 'Operação do Bonde', 'nao lida', '2025-07-21 18:52:29', 0),
(86, 'N/A', 'bondes', 'Reativação de Linhas', 'nao lida', '2025-07-21 18:52:29', 0),
(87, 'N/A', 'bondes', 'Operação do Bonde', 'nao lida', '2025-07-21 18:52:32', 0),
(88, 'N/A', 'bondes', 'Reativação de Linhas', 'nao lida', '2025-07-21 18:52:32', 0),
(89, 'N/A', 'bondes', 'Operação do Bonde', 'nao lida', '2025-07-21 18:52:54', 0),
(90, 'N/A', 'bondes', 'Reativação de Linhas', 'nao lida', '2025-07-21 18:52:54', 0),
(91, 'N/A', 'bondes', 'Operação do Bonde', 'nao lida', '2025-07-21 18:59:36', 0),
(92, 'N/A', 'bondes', 'Reativação de Linhas', 'nao lida', '2025-07-21 18:59:36', 0),
(93, 'N/A', 'bondes', 'Operação do Bonde', 'nao lida', '2025-07-21 19:00:09', 0),
(94, 'N/A', 'bondes', 'Reativação de Linhas', 'nao lida', '2025-07-21 19:00:09', 0),
(95, 'N/A', 'bondes', 'Operação do Bonde', 'nao lida', '2025-07-21 19:01:36', 0),
(96, 'N/A', 'bondes', 'Reativação de Linhas', 'nao lida', '2025-07-21 19:01:36', 0),
(97, 'N/A', 'bondes', 'Reativação de Linhas', 'nao lida', '2025-07-21 19:05:00', 0),
(98, 'N/A', 'bondes', 'Operação do Bonde', 'nao lida', '2025-07-21 19:05:00', 0),
(99, 'N/A', 'bondes', 'Reativação de Linhas', 'nao lida', '2025-07-21 19:05:00', 0),
(100, 'N/A', 'bondes', 'Operação do Bonde', 'nao lida', '2025-07-21 19:05:00', 0),
(101, 'N/A', 'bondes', 'Reativação de Linhas', 'nao lida', '2025-07-21 19:05:22', 0),
(102, 'N/A', 'bondes', 'Operação do Bonde', 'nao lida', '2025-07-21 19:05:22', 0),
(103, 'N/A', 'bondes', 'Operação do Bonde', 'nao lida', '2025-07-21 19:05:34', 0),
(104, 'N/A', 'bondes', 'Reativação de Linhas', 'nao lida', '2025-07-21 19:05:34', 0),
(105, 'N/A', 'bondes', 'Operação do Bonde', 'nao lida', '2025-07-21 19:05:53', 0),
(106, 'N/A', 'bondes', 'Reativação de Linhas', 'nao lida', '2025-07-21 19:05:53', 0),
(107, 'N/A', 'bondes', 'Operação do Bonde', 'nao lida', '2025-07-21 19:05:55', 0),
(108, 'N/A', 'bondes', 'Reativação de Linhas', 'nao lida', '2025-07-21 19:05:55', 0),
(109, 'N/A', 'bondes', 'Operação do Bonde', 'nao lida', '2025-07-21 19:06:49', 0),
(110, 'N/A', 'bondes', 'Reativação de Linhas', 'nao lida', '2025-07-21 19:06:49', 0),
(111, 'N/A', 'bondes', 'Reativação de Linhas', 'nao lida', '2025-07-21 19:07:55', 0),
(112, 'N/A', 'bondes', 'Operação do Bonde', 'nao lida', '2025-07-21 19:07:55', 0),
(113, 'N/A', 'bondes', 'Operação do Bonde', 'nao lida', '2025-07-21 19:10:43', 0),
(114, 'N/A', 'bondes', 'Reativação de Linhas', 'nao lida', '2025-07-21 19:10:43', 0),
(115, 'N/A', 'bondes', 'Operação do Bonde', 'nao lida', '2025-07-21 19:11:31', 0),
(116, 'N/A', 'bondes', 'Reativação de Linhas', 'nao lida', '2025-07-21 19:11:31', 0),
(117, 'N/A', 'bondes', 'Reativação de Linhas', 'nao lida', '2025-07-21 19:11:34', 0),
(118, 'N/A', 'bondes', 'Operação do Bonde', 'nao lida', '2025-07-21 19:11:34', 0),
(119, 'N/A', 'bondes', 'Reativação de Linhas', 'nao lida', '2025-07-21 19:11:58', 0),
(120, 'N/A', 'bondes', 'Operação do Bonde', 'nao lida', '2025-07-21 19:11:58', 0),
(121, 'N/A', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-21 19:12:50', 0),
(122, 'N/A', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-21 19:20:10', 0),
(123, 'y', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-21 19:20:48', 0),
(124, 'N/A', 'teleferico', 'Reativação do Sistema de Transporte do Teleférico do Alemão', 'nao lida', '2025-07-21 19:24:40', 0),
(125, 'N/A', 'teleferico', 'Reativação do Sistema de Transporte do Teleférico do Alemão', 'nao lida', '2025-07-21 19:39:58', 0),
(126, 'N/A', 'ti', 'Aquisição de equipamentos e softwares', 'nao lida', '2025-07-21 19:47:25', 0),
(127, 'N/A', 'ti', 'Reestruturação de toda Infraestrutura de Rede com Segurança', 'nao lida', '2025-07-21 19:47:25', 0),
(128, 'N/A', 'ti', 'Solução de Atendimento ao Usuário – ITSM', 'nao lida', '2025-07-21 19:47:25', 0),
(129, 'N/A', 'ti', 'Wi-Fi corporativo com segurança e gerenciamento', 'nao lida', '2025-07-21 19:47:25', 0),
(130, 'N/A', 'ti', 'Implantação da tecnologia BIM na CENTRAL e capacitar seus usuários', 'nao lida', '2025-07-21 19:47:25', 0),
(131, 'N/A', 'ti', 'Estruturar o banco de dados dos projetos de engenharia', 'nao lida', '2025-07-21 19:47:25', 0),
(132, 'N/A', 'ti', 'Monitoramento e Observabilidade da Infraestrutura', 'nao lida', '2025-07-21 19:47:25', 0),
(133, 'N/A', 'ti', 'Modernização do parque computacional (desktops e notebooks)', 'nao lida', '2025-07-21 19:47:25', 0),
(134, 'N/A', 'ti', 'Aquisição de softwares diversos', 'nao lida', '2025-07-21 19:47:25', 0),
(135, 'N/A', 'ti', 'Serviço de cabeamento estruturado', 'nao lida', '2025-07-21 19:47:25', 0),
(136, 'N/A', 'ti', 'Contratação de link de internet dedicado', 'nao lida', '2025-07-21 19:47:25', 0),
(137, 'N/A', 'patrimonio', 'Tratamento e digitalização de documentação administrativa e histórica', 'nao lida', '2025-07-21 19:52:36', 0),
(138, 'N/A', 'patrimonio', 'Controle de todos os bens móveis e imóveis', 'nao lida', '2025-07-21 19:52:36', 0),
(139, 'N/A', 'patrimonio', 'Identificação dos bens que integram o acervo patrimonial', 'nao lida', '2025-07-21 19:52:36', 0),
(140, 'N/A', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-21 20:45:35', 0),
(141, 'N/A', 'ti', 'Implantação da tecnologia BIM na CENTRAL e capacitar seus usuários', 'nao lida', '2025-07-21 22:05:08', 0),
(142, 'N/A', 'ti', 'Reestruturação de toda Infraestrutura de Rede com Segurança', 'nao lida', '2025-07-21 22:05:08', 0),
(143, 'N/A', 'ti', 'Estruturar o banco de dados dos projetos de engenharia', 'nao lida', '2025-07-21 22:05:08', 0),
(144, 'N/A', 'ti', 'Aquisição de equipamentos e softwares', 'nao lida', '2025-07-21 22:05:08', 0),
(145, 'N/A', 'ti', 'Modernização do parque computacional (desktops e notebooks)', 'nao lida', '2025-07-21 22:05:08', 0),
(146, 'N/A', 'ti', 'Contratação de link de internet dedicado', 'nao lida', '2025-07-21 22:05:08', 0),
(147, 'N/A', 'ti', 'Aquisição de softwares diversos', 'nao lida', '2025-07-21 22:05:08', 0),
(148, 'N/A', 'ti', 'Monitoramento e Observabilidade da Infraestrutura', 'nao lida', '2025-07-21 22:05:08', 0),
(149, 'N/A', 'ti', 'Solução de Atendimento ao Usuário – ITSM', 'nao lida', '2025-07-21 22:05:08', 0),
(150, 'N/A', 'ti', 'Wi-Fi corporativo com segurança e gerenciamento', 'nao lida', '2025-07-21 22:05:08', 0),
(151, 'N/A', 'ti', 'Serviço de cabeamento estruturado', 'nao lida', '2025-07-21 22:05:08', 0),
(152, 'N/A', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-21 22:07:50', 0),
(153, 'N/A', 'teleferico', 'Reativação do Sistema de Transporte do Teleférico do Alemão', 'nao lida', '2025-07-21 22:16:42', 0),
(154, 'N/A', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-23 18:06:10', 0),
(155, 'N/A', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-23 18:06:22', 0),
(156, 'N/A', 'bondes', 'DFD (Doc.de Formalização de Demanda)', 'nao lida', '2025-07-23 19:27:58', 0),
(157, 'N/A', 'bondes', 'Operação do Bonde', 'nao lida', '2025-07-23 19:27:58', 0),
(158, 'N/A', 'bondes', 'Operação do Bonde', 'nao lida', '2025-07-23 19:28:32', 0),
(159, 'N/A', 'bondes', 'Reativação de Linhas', 'nao lida', '2025-07-23 19:28:32', 0),
(160, 'N/A', 'bondes', 'Operação do Bonde', 'nao lida', '2025-07-23 19:28:57', 0),
(161, 'N/A', 'bondes', 'Reativação de Linhas', 'nao lida', '2025-07-23 19:28:57', 0),
(162, 'N/A', 'bondes', 'Operação do Bonde', 'nao lida', '2025-07-23 19:29:31', 0),
(163, 'N/A', 'bondes', 'Reativação de Linhas', 'nao lida', '2025-07-23 19:29:31', 0),
(164, 'N/A', 'bondes', 'Operação do Bonde', 'nao lida', '2025-07-23 19:32:07', 0),
(165, 'N/A', 'bondes', 'Reativação de Linhas', 'nao lida', '2025-07-23 19:32:07', 0),
(166, 'N/A', 'bondes', 'Operação do Bonde', 'nao lida', '2025-07-23 21:18:40', 0),
(167, 'N/A', 'bondes', 'Reativação de Linhas', 'nao lida', '2025-07-23 21:18:40', 0),
(168, 'N/A', 'bondes', 'Reativação de Linhas', 'nao lida', '2025-07-23 21:19:11', 0),
(169, 'N/A', 'bondes', 'Reativação de Linhas', 'nao lida', '2025-07-23 21:19:11', 0),
(170, 'N/A', 'bondes', 'Operação do Bonde', 'nao lida', '2025-07-23 21:19:11', 0),
(171, 'N/A', 'bondes', 'Operação do Bonde', 'nao lida', '2025-07-23 21:19:11', 0),
(172, 'N/A', 'bondes', 'Operação do Bonde', 'nao lida', '2025-07-23 21:19:28', 0),
(173, 'N/A', 'bondes', 'Reativação de Linhas', 'nao lida', '2025-07-23 21:19:28', 0),
(174, 'N/A', 'bondes', 'Reativação de Linhas', 'nao lida', '2025-07-23 21:19:46', 0),
(175, 'N/A', 'bondes', 'Operação do Bonde', 'nao lida', '2025-07-23 21:19:46', 0),
(176, 'N/A', 'bondes', 'Reativação de Linhas', 'nao lida', '2025-07-23 21:20:01', 0),
(177, 'N/A', 'bondes', 'Operação do Bonde', 'nao lida', '2025-07-23 21:20:01', 0),
(178, 'N/A', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-23 21:20:29', 0),
(179, 'N/A', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-23 21:20:43', 0),
(180, 'N/A', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-23 21:20:59', 0),
(181, 'N/A', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-23 21:21:14', 0),
(182, 'N/A', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-23 21:21:27', 0),
(183, 'N/A', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-23 21:21:47', 0),
(184, 'N/A', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-23 21:22:01', 0),
(185, 'N/A', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-23 21:26:01', 0),
(186, 'N/A', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-23 21:26:42', 0),
(187, 'N/A', 'ferrovia', 'Planejamento e Execução', 'nao lida', '2025-07-23 21:26:58', 0),
(188, 'N/A', 'teleferico', 'Reativação do Sistema de Transporte do Teleférico do Alemão', 'nao lida', '2025-07-23 21:27:23', 0),
(189, 'N/A', 'teleferico', 'Reativação do Sistema de Transporte do Teleférico do Alemão', 'nao lida', '2025-07-23 21:27:41', 0),
(190, 'N/A', 'ti', 'Estruturar o banco de dados dos projetos de engenharia', 'nao lida', '2025-07-23 21:28:23', 0),
(191, 'N/A', 'ti', 'Wi-Fi corporativo com segurança e gerenciamento', 'nao lida', '2025-07-23 21:28:23', 0),
(192, 'N/A', 'ti', 'Modernização do parque computacional (desktops e notebooks)', 'nao lida', '2025-07-23 21:28:23', 0),
(193, 'N/A', 'ti', 'Aquisição de equipamentos e softwares', 'nao lida', '2025-07-23 21:28:23', 0),
(194, 'N/A', 'ti', 'Reestruturação de toda Infraestrutura de Rede com Segurança', 'nao lida', '2025-07-23 21:28:23', 0),
(195, 'N/A', 'ti', 'Implantação da tecnologia BIM na CENTRAL e capacitar seus usuários', 'nao lida', '2025-07-23 21:28:23', 0),
(196, 'N/A', 'ti', 'Monitoramento e Observabilidade da Infraestrutura', 'nao lida', '2025-07-23 21:28:23', 0),
(197, 'N/A', 'ti', 'Contratação de link de internet dedicado', 'nao lida', '2025-07-23 21:28:23', 0),
(198, 'N/A', 'ti', 'Aquisição de softwares diversos', 'nao lida', '2025-07-23 21:28:23', 0),
(199, 'N/A', 'ti', 'Serviço de cabeamento estruturado', 'nao lida', '2025-07-23 21:28:23', 0),
(200, 'N/A', 'ti', 'Solução de Atendimento ao Usuário – ITSM', 'nao lida', '2025-07-23 21:28:23', 0),
(201, 'N/A', 'ti', 'Modernização do parque computacional (desktops e notebooks)', 'nao lida', '2025-07-23 21:28:56', 0),
(202, 'N/A', 'ti', 'Modernização do parque computacional (desktops e notebooks)', 'nao lida', '2025-07-23 21:29:23', 0),
(203, 'N/A', 'ti', 'Modernização do parque computacional (desktops e notebooks)', 'nao lida', '2025-07-23 21:29:48', 0),
(204, 'N/A', 'ti', 'Monitoramento e Observabilidade da Infraestrutura', 'nao lida', '2025-07-23 21:31:08', 0),
(205, 'N/A', 'ti', 'Solução de Atendimento ao Usuário – ITSM', 'nao lida', '2025-07-23 21:31:08', 0),
(206, 'N/A', 'ti', 'Wi-Fi corporativo com segurança e gerenciamento', 'nao lida', '2025-07-23 21:31:08', 0),
(207, 'N/A', 'ti', 'Modernização do parque computacional (desktops e notebooks)', 'nao lida', '2025-07-23 21:31:40', 0),
(208, 'N/A', 'ti', 'Modernização do parque computacional (desktops e notebooks)', 'nao lida', '2025-07-23 21:32:20', 0),
(209, 'N/A', 'ti', 'Contratação de link de internet dedicado', 'nao lida', '2025-07-23 21:32:20', 0),
(210, 'N/A', 'ti', 'Serviço de cabeamento estruturado', 'nao lida', '2025-07-23 21:32:20', 0),
(211, 'N/A', 'ti', 'Aquisição de softwares diversos', 'nao lida', '2025-07-23 21:32:20', 0),
(212, 'N/A', 'ti', 'Aquisição de equipamentos e softwares', 'nao lida', '2025-07-23 21:32:42', 0),
(213, 'N/A', 'ti', 'Solução de Atendimento ao Usuário – ITSM', 'nao lida', '2025-07-23 21:32:42', 0),
(214, 'N/A', 'ti', 'Monitoramento e Observabilidade da Infraestrutura', 'nao lida', '2025-07-23 21:32:42', 0),
(215, 'N/A', 'ti', 'Wi-Fi corporativo com segurança e gerenciamento', 'nao lida', '2025-07-23 21:32:42', 0),
(216, 'N/A', 'ti', 'Contratação de link de internet dedicado', 'nao lida', '2025-07-23 21:32:42', 0),
(217, 'N/A', 'ti', 'Aquisição de softwares diversos', 'nao lida', '2025-07-23 21:32:42', 0),
(218, 'N/A', 'ti', 'Implantação da tecnologia BIM na CENTRAL e capacitar seus usuários', 'nao lida', '2025-07-23 21:32:42', 0),
(219, 'N/A', 'ti', 'Serviço de cabeamento estruturado', 'nao lida', '2025-07-23 21:32:42', 0),
(220, 'N/A', 'ti', 'Estruturar o banco de dados dos projetos de engenharia', 'nao lida', '2025-07-23 21:32:42', 0),
(221, 'N/A', 'ti', 'Reestruturação de toda Infraestrutura de Rede com Segurança', 'nao lida', '2025-07-23 21:32:42', 0),
(222, 'N/A', 'ti', 'Modernização do parque computacional (desktops e notebooks)', 'nao lida', '2025-07-23 21:32:42', 0),
(223, 'N/A', 'ti', 'Estruturar o banco de dados dos projetos de engenharia', 'nao lida', '2025-07-23 21:33:09', 0),
(224, 'N/A', 'ti', 'Aquisição de equipamentos e softwares', 'nao lida', '2025-07-23 21:33:09', 0),
(225, 'N/A', 'ti', 'Reestruturação de toda Infraestrutura de Rede com Segurança', 'nao lida', '2025-07-23 21:33:09', 0),
(226, 'N/A', 'ti', 'Implantação da tecnologia BIM na CENTRAL e capacitar seus usuários', 'nao lida', '2025-07-23 21:33:09', 0),
(227, 'N/A', 'ti', 'Modernização do parque computacional (desktops e notebooks)', 'nao lida', '2025-07-23 21:33:09', 0),
(228, 'N/A', 'ti', 'Aquisição de softwares diversos', 'nao lida', '2025-07-23 21:33:09', 0),
(229, 'N/A', 'ti', 'Contratação de link de internet dedicado', 'nao lida', '2025-07-23 21:33:09', 0),
(230, 'N/A', 'ti', 'Monitoramento e Observabilidade da Infraestrutura', 'nao lida', '2025-07-23 21:33:09', 0),
(231, 'N/A', 'ti', 'Solução de Atendimento ao Usuário – ITSM', 'nao lida', '2025-07-23 21:33:09', 0),
(232, 'N/A', 'ti', 'Serviço de cabeamento estruturado', 'nao lida', '2025-07-23 21:33:09', 0),
(233, 'N/A', 'ti', 'Wi-Fi corporativo com segurança e gerenciamento', 'nao lida', '2025-07-23 21:33:09', 0),
(234, 'N/A', 'ti', 'Estruturar o banco de dados dos projetos de engenharia', 'nao lida', '2025-07-23 21:33:09', 0),
(235, 'N/A', 'ti', 'Aquisição de equipamentos e softwares', 'nao lida', '2025-07-23 21:33:09', 0),
(236, 'N/A', 'ti', 'Implantação da tecnologia BIM na CENTRAL e capacitar seus usuários', 'nao lida', '2025-07-23 21:33:09', 0),
(237, 'N/A', 'ti', 'Reestruturação de toda Infraestrutura de Rede com Segurança', 'nao lida', '2025-07-23 21:33:09', 0),
(238, 'N/A', 'ti', 'Wi-Fi corporativo com segurança e gerenciamento', 'nao lida', '2025-07-23 21:33:09', 0),
(239, 'N/A', 'ti', 'Modernização do parque computacional (desktops e notebooks)', 'nao lida', '2025-07-23 21:33:09', 0),
(240, 'N/A', 'ti', 'Aquisição de softwares diversos', 'nao lida', '2025-07-23 21:33:09', 0),
(241, 'N/A', 'ti', 'Contratação de link de internet dedicado', 'nao lida', '2025-07-23 21:33:09', 0),
(242, 'N/A', 'ti', 'Serviço de cabeamento estruturado', 'nao lida', '2025-07-23 21:33:09', 0),
(243, 'N/A', 'ti', 'Monitoramento e Observabilidade da Infraestrutura', 'nao lida', '2025-07-23 21:33:09', 0),
(244, 'N/A', 'ti', 'Solução de Atendimento ao Usuário – ITSM', 'nao lida', '2025-07-23 21:33:09', 0),
(245, 'N/A', 'ti', 'Aquisição de equipamentos e softwares', 'nao lida', '2025-07-23 21:33:35', 0),
(246, 'N/A', 'ti', 'Estruturar o banco de dados dos projetos de engenharia', 'nao lida', '2025-07-23 21:33:35', 0),
(247, 'N/A', 'ti', 'Reestruturação de toda Infraestrutura de Rede com Segurança', 'nao lida', '2025-07-23 21:33:35', 0),
(248, 'N/A', 'ti', 'Wi-Fi corporativo com segurança e gerenciamento', 'nao lida', '2025-07-23 21:33:35', 0),
(249, 'N/A', 'ti', 'Monitoramento e Observabilidade da Infraestrutura', 'nao lida', '2025-07-23 21:33:35', 0),
(250, 'N/A', 'ti', 'Contratação de link de internet dedicado', 'nao lida', '2025-07-23 21:33:35', 0),
(251, 'N/A', 'ti', 'Aquisição de softwares diversos', 'nao lida', '2025-07-23 21:33:35', 0),
(252, 'N/A', 'ti', 'Serviço de cabeamento estruturado', 'nao lida', '2025-07-23 21:33:35', 0),
(253, 'N/A', 'ti', 'Implantação da tecnologia BIM na CENTRAL e capacitar seus usuários', 'nao lida', '2025-07-23 21:33:35', 0),
(254, 'N/A', 'ti', 'Modernização do parque computacional (desktops e notebooks)', 'nao lida', '2025-07-23 21:33:35', 0),
(255, 'N/A', 'ti', 'Solução de Atendimento ao Usuário – ITSM', 'nao lida', '2025-07-23 21:33:35', 0),
(256, 'N/A', 'ti', 'Reestruturação de toda Infraestrutura de Rede com Segurança', 'nao lida', '2025-07-23 21:33:47', 0),
(257, 'N/A', 'ti', 'Wi-Fi corporativo com segurança e gerenciamento', 'nao lida', '2025-07-23 21:33:47', 0),
(258, 'N/A', 'ti', 'Monitoramento e Observabilidade da Infraestrutura', 'nao lida', '2025-07-23 21:33:47', 0),
(259, 'N/A', 'ti', 'Aquisição de equipamentos e softwares', 'nao lida', '2025-07-23 21:33:47', 0),
(260, 'N/A', 'ti', 'Contratação de link de internet dedicado', 'nao lida', '2025-07-23 21:33:47', 0),
(261, 'N/A', 'ti', 'Solução de Atendimento ao Usuário – ITSM', 'nao lida', '2025-07-23 21:33:47', 0),
(262, 'N/A', 'ti', 'Implantação da tecnologia BIM na CENTRAL e capacitar seus usuários', 'nao lida', '2025-07-23 21:33:47', 0),
(263, 'N/A', 'ti', 'Aquisição de softwares diversos', 'nao lida', '2025-07-23 21:33:47', 0),
(264, 'N/A', 'ti', 'Serviço de cabeamento estruturado', 'nao lida', '2025-07-23 21:33:47', 0),
(265, 'N/A', 'ti', 'Estruturar o banco de dados dos projetos de engenharia', 'nao lida', '2025-07-23 21:33:47', 0),
(266, 'N/A', 'ti', 'Modernização do parque computacional (desktops e notebooks)', 'nao lida', '2025-07-23 21:33:47', 0),
(267, 'N/A', 'ti', 'Aquisição de equipamentos e softwares', 'nao lida', '2025-07-23 21:34:03', 0),
(268, 'N/A', 'ti', 'Wi-Fi corporativo com segurança e gerenciamento', 'nao lida', '2025-07-23 21:34:03', 0),
(269, 'N/A', 'ti', 'Modernização do parque computacional (desktops e notebooks)', 'nao lida', '2025-07-23 21:34:03', 0),
(270, 'N/A', 'ti', 'Implantação da tecnologia BIM na CENTRAL e capacitar seus usuários', 'nao lida', '2025-07-23 21:34:03', 0),
(271, 'N/A', 'ti', 'Reestruturação de toda Infraestrutura de Rede com Segurança', 'nao lida', '2025-07-23 21:34:03', 0),
(272, 'N/A', 'ti', 'Monitoramento e Observabilidade da Infraestrutura', 'nao lida', '2025-07-23 21:34:03', 0),
(273, 'N/A', 'ti', 'Contratação de link de internet dedicado', 'nao lida', '2025-07-23 21:34:03', 0),
(274, 'N/A', 'ti', 'Aquisição de softwares diversos', 'nao lida', '2025-07-23 21:34:03', 0),
(275, 'N/A', 'ti', 'Serviço de cabeamento estruturado', 'nao lida', '2025-07-23 21:34:03', 0),
(276, 'N/A', 'ti', 'Solução de Atendimento ao Usuário – ITSM', 'nao lida', '2025-07-23 21:34:03', 0),
(277, 'N/A', 'ti', 'Estruturar o banco de dados dos projetos de engenharia', 'nao lida', '2025-07-23 21:34:03', 0),
(278, 'N/A', 'ti', 'Estruturar o banco de dados dos projetos de engenharia', 'nao lida', '2025-07-23 21:34:20', 0),
(279, 'N/A', 'ti', 'Reestruturação de toda Infraestrutura de Rede com Segurança', 'nao lida', '2025-07-23 21:34:20', 0),
(280, 'N/A', 'ti', 'Aquisição de equipamentos e softwares', 'nao lida', '2025-07-23 21:34:20', 0),
(281, 'N/A', 'ti', 'Implantação da tecnologia BIM na CENTRAL e capacitar seus usuários', 'nao lida', '2025-07-23 21:34:20', 0),
(282, 'N/A', 'ti', 'Solução de Atendimento ao Usuário – ITSM', 'nao lida', '2025-07-23 21:34:20', 0),
(283, 'N/A', 'ti', 'Modernização do parque computacional (desktops e notebooks)', 'nao lida', '2025-07-23 21:34:20', 0),
(284, 'N/A', 'ti', 'Wi-Fi corporativo com segurança e gerenciamento', 'nao lida', '2025-07-23 21:34:20', 0),
(285, 'N/A', 'ti', 'Contratação de link de internet dedicado', 'nao lida', '2025-07-23 21:34:20', 0),
(286, 'N/A', 'ti', 'Monitoramento e Observabilidade da Infraestrutura', 'nao lida', '2025-07-23 21:34:20', 0),
(287, 'N/A', 'ti', 'Aquisição de softwares diversos', 'nao lida', '2025-07-23 21:34:20', 0),
(288, 'N/A', 'ti', 'Serviço de cabeamento estruturado', 'nao lida', '2025-07-23 21:34:20', 0),
(289, 'N/A', 'ti', 'Estruturar o banco de dados dos projetos de engenharia', 'nao lida', '2025-07-23 21:34:42', 0),
(290, 'N/A', 'ti', 'Reestruturação de toda Infraestrutura de Rede com Segurança', 'nao lida', '2025-07-23 21:34:42', 0),
(291, 'N/A', 'ti', 'Aquisição de equipamentos e softwares', 'nao lida', '2025-07-23 21:34:42', 0),
(292, 'N/A', 'ti', 'Monitoramento e Observabilidade da Infraestrutura', 'nao lida', '2025-07-23 21:34:42', 0),
(293, 'N/A', 'ti', 'Modernização do parque computacional (desktops e notebooks)', 'nao lida', '2025-07-23 21:34:42', 0),
(294, 'N/A', 'ti', 'Contratação de link de internet dedicado', 'nao lida', '2025-07-23 21:34:42', 0),
(295, 'N/A', 'ti', 'Aquisição de softwares diversos', 'nao lida', '2025-07-23 21:34:42', 0),
(296, 'N/A', 'ti', 'Serviço de cabeamento estruturado', 'nao lida', '2025-07-23 21:34:42', 0),
(297, 'N/A', 'ti', 'Implantação da tecnologia BIM na CENTRAL e capacitar seus usuários', 'nao lida', '2025-07-23 21:34:42', 0),
(298, 'N/A', 'ti', 'Wi-Fi corporativo com segurança e gerenciamento', 'nao lida', '2025-07-23 21:34:42', 0),
(299, 'N/A', 'ti', 'Solução de Atendimento ao Usuário – ITSM', 'nao lida', '2025-07-23 21:34:42', 0),
(300, 'N/A', 'ti', 'Reestruturação de toda Infraestrutura de Rede com Segurança', 'nao lida', '2025-07-23 21:35:25', 0),
(301, 'N/A', 'ti', 'Aquisição de equipamentos e softwares', 'nao lida', '2025-07-23 21:35:25', 0),
(302, 'N/A', 'ti', 'Solução de Atendimento ao Usuário – ITSM', 'nao lida', '2025-07-23 21:35:25', 0),
(303, 'N/A', 'ti', 'Estruturar o banco de dados dos projetos de engenharia', 'nao lida', '2025-07-23 21:35:25', 0),
(304, 'N/A', 'ti', 'Modernização do parque computacional (desktops e notebooks)', 'nao lida', '2025-07-23 21:35:25', 0),
(305, 'N/A', 'ti', 'Implantação da tecnologia BIM na CENTRAL e capacitar seus usuários', 'nao lida', '2025-07-23 21:35:25', 0),
(306, 'N/A', 'ti', 'Wi-Fi corporativo com segurança e gerenciamento', 'nao lida', '2025-07-23 21:35:25', 0),
(307, 'N/A', 'ti', 'Monitoramento e Observabilidade da Infraestrutura', 'nao lida', '2025-07-23 21:35:25', 0),
(308, 'N/A', 'ti', 'Contratação de link de internet dedicado', 'nao lida', '2025-07-23 21:35:25', 0),
(309, 'N/A', 'ti', 'Aquisição de softwares diversos', 'nao lida', '2025-07-23 21:35:25', 0),
(310, 'N/A', 'ti', 'Serviço de cabeamento estruturado', 'nao lida', '2025-07-23 21:35:25', 0),
(311, 'N/A', 'ti', 'Aquisição de equipamentos e softwares', 'nao lida', '2025-07-23 21:35:47', 0),
(312, 'N/A', 'ti', 'Estruturar o banco de dados dos projetos de engenharia', 'nao lida', '2025-07-23 21:35:47', 0),
(313, 'N/A', 'ti', 'Implantação da tecnologia BIM na CENTRAL e capacitar seus usuários', 'nao lida', '2025-07-23 21:35:47', 0),
(314, 'N/A', 'ti', 'Reestruturação de toda Infraestrutura de Rede com Segurança', 'nao lida', '2025-07-23 21:35:47', 0),
(315, 'N/A', 'ti', 'Solução de Atendimento ao Usuário – ITSM', 'nao lida', '2025-07-23 21:35:47', 0),
(316, 'N/A', 'ti', 'Modernização do parque computacional (desktops e notebooks)', 'nao lida', '2025-07-23 21:35:47', 0),
(317, 'N/A', 'ti', 'Wi-Fi corporativo com segurança e gerenciamento', 'nao lida', '2025-07-23 21:35:47', 0),
(318, 'N/A', 'ti', 'Contratação de link de internet dedicado', 'nao lida', '2025-07-23 21:35:47', 0),
(319, 'N/A', 'ti', 'Monitoramento e Observabilidade da Infraestrutura', 'nao lida', '2025-07-23 21:35:47', 0),
(320, 'N/A', 'ti', 'Aquisição de softwares diversos', 'nao lida', '2025-07-23 21:35:47', 0),
(321, 'N/A', 'ti', 'Serviço de cabeamento estruturado', 'nao lida', '2025-07-23 21:35:47', 0),
(322, 'N/A', 'ti', 'Implantação da tecnologia BIM na CENTRAL e capacitar seus usuários', 'nao lida', '2025-07-23 21:36:08', 0),
(323, 'N/A', 'ti', 'Modernização do parque computacional (desktops e notebooks)', 'nao lida', '2025-07-23 21:36:08', 0),
(324, 'N/A', 'ti', 'Estruturar o banco de dados dos projetos de engenharia', 'nao lida', '2025-07-23 21:36:08', 0),
(325, 'N/A', 'ti', 'Contratação de link de internet dedicado', 'nao lida', '2025-07-23 21:36:08', 0),
(326, 'N/A', 'ti', 'Monitoramento e Observabilidade da Infraestrutura', 'nao lida', '2025-07-23 21:36:08', 0),
(327, 'N/A', 'ti', 'Reestruturação de toda Infraestrutura de Rede com Segurança', 'nao lida', '2025-07-23 21:36:08', 0),
(328, 'N/A', 'ti', 'Wi-Fi corporativo com segurança e gerenciamento', 'nao lida', '2025-07-23 21:36:08', 0),
(329, 'N/A', 'ti', 'Aquisição de equipamentos e softwares', 'nao lida', '2025-07-23 21:36:08', 0),
(330, 'N/A', 'ti', 'Solução de Atendimento ao Usuário – ITSM', 'nao lida', '2025-07-23 21:36:08', 0),
(331, 'N/A', 'ti', 'Aquisição de softwares diversos', 'nao lida', '2025-07-23 21:36:08', 0),
(332, 'N/A', 'ti', 'Serviço de cabeamento estruturado', 'nao lida', '2025-07-23 21:36:08', 0),
(333, 'N/A', 'ti', 'Estruturar o banco de dados dos projetos de engenharia', 'nao lida', '2025-07-23 21:40:23', 0),
(334, 'N/A', 'ti', 'Aquisição de equipamentos e softwares', 'nao lida', '2025-07-23 21:40:23', 0),
(335, 'N/A', 'ti', 'Implantação da tecnologia BIM na CENTRAL e capacitar seus usuários', 'nao lida', '2025-07-23 21:40:23', 0),
(336, 'N/A', 'ti', 'Reestruturação de toda Infraestrutura de Rede com Segurança', 'nao lida', '2025-07-23 21:40:23', 0),
(337, 'N/A', 'ti', 'Modernização do parque computacional (desktops e notebooks)', 'nao lida', '2025-07-23 21:40:23', 0),
(338, 'N/A', 'ti', 'Monitoramento e Observabilidade da Infraestrutura', 'nao lida', '2025-07-23 21:40:23', 0),
(339, 'N/A', 'ti', 'Solução de Atendimento ao Usuário – ITSM', 'nao lida', '2025-07-23 21:40:23', 0),
(340, 'N/A', 'ti', 'Contratação de link de internet dedicado', 'nao lida', '2025-07-23 21:40:23', 0),
(341, 'N/A', 'ti', 'Aquisição de softwares diversos', 'nao lida', '2025-07-23 21:40:23', 0),
(342, 'N/A', 'ti', 'Wi-Fi corporativo com segurança e gerenciamento', 'nao lida', '2025-07-23 21:40:23', 0),
(343, 'N/A', 'ti', 'Serviço de cabeamento estruturado', 'nao lida', '2025-07-23 21:40:23', 0),
(344, 'N/A', 'capacitacao', 'Capacitação dos empregados da GERGEP nos softwares de edição de texto e planilhas', 'nao lida', '2025-07-23 21:42:11', 0),
(345, 'N/A', 'capacitacao', 'Capacitação dos empregados da GERGEP no tratamento do arquivamento de documentos', 'nao lida', '2025-07-23 21:42:11', 0),
(346, 'N/A', 'capacitacao', 'Treinamentos visando à Educação Ambiental', 'nao lida', '2025-07-23 21:42:11', 0),
(347, 'N/A', 'capacitacao', 'Formação e capacitação do corpo técnico em processos de TIC', 'nao lida', '2025-07-23 21:42:11', 0),
(348, 'N/A', 'capacitacao', 'Treinamentos para área de Segurança do Trabalho', 'nao lida', '2025-07-23 21:42:11', 0),
(349, 'N/A', 'capacitacao', 'Treinamentos no Sistema e-Social', 'nao lida', '2025-07-23 21:42:11', 0),
(350, 'N/A', 'capacitacao', 'Treinamentos para área de Segurança do Trabalho', 'nao lida', '2025-07-23 21:42:29', 0),
(351, 'N/A', 'capacitacao', 'Treinamentos no Sistema e-Social', 'nao lida', '2025-07-23 21:42:29', 0),
(352, 'N/A', 'capacitacao', 'Treinamentos visando à Educação Ambiental', 'nao lida', '2025-07-23 21:42:29', 0),
(353, 'N/A', 'capacitacao', 'Capacitação dos empregados da GERGEP nos softwares de edição de texto e planilhas', 'nao lida', '2025-07-23 21:42:48', 0),
(354, 'N/A', 'capacitacao', 'Formação e capacitação do corpo técnico em processos de TIC', 'nao lida', '2025-07-23 21:42:48', 0),
(355, 'N/A', 'capacitacao', 'Capacitação dos empregados da GERGEP no tratamento do arquivamento de documentos', 'nao lida', '2025-07-23 21:42:48', 0),
(356, 'N/A', 'capacitacao', 'Treinamentos no Sistema e-Social', 'nao lida', '2025-07-23 21:42:48', 0),
(357, 'N/A', 'capacitacao', 'Treinamentos para área de Segurança do Trabalho', 'nao lida', '2025-07-23 21:42:48', 0),
(358, 'N/A', 'capacitacao', 'Treinamentos visando à Educação Ambiental', 'nao lida', '2025-07-23 21:42:48', 0),
(359, 'N/A', 'capacitacao', 'Capacitação dos empregados da GERGEP nos softwares de edição de texto e planilhas', 'nao lida', '2025-07-23 21:43:05', 0),
(360, 'N/A', 'capacitacao', 'Capacitação dos empregados da GERGEP no tratamento do arquivamento de documentos', 'nao lida', '2025-07-23 21:43:05', 0),
(361, 'N/A', 'capacitacao', 'Formação e capacitação do corpo técnico em processos de TIC', 'nao lida', '2025-07-23 21:43:05', 0),
(362, 'N/A', 'capacitacao', 'Treinamentos no Sistema e-Social', 'nao lida', '2025-07-23 21:43:05', 0),
(363, 'N/A', 'capacitacao', 'Treinamentos para área de Segurança do Trabalho', 'nao lida', '2025-07-23 21:43:05', 0),
(364, 'N/A', 'capacitacao', 'Treinamentos visando à Educação Ambiental', 'nao lida', '2025-07-23 21:43:05', 0),
(365, 'N/A', 'capacitacao', 'Capacitação dos empregados da GERGEP nos softwares de edição de texto e planilhas', 'nao lida', '2025-07-23 21:43:18', 0),
(366, 'N/A', 'capacitacao', 'Capacitação dos empregados da GERGEP no tratamento do arquivamento de documentos', 'nao lida', '2025-07-23 21:43:18', 0),
(367, 'N/A', 'capacitacao', 'Formação e capacitação do corpo técnico em processos de TIC', 'nao lida', '2025-07-23 21:43:18', 0),
(368, 'N/A', 'capacitacao', 'Treinamentos no Sistema e-Social', 'nao lida', '2025-07-23 21:43:18', 0),
(369, 'N/A', 'capacitacao', 'Treinamentos para área de Segurança do Trabalho', 'nao lida', '2025-07-23 21:43:18', 0),
(370, 'N/A', 'capacitacao', 'Treinamentos visando à Educação Ambiental', 'nao lida', '2025-07-23 21:43:18', 0),
(371, 'N/A', 'capacitacao', 'Capacitação dos empregados da GERGEP no tratamento do arquivamento de documentos', 'nao lida', '2025-07-23 21:43:34', 0),
(372, 'N/A', 'capacitacao', 'Formação e capacitação do corpo técnico em processos de TIC', 'nao lida', '2025-07-23 21:43:34', 0),
(373, 'N/A', 'capacitacao', 'Treinamentos no Sistema e-Social', 'nao lida', '2025-07-23 21:43:34', 0),
(374, 'N/A', 'capacitacao', 'Treinamentos para área de Segurança do Trabalho', 'nao lida', '2025-07-23 21:43:34', 0),
(375, 'N/A', 'capacitacao', 'Treinamentos visando à Educação Ambiental', 'nao lida', '2025-07-23 21:43:34', 0),
(376, 'N/A', 'capacitacao', 'Capacitação dos empregados da GERGEP nos softwares de edição de texto e planilhas', 'nao lida', '2025-07-23 21:43:34', 0),
(377, 'N/A', 'capacitacao', 'Capacitação dos empregados da GERGEP no tratamento do arquivamento de documentos', 'nao lida', '2025-07-23 21:45:30', 0),
(378, 'N/A', 'capacitacao', 'Formação e capacitação do corpo técnico em processos de TIC', 'nao lida', '2025-07-23 21:45:30', 0),
(379, 'N/A', 'capacitacao', 'Treinamentos no Sistema e-Social', 'nao lida', '2025-07-23 21:45:30', 0),
(380, 'N/A', 'capacitacao', 'Capacitação dos empregados da GERGEP nos softwares de edição de texto e planilhas', 'nao lida', '2025-07-23 21:45:30', 0),
(381, 'N/A', 'capacitacao', 'Treinamentos visando à Educação Ambiental', 'nao lida', '2025-07-23 21:45:30', 0),
(382, 'N/A', 'capacitacao', 'Treinamentos para área de Segurança do Trabalho', 'nao lida', '2025-07-23 21:45:30', 0),
(383, 'N/A', 'patrimonio', 'Identificação dos bens que integram o acervo patrimonial', 'nao lida', '2025-07-23 21:45:43', 0),
(384, 'N/A', 'patrimonio', 'Controle de todos os bens móveis e imóveis', 'nao lida', '2025-07-23 21:45:43', 0),
(385, 'N/A', 'patrimonio', 'Tratamento e digitalização de documentação administrativa e histórica', 'nao lida', '2025-07-23 21:45:43', 0),
(386, 'N/A', 'teleferico', 'Reativação do Sistema de Transporte do Teleférico do Alemão', 'nao lida', '2025-07-28 20:09:40', 0),
(387, 'N/A', 'teleferico', 'Reativação do Sistema de Transporte do Teleférico do Alemão', 'nao lida', '2025-07-28 20:10:21', 0),
(388, 'N/A', 'bondes', 'Reativação de Linhas', 'nao lida', '2025-07-28 20:13:24', 0),
(389, 'Gabriel', 'bondes', 'Operação do Bonde', 'nao lida', '2025-07-28 20:13:24', 0),
(390, 'N/A', 'bondes', 'Reativação de Linhas', 'nao lida', '2025-07-28 20:40:40', 0),
(391, 'N/A', 'bondes', 'Operação do Bonde', 'nao lida', '2025-07-28 20:40:40', 0),
(392, 'N/A', 'ti', 'Estruturar o banco de dados dos projetos de engenharia', 'nao lida', '2025-08-01 15:30:17', 0),
(393, 'N/A', 'ti', 'Solução de Atendimento ao Usuário – ITSM', 'nao lida', '2025-08-01 15:30:17', 0),
(394, 'N/A', 'ti', 'Implantação da tecnologia BIM na CENTRAL e capacitar seus usuários', 'nao lida', '2025-08-01 15:30:17', 0),
(395, 'N/A', 'ti', 'Reestruturação de toda Infraestrutura de Rede com Segurança', 'nao lida', '2025-08-01 15:30:17', 0),
(396, 'N/A', 'ti', 'Modernização do parque computacional (desktops e notebooks)', 'nao lida', '2025-08-01 15:30:17', 0),
(397, 'N/A', 'ti', 'Aquisição de equipamentos e softwares', 'nao lida', '2025-08-01 15:30:17', 0),
(398, 'N/A', 'ti', 'Wi-Fi corporativo com segurança e gerenciamento', 'nao lida', '2025-08-01 15:30:17', 0),
(399, 'N/A', 'ti', 'Monitoramento e Observabilidade da Infraestrutura', 'nao lida', '2025-08-01 15:30:17', 0),
(400, 'N/A', 'ti', 'Contratação de link de internet dedicado', 'nao lida', '2025-08-01 15:30:17', 0),
(401, 'N/A', 'ti', 'Serviço de cabeamento estruturado', 'nao lida', '2025-08-01 15:30:17', 0),
(402, 'N/A', 'ti', 'Aquisição de softwares diversos', 'nao lida', '2025-08-01 15:30:17', 0),
(403, 'N/A', 'bondes', 'Estudo Técnico Preliminar / Matriz de Risco', 'nao lida', '2025-08-05 19:25:23', 0),
(404, 'N/A', 'bondes', 'Autorização do Ordenador de Despesa', 'nao lida', '2025-08-05 19:25:23', 0),
(405, 'N/A', 'bondes', 'DDO - Declaração de Dotação Orçamentária', 'nao lida', '2025-08-05 19:25:23', 0),
(406, 'N/A', 'bondes', 'Desenvolvimento de novas soluções tecnológicas', 'nao lida', '2025-08-05 19:25:23', 0),
(407, 'N/A', 'bondes', 'Pesquisa de Preços / Relatório Analítico', 'nao lida', '2025-08-05 19:25:23', 0),
(408, 'N/A', 'bondes', 'Termo de Referência', 'nao lida', '2025-08-05 19:25:23', 0),
(409, 'N/A', 'bondes', 'Minuta Edital/Contrato', 'nao lida', '2025-08-05 19:25:23', 0),
(410, 'N/A', 'bondes', 'Lançamento do Edital no SIGA', 'nao lida', '2025-08-05 19:25:23', 0),
(411, 'N/A', 'bondes', 'Parecer Jurídico (ASSJUR)', 'nao lida', '2025-08-05 19:25:23', 0),
(412, 'N/A', 'bondes', 'Recibo de envio de Edital ao TCE / Lançamento SIGA', 'nao lida', '2025-08-05 19:25:23', 0),
(413, 'N/A', 'bondes', 'Abertura do Pregão / Dispensa de Licitação', 'nao lida', '2025-08-05 19:25:23', 0),
(414, 'N/A', 'bondes', 'Adjudicação (Declaração do Vencedor)', 'nao lida', '2025-08-05 19:25:23', 0),
(415, 'N/A', 'bondes', 'Compliance (Due Diligence - ASSGER)', 'nao lida', '2025-08-05 19:25:23', 0),
(416, 'N/A', 'bondes', 'Homologação e Publicação no D.O', 'nao lida', '2025-08-05 19:25:23', 0),
(417, 'N/A', 'bondes', 'Contratação no SIGA', 'nao lida', '2025-08-05 19:25:23', 0),
(418, 'N/A', 'bondes', 'NAD / Nota de Empenho', 'nao lida', '2025-08-05 19:25:23', 0),
(419, 'N/A', 'bondes', 'Contrato (Assinatura)', 'nao lida', '2025-08-05 19:25:23', 0),
(420, 'N/A', 'bondes', 'Gestores e Fiscais', 'nao lida', '2025-08-05 19:25:23', 0),
(421, 'N/A', 'bondes', 'Garantia Contratual', 'nao lida', '2025-08-05 19:25:23', 0),
(422, 'N/A', 'bondes', 'Estudo Técnico Preliminar / Matriz de Risco', 'nao lida', '2025-08-11 17:36:41', 0),
(423, 'N/A', 'bondes', 'DDO - Declaração de Dotação Orçamentária', 'nao lida', '2025-08-11 17:36:41', 0),
(424, 'N/A', 'bondes', 'Termo de Referência', 'nao lida', '2025-08-11 17:36:41', 0),
(425, 'N/A', 'bondes', 'Pesquisa de Preços / Relatório Analítico', 'nao lida', '2025-08-11 17:36:41', 0),
(426, 'N/A', 'bondes', 'Autorização do Ordenador de Despesa', 'nao lida', '2025-08-11 17:36:41', 0),
(427, 'N/A', 'bondes', 'Desenvolvimento de novas soluções tecnológicas', 'nao lida', '2025-08-11 17:36:41', 0),
(428, 'N/A', 'bondes', 'Minuta Edital/Contrato', 'nao lida', '2025-08-11 17:36:41', 0),
(429, 'N/A', 'bondes', 'Parecer Jurídico (ASSJUR)', 'nao lida', '2025-08-11 17:36:41', 0),
(430, 'N/A', 'bondes', 'Lançamento do Edital no SIGA', 'nao lida', '2025-08-11 17:36:41', 0),
(431, 'N/A', 'bondes', 'Recibo de envio de Edital ao TCE / Lançamento SIGA', 'nao lida', '2025-08-11 17:36:41', 0),
(432, 'N/A', 'bondes', 'Abertura do Pregão / Dispensa de Licitação', 'nao lida', '2025-08-11 17:36:41', 0),
(433, 'N/A', 'bondes', 'Adjudicação (Declaração do Vencedor)', 'nao lida', '2025-08-11 17:36:41', 0),
(434, 'N/A', 'bondes', 'Compliance (Due Diligence - ASSGER)', 'nao lida', '2025-08-11 17:36:41', 0),
(435, 'N/A', 'bondes', 'Homologação e Publicação no D.O', 'nao lida', '2025-08-11 17:36:41', 0),
(436, 'N/A', 'bondes', 'Contratação no SIGA', 'nao lida', '2025-08-11 17:36:41', 0),
(437, 'N/A', 'bondes', 'Contrato (Assinatura)', 'nao lida', '2025-08-11 17:36:41', 0),
(438, 'N/A', 'bondes', 'Gestores e Fiscais', 'nao lida', '2025-08-11 17:36:41', 0),
(439, 'N/A', 'bondes', 'Garantia Contratual', 'nao lida', '2025-08-11 17:36:41', 0),
(440, 'N/A', 'bondes', 'NAD / Nota de Empenho', 'nao lida', '2025-08-11 17:36:41', 0),
(441, 'N/A', 'solucoes_tecnologicas', 'Pesquisa de Preços / Relatório Analítico', 'nao lida', '2025-08-11 19:46:45', 0),
(442, 'N/A', 'solucoes_tecnologicas', 'Termo de Referência', 'nao lida', '2025-08-11 19:46:45', 0),
(443, 'N/A', 'solucoes_tecnologicas', 'Autorização do Ordenador de Despesa', 'nao lida', '2025-08-11 19:46:45', 0),
(444, 'N/A', 'solucoes_tecnologicas', 'DDO - Declaração de Dotação Orçamentária', 'nao lida', '2025-08-11 19:46:45', 0),
(445, 'N/A', 'solucoes_tecnologicas', 'Estudo Técnico Preliminar / Matriz de Risco', 'nao lida', '2025-08-11 19:46:45', 0),
(446, 'N/A', 'solucoes_tecnologicas', 'Desenvolvimento de novas soluções tecnológicas', 'nao lida', '2025-08-11 19:46:45', 0),
(447, 'N/A', 'solucoes_tecnologicas', 'Minuta Edital/Contrato', 'nao lida', '2025-08-11 19:46:45', 0),
(448, 'N/A', 'solucoes_tecnologicas', 'Parecer Jurídico (ASSJUR)', 'nao lida', '2025-08-11 19:46:45', 0),
(449, 'N/A', 'solucoes_tecnologicas', 'Lançamento do Edital no SIGA', 'nao lida', '2025-08-11 19:46:45', 0),
(450, 'N/A', 'solucoes_tecnologicas', 'Abertura do Pregão / Dispensa de Licitação', 'nao lida', '2025-08-11 19:46:45', 0),
(451, 'N/A', 'solucoes_tecnologicas', 'Homologação e Publicação no D.O', 'nao lida', '2025-08-11 19:46:45', 0),
(452, 'N/A', 'solucoes_tecnologicas', 'Adjudicação (Declaração do Vencedor)', 'nao lida', '2025-08-11 19:46:45', 0),
(453, 'N/A', 'solucoes_tecnologicas', 'Compliance (Due Diligence - ASSGER)', 'nao lida', '2025-08-11 19:46:45', 0),
(454, 'N/A', 'solucoes_tecnologicas', 'Contratação no SIGA', 'nao lida', '2025-08-11 19:46:45', 0),
(455, 'N/A', 'solucoes_tecnologicas', 'Recibo de envio de Edital ao TCE / Lançamento SIGA', 'nao lida', '2025-08-11 19:46:45', 0),
(456, 'N/A', 'solucoes_tecnologicas', 'NAD / Nota de Empenho', 'nao lida', '2025-08-11 19:46:45', 0),
(457, 'N/A', 'solucoes_tecnologicas', 'Gestores e Fiscais', 'nao lida', '2025-08-11 19:46:45', 0),
(458, 'N/A', 'solucoes_tecnologicas', 'Garantia Contratual', 'nao lida', '2025-08-11 19:46:45', 0),
(459, 'N/A', 'solucoes_tecnologicas', 'Contrato (Assinatura)', 'nao lida', '2025-08-11 19:46:45', 0),
(460, 'N/A', 'solucoes_tecnologicas', 'Termo de Referência', 'nao lida', '2025-08-11 19:47:00', 0),
(461, 'N/A', 'solucoes_tecnologicas', 'DDO - Declaração de Dotação Orçamentária', 'nao lida', '2025-08-11 19:47:00', 0),
(462, 'N/A', 'solucoes_tecnologicas', 'Estudo Técnico Preliminar / Matriz de Risco', 'nao lida', '2025-08-11 19:47:00', 0),
(463, 'N/A', 'solucoes_tecnologicas', 'Parecer Jurídico (ASSJUR)', 'nao lida', '2025-08-11 19:47:00', 0),
(464, 'N/A', 'solucoes_tecnologicas', 'Minuta Edital/Contrato', 'nao lida', '2025-08-11 19:47:00', 0),
(465, 'N/A', 'solucoes_tecnologicas', 'Autorização do Ordenador de Despesa', 'nao lida', '2025-08-11 19:47:00', 0),
(466, 'N/A', 'solucoes_tecnologicas', 'Lançamento do Edital no SIGA', 'nao lida', '2025-08-11 19:47:00', 0),
(467, 'N/A', 'solucoes_tecnologicas', 'Recibo de envio de Edital ao TCE / Lançamento SIGA', 'nao lida', '2025-08-11 19:47:00', 0),
(468, 'N/A', 'solucoes_tecnologicas', 'Abertura do Pregão / Dispensa de Licitação', 'nao lida', '2025-08-11 19:47:00', 0),
(469, 'N/A', 'solucoes_tecnologicas', 'Pesquisa de Preços / Relatório Analítico', 'nao lida', '2025-08-11 19:47:00', 0),
(470, 'N/A', 'solucoes_tecnologicas', 'Adjudicação (Declaração do Vencedor)', 'nao lida', '2025-08-11 19:47:00', 0),
(471, 'N/A', 'solucoes_tecnologicas', 'Compliance (Due Diligence - ASSGER)', 'nao lida', '2025-08-11 19:47:00', 0),
(472, 'N/A', 'solucoes_tecnologicas', 'Homologação e Publicação no D.O', 'nao lida', '2025-08-11 19:47:00', 0);
INSERT INTO `notificacoes` (`id`, `username`, `setor`, `mensagem`, `situacao`, `data_criacao`, `certidao_id`) VALUES
(473, 'N/A', 'solucoes_tecnologicas', 'Contratação no SIGA', 'nao lida', '2025-08-11 19:47:00', 0),
(474, 'N/A', 'solucoes_tecnologicas', 'Desenvolvimento de novas soluções tecnológicas', 'nao lida', '2025-08-11 19:47:00', 0),
(475, 'N/A', 'solucoes_tecnologicas', 'Contrato (Assinatura)', 'nao lida', '2025-08-11 19:47:00', 0),
(476, 'N/A', 'solucoes_tecnologicas', 'NAD / Nota de Empenho', 'nao lida', '2025-08-11 19:47:00', 0),
(477, 'N/A', 'solucoes_tecnologicas', 'Gestores e Fiscais', 'nao lida', '2025-08-11 19:47:00', 0),
(478, 'N/A', 'solucoes_tecnologicas', 'Garantia Contratual', 'nao lida', '2025-08-11 19:47:00', 0),
(479, 'N/A', 'solucoes_tecnologicas', 'Estudo Técnico Preliminar / Matriz de Risco', 'nao lida', '2025-08-11 19:47:23', 0),
(480, 'N/A', 'solucoes_tecnologicas', 'Desenvolvimento de novas soluções tecnológicas', 'nao lida', '2025-08-11 19:47:23', 0),
(481, 'N/A', 'solucoes_tecnologicas', 'Pesquisa de Preços / Relatório Analítico', 'nao lida', '2025-08-11 19:47:23', 0),
(482, 'N/A', 'solucoes_tecnologicas', 'DDO - Declaração de Dotação Orçamentária', 'nao lida', '2025-08-11 19:47:23', 0),
(483, 'N/A', 'solucoes_tecnologicas', 'Termo de Referência', 'nao lida', '2025-08-11 19:47:23', 0),
(484, 'N/A', 'solucoes_tecnologicas', 'Autorização do Ordenador de Despesa', 'nao lida', '2025-08-11 19:47:23', 0),
(485, 'N/A', 'solucoes_tecnologicas', 'Minuta Edital/Contrato', 'nao lida', '2025-08-11 19:47:23', 0),
(486, 'N/A', 'solucoes_tecnologicas', 'Parecer Jurídico (ASSJUR)', 'nao lida', '2025-08-11 19:47:23', 0),
(487, 'N/A', 'solucoes_tecnologicas', 'Lançamento do Edital no SIGA', 'nao lida', '2025-08-11 19:47:23', 0),
(488, 'N/A', 'solucoes_tecnologicas', 'Recibo de envio de Edital ao TCE / Lançamento SIGA', 'nao lida', '2025-08-11 19:47:23', 0),
(489, 'N/A', 'solucoes_tecnologicas', 'Abertura do Pregão / Dispensa de Licitação', 'nao lida', '2025-08-11 19:47:23', 0),
(490, 'N/A', 'solucoes_tecnologicas', 'Adjudicação (Declaração do Vencedor)', 'nao lida', '2025-08-11 19:47:23', 0),
(491, 'N/A', 'solucoes_tecnologicas', 'Compliance (Due Diligence - ASSGER)', 'nao lida', '2025-08-11 19:47:23', 0),
(492, 'N/A', 'solucoes_tecnologicas', 'Homologação e Publicação no D.O', 'nao lida', '2025-08-11 19:47:23', 0),
(493, 'N/A', 'solucoes_tecnologicas', 'Contratação no SIGA', 'nao lida', '2025-08-11 19:47:23', 0),
(494, 'N/A', 'solucoes_tecnologicas', 'NAD / Nota de Empenho', 'nao lida', '2025-08-11 19:47:23', 0),
(495, 'N/A', 'solucoes_tecnologicas', 'Contrato (Assinatura)', 'nao lida', '2025-08-11 19:47:23', 0),
(496, 'N/A', 'solucoes_tecnologicas', 'Gestores e Fiscais', 'nao lida', '2025-08-11 19:47:23', 0),
(497, 'N/A', 'solucoes_tecnologicas', 'Garantia Contratual', 'nao lida', '2025-08-11 19:47:23', 0),
(498, 'N/A', 'bondes', 'Desenvolvimento de novas soluções tecnológicas', 'nao lida', '2025-08-11 20:29:49', 0),
(499, 'N/A', 'bondes', 'Autorização do Ordenador de Despesa', 'nao lida', '2025-08-11 20:29:49', 0),
(500, 'N/A', 'bondes', 'Pesquisa de Preços / Relatório Analítico', 'nao lida', '2025-08-11 20:29:49', 0),
(501, 'N/A', 'bondes', 'Termo de Referência', 'nao lida', '2025-08-11 20:29:49', 0),
(502, 'N/A', 'bondes', 'Estudo Técnico Preliminar / Matriz de Risco', 'nao lida', '2025-08-11 20:29:49', 0),
(503, 'N/A', 'bondes', 'DDO - Declaração de Dotação Orçamentária', 'nao lida', '2025-08-11 20:29:49', 0),
(504, 'N/A', 'bondes', 'Parecer Jurídico (ASSJUR)', 'nao lida', '2025-08-11 20:29:49', 0),
(505, 'N/A', 'bondes', 'Lançamento do Edital no SIGA', 'nao lida', '2025-08-11 20:29:49', 0),
(506, 'N/A', 'bondes', 'Minuta Edital/Contrato', 'nao lida', '2025-08-11 20:29:49', 0),
(507, 'N/A', 'bondes', 'Recibo de envio de Edital ao TCE / Lançamento SIGA', 'nao lida', '2025-08-11 20:29:49', 0),
(508, 'N/A', 'bondes', 'Abertura do Pregão / Dispensa de Licitação', 'nao lida', '2025-08-11 20:29:49', 0),
(509, 'N/A', 'bondes', 'Adjudicação (Declaração do Vencedor)', 'nao lida', '2025-08-11 20:29:49', 0),
(510, 'N/A', 'bondes', 'Compliance (Due Diligence - ASSGER)', 'nao lida', '2025-08-11 20:29:49', 0),
(511, 'N/A', 'bondes', 'Homologação e Publicação no D.O', 'nao lida', '2025-08-11 20:29:49', 0),
(512, 'N/A', 'bondes', 'NAD / Nota de Empenho', 'nao lida', '2025-08-11 20:29:49', 0),
(513, 'N/A', 'bondes', 'Gestores e Fiscais', 'nao lida', '2025-08-11 20:29:49', 0),
(514, 'N/A', 'bondes', 'Garantia Contratual', 'nao lida', '2025-08-11 20:29:49', 0),
(515, 'N/A', 'bondes', 'Contratação no SIGA', 'nao lida', '2025-08-11 20:29:49', 0),
(516, 'N/A', 'bondes', 'Contrato (Assinatura)', 'nao lida', '2025-08-11 20:29:49', 0),
(517, 'N/A', 'bondes', 'Pesquisa de Preços / Relatório Analítico', 'nao lida', '2025-08-11 20:30:46', 0),
(518, 'N/A', 'bondes', 'Desenvolvimento de novas soluções tecnológicas', 'nao lida', '2025-08-11 20:30:46', 0),
(519, 'N/A', 'bondes', 'DDO - Declaração de Dotação Orçamentária', 'nao lida', '2025-08-11 20:30:46', 0),
(520, 'N/A', 'bondes', 'Minuta Edital/Contrato', 'nao lida', '2025-08-11 20:30:46', 0),
(521, 'N/A', 'bondes', 'Parecer Jurídico (ASSJUR)', 'nao lida', '2025-08-11 20:30:46', 0),
(522, 'N/A', 'bondes', 'Estudo Técnico Preliminar / Matriz de Risco', 'nao lida', '2025-08-11 20:30:46', 0),
(523, 'N/A', 'bondes', 'Termo de Referência', 'nao lida', '2025-08-11 20:30:46', 0),
(524, 'N/A', 'bondes', 'Abertura do Pregão / Dispensa de Licitação', 'nao lida', '2025-08-11 20:30:46', 0),
(525, 'N/A', 'bondes', 'Compliance (Due Diligence - ASSGER)', 'nao lida', '2025-08-11 20:30:46', 0),
(526, 'N/A', 'bondes', 'Recibo de envio de Edital ao TCE / Lançamento SIGA', 'nao lida', '2025-08-11 20:30:46', 0),
(527, 'N/A', 'bondes', 'Lançamento do Edital no SIGA', 'nao lida', '2025-08-11 20:30:46', 0),
(528, 'N/A', 'bondes', 'Homologação e Publicação no D.O', 'nao lida', '2025-08-11 20:30:46', 0),
(529, 'N/A', 'bondes', 'Contratação no SIGA', 'nao lida', '2025-08-11 20:30:46', 0),
(530, 'N/A', 'bondes', 'Gestores e Fiscais', 'nao lida', '2025-08-11 20:30:46', 0),
(531, 'N/A', 'bondes', 'Contrato (Assinatura)', 'nao lida', '2025-08-11 20:30:46', 0),
(532, 'N/A', 'bondes', 'NAD / Nota de Empenho', 'nao lida', '2025-08-11 20:30:46', 0),
(533, 'N/A', 'bondes', 'Garantia Contratual', 'nao lida', '2025-08-11 20:30:46', 0),
(534, 'N/A', 'bondes', 'Adjudicação (Declaração do Vencedor)', 'nao lida', '2025-08-11 20:30:46', 0),
(535, 'N/A', 'bondes', 'Autorização do Ordenador de Despesa', 'nao lida', '2025-08-11 20:30:46', 0),
(536, 'N/A', 'bondes', 'DDO - Declaração de Dotação Orçamentária', 'nao lida', '2025-08-11 20:41:40', 0),
(537, 'N/A', 'bondes', 'Minuta Edital/Contrato', 'nao lida', '2025-08-11 20:41:40', 0),
(538, 'N/A', 'bondes', 'Autorização do Ordenador de Despesa', 'nao lida', '2025-08-11 20:41:40', 0),
(539, 'N/A', 'bondes', 'Termo de Referência', 'nao lida', '2025-08-11 20:41:40', 0),
(540, 'N/A', 'bondes', 'Pesquisa de Preços / Relatório Analítico', 'nao lida', '2025-08-11 20:41:40', 0),
(541, 'N/A', 'bondes', 'Estudo Técnico Preliminar / Matriz de Risco', 'nao lida', '2025-08-11 20:41:40', 0),
(542, 'N/A', 'bondes', 'Lançamento do Edital no SIGA', 'nao lida', '2025-08-11 20:41:40', 0),
(543, 'N/A', 'bondes', 'Parecer Jurídico (ASSJUR)', 'nao lida', '2025-08-11 20:41:40', 0),
(544, 'N/A', 'bondes', 'Abertura do Pregão / Dispensa de Licitação', 'nao lida', '2025-08-11 20:41:40', 0),
(545, 'N/A', 'bondes', 'Recibo de envio de Edital ao TCE / Lançamento SIGA', 'nao lida', '2025-08-11 20:41:40', 0),
(546, 'N/A', 'bondes', 'Desenvolvimento de novas soluções tecnológicas', 'nao lida', '2025-08-11 20:41:40', 0),
(547, 'N/A', 'bondes', 'Homologação e Publicação no D.O', 'nao lida', '2025-08-11 20:41:40', 0),
(548, 'N/A', 'bondes', 'Adjudicação (Declaração do Vencedor)', 'nao lida', '2025-08-11 20:41:40', 0),
(549, 'N/A', 'bondes', 'Contratação no SIGA', 'nao lida', '2025-08-11 20:41:40', 0),
(550, 'N/A', 'bondes', 'NAD / Nota de Empenho', 'nao lida', '2025-08-11 20:41:40', 0),
(551, 'N/A', 'bondes', 'Contrato (Assinatura)', 'nao lida', '2025-08-11 20:41:40', 0),
(552, 'N/A', 'bondes', 'Compliance (Due Diligence - ASSGER)', 'nao lida', '2025-08-11 20:41:40', 0),
(553, 'N/A', 'bondes', 'Gestores e Fiscais', 'nao lida', '2025-08-11 20:41:40', 0),
(554, 'N/A', 'bondes', 'Garantia Contratual', 'nao lida', '2025-08-11 20:41:40', 0),
(555, 'N/A', 'bondes', 'Estudo Técnico Preliminar / Matriz de Risco', 'nao lida', '2025-08-11 20:48:41', 0),
(556, 'N/A', 'bondes', 'Desenvolvimento de novas soluções tecnológicas', 'nao lida', '2025-08-11 20:48:41', 0),
(557, 'N/A', 'bondes', 'Termo de Referência', 'nao lida', '2025-08-11 20:48:41', 0),
(558, 'N/A', 'bondes', 'Autorização do Ordenador de Despesa', 'nao lida', '2025-08-11 20:48:41', 0),
(559, 'N/A', 'bondes', 'DDO - Declaração de Dotação Orçamentária', 'nao lida', '2025-08-11 20:48:41', 0),
(560, 'N/A', 'bondes', 'Pesquisa de Preços / Relatório Analítico', 'nao lida', '2025-08-11 20:48:41', 0),
(561, 'N/A', 'bondes', 'Minuta Edital/Contrato', 'nao lida', '2025-08-11 20:48:41', 0),
(562, 'N/A', 'bondes', 'Lançamento do Edital no SIGA', 'nao lida', '2025-08-11 20:48:41', 0),
(563, 'N/A', 'bondes', 'Abertura do Pregão / Dispensa de Licitação', 'nao lida', '2025-08-11 20:48:41', 0),
(564, 'N/A', 'bondes', 'Recibo de envio de Edital ao TCE / Lançamento SIGA', 'nao lida', '2025-08-11 20:48:41', 0),
(565, 'N/A', 'bondes', 'Adjudicação (Declaração do Vencedor)', 'nao lida', '2025-08-11 20:48:41', 0),
(566, 'N/A', 'bondes', 'Compliance (Due Diligence - ASSGER)', 'nao lida', '2025-08-11 20:48:41', 0),
(567, 'N/A', 'bondes', 'Contratação no SIGA', 'nao lida', '2025-08-11 20:48:41', 0),
(568, 'N/A', 'bondes', 'Homologação e Publicação no D.O', 'nao lida', '2025-08-11 20:48:41', 0),
(569, 'N/A', 'bondes', 'NAD / Nota de Empenho', 'nao lida', '2025-08-11 20:48:41', 0),
(570, 'N/A', 'bondes', 'Gestores e Fiscais', 'nao lida', '2025-08-11 20:48:41', 0),
(571, 'N/A', 'bondes', 'Garantia Contratual', 'nao lida', '2025-08-11 20:48:41', 0),
(572, 'N/A', 'bondes', 'Parecer Jurídico (ASSJUR)', 'nao lida', '2025-08-11 20:48:41', 0),
(573, 'N/A', 'bondes', 'Contrato (Assinatura)', 'nao lida', '2025-08-11 20:48:41', 0),
(574, 'N/A', 'bondes', 'DDO - Declaração de Dotação Orçamentária', 'nao lida', '2025-08-11 20:49:07', 0),
(575, 'N/A', 'bondes', 'Autorização do Ordenador de Despesa', 'nao lida', '2025-08-11 20:49:07', 0),
(576, 'N/A', 'bondes', 'Pesquisa de Preços / Relatório Analítico', 'nao lida', '2025-08-11 20:49:07', 0),
(577, 'N/A', 'bondes', 'Desenvolvimento de novas soluções tecnológicas', 'nao lida', '2025-08-11 20:49:07', 0),
(578, 'N/A', 'bondes', 'Minuta Edital/Contrato', 'nao lida', '2025-08-11 20:49:07', 0),
(579, 'N/A', 'bondes', 'Parecer Jurídico (ASSJUR)', 'nao lida', '2025-08-11 20:49:07', 0),
(580, 'N/A', 'bondes', 'Lançamento do Edital no SIGA', 'nao lida', '2025-08-11 20:49:07', 0),
(581, 'N/A', 'bondes', 'Recibo de envio de Edital ao TCE / Lançamento SIGA', 'nao lida', '2025-08-11 20:49:07', 0),
(582, 'N/A', 'bondes', 'Termo de Referência', 'nao lida', '2025-08-11 20:49:07', 0),
(583, 'N/A', 'bondes', 'Abertura do Pregão / Dispensa de Licitação', 'nao lida', '2025-08-11 20:49:08', 0),
(584, 'N/A', 'bondes', 'Adjudicação (Declaração do Vencedor)', 'nao lida', '2025-08-11 20:49:08', 0),
(585, 'N/A', 'bondes', 'Compliance (Due Diligence - ASSGER)', 'nao lida', '2025-08-11 20:49:08', 0),
(586, 'N/A', 'bondes', 'Homologação e Publicação no D.O', 'nao lida', '2025-08-11 20:49:08', 0),
(587, 'N/A', 'bondes', 'Contratação no SIGA', 'nao lida', '2025-08-11 20:49:08', 0),
(588, 'N/A', 'bondes', 'Gestores e Fiscais', 'nao lida', '2025-08-11 20:49:08', 0),
(589, 'N/A', 'bondes', 'Garantia Contratual', 'nao lida', '2025-08-11 20:49:08', 0),
(590, 'N/A', 'bondes', 'Estudo Técnico Preliminar / Matriz de Risco', 'nao lida', '2025-08-11 20:49:07', 0),
(591, 'N/A', 'bondes', 'NAD / Nota de Empenho', 'nao lida', '2025-08-11 20:49:08', 0),
(592, 'N/A', 'bondes', 'Contrato (Assinatura)', 'nao lida', '2025-08-11 20:49:08', 0),
(593, 'N/A', 'bondes', 'Desenvolvimento de novas soluções tecnológicas', 'nao lida', '2025-08-11 20:49:40', 0),
(594, 'N/A', 'bondes', 'Estudo Técnico Preliminar / Matriz de Risco', 'nao lida', '2025-08-11 20:49:40', 0),
(595, 'N/A', 'bondes', 'Termo de Referência', 'nao lida', '2025-08-11 20:49:40', 0),
(596, 'N/A', 'bondes', 'DDO - Declaração de Dotação Orçamentária', 'nao lida', '2025-08-11 20:49:40', 0),
(597, 'N/A', 'bondes', 'Autorização do Ordenador de Despesa', 'nao lida', '2025-08-11 20:49:40', 0),
(598, 'N/A', 'bondes', 'Parecer Jurídico (ASSJUR)', 'nao lida', '2025-08-11 20:49:40', 0),
(599, 'N/A', 'bondes', 'Pesquisa de Preços / Relatório Analítico', 'nao lida', '2025-08-11 20:49:40', 0),
(600, 'N/A', 'bondes', 'Minuta Edital/Contrato', 'nao lida', '2025-08-11 20:49:40', 0),
(601, 'N/A', 'bondes', 'Lançamento do Edital no SIGA', 'nao lida', '2025-08-11 20:49:40', 0),
(602, 'N/A', 'bondes', 'Abertura do Pregão / Dispensa de Licitação', 'nao lida', '2025-08-11 20:49:40', 0),
(603, 'N/A', 'bondes', 'Recibo de envio de Edital ao TCE / Lançamento SIGA', 'nao lida', '2025-08-11 20:49:40', 0),
(604, 'N/A', 'bondes', 'Adjudicação (Declaração do Vencedor)', 'nao lida', '2025-08-11 20:49:40', 0),
(605, 'N/A', 'bondes', 'Compliance (Due Diligence - ASSGER)', 'nao lida', '2025-08-11 20:49:40', 0),
(606, 'N/A', 'bondes', 'Homologação e Publicação no D.O', 'nao lida', '2025-08-11 20:49:40', 0),
(607, 'N/A', 'bondes', 'Contratação no SIGA', 'nao lida', '2025-08-11 20:49:40', 0),
(608, 'N/A', 'bondes', 'NAD / Nota de Empenho', 'nao lida', '2025-08-11 20:49:40', 0),
(609, 'N/A', 'bondes', 'Contrato (Assinatura)', 'nao lida', '2025-08-11 20:49:40', 0),
(610, 'N/A', 'bondes', 'Gestores e Fiscais', 'nao lida', '2025-08-11 20:49:40', 0),
(611, 'N/A', 'bondes', 'Garantia Contratual', 'nao lida', '2025-08-11 20:49:40', 0),
(612, 'N/A', 'bondes', 'Desenvolvimento de novas soluções tecnológicas', 'nao lida', '2025-08-11 20:50:11', 0),
(613, 'N/A', 'bondes', 'Estudo Técnico Preliminar / Matriz de Risco', 'nao lida', '2025-08-11 20:50:11', 0),
(614, 'N/A', 'bondes', 'Pesquisa de Preços / Relatório Analítico', 'nao lida', '2025-08-11 20:50:11', 0),
(615, 'N/A', 'bondes', 'Termo de Referência', 'nao lida', '2025-08-11 20:50:11', 0),
(616, 'N/A', 'bondes', 'Autorização do Ordenador de Despesa', 'nao lida', '2025-08-11 20:50:11', 0),
(617, 'N/A', 'bondes', 'Minuta Edital/Contrato', 'nao lida', '2025-08-11 20:50:11', 0),
(618, 'N/A', 'bondes', 'Adjudicação (Declaração do Vencedor)', 'nao lida', '2025-08-11 20:50:11', 0),
(619, 'N/A', 'bondes', 'Lançamento do Edital no SIGA', 'nao lida', '2025-08-11 20:50:11', 0),
(620, 'N/A', 'bondes', 'Abertura do Pregão / Dispensa de Licitação', 'nao lida', '2025-08-11 20:50:11', 0),
(621, 'N/A', 'bondes', 'Recibo de envio de Edital ao TCE / Lançamento SIGA', 'nao lida', '2025-08-11 20:50:11', 0),
(622, 'N/A', 'bondes', 'Compliance (Due Diligence - ASSGER)', 'nao lida', '2025-08-11 20:50:11', 0),
(623, 'N/A', 'bondes', 'DDO - Declaração de Dotação Orçamentária', 'nao lida', '2025-08-11 20:50:11', 0),
(624, 'N/A', 'bondes', 'Homologação e Publicação no D.O', 'nao lida', '2025-08-11 20:50:11', 0),
(625, 'N/A', 'bondes', 'Contratação no SIGA', 'nao lida', '2025-08-11 20:50:11', 0),
(626, 'N/A', 'bondes', 'NAD / Nota de Empenho', 'nao lida', '2025-08-11 20:50:11', 0),
(627, 'N/A', 'bondes', 'Contrato (Assinatura)', 'nao lida', '2025-08-11 20:50:11', 0),
(628, 'N/A', 'bondes', 'Gestores e Fiscais', 'nao lida', '2025-08-11 20:50:11', 0),
(629, 'N/A', 'bondes', 'Garantia Contratual', 'nao lida', '2025-08-11 20:50:11', 0),
(630, 'N/A', 'bondes', 'Parecer Jurídico (ASSJUR)', 'nao lida', '2025-08-11 20:50:11', 0),
(631, 'N/A', 'bondes', 'Estudo Técnico Preliminar / Matriz de Risco', 'nao lida', '2025-08-11 20:50:39', 0),
(632, 'N/A', 'bondes', 'Pesquisa de Preços / Relatório Analítico', 'nao lida', '2025-08-11 20:50:39', 0),
(633, 'N/A', 'bondes', 'Termo de Referência', 'nao lida', '2025-08-11 20:50:39', 0),
(634, 'N/A', 'bondes', 'Autorização do Ordenador de Despesa', 'nao lida', '2025-08-11 20:50:39', 0),
(635, 'N/A', 'bondes', 'DDO - Declaração de Dotação Orçamentária', 'nao lida', '2025-08-11 20:50:39', 0),
(636, 'N/A', 'bondes', 'Minuta Edital/Contrato', 'nao lida', '2025-08-11 20:50:39', 0),
(637, 'N/A', 'bondes', 'Desenvolvimento de novas soluções tecnológicas', 'nao lida', '2025-08-11 20:50:39', 0),
(638, 'N/A', 'bondes', 'Parecer Jurídico (ASSJUR)', 'nao lida', '2025-08-11 20:50:39', 0),
(639, 'N/A', 'bondes', 'Lançamento do Edital no SIGA', 'nao lida', '2025-08-11 20:50:39', 0),
(640, 'N/A', 'bondes', 'Recibo de envio de Edital ao TCE / Lançamento SIGA', 'nao lida', '2025-08-11 20:50:39', 0),
(641, 'N/A', 'bondes', 'Abertura do Pregão / Dispensa de Licitação', 'nao lida', '2025-08-11 20:50:39', 0),
(642, 'N/A', 'bondes', 'Adjudicação (Declaração do Vencedor)', 'nao lida', '2025-08-11 20:50:39', 0),
(643, 'N/A', 'bondes', 'NAD / Nota de Empenho', 'nao lida', '2025-08-11 20:50:39', 0),
(644, 'N/A', 'bondes', 'Compliance (Due Diligence - ASSGER)', 'nao lida', '2025-08-11 20:50:39', 0),
(645, 'N/A', 'bondes', 'Contratação no SIGA', 'nao lida', '2025-08-11 20:50:39', 0),
(646, 'N/A', 'bondes', 'Homologação e Publicação no D.O', 'nao lida', '2025-08-11 20:50:39', 0),
(647, 'N/A', 'bondes', 'Gestores e Fiscais', 'nao lida', '2025-08-11 20:50:39', 0),
(648, 'N/A', 'bondes', 'Garantia Contratual', 'nao lida', '2025-08-11 20:50:39', 0),
(649, 'N/A', 'bondes', 'Contrato (Assinatura)', 'nao lida', '2025-08-11 20:50:39', 0),
(650, 'N/A', 'ferrovia', 'Desenvolvimento de novas soluções tecnológicas', 'nao lida', '2025-08-11 20:57:50', 0),
(651, 'N/A', 'ferrovia', 'DDO - Declaração de Dotação Orçamentária', 'nao lida', '2025-08-11 20:57:50', 0),
(652, 'N/A', 'ferrovia', 'Autorização do Ordenador de Despesa', 'nao lida', '2025-08-11 20:57:50', 0),
(653, 'N/A', 'ferrovia', 'Termo de Referência', 'nao lida', '2025-08-11 20:57:50', 0),
(654, 'N/A', 'ferrovia', 'Estudo Técnico Preliminar / Matriz de Risco', 'nao lida', '2025-08-11 20:57:50', 0),
(655, 'N/A', 'ferrovia', 'Pesquisa de Preços / Relatório Analítico', 'nao lida', '2025-08-11 20:57:50', 0),
(656, 'N/A', 'ferrovia', 'Minuta Edital/Contrato', 'nao lida', '2025-08-11 20:57:50', 0),
(657, 'N/A', 'ferrovia', 'Lançamento do Edital no SIGA', 'nao lida', '2025-08-11 20:57:50', 0),
(658, 'N/A', 'ferrovia', 'Adjudicação (Declaração do Vencedor)', 'nao lida', '2025-08-11 20:57:50', 0),
(659, 'N/A', 'ferrovia', 'Compliance (Due Diligence - ASSGER)', 'nao lida', '2025-08-11 20:57:50', 0),
(660, 'N/A', 'ferrovia', 'Abertura do Pregão / Dispensa de Licitação', 'nao lida', '2025-08-11 20:57:50', 0),
(661, 'N/A', 'ferrovia', 'Parecer Jurídico (ASSJUR)', 'nao lida', '2025-08-11 20:57:50', 0),
(662, 'N/A', 'ferrovia', 'Contratação no SIGA', 'nao lida', '2025-08-11 20:57:50', 0),
(663, 'N/A', 'ferrovia', 'Homologação e Publicação no D.O', 'nao lida', '2025-08-11 20:57:50', 0),
(664, 'N/A', 'ferrovia', 'Recibo de envio de Edital ao TCE / Lançamento SIGA', 'nao lida', '2025-08-11 20:57:50', 0),
(665, 'N/A', 'ferrovia', 'NAD / Nota de Empenho', 'nao lida', '2025-08-11 20:57:50', 0),
(666, 'N/A', 'ferrovia', 'Contrato (Assinatura)', 'nao lida', '2025-08-11 20:57:50', 0),
(667, 'N/A', 'ferrovia', 'Gestores e Fiscais', 'nao lida', '2025-08-11 20:57:50', 0),
(668, 'N/A', 'ferrovia', 'Garantia Contratual', 'nao lida', '2025-08-11 20:57:50', 0),
(669, 'N/A', 'ferrovia', 'DDO - Declaração de Dotação Orçamentária', 'nao lida', '2025-08-11 20:58:25', 0),
(670, 'N/A', 'ferrovia', 'Estudo Técnico Preliminar / Matriz de Risco', 'nao lida', '2025-08-11 20:58:25', 0),
(671, 'N/A', 'ferrovia', 'Desenvolvimento de novas soluções tecnológicas', 'nao lida', '2025-08-11 20:58:25', 0),
(672, 'N/A', 'ferrovia', 'Termo de Referência', 'nao lida', '2025-08-11 20:58:25', 0),
(673, 'N/A', 'ferrovia', 'Autorização do Ordenador de Despesa', 'nao lida', '2025-08-11 20:58:25', 0),
(674, 'N/A', 'ferrovia', 'Parecer Jurídico (ASSJUR)', 'nao lida', '2025-08-11 20:58:25', 0),
(675, 'N/A', 'ferrovia', 'Minuta Edital/Contrato', 'nao lida', '2025-08-11 20:58:25', 0),
(676, 'N/A', 'ferrovia', 'Lançamento do Edital no SIGA', 'nao lida', '2025-08-11 20:58:25', 0),
(677, 'N/A', 'ferrovia', 'Pesquisa de Preços / Relatório Analítico', 'nao lida', '2025-08-11 20:58:25', 0),
(678, 'N/A', 'ferrovia', 'Homologação e Publicação no D.O', 'nao lida', '2025-08-11 20:58:25', 0),
(679, 'N/A', 'ferrovia', 'Adjudicação (Declaração do Vencedor)', 'nao lida', '2025-08-11 20:58:25', 0),
(680, 'N/A', 'ferrovia', 'Compliance (Due Diligence - ASSGER)', 'nao lida', '2025-08-11 20:58:25', 0),
(681, 'N/A', 'ferrovia', 'Abertura do Pregão / Dispensa de Licitação', 'nao lida', '2025-08-11 20:58:25', 0),
(682, 'N/A', 'ferrovia', 'Recibo de envio de Edital ao TCE / Lançamento SIGA', 'nao lida', '2025-08-11 20:58:25', 0),
(683, 'N/A', 'ferrovia', 'Contratação no SIGA', 'nao lida', '2025-08-11 20:58:25', 0),
(684, 'N/A', 'ferrovia', 'NAD / Nota de Empenho', 'nao lida', '2025-08-11 20:58:25', 0),
(685, 'N/A', 'ferrovia', 'Contrato (Assinatura)', 'nao lida', '2025-08-11 20:58:25', 0),
(686, 'N/A', 'ferrovia', 'Gestores e Fiscais', 'nao lida', '2025-08-11 20:58:25', 0),
(687, 'N/A', 'ferrovia', 'Garantia Contratual', 'nao lida', '2025-08-11 20:58:25', 0),
(688, 'N/A', 'solucoes_tecnologicas', 'Estudo Técnico Preliminar / Matriz de Risco', 'nao lida', '2025-08-11 21:02:11', 0),
(689, 'N/A', 'solucoes_tecnologicas', 'Pesquisa de Preços / Relatório Analítico', 'nao lida', '2025-08-11 21:02:11', 0),
(690, 'N/A', 'solucoes_tecnologicas', 'Termo de Referência', 'nao lida', '2025-08-11 21:02:11', 0),
(691, 'N/A', 'solucoes_tecnologicas', 'Desenvolvimento de novas soluções tecnológicas', 'nao lida', '2025-08-11 21:02:11', 0),
(692, 'N/A', 'solucoes_tecnologicas', 'Minuta Edital/Contrato', 'nao lida', '2025-08-11 21:02:11', 0),
(693, 'N/A', 'solucoes_tecnologicas', 'DDO - Declaração de Dotação Orçamentária', 'nao lida', '2025-08-11 21:02:11', 0),
(694, 'N/A', 'solucoes_tecnologicas', 'Parecer Jurídico (ASSJUR)', 'nao lida', '2025-08-11 21:02:11', 0),
(695, 'N/A', 'solucoes_tecnologicas', 'Lançamento do Edital no SIGA', 'nao lida', '2025-08-11 21:02:11', 0),
(696, 'N/A', 'solucoes_tecnologicas', 'Autorização do Ordenador de Despesa', 'nao lida', '2025-08-11 21:02:11', 0),
(697, 'N/A', 'solucoes_tecnologicas', 'Recibo de envio de Edital ao TCE / Lançamento SIGA', 'nao lida', '2025-08-11 21:02:11', 0),
(698, 'N/A', 'solucoes_tecnologicas', 'Abertura do Pregão / Dispensa de Licitação', 'nao lida', '2025-08-11 21:02:11', 0),
(699, 'N/A', 'solucoes_tecnologicas', 'Homologação e Publicação no D.O', 'nao lida', '2025-08-11 21:02:11', 0),
(700, 'N/A', 'solucoes_tecnologicas', 'Compliance (Due Diligence - ASSGER)', 'nao lida', '2025-08-11 21:02:11', 0),
(701, 'N/A', 'solucoes_tecnologicas', 'Contratação no SIGA', 'nao lida', '2025-08-11 21:02:11', 0),
(702, 'N/A', 'solucoes_tecnologicas', 'Adjudicação (Declaração do Vencedor)', 'nao lida', '2025-08-11 21:02:11', 0),
(703, 'N/A', 'solucoes_tecnologicas', 'NAD / Nota de Empenho', 'nao lida', '2025-08-11 21:02:11', 0),
(704, 'N/A', 'solucoes_tecnologicas', 'Gestores e Fiscais', 'nao lida', '2025-08-11 21:02:11', 0),
(705, 'N/A', 'solucoes_tecnologicas', 'Contrato (Assinatura)', 'nao lida', '2025-08-11 21:02:11', 0),
(706, 'N/A', 'solucoes_tecnologicas', 'Garantia Contratual', 'nao lida', '2025-08-11 21:02:11', 0),
(707, 'N/A', 'solucoes_tecnologicas', 'Estudo Técnico Preliminar / Matriz de Risco', 'nao lida', '2025-08-11 21:02:24', 0),
(708, 'N/A', 'solucoes_tecnologicas', 'Termo de Referência', 'nao lida', '2025-08-11 21:02:24', 0),
(709, 'N/A', 'solucoes_tecnologicas', 'Pesquisa de Preços / Relatório Analítico', 'nao lida', '2025-08-11 21:02:24', 0),
(710, 'N/A', 'solucoes_tecnologicas', 'Minuta Edital/Contrato', 'nao lida', '2025-08-11 21:02:24', 0),
(711, 'N/A', 'solucoes_tecnologicas', 'DDO - Declaração de Dotação Orçamentária', 'nao lida', '2025-08-11 21:02:24', 0),
(712, 'N/A', 'solucoes_tecnologicas', 'Autorização do Ordenador de Despesa', 'nao lida', '2025-08-11 21:02:24', 0),
(713, 'N/A', 'solucoes_tecnologicas', 'Desenvolvimento de novas soluções tecnológicas', 'nao lida', '2025-08-11 21:02:24', 0),
(714, 'N/A', 'solucoes_tecnologicas', 'Lançamento do Edital no SIGA', 'nao lida', '2025-08-11 21:02:24', 0),
(715, 'N/A', 'solucoes_tecnologicas', 'Recibo de envio de Edital ao TCE / Lançamento SIGA', 'nao lida', '2025-08-11 21:02:24', 0),
(716, 'N/A', 'solucoes_tecnologicas', 'Abertura do Pregão / Dispensa de Licitação', 'nao lida', '2025-08-11 21:02:24', 0),
(717, 'N/A', 'solucoes_tecnologicas', 'Parecer Jurídico (ASSJUR)', 'nao lida', '2025-08-11 21:02:24', 0),
(718, 'N/A', 'solucoes_tecnologicas', 'Adjudicação (Declaração do Vencedor)', 'nao lida', '2025-08-11 21:02:24', 0),
(719, 'N/A', 'solucoes_tecnologicas', 'Compliance (Due Diligence - ASSGER)', 'nao lida', '2025-08-11 21:02:24', 0),
(720, 'N/A', 'solucoes_tecnologicas', 'Homologação e Publicação no D.O', 'nao lida', '2025-08-11 21:02:24', 0),
(721, 'N/A', 'solucoes_tecnologicas', 'NAD / Nota de Empenho', 'nao lida', '2025-08-11 21:02:24', 0),
(722, 'N/A', 'solucoes_tecnologicas', 'Contrato (Assinatura)', 'nao lida', '2025-08-11 21:02:24', 0),
(723, 'N/A', 'solucoes_tecnologicas', 'Gestores e Fiscais', 'nao lida', '2025-08-11 21:02:24', 0),
(724, 'N/A', 'solucoes_tecnologicas', 'Garantia Contratual', 'nao lida', '2025-08-11 21:02:24', 0),
(725, 'N/A', 'solucoes_tecnologicas', 'Contratação no SIGA', 'nao lida', '2025-08-11 21:02:24', 0),
(726, 'N/A', 'solucoes_tecnologicas', 'Desenvolvimento de novas soluções tecnológicas', 'nao lida', '2025-08-11 21:02:36', 0),
(727, 'N/A', 'solucoes_tecnologicas', 'Termo de Referência', 'nao lida', '2025-08-11 21:02:36', 0),
(728, 'N/A', 'solucoes_tecnologicas', 'Pesquisa de Preços / Relatório Analítico', 'nao lida', '2025-08-11 21:02:36', 0),
(729, 'N/A', 'solucoes_tecnologicas', 'Estudo Técnico Preliminar / Matriz de Risco', 'nao lida', '2025-08-11 21:02:36', 0),
(730, 'N/A', 'solucoes_tecnologicas', 'Autorização do Ordenador de Despesa', 'nao lida', '2025-08-11 21:02:36', 0),
(731, 'N/A', 'solucoes_tecnologicas', 'DDO - Declaração de Dotação Orçamentária', 'nao lida', '2025-08-11 21:02:36', 0),
(732, 'N/A', 'solucoes_tecnologicas', 'Minuta Edital/Contrato', 'nao lida', '2025-08-11 21:02:36', 0),
(733, 'N/A', 'solucoes_tecnologicas', 'Parecer Jurídico (ASSJUR)', 'nao lida', '2025-08-11 21:02:36', 0),
(734, 'N/A', 'solucoes_tecnologicas', 'Abertura do Pregão / Dispensa de Licitação', 'nao lida', '2025-08-11 21:02:36', 0),
(735, 'N/A', 'solucoes_tecnologicas', 'Compliance (Due Diligence - ASSGER)', 'nao lida', '2025-08-11 21:02:36', 0),
(736, 'N/A', 'solucoes_tecnologicas', 'Adjudicação (Declaração do Vencedor)', 'nao lida', '2025-08-11 21:02:36', 0),
(737, 'N/A', 'solucoes_tecnologicas', 'Homologação e Publicação no D.O', 'nao lida', '2025-08-11 21:02:36', 0),
(738, 'N/A', 'solucoes_tecnologicas', 'Contratação no SIGA', 'nao lida', '2025-08-11 21:02:36', 0),
(739, 'N/A', 'solucoes_tecnologicas', 'NAD / Nota de Empenho', 'nao lida', '2025-08-11 21:02:36', 0),
(740, 'N/A', 'solucoes_tecnologicas', 'Recibo de envio de Edital ao TCE / Lançamento SIGA', 'nao lida', '2025-08-11 21:02:36', 0),
(741, 'N/A', 'solucoes_tecnologicas', 'Lançamento do Edital no SIGA', 'nao lida', '2025-08-11 21:02:36', 0),
(742, 'N/A', 'solucoes_tecnologicas', 'Gestores e Fiscais', 'nao lida', '2025-08-11 21:02:36', 0),
(743, 'N/A', 'solucoes_tecnologicas', 'Contrato (Assinatura)', 'nao lida', '2025-08-11 21:02:36', 0),
(744, 'N/A', 'solucoes_tecnologicas', 'Garantia Contratual', 'nao lida', '2025-08-11 21:02:36', 0),
(745, 'N/A', 'ferrovia', 'Desenvolvimento de novas soluções tecnológicas', 'nao lida', '2025-08-11 22:23:28', 0),
(746, 'N/A', 'ferrovia', 'Estudo Técnico Preliminar / Matriz de Risco', 'nao lida', '2025-08-11 22:23:28', 0),
(747, 'N/A', 'ferrovia', 'Termo de Referência', 'nao lida', '2025-08-11 22:23:28', 0),
(748, 'N/A', 'ferrovia', 'DDO - Declaração de Dotação Orçamentária', 'nao lida', '2025-08-11 22:23:28', 0),
(749, 'N/A', 'ferrovia', 'Pesquisa de Preços / Relatório Analítico', 'nao lida', '2025-08-11 22:23:28', 0),
(750, 'N/A', 'ferrovia', 'Autorização do Ordenador de Despesa', 'nao lida', '2025-08-11 22:23:28', 0),
(751, 'N/A', 'ferrovia', 'Minuta Edital/Contrato', 'nao lida', '2025-08-11 22:23:28', 0),
(752, 'N/A', 'ferrovia', 'Parecer Jurídico (ASSJUR)', 'nao lida', '2025-08-11 22:23:28', 0),
(753, 'N/A', 'ferrovia', 'Abertura do Pregão / Dispensa de Licitação', 'nao lida', '2025-08-11 22:23:28', 0),
(754, 'N/A', 'ferrovia', 'Adjudicação (Declaração do Vencedor)', 'nao lida', '2025-08-11 22:23:28', 0),
(755, 'N/A', 'ferrovia', 'Compliance (Due Diligence - ASSGER)', 'nao lida', '2025-08-11 22:23:28', 0),
(756, 'N/A', 'ferrovia', 'Contratação no SIGA', 'nao lida', '2025-08-11 22:23:28', 0),
(757, 'N/A', 'ferrovia', 'Contrato (Assinatura)', 'nao lida', '2025-08-11 22:23:28', 0),
(758, 'N/A', 'ferrovia', 'NAD / Nota de Empenho', 'nao lida', '2025-08-11 22:23:28', 0),
(759, 'N/A', 'ferrovia', 'Gestores e Fiscais', 'nao lida', '2025-08-11 22:23:28', 0),
(760, 'N/A', 'ferrovia', 'Homologação e Publicação no D.O', 'nao lida', '2025-08-11 22:23:28', 0),
(761, 'N/A', 'ferrovia', 'Recibo de envio de Edital ao TCE / Lançamento SIGA', 'nao lida', '2025-08-11 22:23:28', 0),
(762, 'N/A', 'ferrovia', 'Lançamento do Edital no SIGA', 'nao lida', '2025-08-11 22:23:28', 0),
(763, 'N/A', 'ferrovia', 'Garantia Contratual', 'nao lida', '2025-08-11 22:23:28', 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `ordens_compra`
--

CREATE TABLE `ordens_compra` (
  `id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `ordens_compra`
--

INSERT INTO `ordens_compra` (`id`, `produto_id`, `quantidade`, `data_criacao`) VALUES
(1, 79, 0, '2025-04-08 18:22:46'),
(2, 82, 0, '2025-04-08 18:22:46'),
(3, 79, 0, '2025-04-08 18:22:55'),
(4, 82, 0, '2025-04-08 18:22:55'),
(5, 79, 0, '2025-04-08 18:22:56'),
(6, 82, 0, '2025-04-08 18:22:56'),
(7, 79, 0, '2025-04-08 18:22:57'),
(8, 82, 0, '2025-04-08 18:22:57'),
(9, 79, 0, '2025-04-08 18:22:59'),
(10, 82, 0, '2025-04-08 18:22:59'),
(11, 79, 0, '2025-04-08 18:23:14'),
(12, 82, 0, '2025-04-08 18:23:14'),
(13, 79, 0, '2025-04-08 18:27:29'),
(14, 82, 0, '2025-04-08 18:27:30'),
(15, 79, 0, '2025-04-08 18:27:33'),
(16, 82, 0, '2025-04-08 18:27:33'),
(17, 79, 0, '2025-04-08 18:27:45'),
(18, 82, 0, '2025-04-08 18:27:45'),
(19, 79, 0, '2025-04-08 18:27:46'),
(20, 82, 0, '2025-04-08 18:27:46'),
(21, 79, 0, '2025-04-08 18:27:49'),
(22, 82, 0, '2025-04-08 18:27:49'),
(23, 79, 0, '2025-04-08 18:47:07'),
(24, 82, 5, '2025-04-08 18:47:07'),
(25, 79, 0, '2025-04-08 18:47:11'),
(26, 82, 5, '2025-04-08 18:47:11'),
(27, 79, 0, '2025-04-08 18:47:26'),
(28, 82, 5, '2025-04-08 18:47:26'),
(29, 79, 0, '0000-00-00 00:00:00'),
(30, 82, 5, '0000-00-00 00:00:00'),
(31, 79, 0, '0000-00-00 00:00:00'),
(32, 82, 5, '0000-00-00 00:00:00'),
(33, 79, 0, '0000-00-00 00:00:00'),
(34, 82, 5, '0000-00-00 00:00:00'),
(35, 79, 0, '0000-00-00 00:00:00'),
(36, 82, 5, '0000-00-00 00:00:00'),
(37, 79, 0, '0000-00-00 00:00:00'),
(38, 82, 5, '0000-00-00 00:00:00'),
(39, 79, 0, '0000-00-00 00:00:00'),
(40, 82, 5, '0000-00-00 00:00:00'),
(41, 79, 0, '0000-00-00 00:00:00'),
(42, 82, 5, '0000-00-00 00:00:00'),
(43, 79, 0, '2025-04-08 18:51:56'),
(44, 82, 5, '2025-04-08 18:51:56'),
(45, 79, 0, '2025-04-08 18:51:58'),
(46, 82, 5, '2025-04-08 18:51:58'),
(47, 79, 0, '2025-04-08 18:51:59'),
(48, 82, 5, '2025-04-08 18:51:59'),
(49, 79, 0, '2025-04-08 18:51:59'),
(50, 82, 5, '2025-04-08 18:51:59'),
(51, 79, 0, '2025-04-08 18:51:59'),
(52, 82, 5, '2025-04-08 18:51:59'),
(53, 79, 0, '2025-04-08 18:52:00'),
(54, 82, 5, '2025-04-08 18:52:00'),
(55, 79, 0, '2025-04-08 18:52:03'),
(56, 82, 5, '2025-04-08 18:52:03'),
(57, 79, 0, '2025-04-08 18:52:22'),
(58, 82, 5, '2025-04-08 18:52:22'),
(59, 79, 0, '2025-04-08 18:52:25'),
(60, 82, 5, '2025-04-08 18:52:25'),
(61, 79, 0, '2025-04-08 18:52:59'),
(62, 82, 5, '2025-04-08 18:52:59'),
(63, 2, 5, '2025-04-27 20:39:40'),
(64, 2, 5, '2025-05-06 21:16:44'),
(65, 2, 5, '2025-05-06 21:21:03'),
(66, 2, 5, '2025-05-15 21:21:04'),
(67, 2, 5, '2025-05-19 17:37:50'),
(68, 2, 5, '2025-05-19 18:56:09');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pagamentos`
--

CREATE TABLE `pagamentos` (
  `id` int(11) NOT NULL,
  `contrato_titulo` varchar(255) NOT NULL,
  `data_pagamento` date NOT NULL,
  `valor_contrato` decimal(15,2) NOT NULL,
  `mes` varchar(20) DEFAULT NULL,
  `empenho` varchar(50) DEFAULT NULL,
  `nota_empenho` varchar(50) DEFAULT NULL,
  `valor_liquidado` decimal(15,2) DEFAULT NULL,
  `ordem_bancaria` varchar(50) DEFAULT NULL,
  `data_atualizacao` date DEFAULT NULL,
  `envio_pagamento` date DEFAULT NULL,
  `vencimento_fatura` date DEFAULT NULL,
  `agencia_bancaria` varchar(50) DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `SEI` varchar(50) DEFAULT NULL,
  `nota_fiscal` varchar(50) DEFAULT NULL,
  `creditos_ativos` varchar(50) DEFAULT NULL,
  `valor_liquidado_ag` decimal(15,2) DEFAULT NULL,
  `fonte` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `painel_config`
--

CREATE TABLE `painel_config` (
  `id` int(11) NOT NULL,
  `painelalmoxarifado` tinyint(1) DEFAULT 1,
  `painelfinanceiro` tinyint(1) DEFAULT 1,
  `painelrh` tinyint(1) DEFAULT 1
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
(1, '600428000012477', 'teste12', 0.00, '2024-11-21', 'Em Processo de baixa', 'sala 703', '2024-11-21 05:08:43', '600428000012477', 'CLAUDIA', 'CLAUDIA', NULL, NULL, 'Transferido', NULL),
(2, '600428000012478', 'NOTEBOOK ', 0.00, '2024-11-23', 'Em Processo de baixa', 'SALA - 404', '2024-11-24 00:18:39', '600428000012478', 'null', NULL, NULL, NULL, '', NULL),
(3, '600428000012479', 'NOTEBOOK ', 0.00, '2024-11-23', 'Em Processo de baixa', 'SALA - 404', '2024-11-24 00:20:17', '600428000012479', 'null', NULL, NULL, NULL, '', 'GABRIEL'),
(4, 'teste', 'teste', 12.00, '2024-11-23', NULL, '202', '2024-11-24 00:21:54', '705100000000196', 'bens_achados', 'GABRIEL', NULL, NULL, '', NULL),
(5, 'teste', 'teste', 780.00, '2024-11-23', 'ativo', 'sala 703', '2024-11-24 00:26:27', '450518000002335', 'moveis_utensilios', 'GABRIEL', NULL, NULL, '', NULL),
(6, 'NOTEBOOK ', 'teste', 90.00, '2024-11-23', 'ativo', 'sala 703', '2024-11-24 00:30:13', '460000000000000', 'reserva_bens_moveis', 'GABRIEL', NULL, NULL, '', NULL),
(7, 'teste120', 'teste', 6.00, '2024-11-23', 'ativo', 'sala 703', '2024-11-24 00:35:53', '1', 'bens_com_baixo_valor', 'GABRIEL', NULL, NULL, '', NULL),
(8, 'NOTEBOOK ', 'teste', 123.00, '2024-11-24', 'Transferido', '202', '2024-11-24 10:47:44', '600428000012480', 'equipamentos_informatica', 'MASTER', NULL, NULL, '', NULL),
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
(20, 'Monitor', 'Monitor DELL', 1.20, '2025-01-08', 'Em Processo de baixa', 'CENTRAL', '2025-01-08 13:41:03', '600428000012490', 'equipamentos_informatica', 'MASTER', NULL, NULL, '', 'default.png'),
(21, 'TESTE', 'yrdyr', 15.00, '2025-05-27', 'ativo', 'yrdyr', '2025-05-27 13:51:52', '705100000000198', 'bens_achados', 'MASTER', NULL, NULL, '', 'patrimonio-6835c378bc1b1.png'),
(22, 'MONITOR', 'teste', 120950.00, '2025-06-19', 'ativo', 'SALA 501', '2025-06-11 19:00:42', '600428000012491', 'equipamentos_informatica', 'MASTER', NULL, NULL, '', 'default.png'),
(23, 'MONITOR', 'w4re', 150.00, '2025-06-28', 'inativo', 'SALA 501', '2025-06-11 19:03:28', '450518000002337', 'moveis_utensilios', 'MASTER', NULL, NULL, '', 'default.png'),
(24, 'SOLICITAR NOVA GUIA ', '23', 2323.00, '2025-06-12', 'em uso', 'SALA 501', '2025-06-12 13:15:01', '2', 'bens_com_baixo_valor', 'GABRIEL', NULL, NULL, '', 'default.png'),
(25, 'NOTEBOOK ', 'teste', 5340.00, '2025-06-17', 'ativo', 'sala 703', '2025-06-12 13:15:41', '450518000002338', 'moveis_utensilios', 'GABRIEL', NULL, NULL, '', 'default.png'),
(26, 'MONITOR', 'lg', 500.00, '2025-06-12', 'ativo', '202', '2025-06-12 13:20:36', '705100000000199', 'bens_achados', 'CLAUDIA', NULL, NULL, '', 'default.png');

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
-- Estrutura para tabela `planejamento`
--

CREATE TABLE `planejamento` (
  `id` int(11) NOT NULL,
  `titulo_oportunidade` varchar(255) NOT NULL,
  `setor` varchar(100) NOT NULL,
  `valor_estimado` decimal(15,2) NOT NULL DEFAULT 0.00,
  `prazo` date NOT NULL,
  `status` varchar(50) NOT NULL,
  `descricao` text DEFAULT NULL,
  `project_plan` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`project_plan`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `pe_code` varchar(220) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `planejamento`
--

INSERT INTO `planejamento` (`id`, `titulo_oportunidade`, `setor`, `valor_estimado`, `prazo`, `status`, `descricao`, `project_plan`, `created_at`, `pe_code`) VALUES
(14, 'Reforma da Estação Carioca', 'bondes', 0.00, '2025-08-01', 'finalizado', '', '[{\"name\":\"Operacionaliza\\u00e7\\u00e3o do Sistema de Bondes\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Reativa\\u00e7\\u00e3o de Linhas\",\"completed\":false}],\"expanded\":true},{\"name\":\"Recupera\\u00e7\\u00e3o do Sistema de Bondes\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Opera\\u00e7\\u00e3o do Bonde\",\"completed\":false}],\"expanded\":true}]', '2025-08-04 22:17:51', ''),
(15, 'Aquisição de 6 Bondes', 'bondes', 0.00, '2025-08-01', 'finalizado', 'Aquisição de 6 Bondes', '[{\"name\":\"Operacionaliza\\u00e7\\u00e3o do Sistema de Bondes\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Reativa\\u00e7\\u00e3o de Linhas\",\"completed\":false}],\"expanded\":true},{\"name\":\"Recupera\\u00e7\\u00e3o do Sistema de Bondes\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Opera\\u00e7\\u00e3o do Bonde\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 21:28:57', ''),
(16, 'Reativação do Ramal Paula Mattos/Silvestre', 'bondes', 70880625.93, '2025-08-08', 'finalizado', '', '[{\"name\":\"Operacionaliza\\u00e7\\u00e3o do Sistema de Bondes\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Reativa\\u00e7\\u00e3o de Linhas\",\"completed\":false}],\"expanded\":true},{\"name\":\"Recupera\\u00e7\\u00e3o do Sistema de Bondes\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Opera\\u00e7\\u00e3o do Bonde\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:15:41', ''),
(17, 'Aquisição de Materiais Diversos - (Reforma bonde 16, 17 e 18)', 'bondes', 2510316.88, '2025-07-24', 'finalizado', '', '[{\"name\":\"Operacionaliza\\u00e7\\u00e3o do Sistema de Bondes\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Reativa\\u00e7\\u00e3o de Linhas\",\"completed\":false}],\"expanded\":true},{\"name\":\"Recupera\\u00e7\\u00e3o do Sistema de Bondes\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Opera\\u00e7\\u00e3o do Bonde\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 21:55:19', ''),
(18, 'Sistema GPS Bonde', 'bondes', 7680.00, '2025-08-09', 'finalizado', 'Sistema GPS Bonde', '[{\"name\":\"Operacionaliza\\u00e7\\u00e3o do Sistema de Bondes\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Reativa\\u00e7\\u00e3o de Linhas\",\"completed\":false}],\"expanded\":true},{\"name\":\"Recupera\\u00e7\\u00e3o do Sistema de Bondes\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Opera\\u00e7\\u00e3o do Bonde\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:18:40', ''),
(19, 'Circuito fechado de TV (CFTV)', 'bondes', 0.00, '2025-08-08', 'finalizado', 'SEI-100006/001486/2023', '[{\"name\":\"Operacionaliza\\u00e7\\u00e3o do Sistema de Bondes\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Reativa\\u00e7\\u00e3o de Linhas\",\"completed\":false}],\"expanded\":true},{\"name\":\"Recupera\\u00e7\\u00e3o do Sistema de Bondes\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Opera\\u00e7\\u00e3o do Bonde\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:19:11', ''),
(20, 'Circuito fechado de TV (CFTV)', 'bondes', 0.00, '2025-08-08', 'finalizado', 'SEI-100006/001486/2023', '[{\"name\":\"Operacionaliza\\u00e7\\u00e3o do Sistema de Bondes\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Reativa\\u00e7\\u00e3o de Linhas\",\"completed\":false}],\"expanded\":true},{\"name\":\"Recupera\\u00e7\\u00e3o do Sistema de Bondes\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Opera\\u00e7\\u00e3o do Bonde\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:19:11', ''),
(21, 'Implantação do controle da manutenção do Sistema de Bondes Santa Teresa.', 'bondes', 0.00, '2025-08-08', 'andamento', 'Implantação do controle da manutenção do Sistema de Bondes Santa Teresa.', '[{\"name\":\"Operacionaliza\\u00e7\\u00e3o do Sistema de Bondes\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Reativa\\u00e7\\u00e3o de Linhas\",\"completed\":false}],\"expanded\":true},{\"name\":\"Recupera\\u00e7\\u00e3o do Sistema de Bondes\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Opera\\u00e7\\u00e3o do Bonde\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:19:28', ''),
(22, 'Bilhetagem Eletrônica', 'bondes', 2966440.00, '2025-08-09', 'andamento', 'Bilhetagem Eletrônica', '[{\"name\":\"Operacionaliza\\u00e7\\u00e3o do Sistema de Bondes\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Reativa\\u00e7\\u00e3o de Linhas\",\"completed\":false}],\"expanded\":true},{\"name\":\"Recupera\\u00e7\\u00e3o do Sistema de Bondes\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Opera\\u00e7\\u00e3o do Bonde\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:19:46', ''),
(23, 'Aquisição de veículo rodoferroviário para serviços auxiliares de manutenção de via permanente; material rodante e elementos de sinalização; e equipamentos para manutenção dos bondes', 'bondes', 0.00, '2025-08-02', 'andamento', 'Aquisição de veículo rodoferroviário para serviços auxiliares de manutenção de via permanente; material rodante e elementos de sinalização; e equipamentos para manutenção dos bondes', '[{\"name\":\"Operacionaliza\\u00e7\\u00e3o do Sistema de Bondes\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Reativa\\u00e7\\u00e3o de Linhas\",\"completed\":false}],\"expanded\":true},{\"name\":\"Recupera\\u00e7\\u00e3o do Sistema de Bondes\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Opera\\u00e7\\u00e3o do Bonde\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:20:01', ''),
(24, 'Aquisição de 6 (Seis) VLT', 'ferrovia', 163935040.38, '2025-08-09', 'finalizado', 'Aquisição de 6 (Seis) VLT', '[{\"name\":\"Projetos de Engenharia\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Planejamento e Execu\\u00e7\\u00e3o\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:20:29', ''),
(25, 'Contratar empresa especializada para a execução de obra de modernização da ligação Saracuruna / Guapimirim – VLT.', 'ferrovia', 0.00, '2025-08-07', 'finalizado', 'Contratar empresa especializada para a execução de obra de modernização da ligação Saracuruna / Guapimirim – VLT.', '[{\"name\":\"Projetos de Engenharia\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Planejamento e Execu\\u00e7\\u00e3o\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:20:43', ''),
(26, 'Contratar empresa especializada para a execução de obras para a modernização da ligação Saracuruna/Vila Inhomirim, via VLT.', 'ferrovia', 0.00, '2025-08-02', 'finalizado', 'Contratar empresa especializada para a execução de obras para a modernização da ligação Saracuruna/Vila Inhomirim, via VLT.', '[{\"name\":\"Projetos de Engenharia\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Planejamento e Execu\\u00e7\\u00e3o\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:20:59', ''),
(27, 'Contratar empresa especializada para a execução de obra de construção de passarelas e muros na via férrea de transporte urbano.', 'ferrovia', 0.00, '2025-08-08', 'andamento', 'Contratar empresa especializada para a execução de obra de construção de passarelas e muros na via férrea de transporte urbano.', '[{\"name\":\"Projetos de Engenharia\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Planejamento e Execu\\u00e7\\u00e3o\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:21:14', ''),
(28, 'Elaborar Termo de Referência e os custos estimados das obras para contratação de empresa para execução de obras cujo objeto é a construção da 2ª Via eletrificada entre as estações de Gramacho e Saracuruna, com a extensão total de 10,6 km.', 'ferrovia', 0.00, '2025-07-30', 'planejamento', 'Elaborar Termo de Referência e os custos estimados das obras para contratação de empresa para execução de obras cujo objeto é a construção da 2ª Via eletrificada entre as estações de Gramacho e Saracuruna, com a extensão total de 10,6 km.', '[{\"name\":\"Projetos de Engenharia\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Planejamento e Execu\\u00e7\\u00e3o\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:21:27', ''),
(29, 'Elaborar Termo de Referência e os custos estimados das obras para contratação de empresa para execução de obras para a construção do 2º circuito de 4,4kV (sinalização) entre a Subestação de Benfica e Saracuruna, com extensão total de 29 km.', 'ferrovia', 0.00, '2025-08-08', 'finalizado', 'Elaborar Termo de Referência e os custos estimados das obras para contratação de empresa para execução de obras para a construção do 2º circuito de 4,4kV (sinalização) entre a Subestação de Benfica e Saracuruna, com extensão total de 29 km.', '[{\"name\":\"Projetos de Engenharia\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Planejamento e Execu\\u00e7\\u00e3o\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:21:47', ''),
(30, 'Elaborar Termo de Referência e os custos estimados das obras para contratação de empresa para execução de obras para a construção do 2º circuito de 44kv (linha de transmissão) entre a Subestação de Benfica e a Subestação de Gramacho, com extensão de 19 km', 'ferrovia', 0.00, '2025-08-08', 'planejamento', 'Elaborar Termo de Referência e os custos estimados das obras para contratação de empresa para execução de obras para a construção do 2º circuito de 44kv (linha de transmissão) entre a Subestação de Benfica e a Subestação de Gramacho, com extensão de 19 km.', '[{\"name\":\"Projetos de Engenharia\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Planejamento e Execu\\u00e7\\u00e3o\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:22:01', ''),
(31, 'Elaborar Termo de Referência e os custos estimados das obras para contratação de empresa para execução de obras para a construção do 2º circuito de 4,4kV (sinalização) entre a Subestação de Benfica e Belford Roxo, com extensão total de 28 km.', 'ferrovia', 0.00, '2025-08-07', 'planejamento', 'Elaborar Termo de Referência e os custos estimados das obras para contratação de empresa para execução de obras para a construção do 2º circuito de 4,4kV (sinalização) entre a Subestação de Benfica e Belford Roxo, com extensão total de 28 km.', '[{\"name\":\"Projetos de Engenharia\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Planejamento e Execu\\u00e7\\u00e3o\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:26:01', ''),
(32, 'Ampliar a Acessibilidade nas Estações Ferroviárias.', 'ferrovia', 0.00, '2025-08-08', 'planejamento', 'Ampliar a Acessibilidade nas Estações Ferroviárias.', '[{\"name\":\"Projetos de Engenharia\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Planejamento e Execu\\u00e7\\u00e3o\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:26:42', ''),
(33, 'Consultoria CTPM', 'ferrovia', 0.00, '2025-08-07', 'andamento', 'Consultoria CTPM', '[{\"name\":\"Projetos de Engenharia\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Planejamento e Execu\\u00e7\\u00e3o\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:26:58', ''),
(34, 'Ano 2023 - Término das obras civis (execução pela SEIC) - 2025', 'teleferico', 0.00, '2025-08-01', 'finalizado', 'Ano 2023 - Término das obras civis (execução pela SEIC) - 2025					\nFinalizada pela SEIOP', '[{\"name\":\"Reativa\\u00e7\\u00e3o e Operacionaliza\\u00e7\\u00e3o\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Reativa\\u00e7\\u00e3o do Sistema de Transporte do Telef\\u00e9rico do Alem\\u00e3o\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:27:23', ''),
(35, 'Vigilância desarmada para o teleférico e bonde de santa teresa (2025)', 'teleferico', 2982285.00, '2025-08-07', 'andamento', 'Vigilância desarmada para o teleférico e bonde de santa teresa (2025)', '[{\"name\":\"Reativa\\u00e7\\u00e3o e Operacionaliza\\u00e7\\u00e3o\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Reativa\\u00e7\\u00e3o do Sistema de Transporte do Telef\\u00e9rico do Alem\\u00e3o\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:27:41', ''),
(36, 'Aquisição de licença permanente do CAD com previsão de treinamento para os funcionários da DIREO.', 'ti', 100000.00, '2025-08-08', 'finalizado', 'Aquisição de licença permanente do CAD com previsão de treinamento para os funcionários da DIREO.', '[{\"name\":\"Moderniza\\u00e7\\u00e3o\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Moderniza\\u00e7\\u00e3o do parque computacional (desktops e notebooks)\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:28:23', ''),
(37, 'Aquisição de Switches gerenciáveis de 24p ou de 48p.', 'ti', 600000.00, '2025-08-01', 'planejamento', 'Aquisição de Switches gerenciáveis de 24p ou de 48p.', '[{\"name\":\"Moderniza\\u00e7\\u00e3o\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Moderniza\\u00e7\\u00e3o do parque computacional (desktops e notebooks)\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:28:56', ''),
(38, 'Aquisição de Desktops.', 'ti', 97350.00, '2025-08-07', 'andamento', 'Aquisição de Desktops.', '[{\"name\":\"Moderniza\\u00e7\\u00e3o\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Moderniza\\u00e7\\u00e3o do parque computacional (desktops e notebooks)\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:29:23', ''),
(39, 'Aquisição de Estabilizadores e No-Breaks', 'ti', 35242.00, '2025-08-08', 'andamento', 'Aquisição de Estabilizadores e No-Breaks', '[{\"name\":\"Moderniza\\u00e7\\u00e3o\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Moderniza\\u00e7\\u00e3o do parque computacional (desktops e notebooks)\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:29:48', ''),
(40, 'Aquisição de  Monitores 23.8', 'ti', 0.00, '2025-08-06', 'andamento', 'oação do Rio Previdência para a CENTRAL. 20 desktops e 20 monitores', '[{\"name\":\"Solu\\u00e7\\u00f5es de Atendimento\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Solu\\u00e7\\u00e3o de Atendimento ao Usu\\u00e1rio \\u2013 ITSM\",\"completed\":false},{\"name\":\"Wi-Fi corporativo com seguran\\u00e7a e gerenciamento\",\"completed\":false}],\"expanded\":true},{\"name\":\"Monitoramento\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Monitoramento e Observabilidade da Infraestrutura\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:31:08', ''),
(41, 'Aquisição de solução corporativa com gerenciamento e segurança, 8 (oito) Aps - (SEDE e BONDE)', 'ti', 0.00, '2025-08-08', 'planejamento', 'Aquisição de solução corporativa com gerenciamento e segurança, 8 (oito) Aps - (SEDE e BONDE)', '[{\"name\":\"Moderniza\\u00e7\\u00e3o\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Moderniza\\u00e7\\u00e3o do parque computacional (desktops e notebooks)\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:31:40', ''),
(42, 'Solução de Atendimento ao Usuário – ITSM (Usado para chamado de suporte da TI)', 'ti', 0.00, '2025-08-09', 'finalizado', 'Gratuito', '[{\"name\":\"Moderniza\\u00e7\\u00e3o\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Moderniza\\u00e7\\u00e3o do parque computacional (desktops e notebooks)\",\"completed\":false}],\"expanded\":true},{\"name\":\"Conectividade\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Contrata\\u00e7\\u00e3o de link de internet dedicado\",\"completed\":false},{\"name\":\"Aquisi\\u00e7\\u00e3o de softwares diversos\",\"completed\":false},{\"name\":\"Servi\\u00e7o de cabeamento estruturado\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:32:20', ''),
(43, 'Serviço de Hospedagem de Mensageria Eletrônica (e-mail) – renovação de contrato com PRODERJ', 'ti', 102600.00, '2025-08-07', 'finalizado', 'Serviço de Hospedagem de Mensageria Eletrônica (e-mail) – renovação de contrato com PRODERJ', '[{\"name\":\"Moderniza\\u00e7\\u00e3o\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Moderniza\\u00e7\\u00e3o do parque computacional (desktops e notebooks)\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:32:42', ''),
(44, 'Serviço de Hospedagem em servidores Virtuais Privados (VPS) – renovação de contrato com PRODERJ', 'ti', 112310.64, '2025-08-07', 'planejamento', 'Serviço de Hospedagem em servidores Virtuais Privados (VPS) – renovação de contrato com PRODERJ', '[{\"name\":\"Infraestrutura de TI\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Estruturar o banco de dados dos projetos de engenharia\",\"completed\":false},{\"name\":\"Aquisi\\u00e7\\u00e3o de equipamentos e softwares\",\"completed\":false},{\"name\":\"Implanta\\u00e7\\u00e3o da tecnologia BIM na CENTRAL e capacitar seus usu\\u00e1rios\",\"completed\":false},{\"name\":\"Reestrutura\\u00e7\\u00e3o de toda Infraestrutura de Rede com Seguran\\u00e7a\",\"completed\":false}],\"expanded\":true},{\"name\":\"Moderniza\\u00e7\\u00e3o\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Moderniza\\u00e7\\u00e3o do parque computacional (desktops e notebooks)\",\"completed\":false}],\"expanded\":true},{\"name\":\"Solu\\u00e7\\u00f5es de Atendimento\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Solu\\u00e7\\u00e3o de Atendimento ao Usu\\u00e1rio \\u2013 ITSM\",\"completed\":false},{\"name\":\"Wi-Fi corporativo com seguran\\u00e7a e gerenciamento\",\"completed\":false}],\"expanded\":true},{\"name\":\"Monitoramento\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Monitoramento e Observabilidade da Infraestrutura\",\"completed\":false}],\"expanded\":true},{\"name\":\"Conectividade\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Contrata\\u00e7\\u00e3o de link de internet dedicado\",\"completed\":false},{\"name\":\"Aquisi\\u00e7\\u00e3o de softwares diversos\",\"completed\":false},{\"name\":\"Servi\\u00e7o de cabeamento estruturado\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:33:09', ''),
(45, 'Serviço de Hospedagem em servidores Virtuais Privados (VPS) – renovação de contrato com PRODERJ', 'ti', 112310.64, '2025-08-07', 'planejamento', 'Serviço de Hospedagem em servidores Virtuais Privados (VPS) – renovação de contrato com PRODERJ', '[{\"name\":\"Moderniza\\u00e7\\u00e3o\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Moderniza\\u00e7\\u00e3o do parque computacional (desktops e notebooks)\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:33:09', ''),
(46, 'Link de internet dedicado (Bonde) – CLARO', 'ti', 0.00, '2025-08-08', 'planejamento', 'Link de internet dedicado (Bonde) – CLARO', '[{\"name\":\"Moderniza\\u00e7\\u00e3o\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Moderniza\\u00e7\\u00e3o do parque computacional (desktops e notebooks)\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:33:35', ''),
(47, 'Link de internet dedicado 100 (Copacabana) - CLARO', 'ti', 0.00, '2025-08-09', 'planejamento', 'Link de internet dedicado 100 (Copacabana) - CLARO', '[{\"name\":\"Moderniza\\u00e7\\u00e3o\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Moderniza\\u00e7\\u00e3o do parque computacional (desktops e notebooks)\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:33:47', ''),
(48, 'Aquisição de Anti-Vírus', 'ti', 0.00, '2025-08-08', 'planejamento', 'Aquisição de Anti-Vírus', '[{\"name\":\"Moderniza\\u00e7\\u00e3o\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Moderniza\\u00e7\\u00e3o do parque computacional (desktops e notebooks)\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:34:03', ''),
(49, 'Aquisição de Sistema de Almoxarifado (O Sistema foi desenvolvido pela equipe da TI, faltando somente a chancela do PRODERJ)', 'ti', 0.00, '2025-08-09', 'planejamento', 'Falta só a chancela obrigatória do PRODERJ', '[{\"name\":\"Moderniza\\u00e7\\u00e3o\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Moderniza\\u00e7\\u00e3o do parque computacional (desktops e notebooks)\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:34:20', ''),
(50, 'Aquisição de Sistema de Folha de Pagamento e Estoque/Patrimônio', 'ti', 0.00, '2025-08-09', 'planejamento', 'Aquisição de Sistema de Folha de Pagamento e Estoque/Patrimônio					\nSEI-100006/000729/2023', '[{\"name\":\"Moderniza\\u00e7\\u00e3o\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Moderniza\\u00e7\\u00e3o do parque computacional (desktops e notebooks)\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:34:42', ''),
(51, 'Aquisição de Sistema de Contas a Pagar e Acompanhamento de Contratos', 'ti', 0.00, '2025-08-08', 'planejamento', 'Aquisição de Sistema de Contas a Pagar e Acompanhamento de Contratos', '[{\"name\":\"Moderniza\\u00e7\\u00e3o\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Moderniza\\u00e7\\u00e3o do parque computacional (desktops e notebooks)\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:35:25', ''),
(52, 'Aquisição de licença anual de Softwares para elaboração de textos, planilhas e Apresentações para 180 usuários', 'ti', 200000.00, '2025-08-09', 'planejamento', 'Aquisição de licença anual de Softwares para elaboração de textos, planilhas e Apresentações para 180 usuários', '[{\"name\":\"Infraestrutura de TI\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Estruturar o banco de dados dos projetos de engenharia\",\"completed\":false},{\"name\":\"Aquisi\\u00e7\\u00e3o de equipamentos e softwares\",\"completed\":false},{\"name\":\"Implanta\\u00e7\\u00e3o da tecnologia BIM na CENTRAL e capacitar seus usu\\u00e1rios\",\"completed\":false},{\"name\":\"Reestrutura\\u00e7\\u00e3o de toda Infraestrutura de Rede com Seguran\\u00e7a\",\"completed\":false}],\"expanded\":true},{\"name\":\"Moderniza\\u00e7\\u00e3o\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Moderniza\\u00e7\\u00e3o do parque computacional (desktops e notebooks)\",\"completed\":false}],\"expanded\":true},{\"name\":\"Solu\\u00e7\\u00f5es de Atendimento\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Solu\\u00e7\\u00e3o de Atendimento ao Usu\\u00e1rio \\u2013 ITSM\",\"completed\":false},{\"name\":\"Wi-Fi corporativo com seguran\\u00e7a e gerenciamento\",\"completed\":false}],\"expanded\":true},{\"name\":\"Monitoramento\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Monitoramento e Observabilidade da Infraestrutura\",\"completed\":false}],\"expanded\":true},{\"name\":\"Conectividade\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Contrata\\u00e7\\u00e3o de link de internet dedicado\",\"completed\":false},{\"name\":\"Aquisi\\u00e7\\u00e3o de softwares diversos\",\"completed\":false},{\"name\":\"Servi\\u00e7o de cabeamento estruturado\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:35:47', ''),
(53, 'Aquisição do software ArcGis', 'ti', 120000.00, '2025-08-07', 'planejamento', 'Aquisição do software ArcGis', '[{\"name\":\"Infraestrutura de TI\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Estruturar o banco de dados dos projetos de engenharia\",\"completed\":false},{\"name\":\"Aquisi\\u00e7\\u00e3o de equipamentos e softwares\",\"completed\":false},{\"name\":\"Implanta\\u00e7\\u00e3o da tecnologia BIM na CENTRAL e capacitar seus usu\\u00e1rios\",\"completed\":false},{\"name\":\"Reestrutura\\u00e7\\u00e3o de toda Infraestrutura de Rede com Seguran\\u00e7a\",\"completed\":false}],\"expanded\":true},{\"name\":\"Moderniza\\u00e7\\u00e3o\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Moderniza\\u00e7\\u00e3o do parque computacional (desktops e notebooks)\",\"completed\":false}],\"expanded\":true},{\"name\":\"Solu\\u00e7\\u00f5es de Atendimento\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Solu\\u00e7\\u00e3o de Atendimento ao Usu\\u00e1rio \\u2013 ITSM\",\"completed\":false},{\"name\":\"Wi-Fi corporativo com seguran\\u00e7a e gerenciamento\",\"completed\":false}],\"expanded\":true},{\"name\":\"Monitoramento\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Monitoramento e Observabilidade da Infraestrutura\",\"completed\":false}],\"expanded\":true},{\"name\":\"Conectividade\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Contrata\\u00e7\\u00e3o de link de internet dedicado\",\"completed\":false},{\"name\":\"Aquisi\\u00e7\\u00e3o de softwares diversos\",\"completed\":false},{\"name\":\"Servi\\u00e7o de cabeamento estruturado\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:36:08', ''),
(54, 'Serviço de Cabeamento Estruturado e Certificado', 'ti', 600000.00, '2025-08-01', 'planejamento', '', '[{\"name\":\"Infraestrutura de TI\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Estruturar o banco de dados dos projetos de engenharia\",\"completed\":false},{\"name\":\"Aquisi\\u00e7\\u00e3o de equipamentos e softwares\",\"completed\":false},{\"name\":\"Implanta\\u00e7\\u00e3o da tecnologia BIM na CENTRAL e capacitar seus usu\\u00e1rios\",\"completed\":false},{\"name\":\"Reestrutura\\u00e7\\u00e3o de toda Infraestrutura de Rede com Seguran\\u00e7a\",\"completed\":false}],\"expanded\":true},{\"name\":\"Moderniza\\u00e7\\u00e3o\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Moderniza\\u00e7\\u00e3o do parque computacional (desktops e notebooks)\",\"completed\":false}],\"expanded\":true},{\"name\":\"Solu\\u00e7\\u00f5es de Atendimento\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Solu\\u00e7\\u00e3o de Atendimento ao Usu\\u00e1rio \\u2013 ITSM\",\"completed\":false},{\"name\":\"Wi-Fi corporativo com seguran\\u00e7a e gerenciamento\",\"completed\":false}],\"expanded\":true},{\"name\":\"Monitoramento\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Monitoramento e Observabilidade da Infraestrutura\",\"completed\":false}],\"expanded\":true},{\"name\":\"Conectividade\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Contrata\\u00e7\\u00e3o de link de internet dedicado\",\"completed\":false},{\"name\":\"Aquisi\\u00e7\\u00e3o de softwares diversos\",\"completed\":false},{\"name\":\"Servi\\u00e7o de cabeamento estruturado\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:40:23', ''),
(55, 'Capacitação dos empregados da GERGEP nos softwares de edição de texto e planilhas.', 'capacitacao', 0.00, '2025-08-09', 'finalizado', 'Capacitação dos empregados da GERGEP nos softwares de edição de texto e planilhas.', '[{\"name\":\"Capacita\\u00e7\\u00e3o Geral\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Capacita\\u00e7\\u00e3o dos empregados da GERGEP nos softwares de edi\\u00e7\\u00e3o de texto e planilhas\",\"completed\":false},{\"name\":\"Capacita\\u00e7\\u00e3o dos empregados da GERGEP no tratamento do arquivamento de documentos\",\"completed\":false},{\"name\":\"Forma\\u00e7\\u00e3o e capacita\\u00e7\\u00e3o do corpo t\\u00e9cnico em processos de TIC\",\"completed\":false}],\"expanded\":true},{\"name\":\"Treinamentos Espec\\u00edficos\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Treinamentos no Sistema e-Social\",\"completed\":false},{\"name\":\"Treinamentos para \\u00e1rea de Seguran\\u00e7a do Trabalho\",\"completed\":false},{\"name\":\"Treinamentos visando \\u00e0 Educa\\u00e7\\u00e3o Ambiental\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:42:11', ''),
(56, 'Contratação de cursos para capacitação dos empregados da GERGEP, pertinentes ao trabalho realizado na gerência.', 'capacitacao', 0.00, '2025-08-07', 'andamento', 'Contratação de cursos para capacitação dos empregados da GERGEP, pertinentes ao trabalho realizado na gerência.', '[{\"name\":\"Treinamentos Espec\\u00edficos\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Treinamentos no Sistema e-Social\",\"completed\":false},{\"name\":\"Treinamentos para \\u00e1rea de Seguran\\u00e7a do Trabalho\",\"completed\":false},{\"name\":\"Treinamentos visando \\u00e0 Educa\\u00e7\\u00e3o Ambiental\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:42:29', ''),
(57, 'Treinamento no software CAD', 'capacitacao', 0.00, '2025-08-07', 'andamento', 'Treinamento no software CAD', '[{\"name\":\"Capacita\\u00e7\\u00e3o Geral\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Capacita\\u00e7\\u00e3o dos empregados da GERGEP nos softwares de edi\\u00e7\\u00e3o de texto e planilhas\",\"completed\":false},{\"name\":\"Capacita\\u00e7\\u00e3o dos empregados da GERGEP no tratamento do arquivamento de documentos\",\"completed\":false},{\"name\":\"Forma\\u00e7\\u00e3o e capacita\\u00e7\\u00e3o do corpo t\\u00e9cnico em processos de TIC\",\"completed\":false}],\"expanded\":true},{\"name\":\"Treinamentos Espec\\u00edficos\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Treinamentos no Sistema e-Social\",\"completed\":false},{\"name\":\"Treinamentos para \\u00e1rea de Seguran\\u00e7a do Trabalho\",\"completed\":false},{\"name\":\"Treinamentos visando \\u00e0 Educa\\u00e7\\u00e3o Ambiental\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:42:48', ''),
(58, 'Treinamento no software BIM', 'capacitacao', 0.00, '2025-08-08', 'andamento', 'Treinamento no software BIM', '[{\"name\":\"Capacita\\u00e7\\u00e3o Geral\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Capacita\\u00e7\\u00e3o dos empregados da GERGEP nos softwares de edi\\u00e7\\u00e3o de texto e planilhas\",\"completed\":false},{\"name\":\"Capacita\\u00e7\\u00e3o dos empregados da GERGEP no tratamento do arquivamento de documentos\",\"completed\":false},{\"name\":\"Forma\\u00e7\\u00e3o e capacita\\u00e7\\u00e3o do corpo t\\u00e9cnico em processos de TIC\",\"completed\":false}],\"expanded\":true},{\"name\":\"Treinamentos Espec\\u00edficos\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Treinamentos no Sistema e-Social\",\"completed\":false},{\"name\":\"Treinamentos para \\u00e1rea de Seguran\\u00e7a do Trabalho\",\"completed\":false},{\"name\":\"Treinamentos visando \\u00e0 Educa\\u00e7\\u00e3o Ambiental\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:43:05', ''),
(59, 'Treinamento para adequação à LGPD (Lei Geral de Proteção de Dados)', 'capacitacao', 0.00, '2025-08-07', 'andamento', 'Treinamento para adequação à LGPD (Lei Geral de Proteção de Dados)', '[{\"name\":\"Capacita\\u00e7\\u00e3o Geral\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Capacita\\u00e7\\u00e3o dos empregados da GERGEP nos softwares de edi\\u00e7\\u00e3o de texto e planilhas\",\"completed\":false},{\"name\":\"Capacita\\u00e7\\u00e3o dos empregados da GERGEP no tratamento do arquivamento de documentos\",\"completed\":false},{\"name\":\"Forma\\u00e7\\u00e3o e capacita\\u00e7\\u00e3o do corpo t\\u00e9cnico em processos de TIC\",\"completed\":false}],\"expanded\":true},{\"name\":\"Treinamentos Espec\\u00edficos\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Treinamentos no Sistema e-Social\",\"completed\":false},{\"name\":\"Treinamentos para \\u00e1rea de Seguran\\u00e7a do Trabalho\",\"completed\":false},{\"name\":\"Treinamentos visando \\u00e0 Educa\\u00e7\\u00e3o Ambiental\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:43:18', ''),
(60, 'Treinamento para qualificação de pessoal em processos de TIC', 'capacitacao', 0.00, '2025-08-09', 'andamento', 'Treinamento para qualificação de pessoal em processos de TIC', '[{\"name\":\"Capacita\\u00e7\\u00e3o Geral\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Capacita\\u00e7\\u00e3o dos empregados da GERGEP nos softwares de edi\\u00e7\\u00e3o de texto e planilhas\",\"completed\":false},{\"name\":\"Capacita\\u00e7\\u00e3o dos empregados da GERGEP no tratamento do arquivamento de documentos\",\"completed\":false},{\"name\":\"Forma\\u00e7\\u00e3o e capacita\\u00e7\\u00e3o do corpo t\\u00e9cnico em processos de TIC\",\"completed\":false}],\"expanded\":true},{\"name\":\"Treinamentos Espec\\u00edficos\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Treinamentos no Sistema e-Social\",\"completed\":false},{\"name\":\"Treinamentos para \\u00e1rea de Seguran\\u00e7a do Trabalho\",\"completed\":false},{\"name\":\"Treinamentos visando \\u00e0 Educa\\u00e7\\u00e3o Ambiental\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:43:34', ''),
(61, 'Treinamento no sistema do e-Social', 'capacitacao', 0.00, '2025-08-09', 'andamento', 'Treinamento no sistema do e-Social', '[{\"name\":\"Capacita\\u00e7\\u00e3o Geral\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Capacita\\u00e7\\u00e3o dos empregados da GERGEP nos softwares de edi\\u00e7\\u00e3o de texto e planilhas\",\"completed\":false},{\"name\":\"Capacita\\u00e7\\u00e3o dos empregados da GERGEP no tratamento do arquivamento de documentos\",\"completed\":false},{\"name\":\"Forma\\u00e7\\u00e3o e capacita\\u00e7\\u00e3o do corpo t\\u00e9cnico em processos de TIC\",\"completed\":false}],\"expanded\":true},{\"name\":\"Treinamentos Espec\\u00edficos\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Treinamentos no Sistema e-Social\",\"completed\":false},{\"name\":\"Treinamentos para \\u00e1rea de Seguran\\u00e7a do Trabalho\",\"completed\":false},{\"name\":\"Treinamentos visando \\u00e0 Educa\\u00e7\\u00e3o Ambiental\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:45:30', ''),
(62, 'Controle de todos os bens móveis de posse da CENTRAL, por meio da atualização dos sistemas existentes ou contratação/criação de sistemas', 'patrimonio', 0.00, '2025-08-07', 'planejamento', 'Controle de todos os bens móveis de posse da CENTRAL, por meio da atualização dos sistemas existentes ou contratação/criação de sistemas', '[{\"name\":\"Gest\\u00e3o Documental\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Tratamento e digitaliza\\u00e7\\u00e3o de documenta\\u00e7\\u00e3o administrativa e hist\\u00f3rica\",\"completed\":false}],\"expanded\":true},{\"name\":\"Controle de Bens\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Controle de todos os bens m\\u00f3veis e im\\u00f3veis\",\"completed\":false},{\"name\":\"Identifica\\u00e7\\u00e3o dos bens que integram o acervo patrimonial\",\"completed\":false}],\"expanded\":true}]', '2025-07-23 23:45:43', ''),
(68, 'Manutenção da Subestação dos Bondes de Santa Teresa', 'operacionalizacao_bonde', 267574.13, '2025-08-09', 'finalizado', '', '[{\"name\":\"PLANEJAMENTO PCA\",\"responsible\":\"\",\"etapas\":[{\"name\":\"DFD (Doc.de Formaliza\\u00e7\\u00e3o de Demanda)\",\"completed\":false},{\"name\":\"Sistema PCA (Plano de Contrata\\u00e7\\u00f5es Anual)\",\"completed\":false}],\"expanded\":true},{\"name\":\"Fase Preparat\\u00f3ria\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Estudo T\\u00e9cnico Preliminar \\/ Matriz de Risco\",\"completed\":false},{\"name\":\"Termo de Refer\\u00eancia\",\"completed\":false},{\"name\":\"Pesquisa de Pre\\u00e7os \\/ Relat\\u00f3rio Anal\\u00edtico\",\"completed\":false},{\"name\":\"Autoriza\\u00e7\\u00e3o do Ordenador de Despesa\",\"completed\":false},{\"name\":\"DDO - Declara\\u00e7\\u00e3o de Dota\\u00e7\\u00e3o Or\\u00e7ament\\u00e1ria\",\"completed\":false},{\"name\":\"Minuta Edital\\/Contrato\",\"completed\":false},{\"name\":\"Parecer Jur\\u00eddico (ASSJUR)\",\"completed\":false},{\"name\":\"Lan\\u00e7amento do Edital no SIGA\",\"completed\":false}],\"expanded\":true},{\"name\":\"Fase Externa\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Publica\\u00e7\\u00e3o do Aviso de Edital\",\"completed\":false},{\"name\":\"Recibo de envio de Edital ao TCE \\/ Lan\\u00e7amento SIGA\",\"completed\":false},{\"name\":\"Abertura do Preg\\u00e3o \\/ Dispensa de Licita\\u00e7\\u00e3o\",\"completed\":false},{\"name\":\"Adjudica\\u00e7\\u00e3o (Declara\\u00e7\\u00e3o do Vencedor)\",\"completed\":false},{\"name\":\"Compliance (Due Diligence - ASSGER)\",\"completed\":false}],\"expanded\":true}]', '2025-08-01 22:56:26', ''),
(88, 'TE', 'solucoes_tecnologicas', 2.00, '2025-09-04', 'planejamento', 'SDF', '[{\"name\":\"PLANEJAMENTO PCA\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Desenvolvimento de novas solu\\u00e7\\u00f5es tecnol\\u00f3gicas\",\"completed\":false}],\"expanded\":true},{\"name\":\"FASE PREPARAT\\u00d3RIA\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Estudo T\\u00e9cnico Preliminar \\/ Matriz de Risco\",\"completed\":false},{\"name\":\"Termo de Refer\\u00eancia\",\"completed\":false},{\"name\":\"Pesquisa de Pre\\u00e7os \\/ Relat\\u00f3rio Anal\\u00edtico\",\"completed\":false},{\"name\":\"Autoriza\\u00e7\\u00e3o do Ordenador de Despesa\",\"completed\":false},{\"name\":\"DDO - Declara\\u00e7\\u00e3o de Dota\\u00e7\\u00e3o Or\\u00e7ament\\u00e1ria\",\"completed\":false},{\"name\":\"Minuta Edital\\/Contrato\",\"completed\":false},{\"name\":\"Parecer Jur\\u00eddico (ASSJUR)\",\"completed\":false},{\"name\":\"Lan\\u00e7amento do Edital no SIGA\",\"completed\":false}],\"expanded\":true},{\"name\":\"FASE EXTERNA\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Recibo de envio de Edital ao TCE \\/ Lan\\u00e7amento SIGA\",\"completed\":false},{\"name\":\"Abertura do Preg\\u00e3o \\/ Dispensa de Licita\\u00e7\\u00e3o\",\"completed\":false},{\"name\":\"Adjudica\\u00e7\\u00e3o (Declara\\u00e7\\u00e3o do Vencedor)\",\"completed\":false},{\"name\":\"Compliance (Due Diligence - ASSGER)\",\"completed\":false},{\"name\":\"Homologa\\u00e7\\u00e3o e Publica\\u00e7\\u00e3o no D.O\",\"completed\":false}],\"expanded\":true},{\"name\":\"FASE DE CONTRATA\\u00c7\\u00c3O\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Contrata\\u00e7\\u00e3o no SIGA\",\"completed\":false},{\"name\":\"NAD \\/ Nota de Empenho\",\"completed\":false},{\"name\":\"Contrato (Assinatura)\",\"completed\":false},{\"name\":\"Gestores e Fiscais\",\"completed\":false},{\"name\":\"Garantia Contratual\",\"completed\":false}],\"expanded\":true}]', '2025-08-11 23:02:11', 'PE 10.1.1'),
(89, 'E', 'solucoes_tecnologicas', 324.00, '2025-08-21', 'planejamento', 'ASDF', '[{\"name\":\"PLANEJAMENTO PCA\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Desenvolvimento de novas solu\\u00e7\\u00f5es tecnol\\u00f3gicas\",\"completed\":false}],\"expanded\":true},{\"name\":\"FASE PREPARAT\\u00d3RIA\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Estudo T\\u00e9cnico Preliminar \\/ Matriz de Risco\",\"completed\":false},{\"name\":\"Termo de Refer\\u00eancia\",\"completed\":false},{\"name\":\"Pesquisa de Pre\\u00e7os \\/ Relat\\u00f3rio Anal\\u00edtico\",\"completed\":false},{\"name\":\"Autoriza\\u00e7\\u00e3o do Ordenador de Despesa\",\"completed\":false},{\"name\":\"DDO - Declara\\u00e7\\u00e3o de Dota\\u00e7\\u00e3o Or\\u00e7ament\\u00e1ria\",\"completed\":false},{\"name\":\"Minuta Edital\\/Contrato\",\"completed\":false},{\"name\":\"Parecer Jur\\u00eddico (ASSJUR)\",\"completed\":false},{\"name\":\"Lan\\u00e7amento do Edital no SIGA\",\"completed\":false}],\"expanded\":true},{\"name\":\"FASE EXTERNA\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Recibo de envio de Edital ao TCE \\/ Lan\\u00e7amento SIGA\",\"completed\":false},{\"name\":\"Abertura do Preg\\u00e3o \\/ Dispensa de Licita\\u00e7\\u00e3o\",\"completed\":false},{\"name\":\"Adjudica\\u00e7\\u00e3o (Declara\\u00e7\\u00e3o do Vencedor)\",\"completed\":false},{\"name\":\"Compliance (Due Diligence - ASSGER)\",\"completed\":false},{\"name\":\"Homologa\\u00e7\\u00e3o e Publica\\u00e7\\u00e3o no D.O\",\"completed\":false}],\"expanded\":true},{\"name\":\"FASE DE CONTRATA\\u00c7\\u00c3O\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Contrata\\u00e7\\u00e3o no SIGA\",\"completed\":false},{\"name\":\"NAD \\/ Nota de Empenho\",\"completed\":false},{\"name\":\"Contrato (Assinatura)\",\"completed\":false},{\"name\":\"Gestores e Fiscais\",\"completed\":false},{\"name\":\"Garantia Contratual\",\"completed\":false}],\"expanded\":true}]', '2025-08-11 23:02:24', 'PE 10.1.2'),
(90, 'R5WE', 'solucoes_tecnologicas', 3.00, '2025-09-05', 'planejamento', 'SDFA', '[{\"name\":\"PLANEJAMENTO PCA\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Desenvolvimento de novas solu\\u00e7\\u00f5es tecnol\\u00f3gicas\",\"completed\":false}],\"expanded\":true},{\"name\":\"FASE PREPARAT\\u00d3RIA\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Estudo T\\u00e9cnico Preliminar \\/ Matriz de Risco\",\"completed\":false},{\"name\":\"Termo de Refer\\u00eancia\",\"completed\":false},{\"name\":\"Pesquisa de Pre\\u00e7os \\/ Relat\\u00f3rio Anal\\u00edtico\",\"completed\":false},{\"name\":\"Autoriza\\u00e7\\u00e3o do Ordenador de Despesa\",\"completed\":false},{\"name\":\"DDO - Declara\\u00e7\\u00e3o de Dota\\u00e7\\u00e3o Or\\u00e7ament\\u00e1ria\",\"completed\":false},{\"name\":\"Minuta Edital\\/Contrato\",\"completed\":false},{\"name\":\"Parecer Jur\\u00eddico (ASSJUR)\",\"completed\":false},{\"name\":\"Lan\\u00e7amento do Edital no SIGA\",\"completed\":false}],\"expanded\":true},{\"name\":\"FASE EXTERNA\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Recibo de envio de Edital ao TCE \\/ Lan\\u00e7amento SIGA\",\"completed\":false},{\"name\":\"Abertura do Preg\\u00e3o \\/ Dispensa de Licita\\u00e7\\u00e3o\",\"completed\":false},{\"name\":\"Adjudica\\u00e7\\u00e3o (Declara\\u00e7\\u00e3o do Vencedor)\",\"completed\":false},{\"name\":\"Compliance (Due Diligence - ASSGER)\",\"completed\":false},{\"name\":\"Homologa\\u00e7\\u00e3o e Publica\\u00e7\\u00e3o no D.O\",\"completed\":false}],\"expanded\":true},{\"name\":\"FASE DE CONTRATA\\u00c7\\u00c3O\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Contrata\\u00e7\\u00e3o no SIGA\",\"completed\":false},{\"name\":\"NAD \\/ Nota de Empenho\",\"completed\":false},{\"name\":\"Contrato (Assinatura)\",\"completed\":false},{\"name\":\"Gestores e Fiscais\",\"completed\":false},{\"name\":\"Garantia Contratual\",\"completed\":false}],\"expanded\":true}]', '2025-08-11 23:02:36', 'PE 10.1.3'),
(91, 'teste', 'ferrovia', 324.00, '2025-08-29', 'planejamento', 'sadf', '[{\"name\":\"PLANEJAMENTO PCA\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Desenvolvimento de novas solu\\u00e7\\u00f5es tecnol\\u00f3gicas\",\"completed\":false}],\"expanded\":true},{\"name\":\"FASE PREPARAT\\u00d3RIA\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Estudo T\\u00e9cnico Preliminar \\/ Matriz de Risco\",\"completed\":false},{\"name\":\"Termo de Refer\\u00eancia\",\"completed\":false},{\"name\":\"Pesquisa de Pre\\u00e7os \\/ Relat\\u00f3rio Anal\\u00edtico\",\"completed\":false},{\"name\":\"Autoriza\\u00e7\\u00e3o do Ordenador de Despesa\",\"completed\":false},{\"name\":\"DDO - Declara\\u00e7\\u00e3o de Dota\\u00e7\\u00e3o Or\\u00e7ament\\u00e1ria\",\"completed\":false},{\"name\":\"Minuta Edital\\/Contrato\",\"completed\":false},{\"name\":\"Parecer Jur\\u00eddico (ASSJUR)\",\"completed\":false},{\"name\":\"Lan\\u00e7amento do Edital no SIGA\",\"completed\":false}],\"expanded\":true},{\"name\":\"FASE EXTERNA\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Recibo de envio de Edital ao TCE \\/ Lan\\u00e7amento SIGA\",\"completed\":false},{\"name\":\"Abertura do Preg\\u00e3o \\/ Dispensa de Licita\\u00e7\\u00e3o\",\"completed\":false},{\"name\":\"Adjudica\\u00e7\\u00e3o (Declara\\u00e7\\u00e3o do Vencedor)\",\"completed\":false},{\"name\":\"Compliance (Due Diligence - ASSGER)\",\"completed\":false},{\"name\":\"Homologa\\u00e7\\u00e3o e Publica\\u00e7\\u00e3o no D.O\",\"completed\":false}],\"expanded\":true},{\"name\":\"FASE DE CONTRATA\\u00c7\\u00c3O\",\"responsible\":\"\",\"etapas\":[{\"name\":\"Contrata\\u00e7\\u00e3o no SIGA\",\"completed\":false},{\"name\":\"NAD \\/ Nota de Empenho\",\"completed\":false},{\"name\":\"Contrato (Assinatura)\",\"completed\":false},{\"name\":\"Gestores e Fiscais\",\"completed\":false},{\"name\":\"Garantia Contratual\",\"completed\":false}],\"expanded\":true}]', '2025-08-12 00:23:28', 'PE 3.1.1');

-- --------------------------------------------------------

--
-- Estrutura para tabela `prestacao_contas`
--

CREATE TABLE `prestacao_contas` (
  `id` int(11) NOT NULL,
  `contrato_id` int(11) NOT NULL,
  `valor_pago` decimal(10,2) NOT NULL,
  `descricao` text NOT NULL,
  `data_pagamento` date NOT NULL,
  `status` varchar(20) DEFAULT 'Pendente',
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `chamado_glpi` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `prestacao_contas`
--

INSERT INTO `prestacao_contas` (`id`, `contrato_id`, `valor_pago`, `descricao`, `data_pagamento`, `status`, `data_criacao`, `chamado_glpi`) VALUES
(1, 14, 500000.00, 'sdaf', '2025-06-28', 'Concluída', '2025-06-04 12:20:46', NULL),
(2, 17, 500000.00, '2', '2025-06-04', 'Pendente', '2025-06-04 12:32:42', NULL),
(3, 22, 58500.00, 'SADF', '2025-06-04', 'Concluída', '2025-06-04 19:57:45', NULL);

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
  `descricao` varchar(50) DEFAULT NULL,
  `unidade` varchar(50) DEFAULT NULL,
  `localizacao` varchar(100) DEFAULT NULL,
  `custo` decimal(10,5) DEFAULT NULL,
  `valor_custo_total` decimal(10,5) NOT NULL,
  `quantidade` int(11) DEFAULT NULL,
  `nf` varchar(50) DEFAULT NULL,
  `preco_medio` decimal(10,2) DEFAULT NULL,
  `tipo_operacao` varchar(50) DEFAULT 'entrada',
  `data_cadastro` datetime DEFAULT NULL,
  `estoque_minimo` int(11) DEFAULT 0,
  `categoria` varchar(220) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `projetos`
--

CREATE TABLE `projetos` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `prioridade` enum('Alta','Média','Baixa') DEFAULT 'Média',
  `data_inicio` date DEFAULT NULL,
  `data_entrega` date DEFAULT NULL,
  `orcamento` decimal(10,2) DEFAULT 0.00,
  `progresso` int(11) DEFAULT 0,
  `status` enum('Em Andamento','Concluído','Pendente') DEFAULT 'Em Andamento'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `projetos`
--

INSERT INTO `projetos` (`id`, `nome`, `descricao`, `prioridade`, `data_inicio`, `data_entrega`, `orcamento`, `progresso`, `status`) VALUES
(1, 'MONITOR', 'w', 'Média', '2025-08-20', '2025-08-30', 5150.00, 0, 'Em Andamento'),
(2, 'NOTEBOOK ', 'test', 'Baixa', '2025-08-28', '2025-09-30', 234.00, 0, 'Em Andamento');

-- --------------------------------------------------------

--
-- Estrutura para tabela `relatorios_agendados`
--

CREATE TABLE `relatorios_agendados` (
  `id` int(11) NOT NULL,
  `tipo_relatorio` varchar(50) DEFAULT NULL COMMENT 'Tipo de relatório (ex.: completo, mensal, anual)',
  `contrato_id` int(11) DEFAULT NULL COMMENT 'ID do contrato, se for um relatório de contrato individual',
  `relatorio_todos` varchar(50) DEFAULT NULL COMMENT 'Tipo de relatório para todos os contratos (ex.: mensal_todos, anual_todos)',
  `mes` int(11) DEFAULT NULL COMMENT 'Mês do relatório, se aplicável (1 a 12)',
  `ano` int(11) DEFAULT NULL COMMENT 'Ano do relatório, se aplicável',
  `email_destinatario` varchar(255) NOT NULL COMMENT 'E-mail do destinatário do relatório',
  `periodicidade` varchar(20) NOT NULL COMMENT 'Periodicidade do envio (diario, semanal, mensal)',
  `proximo_envio` datetime NOT NULL COMMENT 'Data e hora do próximo envio',
  `criado_em` datetime DEFAULT current_timestamp() COMMENT 'Data de criação do agendamento'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `relatorios_agendados`
--

INSERT INTO `relatorios_agendados` (`id`, `tipo_relatorio`, `contrato_id`, `relatorio_todos`, `mes`, `ano`, `email_destinatario`, `periodicidade`, `proximo_envio`, `criado_em`) VALUES
(1, 'completo', 0, NULL, NULL, NULL, 'gabrielzsouzarodrigues@gmail.com', 'diario', '2025-04-27 05:14:48', '2025-04-26 00:14:48'),
(2, 'anual', 0, NULL, NULL, 2024, 'gabrielzsouzarodrigues@gmail.com', 'diario', '2025-04-27 05:15:37', '2025-04-26 00:15:37'),
(3, 'anual_todos', NULL, 'anual_todos', NULL, 2024, 'gabrielzsouzarodrigues@gmail.com', 'diario', '2025-04-27 05:34:01', '2025-04-26 00:34:01'),
(4, 'anual_todos', NULL, 'anual_todos', NULL, 2024, 'gabrielzsouzarodrigues@gmail.com', 'diario', '2025-04-27 05:37:00', '2025-04-26 00:37:00'),
(5, 'anual_todos', NULL, 'anual_todos', NULL, 2024, 'gabrielzsouzarodrigues@gmail.com', 'diario', '2025-04-27 05:37:06', '2025-04-26 00:37:06'),
(6, 'anual_todos', NULL, 'anual_todos', NULL, 2024, 'gabrielzsouzarodrigues@gmail.com', 'diario', '2025-04-27 05:37:27', '2025-04-26 00:37:27'),
(7, 'anual_todos', NULL, 'anual_todos', NULL, 2024, 'gabrielzsouzarodrigues23@gmail.com', 'diario', '2025-04-27 05:38:04', '2025-04-26 00:38:04'),
(8, 'completo', 0, NULL, NULL, NULL, 'gabrielzsouzarodrigues@gmail.com', 'diario', '2025-04-27 05:40:36', '2025-04-26 00:40:36'),
(9, 'completo', 0, NULL, NULL, NULL, 'gabrielzsouzarodrigues@gmail.com', 'diario', '2025-04-27 05:40:45', '2025-04-26 00:40:45'),
(10, 'compromissos_futuros', 0, NULL, NULL, NULL, 'gabrielzsouzarodrigues@gmail.com', 'diario', '2025-04-27 05:41:57', '2025-04-26 00:41:57'),
(11, 'compromissos_futuros', 0, NULL, NULL, NULL, 'gabrielzsouzarodrigues23@gmail.com', 'diario', '2025-04-27 05:42:14', '2025-04-26 00:42:14'),
(12, 'compromissos_futuros', 0, NULL, NULL, NULL, 'gabrielzsouzarodrigues23@gmail.com', 'diario', '2025-04-27 05:42:33', '2025-04-26 00:42:33'),
(13, 'anual_todos', NULL, 'anual_todos', NULL, 2024, 'gabrielzsouzarodrigues@gmail.com', 'diario', '2025-04-28 04:11:52', '2025-04-26 23:11:52'),
(14, 'anual_todos', NULL, 'anual_todos', NULL, 2024, 'gabrielzsouzarodrigues@gmail.com', 'diario', '2025-04-28 04:12:33', '2025-04-26 23:12:33'),
(15, 'anual_todos', NULL, 'anual_todos', NULL, 2024, 'gabrielzsouzarodrigues@gmail.com', 'diario', '2025-04-28 16:07:29', '2025-04-27 11:07:29');

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
(5, 'contratos'),
(6, 'ccooperacao'),
(7, 'sisbonde'),
(8, 'planejamento');

-- --------------------------------------------------------

--
-- Estrutura para tabela `subtopicos`
--

CREATE TABLE `subtopicos` (
  `id` int(11) NOT NULL,
  `topico_id` int(11) DEFAULT NULL,
  `titulo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tarefas`
--

CREATE TABLE `tarefas` (
  `id` int(11) NOT NULL,
  `projeto_id` int(11) DEFAULT NULL,
  `nome` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `prioridade` enum('Alta','Média','Baixa') DEFAULT 'Média',
  `data` date DEFAULT NULL,
  `progresso` int(11) DEFAULT 0,
  `status` enum('Pendente','Em Andamento','Concluído') DEFAULT 'Pendente',
  `responsavel` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `topicos`
--

CREATE TABLE `topicos` (
  `id` int(11) NOT NULL,
  `bloco_id` int(11) DEFAULT NULL,
  `titulo` varchar(255) DEFAULT NULL,
  `descricao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(6, 1, 'SUPLAN', '2024-11-24', NULL, '2024-11-23 23:57:44', '2024-11-23 23:57:44', 'Claudia', 'Transferido'),
(7, 1, 'CONFIS', '2025-06-09', NULL, '2025-06-09 16:54:41', '2025-06-09 16:54:41', 'Claudia', 'Transferido'),
(8, 1, 'CONFIS', '2025-06-09', NULL, '2025-06-09 16:57:46', '2025-06-09 16:57:46', 'Claudia', 'Transferido'),
(9, 8, 'COMAUD', '2025-06-09', NULL, '2025-06-09 17:00:27', '2025-06-09 17:00:27', 'maik', 'Transferido');

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
-- Estrutura para tabela `transicao`
--

CREATE TABLE `transicao` (
  `id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `valor_custo_total` decimal(10,2) NOT NULL,
  `data` date NOT NULL,
  `tipo` enum('Entrada','Saida') NOT NULL
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
  `setor` enum('administrador','estoque','patrimonio','financeiro','contratos','ccooperacao','sisbonde','planejamento') NOT NULL,
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
(6, 'GABRIEL', '$2y$10$a1.2umOUZWvquE1BzU7gg.zrAQhUAcXQ3Xrc6brRzmobxY2P3zRTC', '99000889', 'grodrigues@central.rj.gov.br', 'administrador', 'Analista de Suporte de Sistemas', 'ativo', 'perfil-user-673f82c52395e.jpg', 1, NULL),
(9, 'Rita', '$2y$10$b1rZmXkpaeueSD.fC1jtt.aRWzP9zSiwKcA8mrblToKKmcWkZGuyS', '990002025', 'rita@central.rj.gov.br', 'contratos', 'ASSESSORA', 'ativo', 'default.png', 1, NULL),
(10, 'CONTRATOS', '$2y$10$QG5qwt6JfD48s1.YqqS.M.7SUz/p5hBt4SiKnABpKiMN1cGLctEiW', '00', 'asscon@central.rj.gov.br', 'contratos', 'contratos', 'ativo', 'default.png', 1, '2025-03-25 11:43:45'),
(11, 'MARCO', '$2y$10$V/PS4I1wx9viy9zkpuUMKO7B4AUC/PIRBI.ZZI6LYSvbSBDlQhAIi', '919', 'GERGEP@CENTRAL.RJ.GOV.BR', 'financeiro', 'SUPERINTENDENTE', 'ativo', 'default.png', 1, NULL),
(12, 'BONDE', '$2y$10$RZ0H8zlSb3mp0rNckKM8fe0WHn2Hma19l33MElYEG5KZzQAIksOwK', '990078', 'bonde@central.rj.gov.br', 'ccooperacao', 'ASSESSOR', 'ativo', 'default.png', 1, NULL),
(13, 'controle', '$2y$10$KzXaFQUFoZ3ilRZ.gPlI0OqNvfN5xh.Y4UtIdvsQp2httZrq0Od5u', 'contr50', 'controle@central.rj.gov.br', 'sisbonde', 'ASSESSOR', 'ativo', 'default.png', 1, NULL),
(14, 'planejamento', '$2y$10$m/e8/fLXlIN4OX8yNt./ae3xICRkoofjMHCcWzLMxSAWLczQXs0nm', '223', 'planejamento@central.rj.gov.br', 'planejamento', 'planejamento', 'ativo', 'default.png', 1, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `vencimentos_futuros`
--

CREATE TABLE `vencimentos_futuros` (
  `id` int(11) NOT NULL,
  `contrato_titulo` varchar(255) NOT NULL,
  `tipo_vencimento` enum('Petição','Certidão') NOT NULL,
  `data_vencimento` date NOT NULL,
  `descricao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `viagens`
--

CREATE TABLE `viagens` (
  `id` int(11) NOT NULL,
  `bonde` varchar(50) NOT NULL,
  `saida` varchar(50) NOT NULL,
  `retorno` varchar(50) DEFAULT NULL,
  `maquinista` varchar(50) NOT NULL,
  `agente` varchar(50) NOT NULL,
  `hora` time NOT NULL,
  `pagantes` int(11) NOT NULL DEFAULT 0,
  `gratuidade` int(11) NOT NULL DEFAULT 0,
  `moradores` int(11) NOT NULL DEFAULT 0,
  `passageiros` int(11) NOT NULL DEFAULT 0,
  `tipo_viagem` varchar(20) NOT NULL,
  `data` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `viagens`
--

INSERT INTO `viagens` (`id`, `bonde`, `saida`, `retorno`, `maquinista`, `agente`, `hora`, `pagantes`, `gratuidade`, `moradores`, `passageiros`, `tipo_viagem`, `data`, `created_at`) VALUES
(1, 'BONDE 17', 'Carioca', '', 'Adriano', 'Vinicius', '11:52:05', 17, 3, 12, 32, 'Ida', '2025-08-25', '2025-08-25 14:52:31'),
(2, 'BO2', 'Carioca', '', 'M. Celestino', 'P. Nascimento', '11:53:47', 20, 0, 12, 32, 'Ida', '2025-08-25', '2025-08-25 14:54:29'),
(3, 'BO2', 'Carioca', '', 'M. Celestino', 'P. Nascimento', '11:53:47', 20, 0, 12, 32, 'Ida', '2025-08-25', '2025-08-25 14:54:36'),
(4, 'BO2', 'Carioca', '', 'M. Celestino', 'P. Nascimento', '11:53:47', 20, 0, 12, 32, 'Ida', '2025-08-25', '2025-08-25 14:55:08');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `acidentes`
--
ALTER TABLE `acidentes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `blocos`
--
ALTER TABLE `blocos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `projeto_id` (`projeto_id`);

--
-- Índices de tabela `bondes`
--
ALTER TABLE `bondes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `modelo` (`modelo`);

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `certidoes`
--
ALTER TABLE `certidoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_data_vencimento` (`data_vencimento`),
  ADD KEY `idx_documento` (`documento`),
  ADD KEY `fk_contrato` (`contrato_id`);

--
-- Índices de tabela `codigo_atual`
--
ALTER TABLE `codigo_atual`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `conferencias`
--
ALTER TABLE `conferencias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_conferencia` (`produto_id`,`mes_conferencia`);

--
-- Índices de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `contratos_parcelas`
--
ALTER TABLE `contratos_parcelas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contrato_id` (`contrato_id`);

--
-- Índices de tabela `controle_transicao`
--
ALTER TABLE `controle_transicao`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mes` (`mes`);

--
-- Índices de tabela `data_criacao`
--
ALTER TABLE `data_criacao`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `emails_salvos`
--
ALTER TABLE `emails_salvos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_email_user` (`email`,`username`);

--
-- Índices de tabela `etapas_contratos`
--
ALTER TABLE `etapas_contratos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_etapa` (`contract_id`,`etapa`);

--
-- Índices de tabela `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `fechamento`
--
ALTER TABLE `fechamento`
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
-- Índices de tabela `macroetapas`
--
ALTER TABLE `macroetapas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `manutencoes`
--
ALTER TABLE `manutencoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `materiais`
--
ALTER TABLE `materiais`
  ADD PRIMARY KEY (`codigo`);

--
-- Índices de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `ordens_compra`
--
ALTER TABLE `ordens_compra`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- Índices de tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contrato_titulo` (`contrato_titulo`);

--
-- Índices de tabela `painel_config`
--
ALTER TABLE `painel_config`
  ADD PRIMARY KEY (`id`);

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
-- Índices de tabela `planejamento`
--
ALTER TABLE `planejamento`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `prestacao_contas`
--
ALTER TABLE `prestacao_contas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `projetos`
--
ALTER TABLE `projetos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `relatorios_agendados`
--
ALTER TABLE `relatorios_agendados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_proximo_envio` (`proximo_envio`),
  ADD KEY `idx_email_destinatario` (`email_destinatario`);

--
-- Índices de tabela `setores`
--
ALTER TABLE `setores`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `subtopicos`
--
ALTER TABLE `subtopicos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `topico_id` (`topico_id`);

--
-- Índices de tabela `tarefas`
--
ALTER TABLE `tarefas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `projeto_id` (`projeto_id`);

--
-- Índices de tabela `topicos`
--
ALTER TABLE `topicos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bloco_id` (`bloco_id`);

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
-- Índices de tabela `transicao`
--
ALTER TABLE `transicao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `material_id` (`material_id`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `matricula` (`matricula`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `vencimentos_futuros`
--
ALTER TABLE `vencimentos_futuros`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contrato_titulo` (`contrato_titulo`);

--
-- Índices de tabela `viagens`
--
ALTER TABLE `viagens`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `acidentes`
--
ALTER TABLE `acidentes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `blocos`
--
ALTER TABLE `blocos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `bondes`
--
ALTER TABLE `bondes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `certidoes`
--
ALTER TABLE `certidoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `codigo_atual`
--
ALTER TABLE `codigo_atual`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `conferencias`
--
ALTER TABLE `conferencias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `contratos_parcelas`
--
ALTER TABLE `contratos_parcelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=314;

--
-- AUTO_INCREMENT de tabela `controle_transicao`
--
ALTER TABLE `controle_transicao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `data_criacao`
--
ALTER TABLE `data_criacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `emails_salvos`
--
ALTER TABLE `emails_salvos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `etapas_contratos`
--
ALTER TABLE `etapas_contratos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=200;

--
-- AUTO_INCREMENT de tabela `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT de tabela `fechamento`
--
ALTER TABLE `fechamento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de tabela `funcionario`
--
ALTER TABLE `funcionario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `gestao_contratos`
--
ALTER TABLE `gestao_contratos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT de tabela `log_eventos`
--
ALTER TABLE `log_eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=893;

--
-- AUTO_INCREMENT de tabela `macroetapas`
--
ALTER TABLE `macroetapas`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT de tabela `manutencoes`
--
ALTER TABLE `manutencoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=764;

--
-- AUTO_INCREMENT de tabela `ordens_compra`
--
ALTER TABLE `ordens_compra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT de tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT de tabela `painel_config`
--
ALTER TABLE `painel_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `patrimonio`
--
ALTER TABLE `patrimonio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de tabela `permissoes`
--
ALTER TABLE `permissoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `planejamento`
--
ALTER TABLE `planejamento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT de tabela `prestacao_contas`
--
ALTER TABLE `prestacao_contas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `projetos`
--
ALTER TABLE `projetos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `relatorios_agendados`
--
ALTER TABLE `relatorios_agendados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `setores`
--
ALTER TABLE `setores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `subtopicos`
--
ALTER TABLE `subtopicos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tarefas`
--
ALTER TABLE `tarefas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `topicos`
--
ALTER TABLE `topicos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `transferencias`
--
ALTER TABLE `transferencias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `transferencia_historico`
--
ALTER TABLE `transferencia_historico`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `transicao`
--
ALTER TABLE `transicao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `vencimentos_futuros`
--
ALTER TABLE `vencimentos_futuros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `viagens`
--
ALTER TABLE `viagens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `blocos`
--
ALTER TABLE `blocos`
  ADD CONSTRAINT `blocos_ibfk_1` FOREIGN KEY (`projeto_id`) REFERENCES `projetos` (`id`);

--
-- Restrições para tabelas `certidoes`
--
ALTER TABLE `certidoes`
  ADD CONSTRAINT `fk_contrato` FOREIGN KEY (`contrato_id`) REFERENCES `gestao_contratos` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `conferencias`
--
ALTER TABLE `conferencias`
  ADD CONSTRAINT `conferencias_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`);

--
-- Restrições para tabelas `contratos_parcelas`
--
ALTER TABLE `contratos_parcelas`
  ADD CONSTRAINT `contratos_parcelas_ibfk_1` FOREIGN KEY (`contrato_id`) REFERENCES `gestao_contratos` (`id`);

--
-- Restrições para tabelas `etapas_contratos`
--
ALTER TABLE `etapas_contratos`
  ADD CONSTRAINT `etapas_contratos_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `gestao_contratos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `subtopicos`
--
ALTER TABLE `subtopicos`
  ADD CONSTRAINT `subtopicos_ibfk_1` FOREIGN KEY (`topico_id`) REFERENCES `topicos` (`id`);

--
-- Restrições para tabelas `tarefas`
--
ALTER TABLE `tarefas`
  ADD CONSTRAINT `tarefas_ibfk_1` FOREIGN KEY (`projeto_id`) REFERENCES `projetos` (`id`);

--
-- Restrições para tabelas `topicos`
--
ALTER TABLE `topicos`
  ADD CONSTRAINT `topicos_ibfk_1` FOREIGN KEY (`bloco_id`) REFERENCES `blocos` (`id`);

--
-- Restrições para tabelas `vencimentos_futuros`
--
ALTER TABLE `vencimentos_futuros`
  ADD CONSTRAINT `vencimentos_futuros_ibfk_1` FOREIGN KEY (`contrato_titulo`) REFERENCES `gestao_contratos` (`titulo`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
