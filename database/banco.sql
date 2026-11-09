-- Banco de dados do SuperTrain

CREATE DATABASE IF NOT EXISTS `supertrain_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `supertrain_db`;

-- Tabela `estacoes`
CREATE TABLE `estacoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `tipo_ponto` enum('estacao','terminal','patio_manobras') NOT NULL DEFAULT 'estacao',
  `cidade` varchar(80) NOT NULL DEFAULT 'Joinville',
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `capacidade_plataforma` int(11) NOT NULL DEFAULT 500,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `criado_em` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`),
  KEY `idx_estacoes_nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `estacoes` (`id`, `codigo`, `nome`, `tipo_ponto`, `cidade`, `latitude`, `longitude`, `capacidade_plataforma`, `ativo`, `criado_em`) VALUES
(1, 'EST-IRIRIU', 'Estação Iririú', 'estacao', 'Joinville', -26.28250000, -48.82500000, 600, 1, '2026-09-28 08:01:47'),
(2, 'TERM-NORTE', 'Terminal Norte', 'terminal', 'Joinville', -26.25500000, -48.84800000, 1200, 1, '2026-09-28 08:01:47'),
(3, 'EST-PARAIS', 'Estação Paraíso', 'estacao', 'Joinville', -26.27000000, -48.83500000, 500, 1, '2026-09-28 08:01:47'),
(4, 'TERM-CENTRO', 'Terminal Centro', 'terminal', 'Joinville', -26.30450000, -48.84870000, 2500, 1, '2026-09-28 08:01:47'),
(5, 'PATIO-SUL', 'Pátio de Manobras Sul', 'patio_manobras', 'Joinville', -26.34500000, -48.86500000, 400, 1, '2026-09-28 08:01:47');

-- Tabela `kpis_malha_historico`
CREATE TABLE `kpis_malha_historico` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data_referencia` date NOT NULL,
  `pontualidade_global_percentual` decimal(5,2) NOT NULL DEFAULT 98.40,
  `passageiros_transportados` int(11) NOT NULL DEFAULT 342180,
  `consumo_energetico_kwh_km` decimal(5,2) NOT NULL DEFAULT 4.10,
  `confiabilidade_operacional_percentual` decimal(5,2) NOT NULL DEFAULT 99.20,
  `lotacao_media_percentual` decimal(5,2) NOT NULL DEFAULT 68.00,
  `criado_em` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `data_referencia` (`data_referencia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `kpis_malha_historico` (`id`, `data_referencia`, `pontualidade_global_percentual`, `passageiros_transportados`, `consumo_energetico_kwh_km`, `confiabilidade_operacional_percentual`, `lotacao_media_percentual`, `criado_em`) VALUES
(1, '2026-08-18', 98.40, 342180, 4.10, 99.20, 68.00, '2026-09-28 08:01:47'),
(2, '2026-08-17', 98.10, 339450, 4.25, 98.90, 71.00, '2026-09-28 08:01:47'),
(3, '2026-08-16', 99.00, 210800, 3.95, 99.50, 52.00, '2026-09-28 08:01:47');

