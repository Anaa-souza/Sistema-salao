
<?php
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit;
}

if (isset($_SESSION["ultimo_acesso"]) && time() - $_SESSION["ultimo_acesso"] > 1800) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

$_SESSION["ultimo_acesso"] = time();

include "config.php";

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $agendamento_id = $_POST["agendamento_id"];
    $data = $_POST["data"];
    $descricao = trim($_POST["descricao"]);
    $valor = str_replace(",", ".", $_POST["valor"]);

    if ($agendamento_id != "" && $data != "" && $descricao != "") {
        if ($valor == "" || (is_numeric($valor) && $valor >= 0)) {
            if ($valor == "") {
                $valor = null;
            }

            $sql = "INSERT INTO procedimentos_salao
                    (agendamento_id, data, descricao, valor)
                    VALUES (?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "isss",
                $agendamento_id,
                $data,
                $descricao,
                $valor
            );

            if ($stmt->execute()) {
                $mensagem = "Procedimento cadastrado com sucesso!";
            } else {
                $mensagem = "Erro ao cadastrar procedimento.";
            }

            $stmt->close();
        } else {
            $mensagem = "Informe um valor válido.";
        }
    } else {
        $mensagem = "Preencha todos os campos obrigatórios.";
    }
}

$sql = "SELECT id, cliente_id, data, horario, servico
        FROM agendamentos
        ORDER BY data DESC, horario DESC";

$agendamentos = $conn->query($sql);

$sql = "SELECT p.id, p.data, p.descricao, p.valor,
               c.nome AS cliente,
               a.servico
        FROM procedimentos_salao p
        INNER JOIN agendamentos a
            ON p.agendamento_id = a.id
        INNER JOIN clientes c
            ON a.cliente_id = c.id
        ORDER BY p.data DESC, p.id DESC";

$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procedimentos - Salão de Beleza</title>
</head>

<body>

    <h1>Procedimentos do salão</h1>

    <p><?php echo htmlspecialchars($mensagem); ?></p>

    <h2>Cadastrar procedimento realizado</h2>

    <form method="POST">

        <label>Agendamento:</label>
        <select name="agendamento_id" required>
            <option value="">Selecione um agendamento</option>

            <?php while ($agendamento = $agendamentos->fetch_assoc()) { ?>
                <option value="<?php echo $agendamento["id"]; ?>">
                    <?php
                    echo htmlspecialchars(
                        $agendamento["cliente_id"] . " - " .
                        $agendamento["servico"] . " - " .
                        $agendamento["data"] . " " .
                        $agendamento["horario"]
                    );
                    ?>
                </option>
            <?php } ?>

        </select>

        <br><br>

        <label>Data do procedimento:</label>
        <input type="date" name="data" required>

        <br><br>

        <label>Descrição:</label>
        <input type="text" name="descricao" maxlength="255" required>

        <br><br>

        <label>Valor (R$):</label>
        <input type="number" name="valor" min="0" step="0.01">

        <br><br>

        <button type="submit">Cadastrar procedimento</button>

    </form>

    <hr>

    <h2>Procedimentos cadastrados</h2>

    <table border="1" cellpadding="8">

        <tr>
            <th>Cliente</th>
            <th>Serviço agendado</th>
            <th>Data</th>
            <th>Descrição</th>
            <th>Valor</th>
        </tr>

        <?php while ($procedimento = $resultado->fetch_assoc()) { ?>

            <tr>
                <td>
                    <?php echo htmlspecialchars($procedimento["cliente"]); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($procedimento["servico"]); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($procedimento["data"]); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($procedimento["descricao"]); ?>
                </td>

                <td>
                    <?php
                    if ($procedimento["valor"] !== null) {
                        echo "R$ " . number_format(
                            (float) $procedimento["valor"],
                            2,
                            ",",
                            "."
                        );
                    } else {
                        echo "Não informado";
                    }
                    ?>
                </td>
            </tr>

        <?php } ?>

    </table>

    <br>

    <a href="principal.php">Voltar ao menu</a>

</body>
</html>

<?php
$conn->close();
?>
