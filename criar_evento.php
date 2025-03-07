<?php
require_once 'config.php';
require_once 'auth.php';
?>

<div class="box">
    <h3 class="title is-4 has-text-centered">Criar Novo Evento</h3>

    <form method="POST" action="salvar_evento.php">
        <!-- Nome do Evento -->
        <div class="field">
            <label class="label">Nome do Evento</label>
            <div class="control">
                <input class="input" type="text" name="evento_nome" placeholder="Digite o nome do evento" required>
            </div>
        </div>

        <!-- Data do Evento -->
        <div class="field">
            <label class="label">Data</label>
            <div class="control">
                <input class="input" type="date" name="evento_data" required>
            </div>
        </div>

        <!-- Hora do Evento -->
        <div class="field">
            <label class="label">Hora</label>
            <div class="control">
                <input class="input" type="time" name="evento_hora" required>
            </div>
        </div>

        <!-- Número Máximo de Pessoas -->
        <div class="field">
            <label class="label">Número Máximo de Pessoas</label>
            <div class="control">
                <input class="input" type="number" name="evento_max_pessoas" placeholder="Ex: 10" min="1" required>
            </div>
        </div>

        <!-- Local -->
        <div class="field">
            <label class="label">Local</label>
            <div class="control">
                <input class="input" type="text" name="evento_local" placeholder="Digite o local do evento">
            </div>
        </div>

        <!-- Esporte -->
        <div class="field">
            <label class="label">Esporte</label>
            <div class="control">
                <div class="select">
                    <select name="evento_esporte" required>
                        <option value="" disabled selected>Selecione um esporte</option>
                        <option value="futebol">Futebol</option>
                        <option value="basquete">Basquete</option>
                        <option value="volei">Vôlei</option>
                        <option value="outros">Outros</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Descrição do Evento -->
        <div class="field">
            <label class="label">Descrição</label>
            <div class="control">
                <textarea class="textarea" name="evento_descricao" maxlength="100" placeholder="Digite uma breve descrição do evento" required></textarea>
            </div>
        </div>

        <!-- Botão de Envio -->
        <div class="field is-grouped is-grouped-centered">
            <div class="control">
                <button class="button is-success" type="submit">Criar Evento</button>
            </div>
        </div>
    </form>
</div>