-- Tabela `linhas`
CREATE TABLE `linhas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo_linha` varchar(10) NOT NULL,
  `nome_linha` varchar(100) NOT NULL,
  `sentido_padrao` varchar(50) NOT NULL,
  `tipo_linha` enum('passageiros','carga','expresso','misto') NOT NULL DEFAULT 'passageiros',
  `status_operacional` enum('ativo','parado','manutencao','atencao') NOT NULL DEFAULT 'ativo',
  `cor_hex` varchar(7) NOT NULL DEFAULT '#00d2ff',
  `extensao_km` decimal(6,2) NOT NULL DEFAULT 0.00,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `criado_em` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo_linha` (`codigo_linha`),
  KEY `idx_linhas_codigo` (`codigo_linha`),
  KEY `idx_linhas_status` (`status_operacional`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `linhas` (`id`, `codigo_linha`, `nome_linha`, `sentido_padrao`, `tipo_linha`, `status_operacional`, `cor_hex`, `extensao_km`, `ativo`, `criado_em`) VALUES
(1, '130', 'Linha 130 (Norte/Sul)', 'Norte / Sul', 'passageiros', 'ativo', '#00d2ff', 24.80, 1, '2026-09-28 08:01:47'),
(2, '140', 'Linha 140 (Sul/Norte)', 'Sul / Norte', 'carga', 'ativo', '#20c997', 28.50, 1, '2026-09-28 08:01:47'),
(3, '142', 'Linha 142 (Oeste/Leste)', 'Oeste / Leste', 'carga', 'parado', '#ffc107', 32.10, 1, '2026-09-28 08:01:47'),
(4, '174', 'Linha 174 (Leste/Norte)', 'Leste / Norte', 'carga', 'manutencao', '#fd7e14', 19.40, 1, '2026-09-28 08:01:47'),
(5, '001', 'Linha 001 (Centro - Expresso)', 'Centro', 'expresso', 'ativo', '#e03131', 12.00, 1, '2026-09-28 08:01:47');

-- Tabela `paises`
CREATE TABLE `paises` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(30) NOT NULL,
  `nome` varchar(80) NOT NULL,
  `sigla` char(2) NOT NULL,
  `codigo_telefonico` varchar(10) DEFAULT NULL,
  `criado_em` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `paises` (`id`, `codigo`, `nome`, `sigla`, `codigo_telefonico`, `criado_em`) VALUES
(1, 'brasil', 'Brasil', 'BR', '+55', '2026-09-28 08:01:47'),
(2, 'estadosUnidos', 'Estados Unidos', 'US', '+1', '2026-09-28 08:01:47'),
(3, 'russia', 'Rússia', 'RU', '+7', '2026-09-28 08:01:47'),
(4, 'camboja', 'Camboja', 'KH', '+855', '2026-09-28 08:01:47'),
(5, 'japao', 'Japão', 'JP', '+81', '2026-09-28 08:01:47'),
(6, 'alemanha', 'Alemanha', 'DE', '+49', '2026-09-28 08:01:47');

-- Tabela `estimativas_chegada`
CREATE TABLE `estimativas_chegada` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `linha_id` int(11) NOT NULL,
  `estacao_id` int(11) DEFAULT NULL,
  `sentido` varchar(50) NOT NULL,
  `nivel_fluxo` enum('normal','atencao','intenso') NOT NULL DEFAULT 'normal',
  `capacidade_percentual` int(11) NOT NULL DEFAULT 50,
  `tempo_estimado_minutos` int(11) NOT NULL DEFAULT 5,
  `atualizado_em` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_estimativas_estacao` (`estacao_id`),
  KEY `idx_estimativas_linha` (`linha_id`),
  CONSTRAINT `fk_estimativas_estacao` FOREIGN KEY (`estacao_id`) REFERENCES `estacoes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_estimativas_linha` FOREIGN KEY (`linha_id`) REFERENCES `linhas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `estimativas_chegada` (`id`, `linha_id`, `estacao_id`, `sentido`, `nivel_fluxo`, `capacidade_percentual`, `tempo_estimado_minutos`, `atualizado_em`) VALUES
(1, 1, 4, 'Norte / Sul', 'normal', 30, 2, '2026-09-28 08:01:47'),
(2, 3, 4, 'Oeste / Leste', 'atencao', 60, 5, '2026-09-28 08:01:47'),
(3, 4, 2, 'Leste / Norte', 'atencao', 50, 7, '2026-09-28 08:01:47'),
(4, 1, 2, 'Sul / Norte', 'intenso', 80, 10, '2026-09-28 08:01:47'),
(5, 5, 4, 'Centro', 'intenso', 100, 14, '2026-09-28 08:01:47');

-- Tabela `itinerarios_estacoes`
CREATE TABLE `itinerarios_estacoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `linha_id` int(11) NOT NULL,
  `estacao_id` int(11) NOT NULL,
  `ordem_sequencia` int(11) NOT NULL,
  `distancia_proxima_km` decimal(5,2) DEFAULT 0.00,
  `tempo_parada_segundos` int(11) DEFAULT 45,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_linha_sequencia` (`linha_id`,`ordem_sequencia`),
  KEY `fk_itinerarios_estacao` (`estacao_id`),
  CONSTRAINT `fk_itinerarios_estacao` FOREIGN KEY (`estacao_id`) REFERENCES `estacoes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_itinerarios_linha` FOREIGN KEY (`linha_id`) REFERENCES `linhas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `itinerarios_estacoes` (`id`, `linha_id`, `estacao_id`, `ordem_sequencia`, `distancia_proxima_km`, `tempo_parada_segundos`) VALUES
(1, 1, 2, 1, 3.20, 60),
(2, 1, 3, 2, 2.50, 45),
(3, 1, 1, 3, 4.10, 45),
(4, 1, 4, 4, 5.00, 60),
(5, 5, 4, 1, 4.00, 30);

