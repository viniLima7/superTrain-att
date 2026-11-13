# SuperTrain

![Banner da logo](imagens/banner-readme.png)

Sistema web para gerenciamento e acompanhamento de operações ferroviárias. O SuperTrain reúne informações sobre trens, rotas, sensores, usuários e indicadores operacionais em uma única plataforma, com o objetivo de facilitar a consulta e a organização das informações da malha ferroviária.

## Integrantes

- Gabriel Custodio Jorge
- Paulo Henrique Schulz
- Viniciuis de Lima Pereira

## Objetivo do projeto

O SuperTrain foi desenvolvido com a proposta de oferecer uma plataforma para acompanhamento e gerenciamento de informações relacionadas ao transporte ferroviário. O sistema organiza dados da frota, rotas, sensores e usuários, permitindo consultar informações operacionais e visualizar indicadores por meio de um painel e de relatórios.

A aplicação também possui áreas de acesso para diferentes tipos de usuários, além de recursos para cadastro, edição e consulta de informações.

## Funcionalidades

- **Autenticação:** cadastro de conta, login, encerramento de sessão e recuperação e redefinição de senha.
- **Dashboard:** painel com informações resumidas sobre a operação, incluindo status dos trens, lotação geral, perfil do operador e estimativas de tempo de espera.
- **Gerenciamento de trens:** consulta, cadastro, edição e exclusão de composições ferroviárias, com informações como prefixo, modelo, ano de fabricação, capacidade e status operacional.
- **Gerenciamento de rotas:** cadastro, consulta, edição e exclusão de rotas, com direção, horário de partida e tipo de composição.
- **Gerenciamento de sensores:** área para consultar e configurar os sensores associados à malha ferroviária.
- **Leituras de sensores:** consulta de informações e registros de leitura relacionados aos sensores.
- **Relatórios:** apresentação de indicadores operacionais, gráficos, informações de lotação, dados dos sensores e registros de relatórios, com filtros disponíveis na página.
- **Gerenciamento de usuários:** consulta, cadastro, edição e exclusão de contas.
- **Perfil:** consulta e edição de informações cadastrais, funcionais e preferências relacionadas ao perfil do operador.

> **Observação sobre os dados:** o Dashboard contém informações demonstrativas preenchidas diretamente no código. Já a página de relatórios realiza consultas ao banco de dados. Portanto, nem todos os indicadores exibidos na interface representam dados atualizados automaticamente ou recebidos de sensores físicos.

## Tipos de acesso

O banco de dados prevê os cargos `gestor` e `cliente`.

- **Gestor:** perfil administrativo, com acesso às áreas de gerenciamento disponibilizadas pela interface.
- **Cliente:** perfil com acesso às áreas de consulta disponibilizadas pela interface.

A navegação apresenta ou oculta alguns itens de acordo com o cargo registrado na sessão. A validação central existente em `proteger.php` verifica se o usuário está autenticado. A autorização por cargo deve ser aplicada também no servidor em cada página e operação restrita; a simples ocultação de links no menu não substitui essa validação.

## Tecnologias utilizadas

### Backend

- **PHP:** processamento das páginas, formulários, autenticação e regras da aplicação.
- **MySQL:** armazenamento das informações do sistema.
- **MySQLi:** comunicação entre o PHP e o banco de dados.
- **Sessões PHP:** manutenção do estado de autenticação dos usuários.

### Frontend

- **HTML5:** estruturação das páginas.
- **CSS3:** estilização e organização visual das interfaces.
- **JavaScript:** recursos de interação no navegador.
- **Bootstrap 5.3.3:** componentes e estrutura responsiva.
- **Bootstrap Icons 1.11.3:** ícones utilizados na interface.
- **Chart.js:** biblioteca utilizada na página de relatórios para a apresentação de gráficos.

## Estrutura do repositório

