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

-- A despejar dados para tabela db1231184.acessorios: ~6 rows (aproximadamente)
INSERT INTO `acessorios` (`id_acessorio`, `id_equipamento`, `codigo_componente`, `designacao`, `numero_serie`) VALUES
	(5, 3, 'EQ-0003.01', 'Circuito respiratório', NULL),
	(6, 3, 'EQ-0003.02', 'Sensor de fluxo', 'FLOW-3001'),
	(7, 7, 'EQ-0007.01', 'Pás de desfibrilhação', NULL),
	(8, 7, 'EQ-0007.02', 'Bateria', 'BAT-D-7001'),
	(9, 9, 'EQ-0009.01', 'Sonda convexa 3.5 MHz', 'CVX-9001'),
	(10, 9, 'EQ-0009.02', 'Sonda linear 7.5 MHz', 'LIN-9002');

-- A despejar dados para tabela db1231184.conteudos_site: ~47 rows (aproximadamente)
INSERT INTO `conteudos_site` (`id_conteudo`, `chave`, `valor`, `seccao`, `data_atualizacao`, `id_utilizador`) VALUES
	(1, 'hero_titulo', 'Gestão Inteligente de Equipamentos Médicos', 'Hero', '2026-06-29 23:14:40', NULL),
	(2, 'hero_subtitulo', 'Plataforma integrada para inventário, documentação e ciclo de vida de equipamento hospitalar. Desenvolvida para apoiar as equipas de engenharia biomédica em hospitais e clínicas de todo o país.', 'Hero', '2026-06-29 23:14:40', NULL),
	(3, 'metrica1_valor', 'ISO 13485', 'Metricas', '2026-06-29 23:14:40', NULL),
	(4, 'metrica1_label', 'Certificação para Eq. Médico', 'Metricas', '2026-06-29 23:14:40', NULL),
	(5, 'metrica2_valor', '+50 000', 'Metricas', '2026-06-29 23:14:40', NULL),
	(6, 'metrica2_label', 'Equipamentos Suportados', 'Metricas', '2026-06-29 23:14:40', NULL),
	(7, 'metrica3_valor', '24/7', 'Metricas', '2026-06-29 23:14:40', NULL),
	(8, 'metrica3_label', 'Suporte Técnico', 'Metricas', '2026-06-29 23:14:40', NULL),
	(9, 'metrica4_valor', 'RGPD', 'Metricas', '2026-06-29 23:14:40', NULL),
	(10, 'metrica4_label', 'Proteção de Dados', 'Metricas', '2026-06-29 23:14:41', NULL),
	(11, 'sobre_titulo', 'Sobre a MedStock Solutions', 'Sobre', '2026-06-29 23:14:41', NULL),
	(12, 'sobre_texto', 'A MedStock Solutions é uma empresa portuguesa especializada no desenvolvimento de soluções de gestão para o setor da saúde. Com mais de 10 anos de experiência, apoiamos hospitais, clínicas e centros de saúde na modernização dos seus processos de gestão de equipamento médico.', 'Sobre', '2026-06-29 23:14:41', NULL),
	(13, 'sobre_topicos', 'Certificação ISO 13485 para equipamento médico,Conformidade com RGPD e legislação portuguesa,Suporte técnico especializado 24/7,Formação incluída na implementação', 'Sobre', '2026-06-29 23:14:41', NULL),
	(14, 'servicos_titulo', 'O que oferecemos', 'Servicos', '2026-06-29 23:14:41', NULL),
	(15, 'servicos_subtitulo', 'Soluções completas para a gestão do ciclo de vida dos equipamentos médicos', 'Servicos', '2026-06-29 23:14:41', NULL),
	(16, 'funcionalidades_titulo', 'Tecnologia ao serviço da saúde', 'Servicos', '2026-06-29 23:14:41', NULL),
	(17, 'funcionalidades_subtitulo', 'Desenvolvido especificamente para responder às exigências da engenharia biomédica hospitalar', 'Servicos', '2026-06-29 23:14:41', NULL),
	(18, 'contactos_titulo', 'Fale connosco', 'Contactos', '2026-06-29 23:14:41', NULL),
	(19, 'contactos_texto', 'Tem alguma dúvida sobre a plataforma MedStock Solutions ou quer agendar uma demonstração para a sua unidade de saúde? O nosso suporte está aqui para o ajudar.', 'Contactos', '2026-06-29 23:14:41', NULL),
	(20, 'contactos_morada', 'ISEP - Instituto Superior de Engenharia do Porto, Rua Dr. António Bernardino de Almeida, 431', 'Contactos', '2026-06-29 23:14:41', NULL),
	(21, 'contactos_telefone', '+351 228 340 500', 'Contactos', '2026-06-29 23:14:41', NULL),
	(22, 'contactos_email', 'suporte@medstock.isep.ipp.pt', 'Contactos', '2026-06-29 23:14:41', NULL),
	(23, 'rodape_texto', 'A plataforma líder para a gestão inteligente do ciclo de vida de equipamentos médicos. Simplifique a sua operação hospitalar connosco.', 'Rodape', '2026-06-29 23:14:41', NULL),
	(24, 'rodape_linkedin', '', 'Rodape', '2026-06-29 23:14:41', NULL),
	(25, 'rodape_github', '', 'Rodape', '2026-06-29 23:14:41', NULL),
	(26, 'rodape_twitter', '', 'Rodape', '2026-06-29 23:14:41', NULL),
	(27, 'rodape_copyright', '© 2026 MedStock Solutions. Todos os direitos reservados.', 'Rodape', '2026-06-29 23:14:41', NULL),
	(28, 'servico1_titulo', 'Inventário Completo', 'Servicos', '2026-06-30 19:07:40', NULL),
	(29, 'servico1_descricao', 'Registo detalhado de todos os equipamentos com dados técnicos, localização, estado e criticidade clínica.', 'Servicos', '2026-06-30 19:07:40', NULL),
	(30, 'servico2_titulo', 'Gestão Documental', 'Servicos', '2026-06-30 19:07:40', NULL),
	(31, 'servico2_descricao', 'Centralização de manuais, certificados de calibração, contratos e relatórios de manutenção.', 'Servicos', '2026-06-30 19:07:40', NULL),
	(32, 'servico3_titulo', 'Garantias e Contratos', 'Servicos', '2026-06-30 19:07:40', NULL),
	(33, 'servico3_descricao', 'Acompanhamento de garantias e contratos de manutenção com alertas automáticos de expiração.', 'Servicos', '2026-06-30 19:07:40', NULL),
	(34, 'servico4_titulo', 'Localização por Serviço', 'Servicos', '2026-06-30 19:07:40', NULL),
	(35, 'servico4_descricao', 'Consulta imediata da localização atual de cada equipamento, organizada por edifício, piso, serviço e sala.', 'Servicos', '2026-06-30 19:07:40', NULL),
	(36, 'servico5_titulo', 'Relatórios e Dashboard', 'Servicos', '2026-06-30 19:07:40', NULL),
	(37, 'servico5_descricao', 'Painéis de controlo com indicadores chave, gráficos e exportação de relatórios.', 'Servicos', '2026-06-30 19:07:40', NULL),
	(38, 'servico6_titulo', 'Gestão de Fornecedores', 'Servicos', '2026-06-30 19:07:40', NULL),
	(39, 'servico6_descricao', 'Registo de fabricantes, distribuidores e prestadores de serviços de assistência técnica.', 'Servicos', '2026-06-30 19:07:40', NULL),
	(40, 'func1', 'Pesquisa avançada por múltiplos critérios', 'Funcionalidades', '2026-06-30 19:07:40', NULL),
	(41, 'func2', 'Alertas automáticos de garantias e documentos', 'Funcionalidades', '2026-06-30 19:07:40', NULL),
	(42, 'func3', 'Controlo de acesso com autenticação segura', 'Funcionalidades', '2026-06-30 19:07:40', NULL),
	(43, 'func4', 'Rastreamento por número de série', 'Funcionalidades', '2026-06-30 19:07:40', NULL),
	(44, 'func5', 'Classificação por criticidade clínica', 'Funcionalidades', '2026-06-30 19:07:40', NULL),
	(45, 'func6', 'Registo do estado atual e evolução de cada equipamento', 'Funcionalidades', '2026-06-30 19:07:40', NULL),
	(46, 'func7', 'Associação de fornecedores por equipamento', 'Funcionalidades', '2026-06-30 19:07:40', NULL),
	(47, 'func8', 'Interface responsiva para mobile e desktop', 'Funcionalidades', '2026-06-30 19:07:40', NULL);

