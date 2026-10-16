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
    $nome = trim($_POST["nome"]);
    $cpf = preg_replace("/\D/", "", $_POST["cpf"]);
    $telefone = trim($_POST["telefone"]);
    $email = trim($_POST["email"]);

    if ($nome != "" && strlen($cpf) == 11) {
        $sql = "INSERT INTO dono (nome, cpf, telefone, email)
                VALUES (?, AES_ENCRYPT(?, ?), ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sssss",
            $nome,
            $cpf,
            $chave,
            $telefone,
            $email
        );

        if ($stmt->execute()) {
            $mensagem = "Cliente cadastrado com sucesso!";
        } else {
            $mensagem = "Erro ao cadastrar cliente.";
        }

        $stmt->close();
    } else {
        $mensagem = "Informe o nome e um CPF com 11 números.";
    }
}

$termo = trim($_GET["busca"] ?? "");

$sql = "SELECT id, nome,
        CAST(AES_DECRYPT(cpf, ?) AS CHAR) AS cpf,
        telefone, email
        FROM dono
        WHERE nome LIKE ?
        ORDER BY nome";

$stmt = $conn->prepare($sql);
$busca = "%" . $termo . "%";
$stmt->bind_param("ss", $chave, $busca);
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Clientes - Salão de Beleza</title>
</head>
<body>

<h1>Cadastro de clientes</h1>

<p><?php echo htmlspecialchars($mensagem); ?></p>

<form method="POST">
    <input type="text" name="nome" placeholder="Nome completo" required>
    <input type="text" name="cpf" placeholder="CPF (11 números)"
           maxlength="14" required>
    <input type="text" name="telefone" placeholder="Telefone">
    <input type="email" name="email" placeholder="E-mail">
    <button type="submit">Cadastrar cliente</button>
</form>

<hr>

<h2>Pesquisar clientes</h2>

<form method="GET">
    <input type="text" name="busca" placeholder="Nome do cliente"
           value="<?php echo htmlspecialchars($termo); ?>">
    <button type="submit">Pesquisar</button>
    <a href="donos.php">Limpar</a>
</form>

<h2>Clientes cadastrados</h2>

<table border="1" cellpadding="8">
    <tr>
        <th>Nome</th>
        <th>CPF</th>
        <th>Telefone</th>
        <th>E-mail</th>
        <th>Ações</th>
    </tr>

    <?php while ($cliente = $resultado->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($cliente["nome"]); ?></td>
            <td><?php echo htmlspecialchars($cliente["cpf"] ?? ""); ?></td>
            <td><?php echo htmlspecialchars($cliente["telefone"] ?? ""); ?></td>
            <td><?php echo htmlspecialchars($cliente["email"] ?? ""); ?></td>
            <td>
                <a href="editar_dono.php?id=<?php echo $cliente["id"]; ?>">
                    Editar
                </a>
                |
                <a href="deletar_dono.php?id=<?php echo $cliente["id"]; ?>"
                   onclick="return confirm('Deseja excluir este cliente?')">
                    Excluir
                </a>
            </td>
        </tr>
    <?php } ?>
</table>

<p><a href="principal.php">Voltar ao menu</a></p>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
