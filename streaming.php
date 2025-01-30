# preferenciasstreaming
CODIGO CRIADO EM PHP, QUANDO O CLIENTE ACESSA O SITE ELE É DIRECIONADO PARA UM FORMULARIO COM SEUS GOSTOS SEJA ELE FILMES, SERIES E MUSICAS ATRAVES DA BASE DE DADOS SERA SALVO, E TODA VEZ QUE O CLIENTE ACESSAR O STREAMING A PAGINA DE BOAS VINDAS, JÁ MOSTRARAO AS NOVIDADE DE ACORDO COM OS GOSTOS DO MESMO

<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'streaming_db');

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$usuario_id = $_SESSION['usuario_id'];

$generos = ['Ação', 'Comédia', 'Drama', 'Terror', 'Romance'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $conn->query("DELETE FROM gostos WHERE usuario_id = $usuario_id");

    foreach ($_POST['generos'] as $genero) {
        $stmt = $conn->prepare("INSERT INTO gostos (usuario_id, genero) VALUES (?, ?)");
        $stmt->bind_param("is", $usuario_id, $genero);
        $stmt->execute();
    }

    echo "Seus gostos foram atualizados!";
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selecione seus Gostos</title>
</head>
<body>

<h1>Selecione seus Gostos</h1>
<form method="POST">
    <?php foreach ($generos as $genero): ?>
        <label>
            <input type="checkbox" name="generos[]" value="<?= $genero ?>" />
            <?= $genero ?>
        </label><br />
    <?php endforeach; ?>
    <button type="submit">Salvar Preferências</button>
</form>

</body>
</html>

PAGINA DO CLIENTE ATUALIZADA: 

<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}


$conn = new mysqli('localhost', 'root', '', 'streaming_db');

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$usuario_id = $_SESSION['usuario_id'];

$sql = "SELECT genero FROM gostos WHERE usuario_id = $usuario_id";
$result = $conn->query($sql);

$generos = [];
while ($row = $result->fetch_assoc()) {
    $generos[] = $row['genero'];
}

if (count($generos) > 0) {
    $generos_str = "'" . implode("', '", $generos) . "'";


    $novidades_sql = "SELECT titulo, genero FROM novidades WHERE genero IN ($generos_str)";
    $novidades_result = $conn->query($novidades_sql);

    echo "<h1>Novidades para Você!</h1>";
    while ($novidade = $novidades_result->fetch_assoc()) {
        echo "<p>{$novidade['titulo']} ({$novidade['genero']})</p>";
    }
} else {
    echo "<h1>Atualize suas preferências para ver as novidades!</h1>";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novidades</title>
</head>
<body>

</body>
</html>

CREATE TABLE novidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255),
    genero VARCHAR(50)
);

INSERT INTO novidades (titulo, genero) VALUES
('O Grande Filme de Ação', 'Ação'),
('Comédia para Todos', 'Comédia'),
('O Drama do Século', 'Drama'),
('Filme de Terror Assustador', 'Terror'),
('Romance à Moda Antiga', 'Romance');