-- Tabela `rotas`
CREATE TABLE `rotas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `linha_id` int(11) DEFAULT NULL,
  `direcao` varchar(50) NOT NULL,
  `estacao_origem_id` int(11) DEFAULT NULL,
  `estacao_destino_id` int(11) DEFAULT NULL,
  `horario_partida` time NOT NULL,
  `horario_chegada_previsto` time DEFAULT NULL,
  `tipo_composicao` enum('passageiro','carga','expresso') NOT NULL DEFAULT 'passageiro',
  `status_rota` enum('ativa','desvio','interrompida','programada') DEFAULT 'ativa',
  `dias_operacao` varchar(50) DEFAULT 'DIARIO',
  `criado_em` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_rotas_origem` (`estacao_origem_id`),
  KEY `fk_rotas_destino` (`estacao_destino_id`),
  KEY `idx_rotas_linha` (`linha_id`),
  KEY `idx_rotas_horario` (`horario_partida`),
  CONSTRAINT `fk_rotas_destino` FOREIGN KEY (`estacao_destino_id`) REFERENCES `estacoes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_rotas_linha` FOREIGN KEY (`linha_id`) REFERENCES `linhas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rotas_origem` FOREIGN KEY (`estacao_origem_id`) REFERENCES `estacoes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `rotas` (`id`, `linha_id`, `direcao`, `estacao_origem_id`, `estacao_destino_id`, `horario_partida`, `horario_chegada_previsto`, `tipo_composicao`, `status_rota`, `dias_operacao`, `criado_em`) VALUES
(7, NULL, '142 Norte - Sul', NULL, NULL, '08:23:00', NULL, 'passageiro', 'ativa', 'DIARIO', '2026-10-19 08:21:11'),
(10, NULL, '122 Norte - Leste', NULL, NULL, '15:10:00', NULL, 'carga', 'ativa', 'DIARIO', '2026-10-19 09:04:14'),
(11, NULL, '090 Norte - Oeste', NULL, NULL, '12:30:00', NULL, 'passageiro', 'ativa', 'DIARIO', '2026-10-19 09:09:15'),
(14, NULL, '030 Sul - Oeste', NULL, NULL, '12:26:00', NULL, 'passageiro', 'ativa', 'DIARIO', '2026-10-19 09:21:59'),
(19, NULL, '120 Norte - Leste', NULL, NULL, '11:33:00', NULL, 'expresso', 'ativa', 'DIARIO', '2026-10-19 09:31:19'),
(23, NULL, '130 Norte - Sul', NULL, NULL, '12:00:00', NULL, 'passageiro', 'ativa', 'DIARIO', '2026-10-19 09:56:37'),
(24, NULL, '220 Leste - Oeste', NULL, NULL, '07:40:00', NULL, 'carga', 'ativa', 'DIARIO', '2026-10-23 12:04:44');

-- Tabela `sensores`
CREATE TABLE `sensores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo_identificador` varchar(50) NOT NULL,
  `modelo_tipo` enum('ultrassonico','movimento','localizador','temperatura') NOT NULL,
  `nome_modelo` varchar(100) NOT NULL,
  `estacao_id` int(11) NOT NULL,
  `localizacao_detalhe` varchar(150) NOT NULL,
  `status_sensor` enum('operacional','alerta','em_calibracao','inativo') NOT NULL DEFAULT 'operacional',
  `limite_minimo` decimal(10,2) DEFAULT NULL,
  `limite_maximo` decimal(10,2) DEFAULT NULL,
  `unidade_medida` varchar(20) NOT NULL DEFAULT '°C',
  `data_instalacao` date DEFAULT curdate(),
  `criado_em` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo_identificador` (`codigo_identificador`),
  KEY `fk_sensores_estacao` (`estacao_id`),
  KEY `idx_sensores_tipo` (`modelo_tipo`),
  KEY `idx_sensores_status` (`status_sensor`),
  CONSTRAINT `fk_sensores_estacao` FOREIGN KEY (`estacao_id`) REFERENCES `estacoes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `sensores` (`id`, `codigo_identificador`, `modelo_tipo`, `nome_modelo`, `estacao_id`, `localizacao_detalhe`, `status_sensor`, `limite_minimo`, `limite_maximo`, `unidade_medida`, `data_instalacao`, `criado_em`) VALUES
(1, 'SNS-ULTRA-01', 'ultrassonico', 'Sensor Ultrassônico de Proximidade', 1, 'Estação Iririú - Plataforma 01', 'operacional', 0.50, 15.00, 'metros', '2026-09-28', '2026-09-28 08:01:47'),
(2, 'SNS-PRES-02', 'movimento', 'Sensor de Presença & Movimento', 2, 'Terminal Norte - Agulha de Entrada', 'operacional', 0.00, 1.00, 'booleano', '2026-09-28', '2026-09-28 08:01:47'),
(3, 'SNS-RFID-03', 'localizador', 'Localizador Óptico / RFID', 3, 'Estação Paraíso - Bloco de Baliza B', 'operacional', 0.00, 100.00, 'ms', '2026-09-28', '2026-09-28 08:01:47'),
(4, 'SNS-TERM-04', 'temperatura', 'Sensor Térmico de Trilhos', 4, 'Terminal Centro - Trilho Central 02', 'operacional', 0.00, 65.00, '°C', '2026-09-28', '2026-09-28 08:01:47'),
(5, 'SNS-TERM-05', 'temperatura', 'Sensor Térmico de Trilhos', 5, 'Pátio de Manobras Sul - Linha de Desvio', 'em_calibracao', 0.00, 65.00, '°C', '2026-09-28', '2026-09-28 08:01:47');

-- Tabela `trens`
CREATE TABLE `trens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prefixo` varchar(20) NOT NULL,
  `modelo_composicao` varchar(100) NOT NULL DEFAULT 'SuperTrain Série 2000',
  `linha_id` int(11) DEFAULT NULL,
  `tipo` enum('passageiros','carga','expresso') NOT NULL DEFAULT 'passageiros',
  `status_operacional` enum('ativo','parado','manutencao') NOT NULL DEFAULT 'ativo',
  `capacidade_maxima` int(11) NOT NULL DEFAULT 800,
  `velocidade_maxima_kmh` decimal(5,2) NOT NULL DEFAULT 80.00,
  `ano_fabricacao` year(4) NOT NULL DEFAULT 2024,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `criado_em` datetime DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `prefixo` (`prefixo`),
  KEY `idx_trens_status` (`status_operacional`),
  KEY `idx_trens_linha` (`linha_id`),
  CONSTRAINT `fk_trens_linha` FOREIGN KEY (`linha_id`) REFERENCES `linhas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `trens` (`id`, `prefixo`, `modelo_composicao`, `linha_id`, `tipo`, `status_operacional`, `capacidade_maxima`, `velocidade_maxima_kmh`, `ano_fabricacao`, `ativo`, `criado_em`, `atualizado_em`) VALUES
