
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

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <link
        rel="stylesheet"
        href="estilo/sensores.css?v=1.0"
    >

</head>


<body>


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


            <div
                class="collapse navbar-collapse"
                id="navbarSuperTrain"
            >

                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">


                    <li class="nav-item">

                        <a
                            class="nav-link"
                            href="dashboard.php"
                        >

                            <i class="bi bi-grid-fill"></i>

                            Dashboard

                        </a>

                    </li>


                    <li class="nav-item">

                        <a
                            class="nav-link"
                            href="trens.php"
                        >

                            <i class="bi bi-train-front"></i>

                            Trens

                        </a>

                    </li>


                    <li class="nav-item">

                        <a
                            class="nav-link"
                            href="rotas.php"
                        >

                            <i class="bi bi-signpost-split"></i>

                            Rotas

                        </a>

                    </li>


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


                    <li class="nav-item">

                        <a
                            class="nav-link"
                            href="relatorios.php"
                        >

                            <i class="bi bi-file-earmark-bar-graph"></i>

                            Relatórios

                        </a>

                    </li>


                    <li class="nav-item">

                        <a
                            class="nav-link"
                            href="perfil.php"
                        >

                            <i class="bi bi-person-circle"></i>

                            Perfil

                        </a>

                    </li>


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


<main>


    <!-- TÍTULO -->

    <div class="container-fluid px-4 pt-4">

        <h2 class="texto-indicativo">

            Configuração de Sensores da Malha

        </h2>

    </div>


    <div class="container-fluid px-4 pb-4">

        <div class="row g-4 align-items-start">


            <!-- =========================================================
                 TABELA DE SENSORES
                 ========================================================= -->

            <div class="col-12 col-lg-8">

                <div class="sensores-container">


                    <div class="card card-bg-blur trens-card">


                        <!-- CABEÇALHO -->

                        <div class="card-header-custom">

                            <h1 class="card-title">

                                Sensores

                            </h1>

                        </div>


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


                                    <tbody id="tabelaSensores">

                                        <!--
                                            Os sensores serão carregados
                                            automaticamente pelo JavaScript.
                                        -->

                                    </tbody>


                                </table>


                            </div>


                        </div>


                    </div>


                </div>


            </div>


            <!-- =========================================================
                 CADASTRO DE NOVO SENSOR
                 ========================================================= -->

            <div class="col-12 col-lg-4">


                <div class="novo-sensor-container">


                    <div class="card sensores-card">


                        <h1 class="card-title">

                            Novo Sensor

                        </h1>


                        <form id="formNovoSensor">


                            <!-- TREM -->

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


                            <!-- MODELO DO SENSOR -->

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


                            <!-- PONTO NO TRILHO -->

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


                            <!-- PONTO NO TREM -->

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


                            <!-- BOTÃO SALVAR -->

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


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>


<script>

/* ==========================================================================
   SUPERTRAIN - GERENCIAMENTO DOS SENSORES
   ========================================================================== */


/*
    Nome utilizado para guardar os sensores no navegador.

    O localStorage permanece mesmo depois de:
    - atualizar a página;
    - fechar a página;
    - fechar o navegador.
*/

const CHAVE_SENSORES = "supertrain_sensores";


const formulario =
    document.getElementById("formNovoSensor");


const tabelaSensores =
    document.getElementById("tabelaSensores");


/* ==========================================================================
   CARREGAR SENSORES
   ========================================================================== */

function carregarSensores() {

    const sensoresSalvos =
        localStorage.getItem(CHAVE_SENSORES);


    /*
        Se ainda não existir nenhum sensor salvo,
        retorna uma lista vazia.
    */

    if (!sensoresSalvos) {

        return [];

    }


    try {

        return JSON.parse(sensoresSalvos);

    } catch (erro) {

        console.error(
            "Erro ao carregar os sensores:",
            erro
        );

        return [];

    }

}


/* ==========================================================================
   SALVAR SENSORES
   ========================================================================== */

function salvarSensores(sensores) {

    localStorage.setItem(
        CHAVE_SENSORES,
        JSON.stringify(sensores)
    );

}


/* ==========================================================================
   GERAR ID ÚNICO
   ========================================================================== */

function gerarIdSensor() {

    return Date.now().toString() +
        Math.random()
            .toString(36)
            .substring(2, 9);

}


/* ==========================================================================
   CRIAR LINHA DA TABELA
   ========================================================================== */

function criarLinhaSensor(sensor) {


    const novaLinha =
        document.createElement("tr");


    /*
        O ID fica guardado na própria linha.

        Isso permite descobrir exatamente qual sensor
        deve ser excluído.
    */

    novaLinha.dataset.sensorId =
        sensor.id;


    novaLinha.innerHTML = `

        <td>
            ${sensor.trem}
        </td>

        <td>
            ${sensor.sensor}
        </td>

        <td>
            ${sensor.trilho}
        </td>

        <td>
            ${sensor.pontoTrem}
        </td>

        <td class="acoes">

            <button
                type="button"
                class="botao botao-secundario"
                onclick="editarSensor(this)"
            >

                <i class="bi bi-pencil"></i>

                Editar

            </button>


            <button
                type="button"
                class="botao botao-perigo"
                onclick="excluirSensor(this)"
            >

                <i class="bi bi-trash"></i>

                Excluir

            </button>

        </td>

    `;


    return novaLinha;

}


/* ==========================================================================
   RENDERIZAR SENSORES
   ========================================================================== */

