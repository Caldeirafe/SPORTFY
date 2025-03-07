<?php
require_once 'config.php';
require_once 'auth.php';

// Verifica se está logado
verificaLogin();

// Buscar as informações do usuário logado
$user_id = $_SESSION['usuario_id'];
$conn = conectar();
$stmt = $conn->prepare("SELECT nome, email, telefone, cidade, estado, foto FROM usuarios WHERE usuarios_id = ?");
$stmt->execute([$user_id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<section id="section-edit" class="section">
    <div class="container">
        <div class="box">
            <h3 class="title is-4 has-text-centered">Editar Perfil</h3>

            <!-- Exibe erro caso ocorra -->
            <?php if (isset($_GET['erro'])): ?>
                <div class="notification is-danger">
                    <?php echo htmlspecialchars($_GET['erro']); ?>
                </div>
            <?php endif; ?>

            <!-- Formulário de Edição de Perfil -->
            <form method="POST" action="atualizar_perfil.php" enctype="multipart/form-data">
                <!-- Campo Nome -->
                <div class="field">
                    <label class="label">Nome</label>
                    <div class="control">
                        <input class="input" type="text" name="nome" placeholder="Digite seu nome" required value="<?= htmlspecialchars($usuario['nome']) ?>">
                    </div>
                </div>

                <!-- Campo Foto -->
                <div class="field">
                    <label class="label">Foto de Perfil</label>
                    <div class="file">
                        <label class="file-label">
                            <input class="file-input" type="file" name="foto" accept="image/*">
                            <span class="file-cta">
                                <span class="file-icon">
                                    <i class="fas fa-upload"></i>
                                </span>
                                <span class="file-label">
                                    Escolher arquivo…
                                </span>
                            </span>
                        </label>
                    </div>
                    <p class="help">Formatos suportados: JPG e PNG.</p>


                <!-- Campo Telefone -->
                <div class="field">
                    <label class="label">Telefone</label>
                    <div class="control">
                        <input class="input" type="tel" name="telefone" placeholder="Digite seu telefone" required value="<?= htmlspecialchars($usuario['telefone']) ?>">
                    </div>
                </div>

                <!-- Campo Cidade -->
                <div class="field">
                    <label class="label">Cidade</label>
                    <div class="control">
                        <input class="input" type="text" name="cidade" placeholder="Cidade" required value="<?= htmlspecialchars($usuario['cidade']) ?>">
                    </div>
                </div>

                <!-- Campo Estado -->
                <div class="field">
                    <label class="label">Estado</label>
                    <div class="control">
                        <input class="input" type="text" name="estado" placeholder="Estado" required value="<?= htmlspecialchars($usuario['estado']) ?>">
                    </div>
                </div>

                <!-- Botão de Envio -->
                <div class="field is-grouped is-grouped-centered">
                    <div class="control">
                        <button class="button is-success" type="submit">Salvar Alterações</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