(1, 'Trem 01', 'Alstom Metropolis 2026', 1, 'passageiros', 'manutencao', 1200, 90.00, '2025', 1, '2026-09-28 08:01:47', '2026-10-19 10:00:33'),
(2, 'Trem 02', 'GE Transportation Heavy', 2, 'carga', 'ativo', 2800, 75.00, '2024', 1, '2026-09-28 08:01:47', '2026-09-28 08:01:47'),
(3, 'Trem 03', 'EMD Progress Rail SD70', 4, 'carga', 'manutencao', 2500, 70.00, '2023', 1, '2026-09-28 08:01:47', '2026-09-28 08:01:47'),
(4, 'Trem 04', 'Stadler EuroDual Carga', 3, 'carga', 'parado', 3200, 65.00, '2024', 1, '2026-09-28 08:01:47', '2026-09-28 08:01:47'),
(5, 'Carga 055', 'GE AC4400CW', 2, 'carga', 'ativo', 3000, 75.00, '2023', 1, '2026-09-28 08:01:47', '2026-10-09 11:24:42'),
(6, 'Trem 130', 'Alstom Metropolis ST-X', 1, 'passageiros', 'ativo', 1200, 90.00, '2026', 1, '2026-09-28 08:01:47', '2026-09-28 08:01:47'),
(7, 'Trem 140', 'Siemens Charger Heavy', 2, 'carga', 'ativo', 2800, 80.00, '2025', 1, '2026-09-28 08:01:47', '2026-09-28 08:01:47');

-- Tabela `usuarios`
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(150) NOT NULL,
  `nome_usuario` varchar(50) DEFAULT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `pais_id` int(11) NOT NULL,
  `status_conta` enum('ativo','inativo','bloqueado','pendente') NOT NULL DEFAULT 'ativo',
  `ultimo_login` datetime DEFAULT NULL,
  `criado_em` datetime DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `cargo` enum('gestor','operador','cliente') DEFAULT 'cliente',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `nome_usuario` (`nome_usuario`),
  KEY `fk_usuarios_pais` (`pais_id`),
  CONSTRAINT `fk_usuarios_pais` FOREIGN KEY (`pais_id`) REFERENCES `paises` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `usuarios` (`id`, `email`, `nome_usuario`, `senha_hash`, `pais_id`, `status_conta`, `ultimo_login`, `criado_em`, `atualizado_em`, `cargo`) VALUES
(1, 'carlos.silva@supertrain.com', 'carlos.silva', '$2y$12$eN98Wk0R57yqI2mQo3v1te6hSkmWf67J51J0X6wJkeM5G9i7Qx2gW', 1, 'ativo', NULL, '2026-01-10 08:00:00', '2026-10-26 08:27:46', 'gestor'),
(2, 'gabriel.c@supertrain.com', 'gabriel.custodio', '$2y$12$eN98Wk0R57yqI2mQo3v1te6hSkmWf67J51J0X6wJkeM5G9i7Qx2gW', 1, 'ativo', NULL, '2026-02-01 09:30:00', '2026-10-26 08:27:46', 'gestor'),
(3, 'vinicius.l@supertrain.com', 'vinicius.lima', '$2y$12$eN98Wk0R57yqI2mQo3v1te6hSkmWf67J51J0X6wJkeM5G9i7Qx2gW', 1, 'ativo', NULL, '2026-02-01 09:30:00', '2026-10-26 08:27:46', 'gestor'),
(4, 'gabriel_c_jorge@estudante.sesisenai.org.br', 'Gabrielzinhu', '$2y$10$CDJtDb.gRFPl04QtudUWS.Dr5q/FugpI/NmG8mHs/JPMjyjKBQhta', 1, 'ativo', NULL, '2026-09-28 08:04:37', '2026-10-26 09:08:19', 'cliente'),
(13, 'paulo_h_schulz@estudante.sesisenai.org.br', 'Paulo Schulz', '$2y$10$WUaLeqiLpwBOZq81Qyb8c.FxmzYME6HnNumcLfA4uu.PGYnEA8.rS', 1, 'ativo', NULL, '2026-10-23 11:46:04', '2026-10-26 09:08:05', 'gestor');

