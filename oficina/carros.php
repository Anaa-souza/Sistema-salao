
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

        $sql = "INSERT INTO carros
                (dono_id, modelo, marca, placa, cor, ano)
                VALUES (?, ?, ?, ?, ?, ?)";

        /*
         * Esta versão utiliza a estrutura antiga da tabela carros.
         * Os campos do agendamento serão armazenados nela:
         * modelo = serviço
         * marca = observações
         * placa = horário
         * cor = status
         * ano = ano da data
         *
         * A data completa será armazenada no campo modelo?
         * Não: por isso, a estrutura da tabela precisa ser adaptada
         * antes de executar este arquivo.
         */
        $mensagem = "É necessário adaptar a tabela carros antes de cadastrar agendamentos.";
    } else {
        $mensagem = "Preencha todos os campos obrigatórios.";
    }
}

$clientes = $conn->query(
    "SELECT id, nome FROM dono ORDER BY nome"
);

$agendamentos = $conn->query(
    "SELECT c.id, c.modelo, c.marca, c.placa, c.cor, c.ano, d.nome
     FROM carros c
     INNER JOIN dono d ON c.dono_id = d.id
     ORDER BY c.id DESC"
);
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
        <th>Serviço</th>
        <th>Observações</th>
        <th>Horário</th>
        <th>Status</th>
    </tr>

    <?php while ($agendamento = $agendamentos->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($agendamento["nome"]); ?></td>
            <td><?php echo htmlspecialchars($agendamento["modelo"]); ?></td>
            <td><?php echo htmlspecialchars($agendamento["marca"]); ?></td>
            <td><?php echo htmlspecialchars($agendamento["placa"]); ?></td>
            <td><?php echo htmlspecialchars($agendamento["cor"]); ?></td>
        </tr>
    <?php } ?>
</table>

<p><a href="principal.php">Voltar ao menu</a></p>

</body>
</html>

<?php
$conn->close();
?>
