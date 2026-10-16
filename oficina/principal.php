
<?php

// Inicia a sessão para manter o estado do usuário logado
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

// Verifica se a sessão expirou após 30 minutos
if (time() - $_SESSION['ultimo_acesso'] > 30 * 60) {
    session_destroy();
    header('Location: login.php?expirou=1');
    exit();
}

// Atualiza o horário do último acesso
$_SESSION['ultimo_acesso'] = time();

// Armazena o nome do usuário logado
$nome = $_SESSION['usuario']['nome'];
?>

<link rel="stylesheet" href="css/style.css">

<p>Bem-vindo, <?= htmlspecialchars($nome) ?></p>

<!-- Links para as funcionalidades do salão de beleza -->

<a href="clientes.php">Cadastro de Clientes</a>

<a href="agendamentos.php">Agendamento de Serviços</a>

<a href="procedimentos_salao.php">Registrar Procedimento</a>

<a href="procedimentos_salao.php">Ver Procedimentos</a>

<a href="logout.php">Sair do Sistema</a>