-- A despejar dados para tabela db1231184.documentos: ~10 rows (aproximadamente)
INSERT INTO `documentos` (`id_documento`, `id_equipamento`, `id_fornecedor`, `tipo_documento`, `titulo`, `data_emissao`, `data_validade`, `caminho_ficheiro`, `data_upload`) VALUES
	(1, 1, 1, 'Manual de Utilizador', 'Manual IntelliVue MP5', '2022-03-15', NULL, 'assets/docs/manuais/manual_mp5.pdf', '2026-06-11 14:25:50'),
	(2, 1, 8, 'Certificado de Calibração', 'Certificado Calibração 2024', '2024-02-01', '2025-02-01', 'assets/docs/certificados/cert_cal_eq0001_2024.pdf', '2026-06-11 14:25:50'),
	(3, 3, 2, 'Manual de Serviço', 'Manual Serviço Evita V500', '2021-06-10', NULL, 'assets/docs/manuais/manual_evita.pdf', '2026-06-11 14:25:50'),
	(4, 3, 8, 'Contrato de Manutenção', 'Contrato Full-Service Evita', '2021-06-15', NULL, 'assets/docs/contratos/contrato_evita.pdf', '2026-06-11 14:25:50'),
	(5, 5, 9, 'Certificado de Calibração', 'Certificado Calibração Bomba', '2024-09-01', '2026-09-01', 'assets/docs/certificados/cert_cal_eq0005.pdf', '2026-06-11 14:25:50'),
	(6, 7, 4, 'Declaração CE', 'Declaração CE R Series', '2021-11-20', NULL, 'assets/docs/declaracoes/ce_rseries.pdf', '2026-06-11 14:25:50'),
	(7, 9, 9, 'Certificado de Calibração', 'Certificado Calibração Ecógrafo', '2024-06-01', '2025-06-15', 'assets/docs/certificados/cert_cal_eq0009.pdf', '2026-06-11 14:25:50'),
	(8, 12, 9, 'Certificado de Calibração', 'Certificado Calibração Autoclave', '2023-07-30', '2024-07-30', 'assets/docs/certificados/cert_cal_eq0012.pdf', '2026-06-11 14:25:50'),
	(9, 13, 5, 'Fatura', 'Fatura de Aquisição BS-240', '2020-01-25', NULL, 'assets/docs/faturas/fatura_bs240.pdf', '2026-06-11 14:25:50'),
	(10, 31, NULL, 'Manual de utilizador', NULL, '2026-07-02', NULL, 'assets/docs/sem_ficheiro.pdf', '2026-07-01 15:42:03');

