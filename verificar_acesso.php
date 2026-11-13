<?php

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.html");
    exit;
}

$acesso = $argv[1] ?? null;

if ($acesso == 'gestor' && $_SESSION['cargo'] != 'gestor') {
    header("Location: ../index.php");
    exit;
}

if ($acesso == 'usuario' && $_SESSION['cargo'] == 'gestor') {
    header("Location: ../index.php");
    exit;
}