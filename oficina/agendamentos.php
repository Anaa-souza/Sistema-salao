
<?php
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit;
}

if (isset($_SESSION["ultimo_acesso"]) &&
    time() - $_SESSION["ultimo_acesso"] > 1800) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

$_SESSION["ultimo_acesso"] = time();

include "config.php";

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cliente_id = (int) $_POST["cliente_id"];
    $data = $_POST["data"];
    $horario = $_POST["horario"];
    $servico = trim($_POST["servico"]);
    $observacoes = trim($_POST["observacoes"]);

    if ($cliente_id > 0 && $data != "" &&
        $horario != "" && $servico != "") {

        $sql = "INSERT INTO agendamentos
                (cliente_id, data, horario, servico, observacoes)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "issss",
            $cliente_id,
            $data,
            $horario,
            $servico,
            $observacoes
        );

        if ($stmt->execute()) {
            $mensagem = "Agendamento cadastrado com sucesso!";
        } else {
            $mensagem = "Erro ao cadastrar agendamento.";
        }

        $stmt->close();
    } else {
        $mensagem = "Preencha todos os campos obrigatórios.";
    }
}

$clientes = $conn->query(
    "SELECT id, nome FROM clientes ORDER BY nome"
);

$sql = "SELECT a.id, a.data, a.horario, a.servico,
               a.observacoes, a.status, c.nome AS cliente
        FROM agendamentos a
        INNER JOIN clientes c ON a.cliente_id = c.id
        ORDER BY a.data, a.horario";

$agendamentos = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Agendamentos - Salão de Beleza</title>
</head>
<body>

<h1>Agendamentos do salão</h1>

<p><?php echo htmlspecialchars($mensagem); ?></p>

<form method="POST">

    <label>Cliente:</label>
    <select name="cliente_id" required>
        <option value="">Selecione um cliente</option>

        <?php while ($cliente = $clientes->fetch_assoc()) { ?>
            <option value="<?php echo $cliente["id"]; ?>">
                <?php echo htmlspecialchars($cliente["nome"]); ?>
            </option>
        <?php } ?>
    </select>

    <br><br>

    <label>Data:</label>
    <input type="date" name="data" required>

    <br><br>

    <label>Horário:</label>
    <input type="time" name="horario" required>

    <br><br>

    <label>Serviço:</label>
    <input type="text" name="servico"
           placeholder="Ex.: corte, manicure, escova" required>

    <br><br>

    <label>Observações:</label>
    <input type="text" name="observacoes">

    <br><br>

    <button type="submit">Cadastrar agendamento</button>

</form>

<hr>

<h2>Agendamentos cadastrados</h2>

<table border="1" cellpadding="8">
    <tr>
        <th>Cliente</th>
        <th>Data</th>
        <th>Horário</th>
        <th>Serviço</th>
        <th>Observações</th>
        <th>Status</th>
    </tr>

    <?php while ($agendamento = $agendamentos->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($agendamento["cliente"]); ?></td>
            <td><?php echo htmlspecialchars($agendamento["data"]); ?></td>
            <td><?php echo htmlspecialchars(substr($agendamento["horario"], 0, 5)); ?></td>
            <td><?php echo htmlspecialchars($agendamento["servico"]); ?></td>
            <td><?php echo htmlspecialchars($agendamento["observacoes"] ?? ""); ?></td>
            <td><?php echo htmlspecialchars($agendamento["status"]); ?></td>
        </tr>
    <?php } ?>
</table>

<br>

<a href="clientes.php">Clientes</a> |
<a href="principal.php">Voltar ao menu</a>

</body>
</html>

<?php
$conn->close();
?>