-- A despejar dados para tabela db1231184.edificios: ~3 rows (aproximadamente)
INSERT INTO `edificios` (`id_edificio`, `nome`, `descricao`, `observacoes`) VALUES
	(1, 'Edifício Principal', 'Edifício central com internamento e cuidados intensivos', NULL),
	(2, 'Edifício Nascente', 'Ala de consultas externas e meios complementares de diagnóstico', NULL),
	(3, 'Edifício de Apoio', 'Serviços técnicos, esterilização e laboratórios', NULL);

-- A despejar dados para tabela db1231184.equipamentos: ~27 rows (aproximadamente)
INSERT INTO `equipamentos` (`id_equipamento`, `codigo_interno`, `designacao`, `marca`, `modelo`, `numero_serie`, `ano_fabrico`, `categoria`, `criticidade`, `data_aquisicao`, `custo_aquisicao`, `tipo_entrada`, `estado`, `falta_declaracao_ce`, `falta_manual_utilizador`, `falta_fatura_guia`, `observacoes`, `data_registo`, `id_sala`, `id_servico`, `id_fabricante`) VALUES
	(1, 'EQ-0001', 'Monitor Multiparamétrico', 'Philips', 'INTELLIVUE MP5', 'MP5-2022-45873', 2022, 'Monitorização', 'Suporte de Vida', '2022-03-15', 8500.00, 'Compra', 'Ativo', 0, 0, 0, 'Monitor de sinais vitais da UCI', '2026-06-11 14:25:50', NULL, NULL, NULL),
	(2, 'EQ-0002', 'Monitor Multiparamétrico', 'Philips', 'IntelliVue MP5', 'MP5-2022-45901', 2022, 'Monitorização', 'Suporte de Vida', '2022-03-15', 8500.00, 'Compra', 'Ativo', 0, 0, 0, NULL, '2026-06-11 14:25:50', 4, 2, 1),
	(3, 'EQ-0003', 'Ventilador Pulmonar', 'Dräger', 'Evita V500', 'EV500-2021-9934', 2021, 'Suporte de Vida', 'Suporte de Vida', '2021-06-10', 32000.00, 'Compra', 'Ativo', 0, 0, 0, 'Ventilador de cuidados intensivos', '2026-06-11 14:25:50', 3, 2, 2),
	(4, 'EQ-0004', 'Ventilador Pulmonar', 'Dräger', 'Evita V500', 'EV500-2021-9940', 2021, 'Suporte de Vida', 'Suporte de Vida', '2021-06-10', 32000.00, 'Compra', 'Em Manutenção', 0, 0, 0, NULL, '2026-06-11 14:25:50', 4, 2, 2),
	(5, 'EQ-0005', 'Bomba de Infusão', 'B. Braun', 'INFUSOMAT SPACE', 'INF-2020-88321', 2020, 'Terapia', 'Média', '2020-09-01', 1800.00, 'Compra', 'Ativo', 1, 1, 1, 'Bomba volumétrica', '2026-06-11 14:25:50', 6, 4, 3),
	(6, 'EQ-0006', 'Bomba de Infusão', 'B. Braun', 'Infusomat Space', 'INF-2020-88345', 2020, 'Terapia', 'Média', '2020-09-01', 1800.00, 'Compra', 'Ativo', 0, 1, 0, NULL, '2026-06-11 14:25:50', 6, 4, 3),
	(7, 'EQ-0007', 'Desfibrilhador', 'Zoll', 'R Series', 'ZR-2021-7712', 2021, 'Suporte de Vida', 'Alta', '2021-11-20', 12500.00, 'Compra', 'Ativo', 0, 0, 0, 'Desfibrilhador da urgência', '2026-06-11 14:25:50', 1, 1, 4),
	(8, 'EQ-0008', 'Desfibrilhador', 'Zoll', 'R Series', 'ZR-2021-7720', 2021, 'Suporte de Vida', 'Alta', '2021-11-20', 12500.00, 'Compra', 'Em Calibração', 0, 0, 0, NULL, '2026-06-11 14:25:50', 2, 1, 4),
	(9, 'EQ-0009', 'Ecógrafo', 'Mindray', 'DP-50', 'DP50-2023-1102', 2023, 'Diagnóstico por Imagem', 'Alta', '2023-02-08', 21000.00, 'Compra', 'Ativo', 0, 0, 0, 'Ecógrafo de imagiologia', '2026-06-11 14:25:50', 8, 6, 5),
	(10, 'EQ-0010', 'Ecógrafo Portátil', 'Mindray', 'MX7', 'MX7-2023-3340', 2023, 'Diagnóstico por Imagem', 'Alta', '2023-05-12', 18500.00, 'Compra', 'Ativo', 0, 0, 1, NULL, '2026-06-11 14:25:50', 7, 5, 5),
	(11, 'EQ-0011', 'Eletrocardiógrafo', 'Philips', 'PageWriter TC30', 'TC30-2019-5521', 2019, 'Diagnóstico por Imagem', 'Média', '2019-04-18', 4200.00, 'Compra', 'Ativo', 0, 0, 0, NULL, '2026-06-11 14:25:50', 7, 5, 1),
	(12, 'EQ-0012', 'Autoclave de Esterilização', 'Dräger', 'Vapor 300', 'VAP300-2018-2210', 2018, 'Esterilização', 'Média', '2018-07-30', 15000.00, 'Compra', 'Ativo', 1, 0, 0, 'Esterilização por vapor', '2026-06-11 14:25:50', 9, 7, 2),
	(13, 'EQ-0013', 'Analisador Bioquímico', 'Mindray', 'BS-240', 'BS240-2020-6612', 2020, 'Laboratório', 'Média', '2020-01-25', 9800.00, 'Compra', 'Ativo', 0, 0, 0, NULL, '2026-06-11 14:25:50', 10, 8, 5),
	(14, 'EQ-0014', 'Equipamento de Fisioterapia', 'B. Braun', 'PhysioPlus 200', 'PP200-2017-3301', 2017, 'Reabilitação', 'Baixa', '2017-10-05', NULL, 'Doação', 'Inativo', 0, 1, 1, 'Doado por fundação local', '2026-06-11 14:25:50', 6, 4, 3),
	(15, 'EQ-0015', 'Monitor de Transporte', 'Philips', 'IntelliVue X3', 'X3-2024-0099', 2024, 'Monitorização', 'Alta', '2024-01-10', 6700.00, 'Aluguer', 'Ativo', 0, 0, 0, 'Equipamento em regime de aluguer', '2026-06-11 14:25:50', 1, 1, 1),
	(16, 'EQ-0016', 'Monitor Multiparamétrico De Sinais Vitais', 'Philips', 'MP5', 'SN-DR-2024-002', 2021, 'Monitorização', 'Baixa', '2026-06-17', 212.00, 'Doação', 'Ativo', 0, 0, 0, '', '2026-06-17 00:17:03', NULL, NULL, NULL),
	(17, 'EQ-0017', 'Ventilador', 'Dräger', 'INTELLIVUE MX700', 'SN-PH-2024-001', 2018, 'Monitorização', 'Alta', '2026-06-17', 21215.00, 'Compra', 'Ativo', 0, 0, 0, '', '2026-06-17 01:15:50', NULL, NULL, NULL),
	(21, 'EQ-0018', 'Monitor Multiparamétrico De Sinais Vitais', 'Dräger', 'MP5', 'BS240-2020-6626', 2026, 'Monitorização', 'Média', '2026-06-17', 212.00, 'Doação', 'Ativo', 0, 0, 0, '', '2026-06-17 10:02:20', NULL, NULL, NULL),
	(23, 'EQ-0019', 'Monitor Multiparamétrico De Sinais Vitais', 'Philips', 'MP5', 'BS240-2020-6678', 2026, 'Suporte De Vida', 'Alta', '2026-06-17', 210.00, 'Compra', 'Ativo', 0, 0, 0, '', '2026-06-17 16:45:23', NULL, NULL, NULL),
	(24, 'EQ-0020', 'Desfribilhador', 'Philips', 'MP5', 'SN-PH-2024-0015', 2025, 'Monitorização', 'Média', '2026-06-25', 25.00, 'Compra', 'Abatido', 0, 0, 0, '', '2026-06-25 22:59:06', NULL, 5, NULL),
	(25, 'EQ-0021', 'Diogo Almeida', 'Dräger', 'EVITA INFINITY V500', 'BS240-2020-66125', 2025, 'Monitorização', 'Baixa', '2026-06-25', 25.00, 'Compra', 'Abatido', 1, 1, 1, NULL, '2026-06-25 23:19:15', NULL, 5, 3),
	(26, 'EQ-0022', 'Teste5', 'Philips', 'INTELLIVUE MX700', 'BS240-2020-66267', 2025, 'Laboratório', 'Baixa', '2026-06-25', 0.00, 'Doação', 'Abatido', 1, 1, 1, NULL, '2026-06-25 23:24:55', NULL, 1, 3),
	(27, 'EQ-0023', 'Desfibrilhador Automático Externo', 'Philips', 'HEARTSTART FRX', 'DAE-2024-PH-88731', 2024, 'Suporte De Vida', 'Suporte de Vida', '2026-07-01', 2850.00, 'Compra', 'Ativo', 0, 0, 0, 'Equipamento adquirido no âmbito do reforço do inventário de suporte de vida. Instalado na UCI com formação da equipa realizada em Abril de 2024.', '2026-07-01 14:34:39', NULL, 6, 3),
	(28, 'EQ-0024', 'Desfibrilhador Automático Externo', 'Philips', 'HEARTSTART FRX', 'DAE-2026-PH-12709', 2024, 'Suporte De Vida', 'Suporte de Vida', '2024-03-15', 2850.00, 'Compra', 'Ativo', 1, 1, 1, 'Equipamento adquirido no âmbito do reforço do inventário de suporte de vida. Instalado na UCI com formação da equipa realizada em Abril de 2024.', '2026-07-01 14:45:53', NULL, 7, NULL),
	(29, 'EQ-0025', 'Desfibrilhador Automático Externo', 'Philips', 'HEARTSTART FRX', 'DAE-2026-PH-64084', 2024, 'Suporte De Vida', 'Suporte de Vida', '2024-03-15', 2850.00, 'Compra', 'Ativo', 1, 1, 1, 'Equipamento adquirido no âmbito do reforço do inventário de suporte de vida. Instalado na UCI com formação da equipa realizada em Abril de 2024.', '2026-07-01 14:54:10', NULL, 2, NULL),
	(30, 'EQ-0026', 'Desfibrilhador Automático Externo', 'Philips', 'HEARTSTART FRX', 'DAE-2026-PH-25675', 2025, 'Suporte De Vida', 'Média', '2024-03-15', 2850.00, 'Compra', 'Abatido', 1, 1, 1, 'Equipamento adquirido no âmbito do reforço do inventário de suporte de vida. Instalado na UCI com formação da equipa realizada em Abril de 2024.', '2026-07-01 15:01:48', NULL, 8, 4),
	(31, 'EQ-0027', 'Desfibrilhador Automático Externo', 'Philips', 'HEARTSTART FRX', 'DAE-2026-PH-69810', 2024, 'Suporte De Vida', 'Suporte de Vida', '2024-03-15', 2850.00, 'Compra', 'Ativo', 1, 0, 1, 'Equipamento adquirido no âmbito do reforço do inventário de suporte de vida. Instalado na UCI com formação da equipa realizada em Abril de 2024.', '2026-07-01 15:42:03', NULL, 6, NULL);