function renderizarSensores() {


    /*
        Limpa a tabela antes de carregar novamente.

        Isso evita duplicação.
    */

    tabelaSensores.innerHTML = "";


    const sensores =
        carregarSensores();


    /*
        Cria novamente cada sensor salvo.
    */

    sensores.forEach(function(sensor) {

        const linha =
            criarLinhaSensor(sensor);


        tabelaSensores.appendChild(linha);

    });

}


/* ==========================================================================
   SALVAR NOVO SENSOR
   ========================================================================== */

formulario.addEventListener(
    "submit",
    function(event) {


        /*
            Impede que o formulário recarregue a página.
        */

        event.preventDefault();


        const selectTrem =
            document.getElementById("numTrem");


        const selectSensor =
            document.getElementById("selectTipoSensor");


        const selectTrilho =
            document.getElementById("selectPontoTrilho");


        const selectPontoTrem =
            document.getElementById("selectPontoTrem");


        /*
            Verifica se todos os campos foram preenchidos.
        */

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


        /*
            Pega o texto que aparece para o usuário,
            e não somente o value da opção.
        */

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


        /*
            Cria o objeto do novo sensor.
        */

        const novoSensor = {

            id: gerarIdSensor(),

            trem: trem,

            sensor: sensor,

            trilho: trilho,

            pontoTrem: pontoTrem

        };


        /*
            Recupera os sensores que já existem.
        */

        const sensores =
            carregarSensores();


        /*
            Adiciona o novo sensor à lista.
        */

        sensores.push(novoSensor);


        /*
            SALVA NO NAVEGADOR.

            É esta linha que faz com que o sensor
            não desapareça quando a página for atualizada.
        */

        salvarSensores(sensores);


        /*
            Cria visualmente a nova linha.
        */

        const novaLinha =
            criarLinhaSensor(novoSensor);


        tabelaSensores.appendChild(
            novaLinha
        );


        /*
            Limpa o formulário.
        */

        formulario.reset();


        alert(
            "Sensor cadastrado com sucesso!"
        );

    }
);


/* ==========================================================================
   EXCLUIR SENSOR
   ========================================================================== */

function excluirSensor(botao) {


    const linha =
        botao.closest("tr");


    if (!linha) {

        return;

    }


    const idSensor =
        linha.dataset.sensorId;


    if (!idSensor) {

        return;

    }


    /*
        Confirma a exclusão.
    */

    if (
        !confirm(
            "Deseja realmente excluir este sensor?"
        )
    ) {

        return;

    }


    /*
        Recupera todos os sensores.
    */

    let sensores =
        carregarSensores();


    /*
        Remove somente o sensor selecionado.
    */

    sensores =
        sensores.filter(function(sensor) {

            return String(sensor.id) !==
                String(idSensor);

        });


    /*
        Salva novamente a lista atualizada.

        Assim o sensor também desaparece do localStorage.
    */

    salvarSensores(sensores);


    /*
        Remove visualmente da tabela.
    */

    linha.remove();


}


/* ==========================================================================
   EDITAR SENSOR
   ========================================================================== */

function editarSensor(botao) {


    const linha =
        botao.closest("tr");


    const idSensor =
        linha.dataset.sensorId;


    const celulas =
        linha.querySelectorAll("td");


    /*
        Localiza o sensor no armazenamento.
    */

    const sensores =
        carregarSensores();


    const sensor =
        sensores.find(function(item) {

            return String(item.id) ===
                String(idSensor);

        });


    if (!sensor) {

        return;

    }


    /*
        Preenche novamente o formulário
        com os dados do sensor.
    */

    const selectTrem =
        document.getElementById("numTrem");


    const selectSensor =
        document.getElementById("selectTipoSensor");


    const selectTrilho =
        document.getElementById("selectPontoTrilho");


    const selectPontoTrem =
        document.getElementById("selectPontoTrem");


    /*
        Procura a opção pelo texto exibido.
    */

    selecionarOpcaoPorTexto(
        selectTrem,
        sensor.trem
    );


    selecionarOpcaoPorTexto(
        selectSensor,
        sensor.sensor
    );


    selecionarOpcaoPorTexto(
        selectTrilho,
        sensor.trilho
    );


    selecionarOpcaoPorTexto(
        selectPontoTrem,
        sensor.pontoTrem
    );


    /*
        Remove o sensor antigo.

        Depois o usuário pode salvá-lo novamente
        com os dados modificados.
    */

    const novaLista =
        sensores.filter(function(item) {

            return String(item.id) !==
                String(idSensor);

        });


    salvarSensores(novaLista);


    /*
        Remove a linha antiga.
    */

    linha.remove();


    /*
        Rola a tela para o formulário.
    */

    document
        .querySelector(".novo-sensor-container")
        .scrollIntoView({
            behavior: "smooth",
            block: "start"
        });


    alert(
        "Os dados do sensor foram carregados no formulário. Faça as alterações e clique em 'Salvar Sensor'."
    );

}


/* ==========================================================================
   SELECIONAR OPÇÃO PELO TEXTO
   ========================================================================== */

function selecionarOpcaoPorTexto(
    select,
    texto
) {


    for (
        let i = 0;
        i < select.options.length;
        i++
    ) {


        if (
            select.options[i]
                .text
                .trim() === texto.trim()
        ) {

            select.selectedIndex = i;

            return;

        }

    }

}


/* ==========================================================================
   CARREGAR AUTOMATICAMENTE AO ABRIR A PÁGINA
   ========================================================================== */

document.addEventListener(
    "DOMContentLoaded",
    function() {

        /*
            Recupera os sensores salvos
            assim que a página é aberta.
        */

        renderizarSensores();

    }
);

</script>


</body>

</html>
```
