<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sensores - SuperTrain</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <!-- CSS da página -->
    <link
        rel="stylesheet"
        href="estilo/sensores.css"
    >

</head>


<body>


    <!-- =========================================
         NAVBAR
    ========================================== -->

    <header>

        <nav class="navbar navbar-expand-lg bg-blur">

            <div class="container-fluid">

                <!-- LOGO -->
                <a class="navbar-brand" href="dashboard.php">

                    <img
                        src="imagens/logo.png"
                        alt="SuperTrain Logo"
                        width="36"
                        height="28"
                    >

                    SuperTrain

                </a>


                <!-- BOTÃO MOBILE -->
                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarSuperTrain"
                    aria-controls="navbarSuperTrain"
                    aria-expanded="false"
                    aria-label="Toggle navigation"
                >

                    <span class="navbar-toggler-icon"></span>

                </button>


                <!-- MENU -->
                <div
                    class="collapse navbar-collapse"
                    id="navbarSuperTrain"
                >

                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">


                        <!-- DASHBOARD -->
                        <li class="nav-item">

                            <a
                                class="nav-link"
                                href="dashboard.php"
                            >

                                <i class="bi bi-grid-fill"></i>

                                Dashboard

                            </a>

                        </li>


                        <!-- TRENS -->
                        <li class="nav-item">

                            <a
                                class="nav-link"
                                href="trens.php"
                            >

                                <i class="bi bi-train-front"></i>

                                Trens

                            </a>

                        </li>


                        <!-- ROTAS -->
                        <li class="nav-item">

                            <a
                                class="nav-link"
                                href="rotas.php"
                            >

                                <i class="bi bi-signpost-split"></i>

                                Rotas

                            </a>

                        </li>


                        <!-- SENSORES -->
                        <li class="nav-item">

                            <a
                                class="nav-link active"
                                aria-current="page"
                                href="sensores.php"
                            >

                                <i class="bi bi-broadcast-pin"></i>

                                Sensores

                            </a>

                        </li>


                        <!-- RELATÓRIOS -->
                        <li class="nav-item">

                            <a
                                class="nav-link"
                                href="relatorios.php"
                            >

                                <i class="bi bi-file-earmark-bar-graph"></i>

                                Relatórios

                            </a>

                        </li>


                        <!-- PERFIL -->
                        <li class="nav-item">

                            <a
                                class="nav-link"
                                href="perfil.php"
                            >

                                <i class="bi bi-person-circle"></i>

                                Perfil

                            </a>

                        </li>


                        <!-- SAIR -->
                        <li class="nav-item">

                            <a
                                class="nav-link text-danger"
                                href="sair.php"
                            >

                                <i class="bi bi-box-arrow-right"></i>

                                Sair

                            </a>

                        </li>


                    </ul>

                </div>

            </div>

        </nav>

    </header>


    <!-- =========================================
         CONTEÚDO PRINCIPAL
    ========================================== -->

    <main>


        <!-- TÍTULO -->

        <div class="container-fluid px-4 pt-4">

            <h2 class="texto-indicativo">

                Configuração de Sensores da Malha

            </h2>

        </div>


        <!-- =========================================
             DUAS COLUNAS PRINCIPAIS
        ========================================== -->

        <div class="container-fluid px-4 pb-4">

            <div class="row g-4 align-items-start">


                <!-- =====================================
                     COLUNA ESQUERDA
                     SENSORES
                ====================================== -->

                <div class="col-12 col-lg-8">

                    <div class="sensores-container">


                        <div class="card card-bg-blur trens-card">


                            <!-- CABEÇALHO -->

                            <div class="card-header-custom">

                                <h1 class="card-title">

                                    Sensores

                                </h1>

                            </div>


                            <!-- =================================
                                 ÁREA COM ROLAGEM
                            ================================== -->

                            <div class="sensores-scroll">


                                <div class="table-responsive">


                                    <table class="table text-center mb-0">


                                        <!-- CABEÇALHO DA TABELA -->

                                        <thead>

                                            <tr>

                                                <th scope="col">
                                                    Trem
                                                </th>


                                                <th scope="col">
                                                    Sensor
                                                </th>


                                                <th scope="col">
                                                    Ponto de instalação / Trilho
                                                </th>


                                                <th scope="col">
                                                    Ponto de instalação / Trem
                                                </th>


                                                <th scope="col">
                                                    Ações
                                                </th>

                                            </tr>

                                        </thead>


                                        <!-- =================================
                                             CORPO DA TABELA
                                        ================================== -->

                                        <tbody id="tabelaSensores">

                                            <!--
                                                Os novos sensores serão
                                                adicionados automaticamente
                                                aqui pelo JavaScript.
                                            -->

                                        </tbody>


                                    </table>


                                </div>


                            </div>


                        </div>


                    </div>


                </div>


                <!-- =====================================
                     COLUNA DIREITA
                     NOVO SENSOR
                ====================================== -->

                <div class="col-12 col-lg-4">


                    <div class="novo-sensor-container">


                        <div class="card sensores-card">


                            <h1 class="card-title">

                                Novo Sensor

                            </h1>


                            <!-- =================================
                                 FORMULÁRIO
                            ================================== -->

                            <form id="formNovoSensor">


                                <!-- =================================
                                     NÚMERO DO TREM
                                ================================== -->

                                <div class="form-group mb-3">


                                    <label
                                        for="numTrem"
                                        class="form-label"
                                    >

                                        Trem

                                    </label>


                                    <select
                                        id="numTrem"
                                        name="numeroTrem"
                                        class="form-select"
                                    >


                                        <option
                                            value=""
                                            disabled
                                            selected
                                        >

                                            Selecione o número do trem

                                        </option>


                                        <option value="norte/sul">

                                            130 | Norte/Sul

                                        </option>


                                        <option value="oeste/leste">

                                            142 | Oeste/Leste

                                        </option>


                                        <option value="leste/norte">

                                            174 | Leste/Norte

                                        </option>


                                        <option value="sul/norte">

                                            130 | Sul/Norte

                                        </option>


                                        <option value="centro">

                                            001 | Centro

                                        </option>


                                    </select>


                                </div>


                                <!-- =================================
                                     MODELO DO SENSOR
                                ================================== -->

                                <div class="form-group mb-3">


                                    <label
                                        for="selectTipoSensor"
                                        class="form-label"
                                    >

                                        Modelo do Sensor

                                    </label>


                                    <select
                                        id="selectTipoSensor"
                                        name="tipoSensor"
                                        class="form-select"
                                    >


                                        <option
                                            value=""
                                            disabled
                                            selected
                                        >

                                            Selecione o tipo de sensor

                                        </option>


                                        <option value="ultrassonico">

                                            Sensor Ultrassônico de Proximidade

                                        </option>


                                        <option value="movimento">

                                            Sensor de Presença & Movimento

                                        </option>


                                        <option value="localizador">

                                            Localizador Óptico / RFID

                                        </option>


                                        <option value="temperatura">

                                            Sensor Térmico de Trilhos

                                        </option>


                                    </select>


                                </div>


                                <!-- =================================
                                     PONTO DO TRILHO
                                ================================== -->

                                <div class="form-group mb-4">


                                    <label
                                        for="selectPontoTrilho"
                                        class="form-label"
                                    >

                                        Ponto de Instalação / Trilho

                                    </label>


                                    <select
                                        id="selectPontoTrilho"
                                        name="pontoTrilho"
                                        class="form-select"
                                    >


                                        <option
                                            value=""
                                            disabled
                                            selected
                                        >

                                            Selecione o ponto de monitoramento

                                        </option>


                                        <option value="ponto1">

                                            Não disponível

                                        </option>


                                        <option value="ponto2">

                                            Ponto 1001

                                        </option>


                                        <option value="ponto3">

                                            Ponto 1002

                                        </option>


                                        <option value="ponto4">

                                            Ponto 1003

                                        </option>


                                        <option value="ponto5">

                                            Ponto 1004

                                        </option>


                                    </select>


                                </div>


                                <!-- =================================
                                     PONTO DO TREM
                                ================================== -->

                                <div class="form-group mb-4">


                                    <label
                                        for="selectPontoTrem"
                                        class="form-label"
                                    >

                                        Ponto de Instalação / Trem

                                    </label>


                                    <select
                                        id="selectPontoTrem"
                                        name="pontoTrem"
                                        class="form-select"
                                    >


                                        <option
                                            value=""
                                            disabled
                                            selected
                                        >

                                            Selecione o ponto de monitoramento

                                        </option>


                                        <option value="ponto1">

                                            Não disponível

                                        </option>


                                        <option value="ponto2">

                                            Ponto 2001

                                        </option>


                                        <option value="ponto3">

                                            Ponto 2002

                                        </option>


                                        <option value="ponto4">

                                            Ponto 2003

                                        </option>


                                        <option value="ponto5">

                                            Ponto 2004

                                        </option>


                                    </select>


                                </div>


                                <!-- =================================
                                     BOTÃO SALVAR
                                ================================== -->

                                <button
                                    type="submit"
                                    class="btn w-100 btn-salvar-sensor"
                                >

                                    <i class="bi bi-save2"></i>

                                    Salvar Sensor

                                </button>


                            </form>


                        </div>


                    </div>


                </div>


            </div>


        </div>


    </main>


    <!-- =========================================
         BOOTSTRAP JS
    ========================================== -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>


    <!-- =========================================
         JAVASCRIPT DOS SENSORES
    ========================================== -->

    <script>

        // =========================================
        // ELEMENTOS DO FORMULÁRIO E DA TABELA
        // =========================================

        const formulario = document.getElementById("formNovoSensor");

        const tabelaSensores = document.getElementById("tabelaSensores");


        // =========================================
        // SALVAR NOVO SENSOR
        // =========================================

        formulario.addEventListener("submit", function(event) {

            // Impede o formulário de recarregar a página
            event.preventDefault();


            // =====================================
            // PEGA OS CAMPOS
            // =====================================

            const selectTrem =
                document.getElementById("numTrem");

            const selectSensor =
                document.getElementById("selectTipoSensor");

            const selectTrilho =
                document.getElementById("selectPontoTrilho");

            const selectPontoTrem =
                document.getElementById("selectPontoTrem");


            // =====================================
            // VERIFICAÇÃO
            // =====================================

            if (
                selectTrem.value === "" ||
                selectSensor.value === "" ||
                selectTrilho.value === "" ||
                selectPontoTrem.value === ""
            ) {

                alert(
                    "Preencha todos os campos antes de salvar o sensor."
                );

                return;

            }


            // =====================================
            // PEGA O TEXTO DOS SELECTS
            // =====================================

            const trem =
                selectTrem.options[
                    selectTrem.selectedIndex
                ].text.trim();


            const sensor =
                selectSensor.options[
                    selectSensor.selectedIndex
                ].text.trim();


            const trilho =
                selectTrilho.options[
                    selectTrilho.selectedIndex
                ].text.trim();


            const pontoTrem =
                selectPontoTrem.options[
                    selectPontoTrem.selectedIndex
                ].text.trim();


            // =====================================
            // CRIA UMA NOVA LINHA
            // =====================================

            const novaLinha =
                document.createElement("tr");


            // =====================================
            // CRIA O CONTEÚDO DA LINHA
            // =====================================

            novaLinha.innerHTML = `

                <td>
                    ${trem}
                </td>

                <td>
                    ${sensor}
                </td>

                <td>
                    ${trilho}
                </td>

                <td>
                    ${pontoTrem}
                </td>

                <td class="acoes">

                    <button
                        type="button"
                        class="botao botao-secundario"
                        onclick="editarSensor(this)"
                    >
                        <i class="bi bi-pencil"></i> Editar
                    </button>

                    <button
                        type="button"
                        class="botao botao-perigo"
                        onclick="excluirSensor(this)"
                    >
                        <i class="bi bi-trash"></i> Excluir
                    </button>

                </td>

            `;


            // =====================================
            // ADICIONA A LINHA NA TABELA
            // =====================================

            tabelaSensores.appendChild(novaLinha);


            // =====================================
            // LIMPA O FORMULÁRIO
            // =====================================

            formulario.reset();


            // =====================================
            // MENSAGEM
            // =====================================

            alert("Sensor cadastrado com sucesso!");

        });


        // =========================================
        // EXCLUIR SENSOR
        // =========================================

        function excluirSensor(botao) {

            const linha =
                botao.closest("tr");


            if (
                confirm(
                    "Deseja realmente excluir este sensor?"
                )
            ) {

                linha.remove();

            }

        }


        // =========================================
        // EDITAR SENSOR
        // =========================================

        function editarSensor(botao) {

            const linha =
                botao.closest("tr");


            const celulas =
                linha.querySelectorAll("td");


            alert(

                "Sensor selecionado para edição:\n\n" +

                "Trem: " +
                celulas[0].textContent.trim() +

                "\nSensor: " +
                celulas[1].textContent.trim() +

                "\nTrilho: " +
                celulas[2].textContent.trim() +

                "\nPonto do Trem: " +
                celulas[3].textContent.trim()

            );

        }

    </script>


</body>

</html>