-- A despejar dados para tabela db1231184.equipamento_fornecedor: ~26 rows (aproximadamente)
INSERT INTO `equipamento_fornecedor` (`id_equipamento`, `id_fornecedor`, `papel`) VALUES
	(24, 3, 'Comercial'),
	(25, 3, 'Comercial'),
	(27, 3, 'Assistência'),
	(29, 3, 'Assistência'),
	(31, 3, 'Assistência'),
	(5, 6, 'Comercial'),
	(7, 6, 'Comercial'),
	(26, 6, 'Assistência'),
	(3, 7, 'Comercial'),
	(9, 7, 'Comercial'),
	(12, 7, 'Comercial'),
	(3, 8, 'Assistência'),
	(7, 8, 'Assistência'),
	(9, 8, 'Assistência'),
	(5, 9, 'Assistência'),
	(12, 9, 'Assistência'),
	(25, 9, 'Assistência'),
	(29, 9, 'Comercial'),
	(26, 10, 'Comercial'),
	(28, 10, 'Comercial'),
	(30, 10, 'Assistência'),
	(24, 12, 'Assistência'),
	(27, 12, 'Comercial'),
	(28, 12, 'Assistência'),
	(30, 12, 'Comercial'),
	(31, 12, 'Comercial');

-- A despejar dados para tabela db1231184.fornecedores: ~12 rows (aproximadamente)
INSERT INTO `fornecedores` (`id_fornecedor`, `nome_empresa`, `nif`, `tipo_fornecedor`, `telefone_geral`, `email_geral`, `morada`, `website`, `nome_responsavel`, `telefone_responsavel`, `email_responsavel`, `observacoes`) VALUES
	(1, 'Philips Healthcare Portugal', '500123456', 'Fabricante', '210123456', 'geral@philips.pt', 'Lagoas Park, Oeiras', 'www.philips.pt', 'Carlos Mendes', '910111222', 'c.mendes@philips.pt', NULL),
	(2, 'Dräger Portugal, Lda', '501234567', 'Fabricante', '214567890', 'info@draeger.pt', 'Av. do Forte 6, Carnaxide', 'www.draeger.com', 'Ana Pereira', '910222333', 'a.pereira@draeger.pt', NULL),
	(3, 'B. Braun Medical, Lda', '502345678', 'Fabricante', '214888999', 'geral@bbraun.pt', 'Estrada Nacional 249, Queluz', 'www.bbraun.pt', 'Tiago Lopes', '910333444', 't.lopes@bbraun.pt', NULL),
	(4, 'Zoll Medical Iberia', '503456789', 'Fabricante', '213777888', 'iberia@zoll.com', 'Lisboa', 'www.zoll.com', 'Marta Nunes', '910444555', 'm.nunes@zoll.com', NULL),
	(5, 'Mindray Portugal', '504567890', 'Fabricante', '212333444', 'pt@mindray.com', 'Porto', 'www.mindray.com', 'Hugo Faria', '910555666', 'h.faria@mindray.com', NULL),
	(6, 'TechMed Solutions, Lda', '505678901', 'Distribuidor', '225111222', 'comercial@techmed.pt', 'Rua da Boavista 120, Porto', 'www.techmed.pt', 'Sara Antunes', '910666777', 's.antunes@techmed.pt', 'Distribuidor nacional multimarca'),
	(7, 'IberiaMed Serviços, Lda', '506789012', 'Distribuidor', '226222333', 'vendas@iberiamed.pt', 'Maia', 'www.iberiamed.pt', 'Bruno Silva', '910777888', 'b.silva@iberiamed.pt', NULL),
	(8, 'MedServ Técnica, Lda', '507890123', 'Assistência Técnica', '224333444', 'apoio@medserv.pt', 'Rua de Santa Catarina 80, Porto', 'www.medserv.pt', 'Luísa Marques', '910888999', 'l.marques@medserv.pt', 'Assistência preventiva e corretiva'),
	(9, 'BioCal Calibrações, Lda', '508901234', 'Assistência Técnica', '229444555', 'cal@biocal.pt', 'Gondomar', 'www.biocal.pt', 'Nuno Reis', '910999000', 'n.reis@biocal.pt', 'Calibração metrológica acreditada'),
	(10, 'ConsuMed Distribuição, Lda', '509012345', 'Consumíveis', '221555666', 'encomendas@consumed.pt', 'Vila Nova de Gaia', 'www.consumed.pt', 'Patrícia Dias', '910010020', 'p.dias@consumed.pt', 'Consumíveis e acessórios'),
	(11, 'Teste', '123123123', 'Fabricante', '123123123', 'teste@gmail.com', 'Rua do teste, Teste', 'www.empresa.pt', 'Joao Silva', '123456789', 'joao@gmail.com', 'e3e3e3'),
	(12, 'BTL', '159357789', 'Consumíveis', '951478632', 'btl@gmail.com', 'Rua de mil fontes', '', 'Gabriela Domingues', '963852741', 'gabriela.domingues@gmail.com', 'Este fornecedor foi só de teste');

