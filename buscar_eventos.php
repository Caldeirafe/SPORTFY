<?php
require_once 'config.php';
$pdo = conectar();

$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';

if (!empty($busca)) {
    $sql = "SELECT * FROM eventos 
            WHERE (evento_nome LIKE :busca OR evento_local LIKE :busca OR evento_esporte LIKE :busca) 
            AND evento_data >= CURDATE() 
            ORDER BY evento_data ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':busca', "%$busca%", PDO::PARAM_STR);
} else {
    $sql = "SELECT * FROM eventos WHERE evento_data >= CURDATE() ORDER BY evento_data ASC";
    $stmt = $pdo->prepare($sql);
}

$stmt->execute();
$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($eventos as $evento): ?>
    <div class="column is-4">
        <div class="card">
            <div class="card-content">
                <p class="title is-5"><?= htmlspecialchars($evento['evento_nome']) ?></p>
                <p><strong>Local:</strong> <?= htmlspecialchars($evento['evento_local']) ?> |
                    <strong>Esporte:</strong> <?= htmlspecialchars($evento['evento_esporte']) ?></p>
                <p><strong>Data:</strong> <?= date('d/m/Y', strtotime($evento['evento_data'])) ?> | 
                    <strong>Horário:</strong> <?= htmlspecialchars($evento['evento_hora']) ?></p>
                <p><strong>Descrição:</strong> <?= nl2br(htmlspecialchars($evento['evento_descricao'])) ?></p>
                <button class="button is-success is-fullwidth mt-2">Participar</button>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php if (empty($eventos)): ?>
    <p class="has-text-centered">Nenhum evento encontrado.</p>
<?php endif; ?>
