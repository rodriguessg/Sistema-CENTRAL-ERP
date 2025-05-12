-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 12-Maio-2025 às 21:44
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
-- Banco de dados: `gm_sicbd`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `cor` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `certidoes`
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

--
-- Extraindo dados da tabela `certidoes`
--

INSERT INTO `certidoes` (`id`, `documento`, `data_vencimento`, `nome`, `fornecedor`, `responsavel`, `criado_em`, `atualizado_em`, `arquivo`, `contrato_id`) VALUES
(3, 'Outros', '2025-05-23', 'teste120', 'DELL', 'Claudia', '2025-05-12 19:39:39', '2025-05-12 19:40:53', NULL, NULL);

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
-- Estrutura da tabela `contratos_parcelas`
--

CREATE TABLE `contratos_parcelas` (
  `id` int(11) NOT NULL,
  `contrato_id` int(11) DEFAULT NULL,
  `mes` int(11) DEFAULT NULL,
  `ano` int(11) DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `contratos_parcelas`
--

INSERT INTO `contratos_parcelas` (`id`, `contrato_id`, `mes`, `ano`, `valor`) VALUES
(1, 4, 5, 2025, 125000.00),
(2, 4, 6, 2025, 125000.00),
(3, 4, 7, 2025, 125000.00),
(4, 4, 8, 2025, 125000.00),
(5, 5, 5, 2025, 500000.00),
(6, 6, 3, 2026, 41666.67),
(7, 6, 4, 2026, 41666.67),
(8, 6, 5, 2026, 41666.67),
(9, 6, 6, 2026, 41666.67),
(10, 6, 7, 2026, 41666.67),
(11, 6, 8, 2026, 41666.67),
(12, 6, 9, 2026, 41666.67),
(13, 6, 10, 2026, 41666.67),
(14, 6, 11, 2026, 41666.67),
(15, 6, 12, 2026, 41666.67),
(16, 6, 1, 2027, 41666.67),
(17, 6, 2, 2027, 41666.67),
(18, 7, 1, 2025, 369132.00),
(19, 9, 6, 2025, 4322640.00),
(20, 10, 6, 2025, 41.67),
(21, 10, 7, 2025, 41.67),
(22, 10, 8, 2025, 41.67),
(23, 10, 9, 2025, 41.67),
(24, 10, 10, 2025, 41.67),
(25, 10, 11, 2025, 41.67),
(26, 10, 12, 2025, 41.67),
(27, 10, 1, 2026, 41.67),
(28, 10, 2, 2026, 41.67),
(29, 10, 3, 2026, 41.67),
(30, 10, 4, 2026, 41.67),
(31, 10, 5, 2026, 41.67),
(32, 11, 7, 2025, 1440880.00),
(33, 11, 8, 2025, 1440880.00),
(34, 11, 9, 2025, 1440880.00);

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
(1, '2025-03'),
(2, '2025-04'),
(3, '2025-05');

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
-- Estrutura da tabela `emails_salvos`
--

CREATE TABLE `emails_salvos` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `criado_em` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `emails_salvos`
--

INSERT INTO `emails_salvos` (`id`, `email`, `username`, `criado_em`) VALUES
(1, 'grodrigues@central.rj.gov.br', 'CONTRATOS', '2025-04-25 02:30:34'),
(4, 'maikgtx2@gmail.com', 'CONTRATOS', '2025-04-25 02:54:18'),
(8, 'gabrielzsouzarodrigues@gmail.com', 'CONTRATOS', '2025-04-26 00:40:45'),
(9, 'gabrielzsouzarodrigues23@gmail.com', 'MASTER', '2025-04-26 00:42:33');

-- --------------------------------------------------------

--
-- Estrutura da tabela `eventos`
--

CREATE TABLE `eventos` (
  `id` int(11) NOT NULL,
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
-- Extraindo dados da tabela `eventos`
--

INSERT INTO `eventos` (`id`, `titulo`, `descricao`, `data`, `hora`, `categoria`, `cor`, `criado_em`, `certidao_id`) VALUES
(1, 'Renovar Certidão: Certidão Negativa Teste', 'Certidão ID: 1, vence em 15/05/2025.', '2025-05-12', '00:00:00', 'Renovação', '#dc3545', '2025-05-12 19:15:13', 0),
(2, 'Renovar Certidão: Computador Dell', 'Certidão ID: 2, vence em 15/05/2025.', '2025-05-12', '00:00:00', 'Renovação', '#dc3545', '2025-05-12 19:20:51', 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `fechamento`
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
-- Extraindo dados da tabela `fechamento`
--

INSERT INTO `fechamento` (`id`, `data_fechamento`, `natureza`, `classificacao`, `saldo_anterior`, `total_entrada`, `total_saida`, `saldo_atual`, `custo`, `status`) VALUES
(1, '2025-04-02', '333903001', 'Material cama mesa Banho/Copa e Cozinha', 5439.46, 0.00, 0.00, 0.00, 0.00, 'Pendente'),
(2, '2025-04-02', '333903002', 'Artigos para Limpeza, Higiêne e Toalete', 17503.12, 0.00, 0.00, 0.00, 0.00, 'Pendente'),
(4, '2025-04-02', '333903005', 'Artigos em Geral e Impressos para Expediente, Escritorio', 13899.11, 0.00, 0.00, 0.00, 0.00, 'Pendente'),
(5, '2025-04-02', '333903008', 'Material Radiológico Fotografico,Cinematográfico, de Gravação e Comunicação', 0.00, 0.00, 0.00, 0.00, 0.00, 'Pendente'),
(6, '2025-04-02', '333903010', 'Material Eletrico,material para conservação e manutenção de Bens', 5837.93, 0.00, 0.00, 0.00, 0.00, 'Pendente'),
(7, '2025-04-02', '333903011', 'Material para manutenção e conservação de Bens móveis', 157335.90, 0.00, 0.00, 0.00, 0.00, 'Pendente'),
(8, '2025-04-02', '333903020', 'Produtos Alimentícios e Bebidas', 0.00, 0.00, 0.00, 0.00, 0.00, 'Pendente'),
(9, '2025-04-02', '333903021', 'Matérias Primas', 59061.49, 0.00, 0.00, 0.00, 0.00, 'Pendente'),
(10, '2025-04-02', '333903023', 'Material de Informatica', 847.82, 0.00, 0.00, 0.00, 0.00, 'Pendente'),
(11, '2025-04-02', '333903030', 'Material para manutenção de Veículo', 795.34, 0.00, 0.00, 0.00, 0.00, 'Pendente'),
(12, '2025-04-02', '333903033', 'Material para Sinalização Visual e Outros', 680.65, 0.00, 0.00, 0.00, 0.00, 'Pendente'),
(13, '2025-04-02', '333903042', 'Material Eletrico e Eletrônico', 113069.61, 0.00, 0.00, 0.00, 0.00, 'Pendente'),
(14, '2025-04-02', '344903010', 'Mat. Eletr. Mat. P/ Conserv. e Manut. de Bens Imoveis; Sinaliz. e Demarc', 6554.64, 0.00, 0.00, 0.00, 0.00, 'Pendente'),
(15, '2025-04-02', '344903011', 'Material para manutenção e conservação de Bens móveis', 7836.20, 0.00, 0.00, 0.00, 0.00, 'Pendente'),
(16, '2025-04-02', '344905206', 'Outros Equipamentos', 147.80, 0.00, 0.00, 0.00, 0.00, 'Pendente'),
(17, '2025-04-02', '344905212', 'Utensilios de Copa, Cozinha, Dormitorio e Enfermaria', 320.00, 0.00, 0.00, 0.00, 0.00, 'Pendente'),
(18, '2025-04-02', '344905217', 'Equipamento para áudio, Vídeo e Foto', 25443.80, 0.00, 0.00, 0.00, 0.00, 'Pendente'),
(19, '2025-04-02', '344905220', 'Maquinas, Ferramentas e Utensilios de Oficina', 34.45, 0.00, 0.00, 0.00, 0.00, 'Pendente'),
(20, '2025-04-02', '344905238', 'Equipamento e Material Permanente ( Material de T.I.C )', 0.00, 0.00, 0.00, 0.00, 0.00, 'Pendente');

-- --------------------------------------------------------

--
-- Estrutura da tabela `fechamentos`
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
-- Extraindo dados da tabela `fechamentos`
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
(0, 'PAULO', '344905238', 0.00, 0.00, 0.00, 0.00, '2025-05-06 13:16:55');

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
  `SEI` varchar(220) NOT NULL,
  `objeto` text NOT NULL,
  `gestor` varchar(255) NOT NULL,
  `gestorsb` varchar(255) NOT NULL,
  `fiscais` varchar(255) NOT NULL,
  `validade` date NOT NULL,
  `conta_bancaria` varchar(11) NOT NULL,
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
  `situacao` enum('Ativo','Inativo','Encerrado') NOT NULL DEFAULT 'Ativo',
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  `setor` enum('administrador','estoque','patrimonio','financeiro','contratos') NOT NULL DEFAULT 'contratos',
  `parcelamento` enum('Sim','Não') DEFAULT 'Não',
  `valor_aditivo2` decimal(15,2) DEFAULT NULL,
  `valor_aditivo3` decimal(15,2) DEFAULT NULL,
  `valor_aditivo4` decimal(15,2) DEFAULT NULL,
  `valor_aditivo5` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `gestao_contratos`
--

INSERT INTO `gestao_contratos` (`id`, `titulo`, `SEI`, `objeto`, `gestor`, `gestorsb`, `fiscais`, `validade`, `conta_bancaria`, `fonte`, `publicacao`, `date_service`, `contatos`, `n_despesas`, `outros`, `servicos`, `valor_nf`, `valor_contrato`, `valor_aditivo1`, `num_parcelas`, `descricao`, `situacao`, `data_cadastro`, `setor`, `parcelamento`, `valor_aditivo2`, `valor_aditivo3`, `valor_aditivo4`, `valor_aditivo5`) VALUES
(2, 'Contrato de Consultoria TI', 'SEI-002/2024', 'Consultoria em TI', 'Ana Costa', 'Pedro Souza', 'Mariana Lopes', '2024-08-31', '', '', NULL, NULL, 'consultoria@ti.com', '', 'Não', NULL, 0.00, 12000.00, NULL, 6, 'Consultoria para implementação de sistemas.', 'Inativo', '2024-03-01 03:00:00', 'contratos', 'Não', NULL, NULL, NULL, NULL),
(3, 'Contrato de Locação de Equipamentos', 'SEI-003/2023', 'Locação de equipamentos', 'Carlos Mendes', 'Luiza Almeida', 'Rafael Lima', '2025-09-30', '', '', NULL, NULL, 'locacao@equip.com', '', 'Não', NULL, 0.00, 19200.00, 5000.00, 24, 'Locação de equipamentos para eventos.', 'Encerrado', '2023-06-01 03:00:00', 'contratos', 'Não', NULL, NULL, NULL, NULL),
(5, 'teste de atualização', '7890', 'CENTRAL', 'CHEFE', 'GERENTE', 'GABRIEL, MAIK', '2025-05-16', '', '', NULL, NULL, '400289222', '', 'Não', NULL, 0.00, 500000.00, NULL, 1, 'teste', 'Ativo', '2025-04-27 20:50:03', 'contratos', 'Não', NULL, NULL, NULL, NULL),
(7, 'GREEN CARD S/A REFEIÇÕES, COMÉRCIO E SERVIÇOS', '100006/001752/2023', 'Prestação de serviço de emissão e entrega de cartões eletrônicos na modalidade alimentação, com carga automática - on-line (doravante designados cartões-alimentação ou simplesmente cartões, conforme o caso), bem como dos respectivos valores de carga ou créditos (doravante designados apenas valores de carga) relativos à concessão de benefício natalino aos empregados da CENTRAL, no valor unitário de R$ 500,00 (quinhentos reais), cada carga, para aquisição de gêneros alimentícios, com a finalidade de fornecer a ceia de Natal', 'DAVI VIANNA DE MACEDO OLIVEIRA', 'N/A', 'Raphaela Batista Saldanha - 99.000.900 - ASSCON / VALDENICE ALVES DA SILVA DE PAULA - GERGEP - 99.000.517 / André Miranda Lobão Tavares Mendonça - 99.000.610 - CHEGAB', '2025-12-19', '', '', NULL, NULL, 'susiane.kempfer@grupogreencard.com.br', '', 'Não', NULL, 0.00, 432264000.00, 0.00, 1, 'Prestação de serviço de emissão e entrega de cartões eletrônicos na modalidade alimentação, com carga automática - on-line (doravante designados cartões-alimentação ou simplesmente cartões, conforme o caso), bem como dos respectivos valores de carga ou créditos (doravante designados apenas valores de carga) relativos à concessão de benefício natalino aos empregados da CENTRAL, no valor unitário de R$ 500,00 (quinhentos reais), cada carga, para aquisição de gêneros alimentícios, com a finalidade de fornecer a ceia de Natal', 'Ativo', '2025-05-06 17:45:31', 'contratos', 'Não', NULL, NULL, NULL, NULL),
(10, 'CONTRATO DE PRESTAÇÃO DE SERVIÇOS EMAIL ZIMBRA', '20231600/889345', 'Prestação de serviço de emissão e entrega de cartões eletrônicos na modalidade alimentação, com carga automática - on-line (doravante designados cartões-alimentação ou simplesmente cartões, conforme o caso), bem como dos respectivos valores de carga ou créditos (doravante designados apenas valores de carga) relativos à concessão de benefício natalino aos empregados da CENTRAL, no valor unitário de R$ 500,00 (quinhentos reais), cada carga, para aquisição de gêneros alimentícios, com a finalidade de fornecer a ceia de Natal', 'CARUSO', 'MAIK', 'ALEXANDRE', '2025-05-07', '', '', NULL, NULL, '40028922', '', 'Não', NULL, 0.00, 500.00, 0.00, 12, 'TESTE', 'Ativo', '2025-05-07 18:53:25', 'contratos', 'Não', NULL, NULL, NULL, NULL),
(11, 'Contrato teste de nova atualização', '100006/001752/2023', 'teste', 'MAIK', 'ALEXANDRE', 'CELSO', '2025-05-31', '5542', '100', '2025-04-16', '2025-04-30', '40028922', 'Sem Despesas', 'Não', 'servico1', 0.00, 4322640.00, 0.00, 3, 'TESTE', 'Ativo', '2025-05-08 19:35:16', 'contratos', 'Sim', NULL, NULL, NULL, NULL);

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
(652, 'CONTRATOS', 'Login bem-sucedido', '2025-05-12 19:15:10');

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
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `certidao_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `notificacoes`
--

INSERT INTO `notificacoes` (`id`, `username`, `setor`, `mensagem`, `situacao`, `data_criacao`, `certidao_id`) VALUES
(1, 'contratos', 'contratos', 'Certidão \"Certidão Negativa Teste\" (ID: 1) cadastrada com sucesso. Vence em 15/05/2025. Setor: contratos.', 'lida', '2025-05-12 19:15:13', 0),
(2, 'contratos', 'contratos', 'A certidão \"Certidão Negativa Teste\" (ID: 1) está próxima do vencimento (2 dias restantes, vence em 15/05/2025). Setor: contratos.', 'lida', '2025-05-12 19:15:13', 0),
(3, 'contratos', 'contratos', 'Contrato \'teste de atualização\' com validade em 2025-05-16 prestes a expirar.', 'lida', '2025-05-13 00:17:13', 0),
(4, 'contratos', 'contratos', 'Contrato \'Contrato teste de nova atualização\' com validade em 2025-05-31 prestes a expirar.', 'lida', '2025-05-13 00:17:13', 0),
(5, 'contratos', 'contratos', 'Certidão \"Certidão Negativa Teste\" (ID: 1) cadastrada com sucesso. Vence em 15/05/2025. Setor: contratos.', 'lida', '2025-05-12 19:19:03', 0),
(6, 'contratos', 'contratos', 'Certidão \"Certidão Negativa Teste\" (ID: 1) cadastrada com sucesso. Vence em 15/05/2025. Setor: contratos.', 'lida', '2025-05-12 19:20:26', 0),
(7, 'contratos', 'contratos', 'Certidão \"Computador Dell\" (ID: 2) cadastrada com sucesso. Vence em 15/05/2025. Setor: contratos.', 'lida', '2025-05-12 19:20:51', 0),
(8, 'contratos', 'contratos', 'A certidão \"Computador Dell\" (ID: 2) está próxima do vencimento (2 dias restantes, vence em 15/05/2025). Setor: contratos.', 'lida', '2025-05-12 19:20:51', 0),
(9, 'contratos', 'contratos', 'Certidão \"teste120\" (ID: 3) cadastrada com sucesso. Vence em 31/05/2025. Setor: contratos.', 'lida', '2025-05-12 19:39:39', 0),
(10, 'contratos', 'contratos', 'A certidão \"teste120\"  está próxima do vencimento (3 dias restantes, vence em 18). ', 'lida', '2025-05-12 19:39:39', 0),
(11, 'contratos', 'contratos', 'Certidão \"teste120\" (ID: 3) cadastrada com sucesso. Vence em 15/05/2025. Setor: contratos.', 'lida', '2025-05-12 19:40:16', 0),
(12, 'contratos', 'contratos', 'A certidão \"teste120\"  está próxima do vencimento (3 dias restantes, vence em 2). ', 'lida', '2025-05-12 19:40:16', 0),
(13, 'contratos', 'contratos', 'Certidão \"teste120\" (ID: 3) cadastrada com sucesso. Vence em 23/05/2025. Setor: contratos.', 'lida', '2025-05-12 19:40:53', 0),
(14, 'contratos', 'contratos', 'A certidão \"teste120\"  está próxima do vencimento (3 dias restantes, vence em 10). ', 'lida', '2025-05-12 19:40:53', 0),
(15, 'contratos', 'contratos', 'Evento excluído: Renovar Certidão: teste120', 'lida', '2025-05-13 00:41:11', 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `ordens_compra`
--

CREATE TABLE `ordens_compra` (
  `id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `ordens_compra`
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
(65, 2, 5, '2025-05-06 21:21:03');

-- --------------------------------------------------------

--
-- Estrutura da tabela `pagamentos`
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
  `valor_liquidado_ag` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `pagamentos`
--

INSERT INTO `pagamentos` (`id`, `contrato_titulo`, `data_pagamento`, `valor_contrato`, `mes`, `empenho`, `nota_empenho`, `valor_liquidado`, `ordem_bancaria`, `data_atualizacao`, `envio_pagamento`, `vencimento_fatura`, `agencia_bancaria`, `tipo`, `SEI`, `nota_fiscal`, `creditos_ativos`, `valor_liquidado_ag`) VALUES
(17, 'GREEN CARD S/A REFEIÇÕES, COMÉRCIO E SERVIÇOS', '2025-05-06', 432264000.00, '01/2025', '331.944,00', ' 2025NE00021 ', 329510.00, ' 2025OB00075 ', NULL, '0000-00-00', '2025-12-19', NULL, 'Original', '100006/001752/2023', '2025/1470 e 2025/1472', '23/12/2024', 329.51),
(24, 'Contrato teste de nova atualização', '2025-05-12', 4322640.00, '05/*2025', '330', '1504550', 64500000.00, '100', '2025-05-12', '0000-00-00', '2025-05-31', NULL, 'original', '100006/001752/2023', 'nf-50', '260', 51232.00);

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
-- Estrutura da tabela `prestacao_de_contas`
--

CREATE TABLE `prestacao_de_contas` (
  `id` int(11) NOT NULL,
  `contrato_titulo` int(220) NOT NULL,
  `valor_a_prestar` decimal(10,2) NOT NULL,
  `data_pagamento` date NOT NULL,
  `documentos` varchar(255) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `data_cadastro` datetime DEFAULT NULL,
  `estoque_minimo` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `produtos`
--

INSERT INTO `produtos` (`id`, `produto`, `classificacao`, `natureza`, `contabil`, `descricao`, `unidade`, `localizacao`, `custo`, `quantidade`, `nf`, `preco_medio`, `tipo_operacao`, `data_cadastro`, `estoque_minimo`) VALUES
(1, '42400050002', 'Uniformes Tecidos e Aviamentos', '333903003', '2423.03', 'RESPIRADOR DESCARTAVEL TIPO CONCHA', '2', 'xm1', 3.32300, 148, '2', 14.45, 'retirado', '2025-04-08 17:04:42', 10),
(2, '42400050015', 'Uniformes Tecidos e Aviamentos', '333903003', '2423.03', 'MASCARA RESPIRATORIA TIPO SEMI-FACIAL', 'UM', 'xm1', 4.43400, 5, '1', 192.00, 'retirado', '2025-04-08 17:22:26', 5),
(3, '47100016581', 'Material Eletrico,material para conservação e manutenção de Bens', '333903010', '2424.10', 'TUBO NAO METALICO AGUA 40mm 3M SIGA 0040041', 'UM', 'xm1', 44.34200, 35, '1', 1.93, 'retirado', '2025-04-08 17:28:39', 5);

-- --------------------------------------------------------

--
-- Estrutura da tabela `relatorios_agendados`
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
-- Extraindo dados da tabela `relatorios_agendados`
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
  `tipo` enum('Entrada','Saida') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Estrutura da tabela `vencimentos_futuros`
--

CREATE TABLE `vencimentos_futuros` (
  `id` int(11) NOT NULL,
  `contrato_titulo` varchar(255) NOT NULL,
  `tipo_vencimento` enum('Petição','Certidão') NOT NULL,
  `data_vencimento` date NOT NULL,
  `descricao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `certidoes`
--
ALTER TABLE `certidoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_data_vencimento` (`data_vencimento`),
  ADD KEY `idx_documento` (`documento`),
  ADD KEY `fk_contrato` (`contrato_id`);

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
-- Índices para tabela `contratos_parcelas`
--
ALTER TABLE `contratos_parcelas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contrato_id` (`contrato_id`);

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
-- Índices para tabela `emails_salvos`
--
ALTER TABLE `emails_salvos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_email_user` (`email`,`username`);

--
-- Índices para tabela `eventos`
--
ALTER TABLE `eventos`
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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `titulo_unique` (`titulo`);

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
-- Índices para tabela `ordens_compra`
--
ALTER TABLE `ordens_compra`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- Índices para tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contrato_titulo` (`contrato_titulo`);

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
-- Índices para tabela `prestacao_de_contas`
--
ALTER TABLE `prestacao_de_contas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contrato_id` (`contrato_titulo`);

--
-- Índices para tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `relatorios_agendados`
--
ALTER TABLE `relatorios_agendados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_proximo_envio` (`proximo_envio`),
  ADD KEY `idx_email_destinatario` (`email_destinatario`);

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
-- Índices para tabela `vencimentos_futuros`
--
ALTER TABLE `vencimentos_futuros`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contrato_titulo` (`contrato_titulo`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `certidoes`
--
ALTER TABLE `certidoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
-- AUTO_INCREMENT de tabela `contratos_parcelas`
--
ALTER TABLE `contratos_parcelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de tabela `controle_transicao`
--
ALTER TABLE `controle_transicao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
-- AUTO_INCREMENT de tabela `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `fechamento`
--
ALTER TABLE `fechamento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de tabela `funcionario`
--
ALTER TABLE `funcionario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `gestao_contratos`
--
ALTER TABLE `gestao_contratos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `log_eventos`
--
ALTER TABLE `log_eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=653;

--
-- AUTO_INCREMENT de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `ordens_compra`
--
ALTER TABLE `ordens_compra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT de tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

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
-- AUTO_INCREMENT de tabela `prestacao_de_contas`
--
ALTER TABLE `prestacao_de_contas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `relatorios_agendados`
--
ALTER TABLE `relatorios_agendados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `vencimentos_futuros`
--
ALTER TABLE `vencimentos_futuros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `certidoes`
--
ALTER TABLE `certidoes`
  ADD CONSTRAINT `fk_contrato` FOREIGN KEY (`contrato_id`) REFERENCES `gestao_contratos` (`id`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `contratos_parcelas`
--
ALTER TABLE `contratos_parcelas`
  ADD CONSTRAINT `contratos_parcelas_ibfk_1` FOREIGN KEY (`contrato_id`) REFERENCES `gestao_contratos` (`id`);

--
-- Limitadores para a tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  ADD CONSTRAINT `pagamentos_ibfk_1` FOREIGN KEY (`contrato_titulo`) REFERENCES `gestao_contratos` (`titulo`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `prestacao_de_contas`
--
ALTER TABLE `prestacao_de_contas`
  ADD CONSTRAINT `prestacao_de_contas_ibfk_1` FOREIGN KEY (`contrato_titulo`) REFERENCES `gestao_contratos` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `vencimentos_futuros`
--
ALTER TABLE `vencimentos_futuros`
  ADD CONSTRAINT `vencimentos_futuros_ibfk_1` FOREIGN KEY (`contrato_titulo`) REFERENCES `gestao_contratos` (`titulo`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