-- A despejar dados para tabela db1231184.garantias_contratos: ~4 rows (aproximadamente)
INSERT INTO `garantias_contratos` (`id_contrato`, `id_equipamento`, `tipo_cobertura`, `referencia`, `id_entidade_responsavel`, `tipo_contrato`, `periodicidade`, `data_inicio`, `data_fim`) VALUES
	(2, 3, 'Contrato Manutenção', 'CT-EVITA-2021', 8, 'Full-Service', 'Trimestral', '2021-06-15', '2026-06-15'),
	(4, 7, 'Garantia', 'GAR-ZR-2021', 4, NULL, NULL, '2021-11-20', '2024-11-20'),
	(5, 9, 'Contrato Manutenção', 'CT-ECO-2023', 8, 'Preventivo', 'Semestral', '2023-02-08', '2026-07-01'),
	(6, 12, 'Contrato Manutenção', 'CT-AUTO-2018', 9, 'Full-Service', 'Anual', '2018-07-30', '2025-07-30');

-- A despejar dados para tabela db1231184.historico_movimentacoes: ~22 rows (aproximadamente)
INSERT INTO `historico_movimentacoes` (`id_movimentacao`, `id_equipamento`, `id_servico_origem`, `id_servico_destino`, `data_movimento`, `motivo`, `id_utilizador`) VALUES
	(1, 1, NULL, 2, '2026-06-11 14:25:51', 'Entrada em inventário', 1),
	(2, 4, 2, 3, '2026-06-11 14:25:51', 'Transferência para Bloco Operatório', 2),
	(3, 7, NULL, 1, '2026-06-11 14:25:51', 'Entrada em inventário', 1),
	(4, 15, 2, 1, '2026-06-11 14:25:51', 'Reafetação a Urgência', 2),
	(5, 24, NULL, 5, '2026-06-25 22:59:06', 'Entrada em inventário', NULL),
	(6, 25, NULL, 5, '2026-06-25 23:19:16', 'Entrada em inventário', NULL),
	(7, 26, NULL, 6, '2026-06-25 23:24:56', 'Entrada em inventário', NULL),
	(8, 26, NULL, 6, '2026-06-27 16:12:24', 'Atualização de registo', NULL),
	(9, 26, NULL, 1, '2026-06-27 16:45:07', 'Atualização de registo', NULL),
	(10, 26, NULL, 1, '2026-06-27 18:22:11', 'Atualização de registo', NULL),
	(11, 26, NULL, 1, '2026-06-28 14:48:10', 'Atualização de registo', NULL),
	(12, 25, NULL, 5, '2026-06-28 14:55:23', 'Atualização de registo', NULL),
	(13, 5, NULL, 4, '2026-06-28 15:38:59', 'Atualização de registo', NULL),
	(14, 26, NULL, 1, '2026-06-28 15:39:28', 'Atualização de registo', NULL),
	(15, 25, NULL, 5, '2026-06-28 15:40:09', 'Atualização de registo', NULL),
	(16, 27, NULL, 6, '2026-07-01 14:34:39', 'Entrada em inventário', 1),
	(17, 28, NULL, 7, '2026-07-01 14:45:53', 'Entrada em inventário', 1),
	(18, 29, NULL, 2, '2026-07-01 14:54:10', 'Entrada em inventário', 1),
	(19, 30, NULL, 8, '2026-07-01 15:01:48', 'Entrada em inventário', 1),
	(20, 30, NULL, 8, '2026-07-01 15:06:38', 'Atualização de registo', 1),
	(21, 30, NULL, 8, '2026-07-01 15:06:54', 'Atualização de registo', 1),
	(22, 31, NULL, 6, '2026-07-01 15:42:03', 'Entrada em inventário', 1);

