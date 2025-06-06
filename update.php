<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['titulo']) && !isset($_POST['update_id'])) {
        // Inserir novo card
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
    elseif (isset($_POST['update_id'])) {
        // Atualizar card existente
        $sql = "UPDATE cards SET 
                titulo = ?, 
                descricao = ?, 
                usuario = ?, 
                urgencia = ?, 
                prioridade = ?,
                aplicacao = ?,
                estado = ? 
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['titulo'],
            $_POST['descricao'],
            $_POST['usuario'],
            $_POST['urgencia'],
            $_POST['prioridade'],
            $_POST['aplicacao'],
            $_POST['estado'],
            $_POST['update_id']
        ]);
    } 
    elseif (isset($_POST['delete_id'])) {
        // Deletar card
        $sql = "DELETE FROM cards WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_POST['delete_id']]);
    }
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
            color: #333;
        }

        .logo {
            color: #BLACK;
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
            font-size: 1.5em;
            text-align: center;
            padding: 20px;
            margin-bottom: 30px;
            user-select: none;
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
            box-sizing: border-box;
            font-size: 14px;
        }

        button {
            padding: 10px 15px;
            background: black;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background: #444;
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
            transition: transform 0.2s ease;    
            position: relative;
            border: 1px solid #ccc;
        }

        .card:hover {
            transform: scale(1.02);
        }

        .urgencia-1 {
            border-left: 8px solid #27ae60; 
        }
        .urgencia-2 {
            border-left: 8px solid #f39c12; 
        }
        .urgencia-3 {
            border-left: 8px solid #c0392b; 
        }

        .card h3 {
            margin-top: 0;
        }
        .card p {
            margin: 10px 0;
        }

        .card .actions {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .edit-button {
            background: none;
            border: none;
            color: #007bff;
            text-decoration: underline;
            cursor: pointer;
            padding: 0;
            font: inherit;
        }

        .edit-button:hover {
            color: #0056b3;
            text-decoration: none;
        }

        .delete-button {
            background-color: #e74c3c; 
            color: white;
            border: none;
            padding: 6px 10px;
            margin-top: 10px;
            cursor: pointer;
            border-radius: 6px;
            font-weight: bold;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        .delete-button:hover {
            background-color: #c0392b;
        }

        .editable {
            border: 1px dashed #ccc;
            padding: 5px;
            margin: 5px 0;
        }

        .editable:focus {
            outline: none;
            border: 1px solid #007bff;
            background: #f8f9fa;
        }
    </style>
    <script>
        function enableEdit(cardId) {
            const card = document.getElementById(`card-${cardId}`);
            
            // Habilita edição dos campos de texto
            const titulo = card.querySelector('h3');
            titulo.contentEditable = true;
            titulo.classList.add('editable');
            
            const descricao = card.querySelector('p:nth-of-type(1)');
            descricao.contentEditable = true;
            descricao.classList.add('editable');
            
            const usuario = card.querySelector('p:nth-of-type(2) strong').nextSibling;
            const usuarioSpan = document.createElement('span');
            usuarioSpan.contentEditable = true;
            usuarioSpan.classList.add('editable');
            usuarioSpan.textContent = usuario.textContent.trim();
            usuario.replaceWith(usuarioSpan);
            
            // Substitui urgência por select
            const urgencia = card.querySelector('p:nth-of-type(3) strong').nextSibling;
            const urgenciaSelect = document.createElement('select');
            urgenciaSelect.innerHTML = `
                <option value="1" ${urgencia.textContent.trim() === '1' ? 'selected' : ''}>1 - Baixa</option>
                <option value="2" ${urgencia.textContent.trim() === '2' ? 'selected' : ''}>2 - Média</option>
                <option value="3" ${urgencia.textContent.trim() === '3' ? 'selected' : ''}>3 - Alta</option>
            `;
            urgencia.replaceWith(urgenciaSelect);
            
            // Substitui prioridade por select
            const prioridade = card.querySelector('p:nth-of-type(4) strong').nextSibling;
            const prioridadeSelect = document.createElement('select');
            prioridadeSelect.innerHTML = `
                <option value="Normal" ${prioridade.textContent.trim() === 'Normal' ? 'selected' : ''}>Normal</option>
                <option value="Importante" ${prioridade.textContent.trim() === 'Importante' ? 'selected' : ''}>Importante</option>
                <option value="Crítica" ${prioridade.textContent.trim() === 'Crítica' ? 'selected' : ''}>Crítica</option>
            `;
            prioridade.replaceWith(prioridadeSelect);
            
            // Substitui aplicação por select
            const aplicacao = card.querySelector('p:nth-of-type(5) strong').nextSibling;
            const aplicacaoSelect = document.createElement('select');
            aplicacaoSelect.innerHTML = `
                <option value="Sistema" ${aplicacao.textContent.trim() === 'Sistema' ? 'selected' : ''}>Sistema</option>
                <option value="Website" ${aplicacao.textContent.trim() === 'Website' ? 'selected' : ''}>Website</option>
                <option value="Mobile" ${aplicacao.textContent.trim() === 'Mobile' ? 'selected' : ''}>Mobile</option>
                <option value="Banco de Dados" ${aplicacao.textContent.trim() === 'Banco de Dados' ? 'selected' : ''}>Banco de Dados</option>
            `;
            aplicacao.replaceWith(aplicacaoSelect);
            
            // Substitui estado por select
            const estado = card.querySelector('p:nth-of-type(6) strong').nextSibling;
            const estadoSelect = document.createElement('select');
            estadoSelect.innerHTML = `
                <option ${estado.textContent.trim() === 'Pendente' ? 'selected' : ''}>Pendente</option>
                <option ${estado.textContent.trim() === 'Em andamento' ? 'selected' : ''}>Em andamento</option>
                <option ${estado.textContent.trim() === 'Concluído' ? 'selected' : ''}>Concluído</option>
            `;
            estado.replaceWith(estadoSelect);
            
            // Atualiza botão de edição para salvar
            const editButton = card.querySelector('.edit-button');
            editButton.textContent = 'Salvar';
            editButton.onclick = function() { saveEdit(cardId); };
        }
        
        function saveEdit(cardId) {
            const card = document.getElementById(`card-${cardId}`);
            
            // Coleta os valores editados
            const titulo = card.querySelector('h3').textContent;
            const descricao = card.querySelector('p:nth-of-type(1)').textContent;
            const usuario = card.querySelector('p:nth-of-type(2) span').textContent;
            const urgencia = card.querySelector('p:nth-of-type(3) select').value;
            const prioridade = card.querySelector('p:nth-of-type(4) select').value;
            const aplicacao = card.querySelector('p:nth-of-type(5) select').value;
            const estado = card.querySelector('p:nth-of-type(6) select').value;
            
            // Cria formulário oculto para enviar os dados
            const form = document.createElement('form');
            form.method = 'post';
            form.style.display = 'none';
            
            const fields = {
                update_id: cardId,
                titulo: titulo,
                descricao: descricao,
                usuario: usuario,
                urgencia: urgencia,
                prioridade: prioridade,
                aplicacao: aplicacao,
                estado: estado
            };
            
            for (const [name, value] of Object.entries(fields)) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                input.value = value;
                form.appendChild(input);
            }
            
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</head>
<body>
    <div class="logo">CR7 CARD</div>

    <form method="post">
        <input type="text" name="titulo" placeholder="Título" required>
        <textarea name="descricao" placeholder="Descrição" required></textarea>
        <input type="text" name="usuario" placeholder="Nome do usuário" required>

        <label>Urgência:</label>
        <select name="urgencia" required>
            <option value="1">1 - Baixa</option>
            <option value="2">2 - Média</option>
            <option value="3">3 - Alta</option>
        </select>

        <label>Prioridade:</label>
        <select name="prioridade" required>
            <option value="Normal">Normal</option>
            <option value="Importante">Importante</option>
            <option value="Crítica">Crítica</option>
        </select>

        <label>Aplicação:</label>
        <select name="aplicacao" required>
            <option value="Sistema">Sistema</option>
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
            <div class="card urgencia-<?php echo $card['urgencia']; ?>" id="card-<?php echo $card['id']; ?>">
                <div class="actions">
                    <button class="edit-button" onclick="enableEdit(<?php echo $card['id']; ?>)">Editar</button>
                </div>
                
                <h3><?php echo htmlspecialchars($card['titulo']); ?></h3>
                <p><?php echo nl2br(htmlspecialchars($card['descricao'])); ?></p>
                <p><strong>Responsável:</strong> <?php echo htmlspecialchars($card['usuario']); ?></p>
                <p><strong>Urgência:</strong> <?php echo $card['urgencia']; ?></p>
                <p><strong>Prioridade:</strong> <?php echo htmlspecialchars($card['prioridade']); ?></p>
                <p><strong>Aplicação:</strong> <?php echo htmlspecialchars($card['aplicacao']); ?></p>
                <p><strong>Estado:</strong> <?php echo htmlspecialchars($card['estado']); ?></p>
                <p><em>Criado em: <?php echo $card['criado_em']; ?></em></p>

                <form method="post" onsubmit="return confirm('Tem certeza que deseja excluir este card?');">
                    <input type="hidden" name="delete_id" value="<?php echo $card['id']; ?>">
                    <button type="submit" class="delete-button">Excluir</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>