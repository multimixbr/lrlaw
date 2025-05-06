<style>
    /* Seu código CSS existente */
    body {
        font-family: Arial, sans-serif;
        background-color: #f5f5f5;
        margin: 0;
        padding: 0;
    }

    .main {
        padding: 20px;
    }

    h2 {
        color: #333;
    }

    .card-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-around;
        margin-top: 30px;
    }

    .card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 250px;
        margin: 15px;
        padding: 20px;
        text-align: center;
        transition: transform 0.3s;
        cursor: pointer;
    }

    /* ----- em breve ------ */
    .card { 
        position: relative;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
        text-align: center;
        background-color: #fff;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    
    .alerta-centralizado {
        display: none; /* Começa oculto até que seja exibido pelo JavaScript */
        background-color: #f0f0f0;
        color: #333;
        padding: 10px;
        text-align: center;
        border: 1px solid #ccc;
        border-radius: 5px;
        margin-top: 10px;
        font-size: 16px;
    }

    /* ----- em breve ------ */

    .card:hover {
        transform: translateY(-5px);
    }

    .card-icon {
        font-size: 40px;
        color: #333;
        margin-bottom: 15px;
    }

    .card-title {
        font-size: 18px;
        color: #333;
        margin-bottom: 10px;
    }

    .card-description {
        font-size: 14px;
        color: #777;
    }

    .options-container {
        display: none;
        margin-top: 20px;
    }

    .options-container.active {
        display: block;
    }

    .options-container form {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
    }

    .options-container form .form-group {
        margin-bottom: 15px;
    }

    .options-container form label {
        display: block;
        margin-bottom: 5px;
    }

    .options-container form input,
    .options-container form select {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .options-container form button {
        padding: 10px 15px;
        background-color: #333;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .options-container form button:hover {
        background-color: #555;
    }
</style>

<div>
    <h2>Configurações</h2>

    <div class="card-container">
        <div class="card" data-card="alterarSenha">
            <div class="card-icon"><i class="fas fa-lock"></i></div>
            <div class="card-title">Segurança</div>
            <div class="card-description">Atualize sua senha e configurações de segurança.</div>
        </div>
        <div class="card" data-card="permissao">
            <div class="card-icon"><i class="fas fa-sliders-h"></i></div>
            <div class="card-title">Permissões</div>
            <div class="card-description">Altere as permissões do sistema.</div>
            <div id="alerta-centralizado" class="alerta-centralizado">Em breve</div>
        </div>
        <div class="card" data-card="cadastrarUser">
            <div class="card-icon"><i class="fas fa-user-plus"></i></div>
            <div class="card-title">Cadastrar Usuário</div>
            <div class="card-description">Adicione novos usuários ao sistema.</div>
        </div>
    </div>

    <!-- Opções relacionadas a cada card -->
    <div id="options-alterarSenha" class="options-container">
        <form action="<?= base_url('config/configControllers/alterarSenha') ?>" method="post">
            <h3>Alterar Senha</h3>
            <div class="form-group">
                <label for="current_password">Senha Atual</label>
                <input type="password" name="current_password" id="current_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">Nova Senha</label>
                <input type="password" name="new_password" id="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirmar Nova Senha</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>
            <button type="submit">Salvar</button>
        </form>
    </div>

    <!-- <div id="options-permissao" class="options-container">
        <form action="<?= base_url('config/configControllers/permissao') ?>" method="post">
            <h3>Alterar Permissões</h3>
            <div class="form-group">
                <label for="user_role">Nível de Acesso</label>
                <select name="user_role" id="user_role">
                    <option value="admin">Administrador</option>
                    <option value="editor">Editor</option>
                    <option value="viewer">Visualizador</option>
                </select>
            </div>
            <button type="submit">Salvar</button>
        </form>
    </div> -->

    <div id="options-cadastrarUser" class="options-container">
        <form action="<?= base_url('config/configControllers/cadastrarUser') ?>" method="post">
            <h3>Cadastrar Novo Usuário</h3>
            <div class="form-group">
                <label for="username">Nome de Usuário</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit">Cadastrar</button>
        </form>
    </div>
</div>

<script>
    const cards = document.querySelectorAll('.card');
    const optionsContainers = document.querySelectorAll('.options-container');

    cards.forEach(card => {
        card.addEventListener('click', () => {
            // Esconde todas as opções
            optionsContainers.forEach(container => {
                container.classList.remove('active');
            });
            // Mostra as opções relacionadas ao card clicado
            const cardType = card.getAttribute('data-card');
            const optionsContainer = document.getElementById(`options-${cardType}`);
            if (optionsContainer) {
                optionsContainer.classList.add('active');
                // Rolagem suave até as opções
                optionsContainer.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });

    window.onload = function() {
        var alerta = document.getElementById('alerta-centralizado');
        alerta.style.display = 'block'; // Exibir o alerta ao carregar a página
    };

    // Redireciona para base_url após o envio do formulário
    // optionsContainers.forEach(container => {
    //     const form = container.querySelector('form');
    //     form.addEventListener('submit', (e) => {
    //         e.preventDefault(); // Evita o envio padrão
    //         // Aqui você pode adicionar a lógica para enviar os dados via AJAX, se necessário
    //         window.location.href = '<?= base_url() ?>'; // Redireciona para base_url
    //     });
    // });
</script>
