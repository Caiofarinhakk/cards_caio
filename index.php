<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['titulo'])) {
    $sql = "INSERT INTO cards (titulo, descricao, usuario, urgencia, prioridade, aplicacao, estado) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_POST['titulo'],
        $_POST['descricao'],
        $_POST['usuario'],
        $_POST['urgencia'],
        $_POST['prioridade'],
        $_POST['aplicacao'],
        $_POST['estado']
    ]);
}


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_id'])) {
    $sql = "DELETE FROM cards WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_POST['delete_id']]);
}

$cards = $pdo->query("SELECT * FROM cards ORDER BY criado_em DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 20px;
            padding: 20px;
        }
        
        form {
            background: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        input, textarea, select {
            width: 100%;
            margin-bottom: 10px;
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        button {
            padding: 10px 15px;
            background: black;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
            border-left: 8px solid #ccc;
        }

        .delete-button {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 6px 10px;
            margin-top: 10px;
            cursor: pointer;
            border-radius: 6px;
            width: 100%;
        }
    </style>
</head>
<body>
    <h1>CR7 CARDs</h1>

    <form method="post">
        <input type="text" name="titulo" placeholder="Título" required>
        <textarea name="descricao" placeholder="Descrição" required></textarea>
        <input type="text" name="usuario" placeholder="Responsável" required>

        <label>Urgência:</label>
        <select name="urgencia" required>
            <option value="1">Baixa</option>
            <option value="2">Média</option>
            <option value="3">Alta</option>
        </select>

        <label>Prioridade:</label>
        <select name="prioridade" required>
            <option value="Normal">Normal</option>
            <option value="Importante">Importante</option>
            <option value="Crítica">faça agora</option>
        </select>

        <label>Aplicação:</label>
        <select name="aplicacao" required>
            <option value="Website">Website</option>
            <option value="Mobile">Mobile</option>
            <option value="Banco de Dados">Banco de Dados</option>
        </select>

        <label>Estado:</label>
        <select name="estado" required>
            <option>Pendente</option>
            <option>Em andamento</option>
            <option>Concluído</option>
        </select>

        <button type="submit">Criar Card</button>
    </form>

    <div class="cards-container">
        <?php foreach ($cards as $card): ?>
            <div class="card">
                <h3><?php echo htmlspecialchars($card['titulo']); ?></h3>
                <p><?php echo nl2br(htmlspecialchars($card['descricao'])); ?></p>
                <p><strong>Responsável:</strong> <?php echo htmlspecialchars($card['usuario']); ?></p>
                <p><strong>Urgência:</strong> <?php echo $card['urgencia']; ?></p>
                <p><strong>Prioridade:</strong> <?php echo htmlspecialchars($card['prioridade']); ?></p>
                <p><strong>Aplicação:</strong> <?php echo htmlspecialchars($card['aplicacao']); ?></p>
                <p><strong>Estado:</strong> <?php echo htmlspecialchars($card['estado']); ?></p>
                <p><em>Criado em: <?php echo $card['criado_em']; ?></em></p>

                <form method="post">
                    <input type="hidden" name="delete_id" value="<?php echo $card['id']; ?>">
                    <button type="submit" class="delete-button">Excluir</button>
                </form>
              <a href="update.php?id=<?php echo $card['id']; ?>">Editar</a>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>