-- A despejar dados para tabela db1231184.log_acessos: ~6 rows (aproximadamente)
INSERT INTO `log_acessos` (`id_log`, `email`, `id_utilizador`, `tipo_evento`, `ip_address`, `data_evento`, `detalhe`) VALUES
	(1, 'admin@medstock.pt', 1, 'logout', '127.0.0.1', '2026-07-01 14:11:56', 'Sessão terminada pelo utilizador'),
	(2, 'admin@medstock.pt', 1, 'login_sucesso', '127.0.0.1', '2026-07-01 14:11:59', 'Login efetuado com sucesso — perfil: Administrador'),
	(3, 'admin@medstock.pt', 1, 'logout', '127.0.0.1', '2026-07-01 20:16:39', 'Sessão terminada pelo utilizador'),
	(4, 'admin@medstock.pt', 1, 'login_sucesso', '127.0.0.1', '2026-07-01 20:16:47', 'Login efetuado com sucesso — perfil: Administrador'),
	(5, 'admin@medstock.pt', 1, 'logout', '127.0.0.1', '2026-07-01 20:17:08', 'Sessão terminada pelo utilizador'),
	(6, 'admin@medstock.pt', 1, 'login_sucesso', '127.0.0.1', '2026-07-01 20:17:26', 'Login efetuado com sucesso — perfil: Administrador');