```text
superTrain-att/
├── database/
│   └── banco.sql                 # Criação e estrutura do banco de dados
│
├── estilo/
│   ├── font/                     # Fontes utilizadas no projeto
│   ├── cadastro.css
│   ├── dashboard.css
│   ├── formulario.css
│   ├── global.css
│   ├── login.css
│   ├── perfil.css
│   ├── recuperacao.css
│   ├── relatorios.css
│   ├── rotas.css
│   ├── sensores.css
│   ├── trens.css
│   └── usuario.css
│
├── imagens/                      # Logotipo e imagens da interface
│
├── cadastro.php                  # Cadastro de contas
├── config.php                    # Configuração da conexão com o banco
├── proteger.php                  # Verificação de autenticação
├── verificar_acesso.php           # Verificação de acesso por cargo
├── login.php                      # Login
├── sair.php                       # Encerramento da sessão
├── recuperacao.php                # Solicitação de recuperação de senha
├── redefinir_senha.php            # Redefinição de senha
├── dashboard.php                  # Painel principal
├── trens.php                      # Listagem e gerenciamento de trens
├── formulario.php                 # Cadastro e edição de trens
├── rotas.php                      # Listagem e gerenciamento de rotas
├── formulario_rotas.php            # Cadastro e edição de rotas
├── sensores.php                   # Gerenciamento de sensores
├── leitura.php                    # Consulta de leituras
├── relatorios.php                 # Relatórios e indicadores
├── usuarios.php                   # Gerenciamento de usuários
├── formulario_usuarios.php         # Cadastro e edição de usuários
├── perfil.php                     # Perfil do usuário/operador
└── README.md
```

## Banco de dados

O projeto utiliza o banco de dados MySQL `supertrain_db`. O script de criação está localizado em `database/banco.sql`.

Entre as principais tabelas estão:

| Tabela | Finalidade |
|---|---|
| `usuarios` | Armazena as contas, credenciais e cargos dos usuários. |
| `paises` | Armazena os países disponíveis no cadastro. |
| `linhas` | Guarda informações e características das linhas ferroviárias. |
| `trens` | Armazena os dados das composições ferroviárias. |
| `rotas` | Guarda informações sobre as rotas e suas características. |
| `sensores` | Armazena os sensores cadastrados. |
| `leituras_sensores` | Registra as leituras relacionadas aos sensores. |
| `telemetria_trens` | Armazena informações de telemetria dos trens. |
| `operadores` | Guarda informações funcionais e operacionais dos operadores. |
| `relatorios_operacionais` | Armazena registros de relatórios operacionais. |
| `kpis_malha_historico` | Mantém históricos de indicadores da malha ferroviária. |
| `recuperacao_senha` | Armazena solicitações de recuperação de senha. |
| `logs_auditoria_operador` | Registra informações de auditoria relacionadas aos operadores. |
| `estacoes` | Armazena informações das estações. |
| `itinerarios_estacoes` | Relaciona itinerários e estações. |
| `estimativas_chegada` | Armazena estimativas de chegada. |

## Como executar o projeto localmente

### Requisitos

- XAMPP ou ambiente equivalente com Apache, PHP e MySQL.
- PHP com a extensão MySQLi habilitada.
- Navegador atualizado.

### 1. Baixar o projeto

Clone o repositório ou coloque a pasta do projeto dentro do diretório `htdocs` do XAMPP.

Exemplo:

```text
C:\xampp\htdocs\superTrain-att
```

### 2. Criar o banco de dados

1. Inicie o Apache e o MySQL pelo painel do XAMPP.
2. Acesse o phpMyAdmin pelo navegador, normalmente em `http://localhost/phpmyadmin`.
3. Importe o arquivo `database/banco.sql`.
4. Confira se o banco `supertrain_db` foi criado.

O script SQL já contém a criação do banco e das tabelas utilizadas pelo projeto.

### 3. Configurar a conexão

Abra o arquivo `config.php` e confira os dados de conexão:

```php
$servidor = 'localhost';
$usuario = 'root';
$senha = '';
$banco = 'supertrain_db';
```

Altere os valores caso a configuração local do MySQL seja diferente.

### 4. Acessar a aplicação

Com Apache e MySQL em execução, abra:

```text
http://localhost/superTrain-att/login.php
```

A partir da tela de login, é possível acessar o cadastro de conta e as demais funcionalidades disponíveis conforme o perfil de acesso.

## Organização e funcionamento

As páginas PHP concentram a apresentação das telas e o processamento das operações. O arquivo `config.php` estabelece a conexão com o MySQL, enquanto `proteger.php` verifica a existência de uma sessão autenticada nas páginas que o incluem.

Os arquivos CSS ficam organizados na pasta `estilo/`, separados por página ou finalidade. As imagens e o logotipo utilizados pela interface ficam na pasta `imagens/`. A estrutura e os dados iniciais do banco estão definidos em `database/banco.sql`.

## Créditos

**Desenvolvimento do projeto:** Gabriel Custodio Jorge, Paulo Henrique Schulz e Viniciuis de Lima Pereira.