-- Tabela `leituras_sensores`
CREATE TABLE `leituras_sensores` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sensor_id` int(11) NOT NULL,
  `valor_lido` decimal(10,2) NOT NULL,
  `unidade_medida` varchar(20) NOT NULL,
  `alerta_disparado` tinyint(1) NOT NULL DEFAULT 0,
  `mensagem_status` varchar(255) DEFAULT NULL,
  `data_hora` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_leituras_sensor_data` (`sensor_id`,`data_hora`),
  CONSTRAINT `fk_leituras_sensor` FOREIGN KEY (`sensor_id`) REFERENCES `sensores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `leituras_sensores` (`id`, `sensor_id`, `valor_lido`, `unidade_medida`, `alerta_disparado`, `mensagem_status`, `data_hora`) VALUES
(1, 1, 4.20, 'metros', 0, 'Detecção normal de aproximação', '2026-08-18 08:44:00'),
(2, 2, 1.00, 'booleano', 0, 'Composição passando sobre a agulha', '2026-08-18 08:44:10'),
(3, 3, 11.40, 'ms', 0, 'Leitura de tag RFID confirmada com baixa latência', '2026-08-18 08:44:20'),
(4, 4, 38.50, '°C', 0, 'Temperatura dos trilhos dentro da faixa de segurança', '2026-08-18 08:44:30'),
(5, 5, 52.00, '°C', 1, 'Sensor em procedimento de calibração térmica', '2026-08-18 08:44:40');

-- Tabela `operadores`
CREATE TABLE `operadores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `matricula` varchar(20) NOT NULL,
  `nome_completo` varchar(150) NOT NULL,
  `cargo_funcao` varchar(100) NOT NULL,
  `setor_descricao` varchar(100) NOT NULL DEFAULT 'CCO Central - Malha Sul',
  `telefone_ramal` varchar(60) DEFAULT NULL,
  `foto_perfil_url` varchar(255) DEFAULT 'imagens/foto_perfil.webp',
  `linha_principal_id` int(11) DEFAULT NULL,
  `turno_trabalho` enum('diurno1','vespertino','noturno','escala') NOT NULL DEFAULT 'diurno1',
  `status_turno` enum('ativo','inativo','pausa') NOT NULL DEFAULT 'ativo',
  `horas_em_escala_acumuladas` int(11) NOT NULL DEFAULT 0,
  `trens_despachados_acumulados` int(11) NOT NULL DEFAULT 0,
  `pontualidade_linhas_percentual` decimal(5,2) NOT NULL DEFAULT 98.40,
  `alertas_atendidos_acumulados` int(11) NOT NULL DEFAULT 0,
  `autenticacao_2fa` tinyint(1) NOT NULL DEFAULT 1,
  `alerta_sonoro_emergencia` tinyint(1) NOT NULL DEFAULT 1,
  `notificacao_telemetria` tinyint(1) NOT NULL DEFAULT 1,
  `relatorio_auto_fechamento` tinyint(1) NOT NULL DEFAULT 1,
  `criado_em` datetime DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `telefone` varchar(60) DEFAULT NULL,
  `cargo` varchar(100) DEFAULT NULL,
  `setor_linha` varchar(20) DEFAULT NULL,
  `turno` varchar(20) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `two_fa` tinyint(1) NOT NULL DEFAULT 0,
  `alerta_sonoro` tinyint(1) NOT NULL DEFAULT 1,
  `notif_telemetria` tinyint(1) NOT NULL DEFAULT 1,
  `relatorio_auto` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario_id` (`usuario_id`),
  UNIQUE KEY `matricula` (`matricula`),
  UNIQUE KEY `uk_usuario` (`usuario_id`),
  KEY `fk_operadores_linha` (`linha_principal_id`),
  KEY `idx_operadores_matricula` (`matricula`),
  CONSTRAINT `fk_operadores_linha` FOREIGN KEY (`linha_principal_id`) REFERENCES `linhas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_operadores_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `operadores` (`id`, `usuario_id`, `matricula`, `nome_completo`, `cargo_funcao`, `setor_descricao`, `telefone_ramal`, `foto_perfil_url`, `linha_principal_id`, `turno_trabalho`, `status_turno`, `horas_em_escala_acumuladas`, `trens_despachados_acumulados`, `pontualidade_linhas_percentual`, `alertas_atendidos_acumulados`, `autenticacao_2fa`, `alerta_sonoro_emergencia`, `notificacao_telemetria`, `relatorio_auto_fechamento`, `criado_em`, `atualizado_em`, `telefone`, `cargo`, `setor_linha`, `turno`, `foto`, `two_fa`, `alerta_sonoro`, `notif_telemetria`, `relatorio_auto`) VALUES