-- A despejar dados para tabela db1231184.pisos: ~7 rows (aproximadamente)
INSERT INTO `pisos` (`id_piso`, `id_edificio`, `designacao`, `observacoes`) VALUES
	(1, 1, 'Piso 0', 'Urgência e admissão'),
	(2, 1, 'Piso 1', 'Unidade de Cuidados Intensivos e Bloco Operatório'),
	(3, 1, 'Piso 2', 'Internamento de Medicina'),
	(4, 2, 'Piso 0', 'Consultas externas'),
	(5, 2, 'Piso 2', 'Imagiologia'),
	(6, 3, 'Cave -1', 'Esterilização central'),
	(7, 3, 'Piso 0', 'Laboratório de análises clínicas');

-- A despejar dados para tabela db1231184.salas: ~10 rows (aproximadamente)
INSERT INTO `salas` (`id_sala`, `id_servico`, `identificacao`, `observacoes`) VALUES
	(1, 1, 'Sala de Reanimação 1', NULL),
	(2, 1, 'Box de Urgência 3', NULL),
	(3, 2, 'UCI-1', 'Sala com isolamento de pressão negativa'),
	(4, 2, 'UCI-2', NULL),
	(5, 3, 'Sala Operatória A', NULL),
	(6, 4, 'Enfermaria B', NULL),
	(7, 5, 'Gabinete de Cardiologia 2', NULL),
	(8, 6, 'Sala de Ecografia', NULL),
	(9, 7, 'Sala de Autoclaves', NULL),
	(10, 8, 'Laboratório de Bioquímica', NULL);

