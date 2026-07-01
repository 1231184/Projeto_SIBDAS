-- --------------------------------------------------------
-- Anfitrião:                    vsgate-s1.dei.isep.ipp.pt
-- Versão do servidor:           8.0.45 - MySQL Community Server - GPL
-- SO do servidor:               Linux
-- HeidiSQL Versão:              12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- A despejar estrutura da base de dados para db1231184
CREATE DATABASE IF NOT EXISTS `db1231184` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `db1231184`;

-- A despejar estrutura para tabela db1231184.acessorios
CREATE TABLE IF NOT EXISTS `acessorios` (
  `id_acessorio` int NOT NULL AUTO_INCREMENT,
  `id_equipamento` int NOT NULL,
  `codigo_componente` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Campo: acessorioCodigo',
  `designacao` varchar(150) COLLATE utf8mb4_bin NOT NULL COMMENT 'Campo: acessorioDesignacao',
  `numero_serie` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Campo: acessorioSerie',
  PRIMARY KEY (`id_acessorio`),
  KEY `fk_acessorios_equipamento` (`id_equipamento`),
  CONSTRAINT `fk_acessorios_equipamento` FOREIGN KEY (`id_equipamento`) REFERENCES `equipamentos` (`id_equipamento`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Exportação de dados não seleccionada.

-- A despejar estrutura para tabela db1231184.conteudos_site
CREATE TABLE IF NOT EXISTS `conteudos_site` (
  `id_conteudo` int NOT NULL AUTO_INCREMENT,
  `chave` varchar(100) COLLATE utf8mb4_bin NOT NULL COMMENT 'Identificador do campo. Ex: hero_titulo, contacto_email',
  `valor` text COLLATE utf8mb4_bin COMMENT 'Conteudo editavel (texto, lista separada por virgula, URL...)',
  `seccao` varchar(50) COLLATE utf8mb4_bin NOT NULL COMMENT 'Hero, Metricas, Sobre, Servicos, Contactos, Rodape',
  `data_atualizacao` datetime DEFAULT (now()),
  `id_utilizador` int DEFAULT NULL COMMENT 'Quem fez a ultima edicao',
  PRIMARY KEY (`id_conteudo`),
  UNIQUE KEY `uq_conteudos_site_chave` (`chave`),
  KEY `fk_conteudos_utilizador` (`id_utilizador`),
  CONSTRAINT `fk_conteudos_utilizador` FOREIGN KEY (`id_utilizador`) REFERENCES `utilizadores` (`id_utilizador`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Exportação de dados não seleccionada.

-- A despejar estrutura para tabela db1231184.documentos
CREATE TABLE IF NOT EXISTS `documentos` (
  `id_documento` int NOT NULL AUTO_INCREMENT,
  `id_equipamento` int NOT NULL,
  `id_fornecedor` int DEFAULT NULL COMMENT 'Fornecedor associado, se aplicavel',
  `tipo_documento` varchar(100) COLLATE utf8mb4_bin NOT NULL COMMENT 'Manual, Certificado Calibracao, Fatura, Declaracao CE, etc.',
  `titulo` varchar(150) COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Campo: docTitulo',
  `data_emissao` date DEFAULT NULL COMMENT 'Campo: docEmissao',
  `data_validade` date DEFAULT NULL COMMENT 'Campo: dataValidadeDoc. So gera alerta p/ Certificado de Calibracao',
  `caminho_ficheiro` varchar(255) COLLATE utf8mb4_bin NOT NULL COMMENT 'Nome/Caminho do PDF enviado',
  `data_upload` datetime DEFAULT (now()),
  PRIMARY KEY (`id_documento`),
  KEY `fk_documentos_equipamento` (`id_equipamento`),
  KEY `fk_documentos_fornecedor` (`id_fornecedor`),
  CONSTRAINT `fk_documentos_equipamento` FOREIGN KEY (`id_equipamento`) REFERENCES `equipamentos` (`id_equipamento`),
  CONSTRAINT `fk_documentos_fornecedor` FOREIGN KEY (`id_fornecedor`) REFERENCES `fornecedores` (`id_fornecedor`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Exportação de dados não seleccionada.

-- A despejar estrutura para tabela db1231184.edificios
CREATE TABLE IF NOT EXISTS `edificios` (
  `id_edificio` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  `descricao` text COLLATE utf8mb4_bin,
  `observacoes` text COLLATE utf8mb4_bin,
  PRIMARY KEY (`id_edificio`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Exportação de dados não seleccionada.

-- A despejar estrutura para tabela db1231184.equipamentos
CREATE TABLE IF NOT EXISTS `equipamentos` (
  `id_equipamento` int NOT NULL AUTO_INCREMENT,
  `codigo_interno` varchar(50) COLLATE utf8mb4_bin NOT NULL COMMENT 'Campo: internalCode',
  `designacao` varchar(150) COLLATE utf8mb4_bin NOT NULL COMMENT 'Campo: name',
  `marca` varchar(100) COLLATE utf8mb4_bin NOT NULL COMMENT 'Campo: brand',
  `modelo` varchar(100) COLLATE utf8mb4_bin NOT NULL COMMENT 'Campo: model',
  `numero_serie` varchar(100) COLLATE utf8mb4_bin NOT NULL COMMENT 'Campo: serialNumber',
  `ano_fabrico` int DEFAULT NULL COMMENT 'Campo: manufacturingYear',
  `categoria` varchar(100) COLLATE utf8mb4_bin NOT NULL COMMENT 'Campo: categoria',
  `criticidade` varchar(50) COLLATE utf8mb4_bin NOT NULL COMMENT 'Baixa, Media, Alta, Suporte de Vida',
  `data_aquisicao` date DEFAULT NULL COMMENT 'Campo: acquisitionDate',
  `custo_aquisicao` decimal(10,2) DEFAULT NULL COMMENT 'Campo: cost (nulo p/ aluguer/emprestimo)',
  `tipo_entrada` varchar(50) COLLATE utf8mb4_bin NOT NULL COMMENT 'Compra, Doacao, Aluguer, Emprestimo',
  `estado` varchar(50) COLLATE utf8mb4_bin NOT NULL COMMENT 'Ativo, Em Manutencao, Em Calibracao, Inativo, Em Quarentena, Abatido',
  `falta_declaracao_ce` tinyint(1) DEFAULT '0' COMMENT 'Checkbox: faltaCE',
  `falta_manual_utilizador` tinyint(1) DEFAULT '0' COMMENT 'Checkbox: faltaManual',
  `falta_fatura_guia` tinyint(1) DEFAULT '0' COMMENT 'Checkbox: faltaFatura',
  `observacoes` text COLLATE utf8mb4_bin COMMENT 'Campo: observations',
  `data_registo` datetime DEFAULT (now()),
  `id_sala` int DEFAULT NULL,
  `id_servico` int DEFAULT NULL COMMENT 'Serviço onde o equipamento está localizado (sempre preenchido)',
  `id_fabricante` int DEFAULT NULL COMMENT 'Campo: manufacturer',
  PRIMARY KEY (`id_equipamento`),
  UNIQUE KEY `uq_equipamentos_codigo_interno` (`codigo_interno`),
  UNIQUE KEY `uq_equipamentos_numero_serie` (`numero_serie`),
  KEY `fk_equipamentos_sala` (`id_sala`),
  KEY `fk_equipamentos_fabricante` (`id_fabricante`),
  KEY `fk_equipamentos_servico` (`id_servico`),
  CONSTRAINT `fk_equipamentos_fabricante` FOREIGN KEY (`id_fabricante`) REFERENCES `fornecedores` (`id_fornecedor`),
  CONSTRAINT `fk_equipamentos_sala` FOREIGN KEY (`id_sala`) REFERENCES `salas` (`id_sala`),
  CONSTRAINT `fk_equipamentos_servico` FOREIGN KEY (`id_servico`) REFERENCES `servicos` (`id_servico`),
  CONSTRAINT `ck_equipamentos_ano_fabrico` CHECK (((`ano_fabrico` is null) or (`ano_fabrico` > 1900))),
  CONSTRAINT `ck_equipamentos_criticidade` CHECK ((`criticidade` in (_utf8mb4'Baixa',_utf8mb4'Média',_utf8mb4'Alta',_utf8mb4'Suporte de Vida'))),
  CONSTRAINT `ck_equipamentos_custo` CHECK (((`custo_aquisicao` is null) or (`custo_aquisicao` >= 0))),
  CONSTRAINT `ck_equipamentos_estado` CHECK ((`estado` in (_utf8mb4'Ativo',_utf8mb4'Em Manutenção',_utf8mb4'Em Calibração',_utf8mb4'Inativo',_utf8mb4'Em Quarentena',_utf8mb4'Abatido'))),
  CONSTRAINT `ck_equipamentos_tipo_entrada` CHECK ((`tipo_entrada` in (_utf8mb4'Compra',_utf8mb4'Doação',_utf8mb4'Aluguer',_utf8mb4'Empréstimo')))
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Exportação de dados não seleccionada.

-- A despejar estrutura para tabela db1231184.equipamento_fornecedor
CREATE TABLE IF NOT EXISTS `equipamento_fornecedor` (
  `id_equipamento` int NOT NULL,
  `id_fornecedor` int NOT NULL,
  `papel` varchar(50) COLLATE utf8mb4_bin NOT NULL COMMENT 'Comercial, Assistencia, Consumiveis',
  PRIMARY KEY (`id_equipamento`,`id_fornecedor`,`papel`),
  KEY `fk_ef_fornecedor` (`id_fornecedor`),
  CONSTRAINT `fk_ef_equipamento` FOREIGN KEY (`id_equipamento`) REFERENCES `equipamentos` (`id_equipamento`),
  CONSTRAINT `fk_ef_fornecedor` FOREIGN KEY (`id_fornecedor`) REFERENCES `fornecedores` (`id_fornecedor`),
  CONSTRAINT `ck_equip_forn_papel` CHECK ((`papel` in (_utf8mb4'Comercial',_utf8mb4'Assistência',_utf8mb4'Consumíveis')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Exportação de dados não seleccionada.

-- A despejar estrutura para tabela db1231184.fornecedores
CREATE TABLE IF NOT EXISTS `fornecedores` (
  `id_fornecedor` int NOT NULL AUTO_INCREMENT,
  `nome_empresa` varchar(150) COLLATE utf8mb4_bin NOT NULL,
  `nif` varchar(20) COLLATE utf8mb4_bin DEFAULT NULL,
  `tipo_fornecedor` varchar(50) COLLATE utf8mb4_bin NOT NULL COMMENT 'Fabricante, Distribuidor, Assistencia Tecnica, Consumiveis',
  `telefone_geral` varchar(20) COLLATE utf8mb4_bin NOT NULL,
  `email_geral` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  `morada` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `website` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL,
  `nome_responsavel` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL,
  `telefone_responsavel` varchar(20) COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Campo: Telefone Direto / Telemovel',
  `email_responsavel` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Campo: Email Direto',
  `observacoes` text COLLATE utf8mb4_bin,
  PRIMARY KEY (`id_fornecedor`),
  UNIQUE KEY `uq_fornecedores_nif` (`nif`),
  CONSTRAINT `ck_fornecedores_tipo` CHECK ((`tipo_fornecedor` in (_utf8mb4'Fabricante',_utf8mb4'Distribuidor',_utf8mb4'Assistência Técnica',_utf8mb4'Consumíveis')))
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Exportação de dados não seleccionada.

-- A despejar estrutura para tabela db1231184.garantias_contratos
CREATE TABLE IF NOT EXISTS `garantias_contratos` (
  `id_contrato` int NOT NULL AUTO_INCREMENT,
  `id_equipamento` int NOT NULL,
  `tipo_cobertura` varchar(50) COLLATE utf8mb4_bin NOT NULL COMMENT 'Garantia ou Contrato Manutencao',
  `referencia` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Campo: referenciaContrato',
  `id_entidade_responsavel` int DEFAULT NULL COMMENT 'Campo: entidadeContrato',
  `tipo_contrato` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Campo: tipoContrato (Ex: Full-Service)',
  `periodicidade` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Campo: periodicidadeContrato',
  `data_inicio` date NOT NULL COMMENT 'garantiaInicio ou contratoInicio',
  `data_fim` date NOT NULL COMMENT 'garantiaFim ou contratoFim (usado nos alertas)',
  PRIMARY KEY (`id_contrato`),
  KEY `fk_garantias_equipamento` (`id_equipamento`),
  KEY `fk_garantias_entidade` (`id_entidade_responsavel`),
  CONSTRAINT `fk_garantias_entidade` FOREIGN KEY (`id_entidade_responsavel`) REFERENCES `fornecedores` (`id_fornecedor`),
  CONSTRAINT `fk_garantias_equipamento` FOREIGN KEY (`id_equipamento`) REFERENCES `equipamentos` (`id_equipamento`),
  CONSTRAINT `ck_garantias_datas` CHECK ((`data_fim` >= `data_inicio`)),
  CONSTRAINT `ck_garantias_tipo_cobertura` CHECK ((`tipo_cobertura` in (_utf8mb4'Garantia',_utf8mb4'Contrato Manutenção')))
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Exportação de dados não seleccionada.

-- A despejar estrutura para tabela db1231184.historico_movimentacoes
CREATE TABLE IF NOT EXISTS `historico_movimentacoes` (
  `id_movimentacao` int NOT NULL AUTO_INCREMENT,
  `id_equipamento` int NOT NULL,
  `id_servico_origem` int DEFAULT NULL,
  `id_servico_destino` int NOT NULL,
  `data_movimento` datetime DEFAULT (now()),
  `motivo` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Ex: Transferencia de Servico, Entrada em Inventario',
  `id_utilizador` int DEFAULT NULL,
  PRIMARY KEY (`id_movimentacao`),
  KEY `fk_hist_equipamento` (`id_equipamento`),
  KEY `fk_hist_servico_origem` (`id_servico_origem`),
  KEY `fk_hist_servico_destino` (`id_servico_destino`),
  KEY `fk_hist_utilizador` (`id_utilizador`),
  CONSTRAINT `fk_hist_equipamento` FOREIGN KEY (`id_equipamento`) REFERENCES `equipamentos` (`id_equipamento`),
  CONSTRAINT `fk_hist_servico_destino` FOREIGN KEY (`id_servico_destino`) REFERENCES `servicos` (`id_servico`),
  CONSTRAINT `fk_hist_servico_origem` FOREIGN KEY (`id_servico_origem`) REFERENCES `servicos` (`id_servico`),
  CONSTRAINT `fk_hist_utilizador` FOREIGN KEY (`id_utilizador`) REFERENCES `utilizadores` (`id_utilizador`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Exportação de dados não seleccionada.

-- A despejar estrutura para tabela db1231184.log_acessos
CREATE TABLE IF NOT EXISTS `log_acessos` (
  `id_log` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `id_utilizador` int DEFAULT NULL,
  `tipo_evento` varchar(50) COLLATE utf8mb4_bin NOT NULL COMMENT 'login_sucesso, login_falhado, logout',
  `ip_address` varchar(45) COLLATE utf8mb4_bin DEFAULT NULL,
  `data_evento` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `detalhe` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  PRIMARY KEY (`id_log`),
  KEY `fk_log_utilizador` (`id_utilizador`),
  CONSTRAINT `fk_log_utilizador` FOREIGN KEY (`id_utilizador`) REFERENCES `utilizadores` (`id_utilizador`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Exportação de dados não seleccionada.

-- A despejar estrutura para tabela db1231184.pisos
CREATE TABLE IF NOT EXISTS `pisos` (
  `id_piso` int NOT NULL AUTO_INCREMENT,
  `id_edificio` int NOT NULL,
  `designacao` varchar(50) COLLATE utf8mb4_bin NOT NULL COMMENT 'Ex: Piso 2, Cave -1',
  `observacoes` text COLLATE utf8mb4_bin,
  PRIMARY KEY (`id_piso`),
  UNIQUE KEY `uq_pisos_edificio_designacao` (`id_edificio`,`designacao`),
  CONSTRAINT `fk_pisos_edificio` FOREIGN KEY (`id_edificio`) REFERENCES `edificios` (`id_edificio`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Exportação de dados não seleccionada.

-- A despejar estrutura para tabela db1231184.salas
CREATE TABLE IF NOT EXISTS `salas` (
  `id_sala` int NOT NULL AUTO_INCREMENT,
  `id_servico` int NOT NULL,
  `identificacao` varchar(100) COLLATE utf8mb4_bin NOT NULL COMMENT 'Ex: Gabinete 3, BO-1, Enfermaria A',
  `observacoes` text COLLATE utf8mb4_bin,
  PRIMARY KEY (`id_sala`),
  KEY `fk_salas_servico` (`id_servico`),
  CONSTRAINT `fk_salas_servico` FOREIGN KEY (`id_servico`) REFERENCES `servicos` (`id_servico`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Exportação de dados não seleccionada.

-- A despejar estrutura para tabela db1231184.servicos
CREATE TABLE IF NOT EXISTS `servicos` (
  `id_servico` int NOT NULL AUTO_INCREMENT,
  `id_piso` int NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_bin NOT NULL COMMENT 'Ex: Cardiologia, Consultas Externas',
  `centro_custo` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Campo: Centro de Custo',
  `diretor_responsavel` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Campo: Diretor / Responsavel',
  `observacoes` text COLLATE utf8mb4_bin,
  PRIMARY KEY (`id_servico`),
  KEY `fk_servicos_piso` (`id_piso`),
  CONSTRAINT `fk_servicos_piso` FOREIGN KEY (`id_piso`) REFERENCES `pisos` (`id_piso`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Exportação de dados não seleccionada.

-- A despejar estrutura para tabela db1231184.utilizadores
CREATE TABLE IF NOT EXISTS `utilizadores` (
  `id_utilizador` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  `email` blob NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `perfil` varchar(50) COLLATE utf8mb4_bin NOT NULL COMMENT 'Administrador, Tecnico',
  `data_criacao` datetime DEFAULT (now()),
  PRIMARY KEY (`id_utilizador`),
  CONSTRAINT `ck_utilizadores_perfil` CHECK ((`perfil` in (_utf8mb4'Administrador',_utf8mb4'Técnico')))
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Exportação de dados não seleccionada.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