(1, 1, 'ST-0482', 'Carlos Eduardo Silva', 'Operador CCO Sênior / Administrador', 'CCO Central - Malha Sul', '(47) 3451-9842 • Ramal 204', 'imagens/foto_perfil.webp', 1, 'diurno1', 'ativo', 168, 1420, 98.40, 47, 1, 1, 1, 1, '2026-09-28 08:01:47', '2026-09-28 08:01:47', NULL, NULL, NULL, NULL, NULL, 0, 1, 1, 1),
(2, 2, 'ST-0105', 'Gabriel Custódio', 'Operador CCO Pleno', 'CCO Central - Malha Leste', '(47) 3451-9842 • Ramal 205', 'imagens/foto_perfil.webp', 3, 'diurno1', 'ativo', 152, 1180, 97.90, 31, 1, 1, 1, 0, '2026-09-28 08:01:47', '2026-09-28 08:01:47', NULL, NULL, NULL, NULL, NULL, 0, 1, 1, 1),
(3, 3, 'ST-0208', 'Vinícius de Lima', 'Engenheiro de Telemetria e Sistemas', 'CCO Central - Engenharia', '(47) 3451-9842 • Ramal 206', 'imagens/foto_perfil.webp', 4, 'diurno1', 'ativo', 160, 990, 99.10, 18, 1, 1, 1, 1, '2026-09-28 08:01:47', '2026-09-28 08:01:47', NULL, NULL, NULL, NULL, NULL, 0, 1, 1, 1);

-- Tabela `recuperacoes_senha`
CREATE TABLE `recuperacoes_senha` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `token_hash` char(64) NOT NULL,
  `data_expiracao` datetime NOT NULL,
  `utilizado` tinyint(1) NOT NULL DEFAULT 0,
  `ip_solicitacao` varchar(45) DEFAULT NULL,
  `criado_em` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `token_hash` (`token_hash`),
  KEY `fk_recuperacoes_usuario` (`usuario_id`),
  KEY `idx_recuperacoes_token` (`token_hash`,`data_expiracao`,`utilizado`),
  CONSTRAINT `fk_recuperacoes_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela `telemetria_trens`
CREATE TABLE `telemetria_trens` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `trem_id` int(11) NOT NULL,
  `velocidade_kmh` decimal(5,2) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `localizacao_nome` varchar(100) NOT NULL DEFAULT 'Joinville',
  `ocupacao_percentual` decimal(5,2) NOT NULL DEFAULT 0.00,
  `consumo_kwh_km` decimal(5,2) NOT NULL DEFAULT 4.10,
  `temperatura_motor_c` decimal(5,2) DEFAULT 62.50,
  `pressao_freios_bar` decimal(5,2) DEFAULT 5.00,
  `data_hora` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_telemetria_trem_data` (`trem_id`,`data_hora`),
  CONSTRAINT `fk_telemetria_trem` FOREIGN KEY (`trem_id`) REFERENCES `trens` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `telemetria_trens` (`id`, `trem_id`, `velocidade_kmh`, `latitude`, `longitude`, `localizacao_nome`, `ocupacao_percentual`, `consumo_kwh_km`, `temperatura_motor_c`, `pressao_freios_bar`, `data_hora`) VALUES
(1, 1, 37.00, -26.30450000, -48.84870000, 'Joinville - Centro / Malha Norte', 68.00, 4.10, 63.40, 5.00, '2026-08-18 08:45:00'),
(2, 6, 37.00, -26.30450000, -48.84870000, 'Joinville - Centro / Linha 130', 68.00, 4.10, 64.00, 5.00, '2026-08-18 08:45:00'),
(3, 2, 45.00, -26.25500000, -48.84800000, 'Joinville - Terminal Norte', 0.00, 3.90, 68.20, 5.00, '2026-08-18 08:45:00'),
(4, 7, 45.00, -26.25500000, -48.84800000, 'Joinville - Sentido Sul/Norte', 0.00, 3.90, 67.80, 5.00, '2026-08-18 08:45:00'),
(5, 4, 0.00, -26.34500000, -48.86500000, 'Joinville - Pátio Sul', 0.00, 0.50, 42.00, 5.00, '2026-08-18 08:45:00'),
(6, 3, 0.00, -26.30450000, -48.84870000, 'Joinville - Oficina Centro', 0.00, 0.80, 40.00, 5.00, '2026-08-18 08:45:00');

-- Tabela `logs_auditoria_operador`
CREATE TABLE `logs_auditoria_operador` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `operador_id` int(11) NOT NULL,
  `acao` varchar(255) NOT NULL,
  `categoria` enum('OPERACIONAL','SEGURANCA','SISTEMA','MANUTENCAO') NOT NULL DEFAULT 'OPERACIONAL',
  `detalhes` text DEFAULT NULL,
  `ip_origem` varchar(45) DEFAULT NULL,
  `data_hora` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_logs_operador_data` (`operador_id`,`data_hora`),
  CONSTRAINT `fk_logs_operador` FOREIGN KEY (`operador_id`) REFERENCES `operadores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `logs_auditoria_operador` (`id`, `operador_id`, `acao`, `categoria`, `detalhes`, `ip_origem`, `data_hora`) VALUES
