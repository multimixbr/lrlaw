<?php
$session = session()->get();
$permissoesModels = new \App\Models\Admin\PermissoesModels();
$dashboard = $permissoesModels->getFuncionalidades();
$permitidos = $permissoesModels->getFuncionalidadesPermitidas($session['id_usuario']);

// Cria um array associativo de módulos permitidos, indexado pelo id_modulo
$permitidosArray = [];
if (isset($permitidos) && is_array($permitidos)) {
    foreach ($permitidos as $moduloPermitido) {
        $permitidosArray[$moduloPermitido['id_modulo']] = $moduloPermitido;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>LrLaw - Sistema de Gestão</title>

    <!-- CSS de bibliotecas -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />

    <!-- JS de bibliotecas -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.6/jquery.inputmask.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        /* Geral */
        html,
        body {
            margin: 0;
            padding: 0;
            font-size: 13px;
        }
        /* HEADER */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 45px;
            background-color: #343a40;
            display: flex;
            align-items: center;
            padding: 0 1rem;
            z-index: 1000;
        }
        .navbar-brand {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            font-size: 16px;
        }
        .navbar-brand:hover {
            color: #e2e2e2;
        }
        .logout-btn {
            margin-left: auto;
        }
        /* SIDEBAR */
        .sidebar {
            position: fixed;
            top: 45px;
            left: 0;
            width: 70px;
            height: calc(100% - 45px);
            background-color: #343a40;
            overflow: hidden;
            transition: width 0.3s;
            padding-top: 15px;
            z-index: 999;
        }
        /* Estado aberto: alinha à esquerda */
        .sidebar:hover a,
        .sidebar:hover .sidebar-module-header {
            text-align: left;
            padding-left: 24px;
        }
        /* Links e cabeçalhos */
        .sidebar a,
        .sidebar-module-header {
            padding: 12px 8px;
            text-decoration: none;
            font-size: 14px;
            color: #f1f1f1;
            display: block;
            transition: background-color 0.3s;
        }
        /* Cursor pointer no cabeçalho do módulo */
        .sidebar-module-header {
            cursor: pointer;
        }
        .sidebar a:hover,
        .sidebar-module-header:hover {
            background-color: #495057;
        }
        /* Expansão da sidebar */
        .sidebar:hover {
            width: 220px;
        }
        /* Regras para os itens de funcionalidades (módulos derivados) */
        .funcionalidades a {
            font-size: 11px;
            padding-left: 60px;
            display: block;
            color: #ced4da;
            transition: background-color 0.3s;
        }
        .funcionalidades a:hover {
            background-color: #495057;
            color: #fff;
        }
        .sidebar:hover .funcionalidades a {
            font-size: 10px !important;
            padding-left: 60px !important;
            text-align: left;
        }
        /* MAIN */
        .main {
            margin-top: 45px;
            margin-left: 70px;
            padding: 15px;
            transition: margin-left 0.3s;
        }
        .sidebar:hover ~ .main {
            margin-left: 220px;
        }
        /* ALERTAS */
        #alert-container {
            position: fixed;
            top: 50px;
            right: 15px;
            z-index: 2000;
            font-size: 14px;
        }
        /* Ajuste para centralizar os ícones quando o sidebar estiver encolhido */
        .sidebar:not(:hover) a,
        .sidebar:not(:hover) .sidebar-module-header,
        .sidebar:not(:hover) .funcionalidades a {
            text-align: center !important;
            padding-left: 0 !important;
        }
        /* Esconde nomes dos módulos e setas quando o sidebar estiver fechado */
        .sidebar:not(:hover) .module-text,
        .sidebar:not(:hover) .chevron,
        .sidebar:not(:hover) a span {
            display: none !important;
        }
        /* Exibe nomes e setas quando o sidebar estiver aberto */
        .sidebar:hover .module-text,
        .sidebar:hover .chevron,
        .sidebar:hover a span {
            display: inline !important;
        }
    </style>
