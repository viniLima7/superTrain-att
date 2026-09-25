<?php
$servidor = 'localhost';
$usuario = 'root';
$senha = '';
$banco = 'supertrain_db';

$conexao = new mysqli($servidor, $usuario, $senha, $banco);

if ($conexao-> connect_error) {
    die("Falha na conexão: ". $conexao ->connect_error);
}

$conexao->set_charset('utf8mb4'); // Para comportar ascentuações do portugues brasileiro

?>