(1, 1, 'Atualizou status do Trem 130 para Ativo', 'OPERACIONAL', 'Composição liberada após vistoria de freios', NULL, '2026-08-18 08:42:00'),
(2, 1, 'Registrou novo sensor térmico no Terminal Centro', 'OPERACIONAL', 'Instalação do sensor térmico de trilho trilho-sul-02', NULL, '2026-08-18 07:15:00'),
(3, 1, 'Aprovou desvio de rota na Linha 142 (Oeste/Leste)', 'OPERACIONAL', 'Desvio devido à manutenção preventiva no trecho 04', NULL, '2026-08-17 13:50:00'),
(4, 1, 'Exportou relatório mensal de pontualidade da malha', 'SISTEMA', 'Geração de PDF com índice global de 98.4%', NULL, '2026-08-17 11:20:00');

-- Tabela `relatorios_operacionais`
CREATE TABLE `relatorios_operacionais` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `protocolo` varchar(30) NOT NULL,
  `tipo_relatorio` enum('pontualidade','lotacao','ocorrencias','telemetria','energia') NOT NULL,
  `linha_id` int(11) NOT NULL,
  `trem_id` int(11) DEFAULT NULL,
  `operador_id` int(11) NOT NULL,
  `data_hora_registro` datetime NOT NULL,
  `status_sumario` varchar(50) NOT NULL,
  `parecer_tecnico` text NOT NULL,
  `assinatura_digital_sha256` char(64) NOT NULL,
  `criado_em` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `protocolo` (`protocolo`),
  KEY `fk_relatorios_trem` (`trem_id`),
  KEY `fk_relatorios_operador` (`operador_id`),
  KEY `idx_relatorios_tipo` (`tipo_relatorio`),
  KEY `idx_relatorios_linha` (`linha_id`),
  KEY `idx_relatorios_data` (`data_hora_registro`),
  CONSTRAINT `fk_relatorios_linha` FOREIGN KEY (`linha_id`) REFERENCES `linhas` (`id`),
  CONSTRAINT `fk_relatorios_operador` FOREIGN KEY (`operador_id`) REFERENCES `operadores` (`id`),
  CONSTRAINT `fk_relatorios_trem` FOREIGN KEY (`trem_id`) REFERENCES `trens` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `relatorios_operacionais` (`id`, `protocolo`, `tipo_relatorio`, `linha_id`, `trem_id`, `operador_id`, `data_hora_registro`, `status_sumario`, `parecer_tecnico`, `assinatura_digital_sha256`, `criado_em`) VALUES
(1, 'REL-2026-89', 'pontualidade', 1, 1, 1, '2026-08-18 08:30:00', 'Regular (99%)', 'Operação em velocidade de cruzeiro de 37 km/h. Pontualidade mantida dentro da margem de 2 minutos. Telemetria e sensores de via normais.', 'a8f94d8e578c7b89d6e8790cb47e12789f41ab78c34e098df123456789abcdef', '2026-09-28 08:01:47'),
(2, 'REL-2026-88', 'ocorrencias', 3, 4, 2, '2026-08-18 07:15:00', 'Concluído', 'Inspeção térmica dos freios magnéticos realizada no Terminal Centro. Sensor calibrado com sucesso e composição liberada.', 'b4c12d9e875a6f43219087cb123e456789f1ab34c56e789df987654321fedcba', '2026-09-28 08:01:47'),
(3, 'REL-2026-87', 'lotacao', 5, NULL, 1, '2026-08-17 18:40:00', 'Lotação 94%', 'Pico de passageiros às 18h30. Tempo de parada nas plataformas ampliado em 25 segundos para embarque seguro.', 'c7d89e01234f56789abcdef0123456789abcdef0123456789abcdef012345678', '2026-09-28 08:01:47'),
(4, 'REL-2026-86', 'telemetria', 4, 3, 3, '2026-08-17 14:10:00', 'Normal', 'Leitura contínua de proximidade e sensor RFID operando com latência inferior a 12ms. Rota cumprida sem ocorrências.', 'd1e2f34567890abcdef123456789abcdef0123456789abcdef0123456789abcd', '2026-09-28 08:01:47'),
(5, 'REL-2026-85', 'energia', 2, 5, 1, '2026-08-17 09:20:00', 'Econômico', 'Frenagem regenerativa recuperou 18% da energia cinética no trecho montanhoso. Consumo médio de 3.9 kWh/km.', 'e9f8a7b6c5d4e3f2109876543210fedcba9876543210abcdef0123456789abcd', '2026-09-28 08:01:47');

-- View `vw_dashboard_status_trens`
CREATE VIEW `vw_dashboard_status_trens` AS SELECT `l`.`codigo_linha` AS `linha`, `l`.`sentido_padrao` AS `destino`, `t`.`tipo` AS `tipo`, `t`.`status_operacional` AS `status`, `t`.`prefixo` AS `composicao` FROM (`trens` `t` join `linhas` `l` on(`t`.`linha_id` = `l`.`id`)) WHERE `t`.`ativo` = 1;

