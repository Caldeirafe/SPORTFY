<?php

require_once 'config.php';
require_once 'auth.php';

verificaLogin(); // Garante que o usuário está logado

$pdo = conectar();
$usuario_id = $_SESSION['usuario_id'];

// Buscar os eventos do usuário logado
$sql = "
    SELECT evento_id, evento_nome, evento_data, evento_hora, evento_local, evento_esporte, evento_max_pessoas,
        (SELECT COUNT(*) FROM participacoes WHERE evento_id = eventos.evento_id) AS inscritos
    FROM eventos
    WHERE usuario_id = :usuario_id OR evento_id IN (SELECT evento_id FROM participacoes WHERE usuario_id = :usuario_id)
";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
$stmt->execute();
$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Compromissos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
</head>
<body>
    <section id="section-comp" class="section">
        <div class="container">
            <h1 class="title has-text-centered">Meus Compromissos</h1>

            <!-- Lista de Eventos -->
            <div class="box">
                <?php if (count($eventos) > 0): ?>
                    <?php foreach ($eventos as $evento): ?>
                        <div class="card mb-4">
                            <div class="card-content">
                                <div class="media">
                                    <div class="media-content">
                                        <p class="title is-4"><?= htmlspecialchars($evento['evento_nome']) ?></p>
                                        <p class="subtitle is-6">
                                            <strong>Data:</strong> <?= htmlspecialchars($evento['evento_data']) ?> |
                                            <strong>Hora:</strong> <?= htmlspecialchars($evento['evento_hora']) ?> |
                                            <strong>Local:</strong> <?= htmlspecialchars($evento['evento_local']) ?> |
                                            <strong>Esporte:</strong> <?= htmlspecialchars($evento['evento_esporte']) ?>
                                        </p>
                                    </div>
                                </div>

                                <!-- Número de Inscritos -->
                                <p><strong>Participantes:</strong> <?= $evento['inscritos'] ?> / <?= $evento['evento_max_pessoas'] ?> pessoas</p>

                                <?php
// Verifica se o usuário está autenticado
$usuario_id = $_SESSION['usuario_id']; 

// Prepara a consulta para buscar o evento, e verificar se o usuário é o criador ou participante
$stmt = $pdo->prepare(
    "SELECT e.usuario_id AS criador, p.usuario_id AS participante
    FROM eventos e
    LEFT JOIN participacoes p ON e.evento_id = p.evento_id AND p.usuario_id = ?
    WHERE e.evento_id = ?"
);
$stmt->execute([$usuario_id, $evento['evento_id']]);
$eventoData = $stmt->fetch();

// Determina se o usuário é o criador ou participante
$isCriador = ($eventoData && $eventoData['criador'] == $usuario_id);
$isParticipante = ($eventoData && $eventoData['participante'] == $usuario_id);

?>

                            <!-- Botões de Ação -->
                            <div class="buttons mt-3">
                                <?php if ($isCriador): ?>
                                    <!-- Botões de Editar e Excluir para o Criador -->
                                    <a href="editar_evento.php?evento_id=<?= $evento['evento_id'] ?>" class="button is-warning">Editar</a>
                                    <form method="POST" action="deletar_evento.php" style="display:inline;">
                                        <input type="hidden" name="evento_id" value="<?= $evento['evento_id'] ?>">
                                        <button type="submit" class="button is-danger" onclick="return confirm('Tem certeza que deseja excluir este evento?')">Excluir</button>
                                    </form>
                                <?php elseif ($isParticipante): ?>
                                    <!-- Botão de Desistir para Participantes -->
                                    <form method="POST" action="desistir_evento.php" style="display:inline;">
                                        <input type="hidden" name="evento_id" value="<?= $evento['evento_id'] ?>">
                                        <button type="submit" class="button is-danger" onclick="return confirm('Tem certeza que deseja desistir deste evento?')">Desistir</button>
                                    </form>
                                <?php endif; ?>
                            </div>

                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="has-text-centered">Nenhum compromisso encontrado.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
</body>
</html>
