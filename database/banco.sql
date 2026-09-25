CREATE DATABASE IF NOT EXISTS `supertrain_db`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `supertrain_db`;

-- Desativa temporariamente a checagem de chaves estrangeiras para importação limpa
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------------------------
-- 1. TABELA: paises (cadastro.html)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `paises`;
CREATE TABLE `paises` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `codigo` VARCHAR(30) NOT NULL UNIQUE,
    `nome` VARCHAR(80) NOT NULL,
    `sigla` CHAR(2) NOT NULL,
    `codigo_telefonico` VARCHAR(10) NULL,
    `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `paises` (`id`, `codigo`, `nome`, `sigla`, `codigo_telefonico`) VALUES
(1, 'brasil', 'Brasil', 'BR', '+55'),
(2, 'estadosUnidos', 'Estados Unidos', 'US', '+1'),
(3, 'russia', 'Rússia', 'RU', '+7'),
(4, 'camboja', 'Camboja', 'KH', '+855'),
(5, 'japao', 'Japão', 'JP', '+81'),
(6, 'alemanha', 'Alemanha', 'DE', '+49');

-- -----------------------------------------------------------------------------
-- 2. TABELA: linhas (dashboard.html, rotas.html, trens.html, relatorios.html)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `linhas`;
CREATE TABLE `linhas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `codigo_linha` VARCHAR(10) NOT NULL UNIQUE,
    `nome_linha` VARCHAR(100) NOT NULL,
    `sentido_padrao` VARCHAR(50) NOT NULL,
    `tipo_linha` ENUM('passageiros', 'carga', 'expresso', 'misto') NOT NULL DEFAULT 'passageiros',
    `status_operacional` ENUM('ativo', 'parado', 'manutencao', 'atencao') NOT NULL DEFAULT 'ativo',
    `cor_hex` VARCHAR(7) NOT NULL DEFAULT '#00d2ff',
    `extensao_km` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
    `ativo` BOOLEAN NOT NULL DEFAULT TRUE,
    `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_linhas_codigo` (`codigo_linha`),
    INDEX `idx_linhas_status` (`status_operacional`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `linhas` (`id`, `codigo_linha`, `nome_linha`, `sentido_padrao`, `tipo_linha`, `status_operacional`, `cor_hex`, `extensao_km`) VALUES
(1, '130', 'Linha 130 (Norte/Sul)', 'Norte / Sul', 'passageiros', 'ativo', '#00d2ff', 24.80),
(2, '140', 'Linha 140 (Sul/Norte)', 'Sul / Norte', 'carga', 'ativo', '#20c997', 28.50),
(3, '142', 'Linha 142 (Oeste/Leste)', 'Oeste / Leste', 'carga', 'parado', '#ffc107', 32.10),
(4, '174', 'Linha 174 (Leste/Norte)', 'Leste / Norte', 'carga', 'manutencao', '#fd7e14', 19.40),
(5, '001', 'Linha 001 (Centro - Expresso)', 'Centro', 'expresso', 'ativo', '#e03131', 12.00);

-- -----------------------------------------------------------------------------
-- 3. TABELA: usuarios (cadastro.html, login.html)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `nome_usuario` VARCHAR(50) NOT NULL UNIQUE,
    `senha_hash` VARCHAR(255) NOT NULL,
    `pais_id` INT NOT NULL,
    `status_conta` ENUM('ativo', 'inativo', 'bloqueado', 'pendente') NOT NULL DEFAULT 'ativo',
    `ultimo_login` DATETIME NULL,
    `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `atualizado_em` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_usuarios_pais` FOREIGN KEY (`pais_id`) REFERENCES `paises`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `usuarios` (`id`, `email`, `nome_usuario`, `senha_hash`, `pais_id`, `status_conta`, `criado_em`) VALUES
(1, 'carlos.silva@supertrain.com', 'carlos.silva', '$2y$12$eN98Wk0R57yqI2mQo3v1te6hSkmWf67J51J0X6wJkeM5G9i7Qx2gW', 1, 'ativo', '2026-01-10 08:00:00'),
(2, 'gabriel.c@supertrain.com', 'gabriel.custodio', '$2y$12$eN98Wk0R57yqI2mQo3v1te6hSkmWf67J51J0X6wJkeM5G9i7Qx2gW', 1, 'ativo', '2026-02-01 09:30:00'),
(3, 'vinicius.l@supertrain.com', 'vinicius.lima', '$2y$12$eN98Wk0R57yqI2mQo3v1te6hSkmWf67J51J0X6wJkeM5G9i7Qx2gW', 1, 'ativo', '2026-02-01 09:30:00');

-- -----------------------------------------------------------------------------
-- 4. TABELA: recuperacoes_senha (recuperacao.html)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `recuperacoes_senha`;
CREATE TABLE `recuperacoes_senha` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT NOT NULL,
    `token_hash` CHAR(64) NOT NULL UNIQUE,
    `data_expiracao` DATETIME NOT NULL,
    `utilizado` BOOLEAN NOT NULL DEFAULT FALSE,
    `ip_solicitacao` VARCHAR(45) NULL,
    `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_recuperacoes_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
    INDEX `idx_recuperacoes_token` (`token_hash`, `data_expiracao`, `utilizado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 5. TABELA: operadores (perfil.html, relatorios.html)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `operadores`;
CREATE TABLE `operadores` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT NOT NULL UNIQUE,
    `matricula` VARCHAR(20) NOT NULL UNIQUE,
    `nome_completo` VARCHAR(150) NOT NULL,
    `cargo_funcao` VARCHAR(100) NOT NULL,
    `setor_descricao` VARCHAR(100) NOT NULL DEFAULT 'CCO Central - Malha Sul',
    `telefone_ramal` VARCHAR(60) NULL,
    `foto_perfil_url` VARCHAR(255) DEFAULT 'imagens/foto_perfil.webp',
    `linha_principal_id` INT NULL,
    `turno_trabalho` ENUM('diurno1', 'vespertino', 'noturno', 'escala') NOT NULL DEFAULT 'diurno1',
    `status_turno` ENUM('ativo', 'inativo', 'pausa') NOT NULL DEFAULT 'ativo',
    `horas_em_escala_acumuladas` INT NOT NULL DEFAULT 0,
    `trens_despachados_acumulados` INT NOT NULL DEFAULT 0,
    `pontualidade_linhas_percentual` DECIMAL(5,2) NOT NULL DEFAULT 98.40,
    `alertas_atendidos_acumulados` INT NOT NULL DEFAULT 0,
    `autenticacao_2fa` BOOLEAN NOT NULL DEFAULT TRUE,
    `alerta_sonoro_emergencia` BOOLEAN NOT NULL DEFAULT TRUE,
    `notificacao_telemetria` BOOLEAN NOT NULL DEFAULT TRUE,
    `relatorio_auto_fechamento` BOOLEAN NOT NULL DEFAULT TRUE,
    `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `atualizado_em` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_operadores_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_operadores_linha` FOREIGN KEY (`linha_principal_id`) REFERENCES `linhas`(`id`) ON DELETE SET NULL,
    INDEX `idx_operadores_matricula` (`matricula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `operadores` (
    `id`, `usuario_id`, `matricula`, `nome_completo`, `cargo_funcao`,
    `setor_descricao`, `telefone_ramal`, `foto_perfil_url`, `linha_principal_id`,
    `turno_trabalho`, `status_turno`, `horas_em_escala_acumuladas`,
    `trens_despachados_acumulados`, `pontualidade_linhas_percentual`,
    `alertas_atendidos_acumulados`, `autenticacao_2fa`,
    `alerta_sonoro_emergencia`, `notificacao_telemetria`, `relatorio_auto_fechamento`
) VALUES
(1, 1, 'ST-0482', 'Carlos Eduardo Silva', 'Operador CCO Sênior / Administrador', 'CCO Central - Malha Sul', '(47) 3451-9842 • Ramal 204', 'imagens/foto_perfil.webp', 1, 'diurno1', 'ativo', 168, 1420, 98.40, 47, TRUE, TRUE, TRUE, TRUE),
(2, 2, 'ST-0105', 'Gabriel Custódio', 'Operador CCO Pleno', 'CCO Central - Malha Leste', '(47) 3451-9842 • Ramal 205', 'imagens/foto_perfil.webp', 3, 'diurno1', 'ativo', 152, 1180, 97.90, 31, TRUE, TRUE, TRUE, FALSE),
(3, 3, 'ST-0208', 'Vinícius de Lima', 'Engenheiro de Telemetria e Sistemas', 'CCO Central - Engenharia', '(47) 3451-9842 • Ramal 206', 'imagens/foto_perfil.webp', 4, 'diurno1', 'ativo', 160, 990, 99.10, 18, TRUE, TRUE, TRUE, TRUE);

-- -----------------------------------------------------------------------------
-- 6. TABELA: logs_auditoria_operador (perfil.html)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `logs_auditoria_operador`;
CREATE TABLE `logs_auditoria_operador` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `operador_id` INT NOT NULL,
    `acao` VARCHAR(255) NOT NULL,
    `categoria` ENUM('OPERACIONAL', 'SEGURANCA', 'SISTEMA', 'MANUTENCAO') NOT NULL DEFAULT 'OPERACIONAL',
    `detalhes` TEXT NULL,
    `ip_origem` VARCHAR(45) NULL,
    `data_hora` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_logs_operador` FOREIGN KEY (`operador_id`) REFERENCES `operadores`(`id`) ON DELETE CASCADE,
    INDEX `idx_logs_operador_data` (`operador_id`, `data_hora` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `logs_auditoria_operador` (`id`, `operador_id`, `acao`, `categoria`, `detalhes`, `data_hora`) VALUES
(1, 1, 'Atualizou status do Trem 130 para Ativo', 'OPERACIONAL', 'Composição liberada após vistoria de freios', '2026-08-18 08:42:00'),
(2, 1, 'Registrou novo sensor térmico no Terminal Centro', 'OPERACIONAL', 'Instalação do sensor térmico de trilho trilho-sul-02', '2026-08-18 07:15:00'),
(3, 1, 'Aprovou desvio de rota na Linha 142 (Oeste/Leste)', 'OPERACIONAL', 'Desvio devido à manutenção preventiva no trecho 04', '2026-08-17 13:50:00'),
(4, 1, 'Exportou relatório mensal de pontualidade da malha', 'SISTEMA', 'Geração de PDF com índice global de 98.4%', '2026-08-17 11:20:00');

-- -----------------------------------------------------------------------------
-- 7. TABELA: estacoes (sensores.html, rotas.html)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `estacoes`;
CREATE TABLE `estacoes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `codigo` VARCHAR(20) NOT NULL UNIQUE,
    `nome` VARCHAR(100) NOT NULL,
    `tipo_ponto` ENUM('estacao', 'terminal', 'patio_manobras') NOT NULL DEFAULT 'estacao',
    `cidade` VARCHAR(80) NOT NULL DEFAULT 'Joinville',
    `latitude` DECIMAL(10,8) NOT NULL,
    `longitude` DECIMAL(11,8) NOT NULL,
    `capacidade_plataforma` INT NOT NULL DEFAULT 500,
    `ativo` BOOLEAN NOT NULL DEFAULT TRUE,
    `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_estacoes_nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `estacoes` (`id`, `codigo`, `nome`, `tipo_ponto`, `cidade`, `latitude`, `longitude`, `capacidade_plataforma`) VALUES
(1, 'EST-IRIRIU', 'Estação Iririú', 'estacao', 'Joinville', -26.28250000, -48.82500000, 600),
(2, 'TERM-NORTE', 'Terminal Norte', 'terminal', 'Joinville', -26.25500000, -48.84800000, 1200),
(3, 'EST-PARAIS', 'Estação Paraíso', 'estacao', 'Joinville', -26.27000000, -48.83500000, 500),
(4, 'TERM-CENTRO', 'Terminal Centro', 'terminal', 'Joinville', -26.30450000, -48.84870000, 2500),
(5, 'PATIO-SUL', 'Pátio de Manobras Sul', 'patio_manobras', 'Joinville', -26.34500000, -48.86500000, 400);

-- -----------------------------------------------------------------------------
-- 8. TABELA: itinerarios_estacoes (Sequência de paradas)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `itinerarios_estacoes`;
CREATE TABLE `itinerarios_estacoes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `linha_id` INT NOT NULL,
    `estacao_id` INT NOT NULL,
    `ordem_sequencia` INT NOT NULL,
    `distancia_proxima_km` DECIMAL(5,2) DEFAULT 0.00,
    `tempo_parada_segundos` INT DEFAULT 45,
    CONSTRAINT `fk_itinerarios_linha` FOREIGN KEY (`linha_id`) REFERENCES `linhas`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_itinerarios_estacao` FOREIGN KEY (`estacao_id`) REFERENCES `estacoes`(`id`) ON DELETE CASCADE,
    CONSTRAINT `uq_linha_sequencia` UNIQUE (`linha_id`, `ordem_sequencia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `itinerarios_estacoes` (`linha_id`, `estacao_id`, `ordem_sequencia`, `distancia_proxima_km`, `tempo_parada_segundos`) VALUES
(1, 2, 1, 3.20, 60),
(1, 3, 2, 2.50, 45),
(1, 1, 3, 4.10, 45),
(1, 4, 4, 5.00, 60),
(5, 4, 1, 4.00, 30);

-- -----------------------------------------------------------------------------
-- 9. TABELA: trens (dashboard.html, trens.html, relatorios.html)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `trens`;
CREATE TABLE `trens` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `prefixo` VARCHAR(20) NOT NULL UNIQUE,
    `modelo_composicao` VARCHAR(100) NOT NULL DEFAULT 'SuperTrain Série 2000',
    `linha_id` INT NULL,
    `tipo` ENUM('passageiros', 'carga', 'expresso') NOT NULL DEFAULT 'passageiros',
    `status_operacional` ENUM('ativo', 'parado', 'manutencao') NOT NULL DEFAULT 'ativo',
    `capacidade_maxima` INT NOT NULL DEFAULT 800,
    `velocidade_maxima_kmh` DECIMAL(5,2) NOT NULL DEFAULT 80.00,
    `ano_fabricacao` YEAR NOT NULL DEFAULT 2024,
    `ativo` BOOLEAN NOT NULL DEFAULT TRUE,
    `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `atualizado_em` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_trens_linha` FOREIGN KEY (`linha_id`) REFERENCES `linhas`(`id`) ON DELETE SET NULL,
    INDEX `idx_trens_status` (`status_operacional`),
    INDEX `idx_trens_linha` (`linha_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `trens` (`id`, `prefixo`, `modelo_composicao`, `linha_id`, `tipo`, `status_operacional`, `capacidade_maxima`, `velocidade_maxima_kmh`, `ano_fabricacao`) VALUES
(1, 'Trem 01', 'Alstom Metropolis 2026', 1, 'passageiros', 'ativo', 1200, 90.00, 2025),
(2, 'Trem 02', 'GE Transportation Heavy', 2, 'carga', 'ativo', 2800, 75.00, 2024),
(3, 'Trem 03', 'EMD Progress Rail SD70', 4, 'carga', 'manutencao', 2500, 70.00, 2023),
(4, 'Trem 04', 'Stadler EuroDual Carga', 3, 'carga', 'parado', 3200, 65.00, 2024),
(5, 'Carga 02', 'GE AC4400CW', 2, 'carga', 'ativo', 3000, 75.00, 2023),
(6, 'Trem 130', 'Alstom Metropolis ST-X', 1, 'passageiros', 'ativo', 1200, 90.00, 2026),
(7, 'Trem 140', 'Siemens Charger Heavy', 2, 'carga', 'ativo', 2800, 80.00, 2025);

-- -----------------------------------------------------------------------------
-- 10. TABELA: telemetria_trens (trens.html)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `telemetria_trens`;
CREATE TABLE `telemetria_trens` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `trem_id` INT NOT NULL,
    `velocidade_kmh` DECIMAL(5,2) NOT NULL,
    `latitude` DECIMAL(10,8) NOT NULL,
    `longitude` DECIMAL(11,8) NOT NULL,
    `localizacao_nome` VARCHAR(100) NOT NULL DEFAULT 'Joinville',
    `ocupacao_percentual` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `consumo_kwh_km` DECIMAL(5,2) NOT NULL DEFAULT 4.10,
    `temperatura_motor_c` DECIMAL(5,2) DEFAULT 62.50,
    `pressao_freios_bar` DECIMAL(5,2) DEFAULT 5.00,
    `data_hora` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_telemetria_trem` FOREIGN KEY (`trem_id`) REFERENCES `trens`(`id`) ON DELETE CASCADE,
    INDEX `idx_telemetria_trem_data` (`trem_id`, `data_hora` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `telemetria_trens` (`trem_id`, `velocidade_kmh`, `latitude`, `longitude`, `localizacao_nome`, `ocupacao_percentual`, `consumo_kwh_km`, `temperatura_motor_c`, `data_hora`) VALUES
(1, 37.00, -26.30450000, -48.84870000, 'Joinville - Centro / Malha Norte', 68.00, 4.10, 63.40, '2026-08-18 08:45:00'),
(6, 37.00, -26.30450000, -48.84870000, 'Joinville - Centro / Linha 130', 68.00, 4.10, 64.00, '2026-08-18 08:45:00'),
(2, 45.00, -26.25500000, -48.84800000, 'Joinville - Terminal Norte', 0.00, 3.90, 68.20, '2026-08-18 08:45:00'),
(7, 45.00, -26.25500000, -48.84800000, 'Joinville - Sentido Sul/Norte', 0.00, 3.90, 67.80, '2026-08-18 08:45:00'),
(4, 0.00, -26.34500000, -48.86500000, 'Joinville - Pátio Sul', 0.00, 0.50, 42.00, '2026-08-18 08:45:00'),
(3, 0.00, -26.30450000, -48.84870000, 'Joinville - Oficina Centro', 0.00, 0.80, 40.00, '2026-08-18 08:45:00');

-- -----------------------------------------------------------------------------
-- 11. TABELA: rotas (rotas.html)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `rotas`;
CREATE TABLE `rotas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `linha_id` INT NOT NULL,
    `direcao` VARCHAR(50) NOT NULL,
    `estacao_origem_id` INT NULL,
    `estacao_destino_id` INT NULL,
    `horario_partida` TIME NOT NULL,
    `horario_chegada_previsto` TIME NULL,
    `tipo_composicao` ENUM('passageiro', 'carga', 'expresso') NOT NULL DEFAULT 'passageiro',
    `status_rota` ENUM('ativa', 'desvio', 'interrompida', 'programada') NOT NULL DEFAULT 'ativa',
    `dias_operacao` VARCHAR(50) NOT NULL DEFAULT 'DIARIO',
    `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_rotas_linha` FOREIGN KEY (`linha_id`) REFERENCES `linhas`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_rotas_origem` FOREIGN KEY (`estacao_origem_id`) REFERENCES `estacoes`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_rotas_destino` FOREIGN KEY (`estacao_destino_id`) REFERENCES `estacoes`(`id`) ON DELETE SET NULL,
    INDEX `idx_rotas_linha` (`linha_id`),
    INDEX `idx_rotas_horario` (`horario_partida`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `rotas` (`id`, `linha_id`, `direcao`, `estacao_origem_id`, `estacao_destino_id`, `horario_partida`, `horario_chegada_previsto`, `tipo_composicao`, `status_rota`, `dias_operacao`) VALUES
(1, 1, 'norte/sul', 2, 4, '06:30:00', '07:05:00', 'passageiro', 'ativa', 'DIARIO'),
(2, 2, 'sul/norte', 4, 2, '07:00:00', '07:45:00', 'carga', 'ativa', 'DIARIO'),
(3, 3, 'oeste/leste', 1, 4, '08:00:00', '08:40:00', 'carga', 'desvio', 'DIARIO'),
(4, 4, 'leste/norte', 4, 2, '13:30:00', '14:15:00', 'carga', 'ativa', 'DIAS_UTEIS'),
(5, 5, 'centro', 4, 4, '06:00:00', '06:30:00', 'expresso', 'ativa', 'DIARIO');

-- -----------------------------------------------------------------------------
-- 12. TABELA: estimativas_chegada (dashboard.html: Tempo de Espera)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `estimativas_chegada`;
CREATE TABLE `estimativas_chegada` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `linha_id` INT NOT NULL,
    `estacao_id` INT NULL,
    `sentido` VARCHAR(50) NOT NULL,
    `nivel_fluxo` ENUM('normal', 'atencao', 'intenso') NOT NULL DEFAULT 'normal',
    `capacidade_percentual` INT NOT NULL DEFAULT 50,
    `tempo_estimado_minutos` INT NOT NULL DEFAULT 5,
    `atualizado_em` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_estimativas_linha` FOREIGN KEY (`linha_id`) REFERENCES `linhas`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_estimativas_estacao` FOREIGN KEY (`estacao_id`) REFERENCES `estacoes`(`id`) ON DELETE SET NULL,
    INDEX `idx_estimativas_linha` (`linha_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `estimativas_chegada` (`id`, `linha_id`, `estacao_id`, `sentido`, `nivel_fluxo`, `capacidade_percentual`, `tempo_estimado_minutos`) VALUES
(1, 1, 4, 'Norte / Sul', 'normal', 30, 2),
(2, 3, 4, 'Oeste / Leste', 'atencao', 60, 5),
(3, 4, 2, 'Leste / Norte', 'atencao', 50, 7),
(4, 1, 2, 'Sul / Norte', 'intenso', 80, 10),
(5, 5, 4, 'Centro', 'intenso', 100, 14);

-- -----------------------------------------------------------------------------
-- 13. TABELA: sensores (sensores.html)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `sensores`;
CREATE TABLE `sensores` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `codigo_identificador` VARCHAR(50) NOT NULL UNIQUE,
    `modelo_tipo` ENUM('ultrassonico', 'movimento', 'localizador', 'temperatura') NOT NULL,
    `nome_modelo` VARCHAR(100) NOT NULL,
    `estacao_id` INT NOT NULL,
    `localizacao_detalhe` VARCHAR(150) NOT NULL,
    `status_sensor` ENUM('operacional', 'alerta', 'em_calibracao', 'inativo') NOT NULL DEFAULT 'operacional',
    `limite_minimo` DECIMAL(10,2) NULL,
    `limite_maximo` DECIMAL(10,2) NULL,
    `unidade_medida` VARCHAR(20) NOT NULL DEFAULT '°C',
    `data_instalacao` DATE DEFAULT (CURRENT_DATE),
    `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_sensores_estacao` FOREIGN KEY (`estacao_id`) REFERENCES `estacoes`(`id`) ON DELETE CASCADE,
    INDEX `idx_sensores_tipo` (`modelo_tipo`),
    INDEX `idx_sensores_status` (`status_sensor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `sensores` (`id`, `codigo_identificador`, `modelo_tipo`, `nome_modelo`, `estacao_id`, `localizacao_detalhe`, `status_sensor`, `limite_minimo`, `limite_maximo`, `unidade_medida`) VALUES
(1, 'SNS-ULTRA-01', 'ultrassonico', 'Sensor Ultrassônico de Proximidade', 1, 'Estação Iririú - Plataforma 01', 'operacional', 0.50, 15.00, 'metros'),
(2, 'SNS-PRES-02', 'movimento', 'Sensor de Presença & Movimento', 2, 'Terminal Norte - Agulha de Entrada', 'operacional', 0.00, 1.00, 'booleano'),
(3, 'SNS-RFID-03', 'localizador', 'Localizador Óptico / RFID', 3, 'Estação Paraíso - Bloco de Baliza B', 'operacional', 0.00, 100.00, 'ms'),
(4, 'SNS-TERM-04', 'temperatura', 'Sensor Térmico de Trilhos', 4, 'Terminal Centro - Trilho Central 02', 'operacional', 0.00, 65.00, '°C'),
(5, 'SNS-TERM-05', 'temperatura', 'Sensor Térmico de Trilhos', 5, 'Pátio de Manobras Sul - Linha de Desvio', 'em_calibracao', 0.00, 65.00, '°C');

-- -----------------------------------------------------------------------------
-- 14. TABELA: leituras_sensores (sensores.html, relatorios.html)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `leituras_sensores`;
CREATE TABLE `leituras_sensores` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `sensor_id` INT NOT NULL,
    `valor_lido` DECIMAL(10,2) NOT NULL,
    `unidade_medida` VARCHAR(20) NOT NULL,
    `alerta_disparado` BOOLEAN NOT NULL DEFAULT FALSE,
    `mensagem_status` VARCHAR(255) NULL,
    `data_hora` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_leituras_sensor` FOREIGN KEY (`sensor_id`) REFERENCES `sensores`(`id`) ON DELETE CASCADE,
    INDEX `idx_leituras_sensor_data` (`sensor_id`, `data_hora` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `leituras_sensores` (`sensor_id`, `valor_lido`, `unidade_medida`, `alerta_disparado`, `mensagem_status`, `data_hora`) VALUES
(1, 4.20, 'metros', FALSE, 'Detecção normal de aproximação', '2026-08-18 08:44:00'),
(2, 1.00, 'booleano', FALSE, 'Composição passando sobre a agulha', '2026-08-18 08:44:10'),
(3, 11.40, 'ms', FALSE, 'Leitura de tag RFID confirmada com baixa latência', '2026-08-18 08:44:20'),
(4, 38.50, '°C', FALSE, 'Temperatura dos trilhos dentro da faixa de segurança', '2026-08-18 08:44:30'),
(5, 52.00, '°C', TRUE, 'Sensor em procedimento de calibração térmica', '2026-08-18 08:44:40');

-- -----------------------------------------------------------------------------
-- 15. TABELA: relatorios_operacionais (relatorios.html)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `relatorios_operacionais`;
CREATE TABLE `relatorios_operacionais` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `protocolo` VARCHAR(30) NOT NULL UNIQUE,
    `tipo_relatorio` ENUM('pontualidade', 'lotacao', 'ocorrencias', 'telemetria', 'energia') NOT NULL,
    `linha_id` INT NOT NULL,
    `trem_id` INT NULL,
    `operador_id` INT NOT NULL,
    `data_hora_registro` DATETIME NOT NULL,
    `status_sumario` VARCHAR(50) NOT NULL,
    `parecer_tecnico` TEXT NOT NULL,
    `assinatura_digital_sha256` CHAR(64) NOT NULL,
    `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_relatorios_linha` FOREIGN KEY (`linha_id`) REFERENCES `linhas`(`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_relatorios_trem` FOREIGN KEY (`trem_id`) REFERENCES `trens`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_relatorios_operador` FOREIGN KEY (`operador_id`) REFERENCES `operadores`(`id`) ON DELETE RESTRICT,
    INDEX `idx_relatorios_tipo` (`tipo_relatorio`),
    INDEX `idx_relatorios_linha` (`linha_id`),
    INDEX `idx_relatorios_data` (`data_hora_registro` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `relatorios_operacionais` (
    `id`, `protocolo`, `tipo_relatorio`, `linha_id`, `trem_id`, `operador_id`,
    `data_hora_registro`, `status_sumario`, `parecer_tecnico`, `assinatura_digital_sha256`
) VALUES
(1, 'REL-2026-89', 'pontualidade', 1, 1, 1, '2026-08-18 08:30:00', 'Regular (99%)', 'Operação em velocidade de cruzeiro de 37 km/h. Pontualidade mantida dentro da margem de 2 minutos. Telemetria e sensores de via normais.', 'a8f94d8e578c7b89d6e8790cb47e12789f41ab78c34e098df123456789abcdef'),
(2, 'REL-2026-88', 'ocorrencias', 3, 4, 2, '2026-08-18 07:15:00', 'Concluído', 'Inspeção térmica dos freios magnéticos realizada no Terminal Centro. Sensor calibrado com sucesso e composição liberada.', 'b4c12d9e875a6f43219087cb123e456789f1ab34c56e789df987654321fedcba'),
(3, 'REL-2026-87', 'lotacao', 5, NULL, 1, '2026-08-17 18:40:00', 'Lotação 94%', 'Pico de passageiros às 18h30. Tempo de parada nas plataformas ampliado em 25 segundos para embarque seguro.', 'c7d89e01234f56789abcdef0123456789abcdef0123456789abcdef0123456789'),
(4, 'REL-2026-86', 'telemetria', 4, 3, 3, '2026-08-17 14:10:00', 'Normal', 'Leitura contínua de proximidade e sensor RFID operando com latência inferior a 12ms. Rota cumprida sem ocorrências.', 'd1e2f34567890abcdef123456789abcdef0123456789abcdef0123456789abcdef'),
(5, 'REL-2026-85', 'energia', 2, 5, 1, '2026-08-17 09:20:00', 'Econômico', 'Frenagem regenerativa recuperou 18% da energia cinética no trecho montanhoso. Consumo médio de 3.9 kWh/km.', 'e9f8a7b6c5d4e3f2109876543210fedcba9876543210abcdef0123456789abcde');

-- -----------------------------------------------------------------------------
-- 16. TABELA: kpis_malha_historico (relatorios.html, dashboard.html)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `kpis_malha_historico`;
CREATE TABLE `kpis_malha_historico` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `data_referencia` DATE NOT NULL UNIQUE,
    `pontualidade_global_percentual` DECIMAL(5,2) NOT NULL DEFAULT 98.40,
    `passageiros_transportados` INT NOT NULL DEFAULT 342180,
    `consumo_energetico_kwh_km` DECIMAL(5,2) NOT NULL DEFAULT 4.10,
    `confiabilidade_operacional_percentual` DECIMAL(5,2) NOT NULL DEFAULT 99.20,
    `lotacao_media_percentual` DECIMAL(5,2) NOT NULL DEFAULT 68.00,
    `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `kpis_malha_historico` (`id`, `data_referencia`, `pontualidade_global_percentual`, `passageiros_transportados`, `consumo_energetico_kwh_km`, `confiabilidade_operacional_percentual`, `lotacao_media_percentual`) VALUES
(1, '2026-08-18', 98.40, 342180, 4.10, 99.20, 68.00),
(2, '2026-08-17', 98.10, 339450, 4.25, 98.90, 71.00),
(3, '2026-08-16', 99.00, 210800, 3.95, 99.50, 52.00);

-- -----------------------------------------------------------------------------
-- VIEWS ESPECIAIS PARA O FRONT-END
-- -----------------------------------------------------------------------------

CREATE OR REPLACE VIEW `vw_dashboard_status_trens` AS
SELECT 
    l.codigo_linha AS linha,
    l.sentido_padrao AS destino,
    t.tipo AS tipo,
    t.status_operacional AS status,
    t.prefixo AS composicao
FROM trens t
JOIN linhas l ON t.linha_id = l.id
WHERE t.ativo = TRUE;

CREATE OR REPLACE VIEW `vw_dashboard_tempo_espera` AS
SELECT 
    l.codigo_linha AS linha,
    e.sentido AS sentido,
    e.nivel_fluxo AS nivel_fluxo,
    e.capacidade_percentual AS capacidade,
    CONCAT(e.tempo_estimado_minutos, ' min') AS previsao,
    e.tempo_estimado_minutos AS minutos_numerico
FROM estimativas_chegada e
JOIN linhas l ON e.linha_id = l.id
ORDER BY e.tempo_estimado_minutos ASC;

CREATE OR REPLACE VIEW `vw_frota_telemetria_atual` AS
SELECT 
    t.id AS trem_id,
    t.prefixo,
    l.codigo_linha AS linha,
    l.sentido_padrao AS trajeto,
    t.tipo,
    t.status_operacional AS status,
    COALESCE(tel.velocidade_kmh, 0.00) AS velocidade_kmh,
    COALESCE(tel.latitude, -26.3045) AS latitude,
    COALESCE(tel.longitude, -48.8487) AS longitude,
    COALESCE(tel.localizacao_nome, 'Joinville') AS localizacao_nome,
    tel.data_hora AS ultima_leitura
FROM trens t
JOIN linhas l ON t.linha_id = l.id
LEFT JOIN (
    SELECT tt.*
    FROM telemetria_trens tt
    INNER JOIN (
        SELECT trem_id, MAX(id) AS max_id
        FROM telemetria_trens
        GROUP BY trem_id
    ) ult ON tt.id = ult.max_id
) tel ON t.id = tel.trem_id
WHERE t.ativo = TRUE;

CREATE OR REPLACE VIEW `vw_relatorios_completos` AS
SELECT 
    r.id,
    CONCAT('#', r.protocolo) AS protocolo,
    r.protocolo AS protocolo_puro,
    r.tipo_relatorio,
    CASE r.tipo_relatorio
        WHEN 'pontualidade' THEN 'Pontualidade & Fluxo'
        WHEN 'lotacao' THEN 'Lotação & Horário de Pico'
        WHEN 'ocorrencias' THEN 'Manutenção Preventiva'
        WHEN 'telemetria' THEN 'Telemetria & Sensores'
        WHEN 'energia' THEN 'Eficiência Energética'
        ELSE 'Geral'
    END AS categoria_formatada,
    DATE_FORMAT(r.data_hora_registro, '%d/%m/%Y • %H:%i') AS data_hora_formatada,
    r.data_hora_registro,
    l.codigo_linha,
    CONCAT('Linha ', l.codigo_linha, ' - ', COALESCE(t.prefixo, l.sentido_padrao)) AS linha_composicao,
    CONCAT('Linha ', l.codigo_linha, ' - ', l.sentido_padrao) AS linha_sentido,
    o.nome_completo AS operador_nome,
    r.status_sumario,
    r.parecer_tecnico,
    r.assinatura_digital_sha256
FROM relatorios_operacionais r
JOIN linhas l ON r.linha_id = l.id
LEFT JOIN trens t ON r.trem_id = t.id
JOIN operadores o ON r.operador_id = o.id;

CREATE OR REPLACE VIEW `vw_sensores_resumo` AS
SELECT 
    s.id,
    s.codigo_identificador,
    s.modelo_tipo,
    s.nome_modelo,
    e.nome AS ponto_instalacao,
    e.tipo_ponto,
    e.cidade,
    s.status_sensor,
    s.unidade_medida
FROM sensores s
JOIN estacoes e ON s.estacao_id = e.id;

-- Reativa a checagem de integridade referencial
SET FOREIGN_KEY_CHECKS = 1;

-- =============================================================================
-- FIM DO SCRIPT UNIFICADO PHPMYADMIN
-- =============================================================================
 