-- View `vw_dashboard_tempo_espera`
CREATE VIEW `vw_dashboard_tempo_espera` AS SELECT `l`.`codigo_linha` AS `linha`, `e`.`sentido` AS `sentido`, `e`.`nivel_fluxo` AS `nivel_fluxo`, `e`.`capacidade_percentual` AS `capacidade`, concat(`e`.`tempo_estimado_minutos`,' min') AS `previsao`, `e`.`tempo_estimado_minutos` AS `minutos_numerico` FROM (`estimativas_chegada` `e` join `linhas` `l` on(`e`.`linha_id` = `l`.`id`)) ORDER BY `e`.`tempo_estimado_minutos` ASC;

-- View `vw_frota_telemetria_atual`
CREATE VIEW `vw_frota_telemetria_atual` AS SELECT `t`.`id` AS `trem_id`, `t`.`prefixo` AS `prefixo`, `l`.`codigo_linha` AS `linha`, `l`.`sentido_padrao` AS `trajeto`, `t`.`tipo` AS `tipo`, `t`.`status_operacional` AS `status`, coalesce(`tel`.`velocidade_kmh`,0.00) AS `velocidade_kmh`, coalesce(`tel`.`latitude`,-26.3045) AS `latitude`, coalesce(`tel`.`longitude`,-48.8487) AS `longitude`, coalesce(`tel`.`localizacao_nome`,'Joinville') AS `localizacao_nome`, `tel`.`data_hora` AS `ultima_leitura` FROM ((`trens` `t` join `linhas` `l` on(`t`.`linha_id` = `l`.`id`)) left join (select `tt`.`id` AS `id`,`tt`.`trem_id` AS `trem_id`,`tt`.`velocidade_kmh` AS `velocidade_kmh`,`tt`.`latitude` AS `latitude`,`tt`.`longitude` AS `longitude`,`tt`.`localizacao_nome` AS `localizacao_nome`,`tt`.`ocupacao_percentual` AS `ocupacao_percentual`,`tt`.`consumo_kwh_km` AS `consumo_kwh_km`,`tt`.`temperatura_motor_c` AS `temperatura_motor_c`,`tt`.`pressao_freios_bar` AS `pressao_freios_bar`,`tt`.`data_hora` AS `data_hora` from (`telemetria_trens` `tt` join (select `telemetria_trens`.`trem_id` AS `trem_id`,max(`telemetria_trens`.`id`) AS `max_id` from `telemetria_trens` group by `telemetria_trens`.`trem_id`) `ult` on(`tt`.`id` = `ult`.`max_id`))) `tel` on(`t`.`id` = `tel`.`trem_id`)) WHERE `t`.`ativo` = 1;

-- View `vw_relatorios_completos`
CREATE VIEW `vw_relatorios_completos` AS SELECT `r`.`id` AS `id`, concat('#',`r`.`protocolo`) AS `protocolo`, `r`.`protocolo` AS `protocolo_puro`, `r`.`tipo_relatorio` AS `tipo_relatorio`, CASE `r`.`tipo_relatorio` WHEN 'pontualidade' THEN 'Pontualidade & Fluxo' WHEN 'lotacao' THEN 'Lotação & Horário de Pico' WHEN 'ocorrencias' THEN 'Manutenção Preventiva' WHEN 'telemetria' THEN 'Telemetria & Sensores' WHEN 'energia' THEN 'Eficiência Energética' ELSE 'Geral' END AS `categoria_formatada`, date_format(`r`.`data_hora_registro`,'%d/%m/%Y • %H:%i') AS `data_hora_formatada`, `r`.`data_hora_registro` AS `data_hora_registro`, `l`.`codigo_linha` AS `codigo_linha`, concat('Linha ',`l`.`codigo_linha`,' - ',coalesce(`t`.`prefixo`,`l`.`sentido_padrao`)) AS `linha_composicao`, concat('Linha ',`l`.`codigo_linha`,' - ',`l`.`sentido_padrao`) AS `linha_sentido`, `o`.`nome_completo` AS `operador_nome`, `r`.`status_sumario` AS `status_sumario`, `r`.`parecer_tecnico` AS `parecer_tecnico`, `r`.`assinatura_digital_sha256` AS `assinatura_digital_sha256` FROM (((`relatorios_operacionais` `r` join `linhas` `l` on(`r`.`linha_id` = `l`.`id`)) left join `trens` `t` on(`r`.`trem_id` = `t`.`id`)) join `operadores` `o` on(`r`.`operador_id` = `o`.`id`));

-- View `vw_sensores_resumo`
CREATE VIEW `vw_sensores_resumo` AS SELECT `s`.`id` AS `id`, `s`.`codigo_identificador` AS `codigo_identificador`, `s`.`modelo_tipo` AS `modelo_tipo`, `s`.`nome_modelo` AS `nome_modelo`, `e`.`nome` AS `ponto_instalacao`, `e`.`tipo_ponto` AS `tipo_ponto`, `e`.`cidade` AS `cidade`, `s`.`status_sensor` AS `status_sensor`, `s`.`unidade_medida` AS `unidade_medida` FROM (`sensores` `s` join `estacoes` `e` on(`s`.`estacao_id` = `e`.`id`));