</head>
<body>
    <!-- HEADER / NAVBAR -->
    <nav class="navbar">
        <a class="navbar-brand" href="<?= base_url('/home') ?>">
            <i class="fas fa-gavel"></i> LrLaw
        </a>
        <div class="header-dropdown dropdown">
            <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li>
                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalTrocarSenha">
                        <i class="fas fa-key"></i> Trocar Senha
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="<?= base_url("loginControllers/logout") ?>">
                        <i class="fas fa-sign-out-alt"></i> Sair
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- SIDEBAR LATERAL -->
    <div class="sidebar">
        <?php if (isset($dashboard) && is_array($dashboard)): ?>
            <?php foreach ($dashboard as $modulo): ?>
                <?php
                // Verifica se o módulo está ativo e se o usuário possui funcionalidades permitidas para esse módulo
                if ($modulo['modulo_ativo'] == 1 &&
                    isset($permitidosArray[$modulo['id_modulo']]) &&
                    !empty($permitidosArray[$modulo['id_modulo']]['funcionalidades'])
                ):
                ?>
                    <div class="sidebar-module">
                        <div class="sidebar-module-header" onclick="toggleFuncionalidades('modulo-<?= $modulo['id_modulo'] ?>')">
                            <i class="<?= $modulo['modulo_icone'] ?>"></i>
                            <span class="module-text"><?= $modulo['nm_modulo'] ?></span>
                            <span class="chevron"><i class="fas fa-chevron-down"></i></span>
                        </div>
                        <div id="modulo-<?= $modulo['id_modulo'] ?>" class="funcionalidades" style="display: none;">
                            <?php foreach ($permitidosArray[$modulo['id_modulo']]['funcionalidades'] as $funcionalidade): ?>
                                <?php if ($funcionalidade['is_ativo'] == 1): ?>
                                    <a href="<?= base_url($funcionalidade['url']) ?>">
                                        <i class="<?= $funcionalidade['icone'] ?>"></i>
                                        <span><?= $funcionalidade['nm_funcionalidade'] ?></span>
                                    </a>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- ALERTAS SUSPENSOS -->
    <div id="alert-container">
        <?php if (session()->has('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->has('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->has('warning')): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <?= session('warning') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->has('info')): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <?= session('info') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    </div>

    <!-- MODAL TROCAR SENHA -->
    <div class="modal fade" id="modalTrocarSenha" tabindex="-1" aria-labelledby="modalTrocarSenhaLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTrocarSenhaLabel">Trocar Senha</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form id="formTrocarSenha">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="senhaAtual" class="form-label">Senha Atual:</label>
                            <input type="password" class="form-control" id="senhaAtual" name="senhaAtual" required>
                        </div>
                        <div class="mb-3">
                            <label for="novaSenha" class="form-label">Nova Senha:</label>
                            <input type="password" class="form-control" id="novaSenha" name="novaSenha" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirmarSenha" class="form-label">Confirmar Nova Senha:</label>
                            <input type="password" class="form-control" id="confirmarSenha" name="confirmarSenha"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="main">
        <?= @$content ?>
    </div>

    <!-- SCRIPTS PERSONALIZADOS -->
    <script>
        // Variável global para armazenar o módulo aberto
        var currentModuleOpen = null;

        function toggleFuncionalidades(id) {
            var elem = document.getElementById(id);
            // Se o módulo já estiver aberto, fecha e limpa a variável
            if (elem.style.display === "block") {
                elem.style.display = "none";
                currentModuleOpen = null;
            } else {
                // Fecha todos os módulos
                document.querySelectorAll('.funcionalidades').forEach(function(div) {
                    div.style.display = 'none';
                });
                // Abre o módulo clicado e armazena seu ID
                elem.style.display = "block";
                currentModuleOpen = id;
            }
        }

        // Quando o mouse sair do sidebar, fecha o módulo aberto (mas mantém a referência)
        document.querySelector('.sidebar').addEventListener('mouseleave', function() {
            if (currentModuleOpen) {
                var elem = document.getElementById(currentModuleOpen);
                if (elem) {
                    elem.style.display = 'none';
                }
            }
        });

        // Quando o mouse entrar no sidebar, reabre o módulo que estava aberto (se houver)
        document.querySelector('.sidebar').addEventListener('mouseenter', function() {
            if (currentModuleOpen) {
                var elem = document.getElementById(currentModuleOpen);
                if (elem) {
                    elem.style.display = 'block';
                }
            }
        });

        // Exemplo de data atual
        let today = new Date();

        // Alerta customizado
        function showCustomAlert(message, type = 'success') {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            $('#alert-container').append(alertHtml);
            setTimeout(() => {
                $('#alert-container .alert').first().alert('close');
            }, 5000);
        }

        function inicializarSelect2(selector, placeholder) {
            $(selector).select2({
                placeholder: placeholder,
                language: 'pt-BR',
                theme: 'bootstrap-5',
                width: '100%',
                minimumResultsForSearch: 0
            });
            $(selector).data('select2').$container.find('.select2-selection').on('focus', function () {
                $(selector).select2('open');
            });
        }

        $(document).on('select2:open', function () {
            const campoBusca = document.querySelector('.select2-search__field');
            if (campoBusca) {
                campoBusca.focus();
            }
        });

        function atualizarPessoasNosSelects(selectIds, pessoaId, pessoaNome) {
            selectIds.forEach(function (selectId) {
                const select = document.getElementById(selectId);
                if (select) {
                    let option = document.createElement("option");
                    option.value = pessoaId;
                    option.text = pessoaNome;
                    select.appendChild(option);
                }
            });
        }

        function inicializarDatepicker(selector) {
            $(selector).datepicker({
                dateFormat: 'dd/mm/yy',
                showAnim: 'slideDown',
                changeMonth: true,
                changeYear: true,
                yearRange: '1900:2100'
            });
            $(selector).mask('00/00/0000', { placeholder: 'dd/mm/yyyy' });
            $(selector).on('blur', function () {
                const regex = /^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0-2])\/\d{4}$/;
                const valor = $(selector).val();
                if (!regex.test(valor) && valor !== '') {
                    showCustomAlert('Por favor, insira uma data válida no formato dd/mm/yyyy.', 'danger');
                    $(selector).val('');
                }
            });
        }

        function inicializarPaginator(selector) {
            $(selector).DataTable({
                paging: true,
                lengthMenu: [10, 25, 50, 100],
                ordering: true,
                info: true,
                searching: true,
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/Portuguese-Brasil.json"
                }
            });
        }

        function exibirMensagemNoConsole() {
            console.log("Função de exemplo: exibirMensagemNoConsole");
        }
        function somarDoisNumeros(a, b) {
            return a + b;
        }
        function verificarUsuarioLogado() {
            let usuarioLogado = true;
            if (usuarioLogado) {
                console.log("Usuário está logado!");
            } else {
                console.log("Usuário não está logado.");
            }
        }

        inicializarDatepicker('.datepicker');

        function salvarNovaSenha() {
            var senhaAtual = $('#senhaAtual').val();
            var novaSenha = $('#novaSenha').val();
            var confirmarSenha = $('#confirmarSenha').val();

            if (!senhaAtual || !novaSenha || !confirmarSenha) {
                showCustomAlert('Por favor, preencha todos os campos.', 'danger');
                return;
            }
            if (novaSenha !== confirmarSenha) {
                showCustomAlert('As senhas não conferem.', 'danger');
                return;
            }

            $.ajax({
                url: '<?= base_url("config/configControllers/alterarSenha") ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    senhaAtual: senhaAtual,
                    novaSenha: novaSenha,
                    confirmarSenha: confirmarSenha
                },
                success: function (data) {
                    if (data.status === 'ok') {
                        showCustomAlert('Senha alterada com sucesso!', 'success');
                        $('#modalTrocarSenha').modal('hide');
                    } else {
                        showCustomAlert(data.mensagem || 'Erro ao alterar a senha.', 'danger');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    showCustomAlert('Erro de conexão: ' + errorThrown, 'danger');
                }
            });
        }
		
		// Adiciona um listener para submissão do formulário de troca de senha via tecla Enter
        $(document).ready(function () {
            $("#formTrocarSenha").on('submit', function (e) {
                e.preventDefault();
                salvarNovaSenha();
            });
        });
    </script>
</body>
</html>
