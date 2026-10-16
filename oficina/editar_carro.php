<?php


// Inicia a sessão para acessar os dados do usuário logado
session_start();


// Verifica se o usuário está autenticado; caso contrário, redireciona para a página de login
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}


// Verifica o tempo de expiração da sessão: se ficou mais de 30 minutos sem usar o sistema,
// encerra a sessão e volta para o login avisando que expirou
if (time() - $_SESSION['ultimo_acesso'] > 30 * 60) {
    session_destroy();
    header('Location: login.php?expirou=1');
    exit();
}


// Ainda está no prazo: renova o horário do último acesso
$_SESSION['ultimo_acesso'] = time();


// Inclui o arquivo de configuração para estabelecer a conexão com a base de dados
include 'config.php';


// Recebe o ID do carro a ser editado via parâmetro GET na URL; se não informado, assume 0
$id = $_GET['id'] ?? 0;


// Prepara e executa uma consulta para buscar os dados do carro com o ID informado
$sql = "SELECT * FROM carros WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$carro = $res->fetch_assoc();


// Se não encontrou o carro, exibe mensagem e termina a execução
if (!$carro) {
    echo "Carro não encontrado.";
    exit();
}


// Processa o formulário quando enviado via POST para atualizar o carro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coleta os dados atualizados do formulário
    $dono_id = $_POST['dono_id'];   // (clínica: $tutor_id = $_POST['tutor_id'];)
    $modelo = $_POST['modelo'];     // (clínica: $nome = $_POST['nome'];)
    $marca = $_POST['marca'];       // (clínica: $especie = $_POST['especie'];)
    $placa = $_POST['placa'];       // (clínica: $raca = $_POST['raca'];)
    $cor = $_POST['cor'];           // (clínica: fica igual)
    $ano = $_POST['ano'];           // (clínica: $data_nascimento = $_POST['data_nascimento'];)


    // Validação simples: dono, modelo e marca são obrigatórios
    if (!$dono_id || !$modelo || !$marca) {
        echo "Dono, modelo e marca são obrigatórios.";
    } else {
        // Atualiza os dados do carro no banco de dados
        $sql = "UPDATE carros SET dono_id=?, modelo=?, marca=?, placa=?, cor=?, ano=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssii", $dono_id, $modelo, $marca, $placa, $cor, $ano, $id);
        $stmt->execute();


        // Redireciona o usuário de volta para a lista de carros após atualizar
        
        header("Location: carros.php");
        exit();
    }
}


// Busca todos os donos para preencher o select (permite trocar o dono do carro)
$donos = $conn->query("SELECT id, nome FROM dono ORDER BY nome");
?>
<!-- Folha de estilo padrão do sistema (todas as telas usam o mesmo arquivo css/style.css) -->
<link rel="stylesheet" href="css/style.css">


<h2>Editar Carro</h2>


<!-- Formulário para edição do carro com dados já preenchidos -->
<form method="post">
    <select name="dono_id" required>
        <!-- Deixa selecionado o dono atual do carro -->
        <?php while ($d = $donos->fetch_assoc()): ?>
            <option value="<?= $d['id'] ?>" <?= $d['id'] == $carro['dono_id'] ? 'selected' : '' ?>><?= htmlspecialchars($d['nome']) ?></option>
        <?php endwhile; ?>
    </select>
    <input name="modelo" placeholder="Modelo" value="<?= htmlspecialchars($carro['modelo']) ?>" required>
    <input name="marca" placeholder="Marca" value="<?= htmlspecialchars($carro['marca']) ?>" required>
    <input name="placa" placeholder="Placa" value="<?= htmlspecialchars($carro['placa'] ?? '') ?>">
    <input name="cor" placeholder="Cor" value="<?= htmlspecialchars($carro['cor'] ?? '') ?>">
    <input type="number" name="ano" required placeholder="Ano" value="<?= htmlspecialchars($carro['ano'] ?? '') ?>">
    <button>Salvar</button>
</form>


<!-- Link para voltar à página de listagem de carros -->
<a href="carros.php">Voltar</a>




