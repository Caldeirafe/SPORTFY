<?php
require_once 'config.php';
require_once 'auth.php';

// Verifica se o usuário está logado
$logado = isset($_SESSION['usuario_id']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Limpa e valida os dados recebidos
    $nome = trim($_POST['nome']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $sexo = $_POST['sexo'];
    $data_nascimento = $_POST['nascimento'];
    $cidade = trim($_POST['cidade']);
    $estado = trim($_POST['estado']);
    $telefone = trim($_POST['telefone']);

    // Validações
    if (empty($nome)) {
        $erro = "O nome é obrigatório";
    } else if (!$email) {
        $erro = "Email inválido";
    } else if (strlen($_POST['senha']) < 6) {
        $erro = "A senha deve ter pelo menos 6 caracteres";
    } else if (empty($cidade)) {
        $erro = "A cidade é obrigatória";
    } else if (empty($estado)) {
        $erro = "O estado é obrigatório";
    } else if (empty($telefone)) {
        $erro = "O telefone é obrigatório";
    } else {
        $conn = conectar();

        try {
            $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, sexo, data_nascimento, cidade, estado, telefone) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nome, $email, $senha, $sexo, $data_nascimento, $cidade, $estado, $telefone]);

            header("Location: login.php?cadastro=sucesso");
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Erro de duplicidade
                $erro = "Este email já está cadastrado";
            } else {
                $erro = "Erro no cadastro: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="assets/Frame 1.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <link rel="stylesheet" href="./style/style.css">
    <link rel="shortcut icon" href="assets/Frame 1.png" type="image/x-icon">
    <title>Cadastro - Sportfy</title>
</head>

<body>
    <nav class="navbar is-dark" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
            <a class="navbar-item" href="index.php">
                <img src="assets/Logo.png" alt="Logo Sportify">
            </a>
            <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarMenu">
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
            </a>
        </div>

        <div id="navbarMenu" class="navbar-menu">
            <div class="navbar-end">
                <a class="navbar-item" href="index.php">Home</a>
                <?php if ($logado): ?>
                    <a class="navbar-item" href="daoplay.php">Da o Play</a>
                    <a class="navbar-item" href="perfil.php">Perfil</a>
                    <a class="navbar-item" href="logout.php">Sair</a>
                <?php else: ?>
                    <a class="navbar-item" href="daoplay.php">Da o Play</a>
                    <a class="navbar-item" href="login.php">Entrar</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <section class="section">
        <div class="container">
            <div class="box">
                <h1 class="title has-text-centered">Cadastro do Atleta</h1>

                <?php if (isset($erro)): ?>
                    <div class="notification is-danger">
                        <?php echo $erro; ?>
                    </div>
                <?php endif; ?>

                <form action="" method="post" id="form">
                    <div class="field">
                        <label class="label">Nome</label>
                        <div class="control">
                            <input class="input" type="text" name="nome" placeholder="Nome Completo" required
                                value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : ''; ?>">
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Sexo</label>
                        <div class="control">
                            <label class="radio">
                                <input type="radio" name="sexo" value="Masculino"
                                    <?php echo (!isset($_POST['sexo']) || $_POST['sexo'] == 'Masculino') ? 'checked' : ''; ?>> Masculino
                            </label>
                            <label class="radio">
                                <input type="radio" name="sexo" value="Feminino"
                                    <?php echo (isset($_POST['sexo']) && $_POST['sexo'] == 'Feminino') ? 'checked' : ''; ?>> Feminino
                            </label>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Data de Nascimento</label>
                        <div class="control">
                            <input class="input" type="date" name="nascimento" required
                                value="<?php echo isset($_POST['nascimento']) ? htmlspecialchars($_POST['nascimento']) : ''; ?>">
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Cidade</label>
                        <div class="control">
                            <input class="input" type="text" name="cidade" placeholder="Cidade" required
                                value="<?php echo isset($_POST['cidade']) ? htmlspecialchars($_POST['cidade']) : ''; ?>">
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Estado</label>
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select name="estado" required>
                                    <option value="">Selecione o Estado</option>
                                    <?php
                                    $estados = array(
                                        'AC' => 'Acre',
                                        'AL' => 'Alagoas',
                                        'AP' => 'Amapá',
                                        'AM' => 'Amazonas',
                                        'BA' => 'Bahia',
                                        'CE' => 'Ceará',
                                        'DF' => 'Distrito Federal',
                                        'ES' => 'Espírito Santo',
                                        'GO' => 'Goiás',
                                        'MA' => 'Maranhão',
                                        'MT' => 'Mato Grosso',
                                        'MS' => 'Mato Grosso do Sul',
                                        'MG' => 'Minas Gerais',
                                        'PA' => 'Pará',
                                        'PB' => 'Paraíba',
                                        'PR' => 'Paraná',
                                        'PE' => 'Pernambuco',
                                        'PI' => 'Piauí',
                                        'RJ' => 'Rio de Janeiro',
                                        'RN' => 'Rio Grande do Norte',
                                        'RS' => 'Rio Grande do Sul',
                                        'RO' => 'Rondônia',
                                        'RR' => 'Roraima',
                                        'SC' => 'Santa Catarina',
                                        'SP' => 'São Paulo',
                                        'SE' => 'Sergipe',
                                        'TO' => 'Tocantins'
                                    );
                                    foreach ($estados as $sigla => $nome): ?>
                                        <option value="<?php echo $sigla; ?>" <?php echo (isset($_POST['estado']) && $_POST['estado'] == $sigla) ? 'selected' : ''; ?>>
                                            <?php echo $nome; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <script type="text/javascript">
                        function mascara(telefone) {
                            if (telefone.value.length == 0)
                                telefone.value = '(' + telefone.value; //quando começamos a digitar, o script irá inserir um parênteses no começo do campo.
                            if (telefone.value.length == 3)
                                telefone.value = telefone.value + ') '; //quando o campo já tiver 3 caracteres (um parênteses e 2 números) o script irá inserir mais um parênteses, fechando assim o código de área.

                            if (telefone.value.length == 10)
                                telefone.value = telefone.value + '-'; //quando o campo já tiver 8 caracteres, o script irá inserir um tracinho, para melhor visualização do telefone.

                        }
                    </script>
                    <div class="field">
                        <label class="label">Telefone</label>
                        <div class="control">
                            <input maxlength="16" onkeypress="mascara(this)" class="input" type="tel" name="telefone" placeholder="(xx)xxxx-xxxx" required
                                value="<?php echo isset($_POST['telefone']) ? htmlspecialchars($_POST['telefone']) : ''; ?>">
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Email</label>
                        <div class="control">
                            <input class="input" type="email" name="email" placeholder="Email" required
                                value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Senha</label>
                        <div class="control">
                            <input class="input" type="password" name="senha" placeholder="Senha" required>
                        </div>
                    </div>
                    <div class="field">
                        <div class="control">
                            <button class="button is-primary is-fullwidth" type="submit">Cadastrar</button>
                        </div>
                    </div>
                    <div class="has-text-centered">
                        <p>Já tem uma conta? <a href="login.php">Entrar</a></p>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <footer class="footer has-background-dark has-text-white">
        <div class="content has-text-centered">
            <p>&copy; 2024 - Criado por DFG</p>
            <div class="social">
                <a href="#" class="icon"><i class="fa-brands fa-instagram"></i></a>
                <a href="#" class="icon"><i class="fa-brands fa-facebook"></i></a>
                <a href="#" class="icon"><i class="fa-brands fa-twitter"></i></a>
                <a href="#" class="icon"><i class="fa-brands fa-youtube"></i></a>
            </div>
        </div>
    </footer>
    <script>
        // Script para ativar o menu hamburguer
        document.addEventListener('DOMContentLoaded', () => {
            const navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);
            if (navbarBurgers.length > 0) {
                navbarBurgers.forEach(el => {
                    el.addEventListener('click', () => {
                        const target = el.dataset.target;
                        const $target = document.getElementById(target);
                        el.classList.toggle('is-active');
                        $target.classList.toggle('is-active');
                    });
                });
            }
        });
    </script>
</body>

</html>