-- A despejar dados para tabela db1231184.servicos: ~8 rows (aproximadamente)
INSERT INTO `servicos` (`id_servico`, `id_piso`, `nome`, `centro_custo`, `diretor_responsavel`, `observacoes`) VALUES
	(1, 1, 'Urgência Geral', 'CC-URG-01', 'Dr. António Ferreira', NULL),
	(2, 2, 'Unidade de Cuidados Intensivos', 'CC-UCI-01', 'Dra. Marta Sousa', 'Serviço crítico, equipamentos de suporte de vida'),
	(3, 2, 'Bloco Operatório', 'CC-BLO-01', 'Dr. Rui Oliveira', NULL),
	(4, 3, 'Medicina Interna', 'CC-MED-01', 'Dra. Helena Costa', NULL),
	(5, 4, 'Cardiologia', 'CC-CAR-01', 'Dr. João Martins', NULL),
	(6, 5, 'Imagiologia', 'CC-IMG-01', 'Dra. Sofia Ribeiro', NULL),
	(7, 6, 'Esterilização Central', 'CC-EST-01', 'Enf. Pedro Gomes', NULL),
	(8, 7, 'Patologia Clínica', 'CC-LAB-01', 'Dra. Inês Carvalho', NULL);

-- A despejar dados para tabela db1231184.utilizadores: ~2 rows (aproximadamente)
INSERT INTO `utilizadores` (`id_utilizador`, `nome`, `email`, `password_hash`, `perfil`, `data_criacao`) VALUES
	(1, 'Administrador MedStock', _binary 0x6a4ce126d313454aaf687d46a53853418313b3bc9cc65a736e7e058a58237693, '$2y$10$jy5nBXQg7myihJArsHtw.OqAAEzsmwVn1u/zM39.Oq4PML7HKAvkO', 'Administrador', '2026-06-11 14:25:50'),
	(2, 'Diogo Técnico', _binary 0xa29fb8d1eda04d820012bcadfee17b5eff49d2e0d5eddbd63a722f5fe3a1b5e1, '$2y$10$kvX.PGEi41SwYjHHfT5A7O3QzsPzYJmLVeWSViiRPaA7zzJZrkqpu', 'Técnico', '2026-06-11 14:25:50');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
