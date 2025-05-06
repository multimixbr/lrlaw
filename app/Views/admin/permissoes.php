<style>
    .card-header.bg-primary-custom {
        background-color: #343a40;
        color: #ffffff;
    }
    .checkbox-permissao {
        margin-bottom: 5px;
    }
    /* Espaçamento entre grupos de módulos */
    .modulo-container {
        margin-bottom: 1rem;
        border-left: 3px solid #343a40;
        padding-left: 10px;
    }
    /* Módulo sem checkbox e com toggle próximo ao nome */
    .modulo-titulo {
        font-weight: bold;
        margin-bottom: 8px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
    }
    .toggle-icon {
        font-size: 0.8rem;
        transition: transform 0.3s;
        margin-left: 5px;
    }
    .toggle-icon.rotate {
        transform: rotate(180deg);
    }
    .toggle-icon-operacao {
        font-size: 0.8rem;
        transition: transform 0.3s;
        cursor: pointer;
        margin-left: 10px;
    }
    .toggle-icon-operacao.rotate {
        transform: rotate(180deg);
    }
</style>

<div class="container-fluid mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary-custom text-white">
            <span>Gestão de Permissões</span>
        </div>
        <div class="card-body">
            <!-- Início do Form -->
            <form action="<?= base_url('admin/permissaoControllers/savePermissoes/') . $usuario->id_usuario ?>" method="POST">
                <?php if (!empty($usuario)) { ?>
                    <!-- Informações do usuário -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Nome do Usuário:</h5>
                            <p><?= $usuario->username ?></p>
                        </div>
                        <div class="col-md-6">
                            <h5>Email:</h5>
                            <p><?= $usuario->email ?></p>
                        </div>
                    </div>
                    <!-- Permissões -->
                    <h5>Permissões:</h5>
                    <?php foreach ($permissoes as $modulo) { ?>
                        <div class="modulo-container">
                            <!-- Cabeçalho do Módulo (sem checkbox) com toggle próximo ao nome -->
                            <div class="modulo-titulo">
                                <div>
                                    <i class="<?= $modulo['modulo_icone'] ?>"></i>
                                    <?= $modulo['nm_modulo'] ?>
                                </div>
                                <span class="toggle-icon">&#9660;</span>
                            </div>
                            
                            <!-- Container das funcionalidades (oculto por padrão) -->
                            <div class="funcionalidades-container" style="display: none; margin-left: 20px;">
                                <?php foreach ($modulo['funcionalidades'] as $funcionalidade) { ?>
                                    <div class="form-check checkbox-permissao">
                                        <input 
                                            class="form-check-input check-funcionalidade"
                                            type="checkbox"
                                            name="permissions[]" 
                                            value="f|<?= $funcionalidade['id_funcionalidade'] ?>"
                                            id="check_func_<?= $funcionalidade['id_funcionalidade'] ?>"
                                            data-funcionalidade-id="<?= $funcionalidade['id_funcionalidade'] ?>"
                                            <?php 
                                                // Se houver operações associadas, verifica se todas estão selecionadas
                                                if (!empty($funcionalidade['operacoes'])) {
                                                    $todasMarcadas = true;
                                                    foreach ($funcionalidade['operacoes'] as $operacao) {
                                                        $achou = false;
                                                        foreach ($permissoesUser as $perm) {
                                                            if ($perm['id_funcionalidade'] == $funcionalidade['id_funcionalidade'] && $perm['id_operacao'] == $operacao['id_operacao']) {
                                                                $achou = true;
                                                                break;
                                                            }
                                                        }
                                                        if (!$achou) {
                                                            $todasMarcadas = false;
                                                            break;
                                                        }
                                                    }
                                                    if ($todasMarcadas) {
                                                        echo 'checked';
                                                    }
                                                }
                                            ?>
                                        >
                                        <label class="form-check-label" for="check_func_<?= $funcionalidade['id_funcionalidade'] ?>">
                                            <i class="<?= $funcionalidade['icone'] ?>"></i>
                                            <?= $funcionalidade['nm_funcionalidade'] ?>
                                        </label>
                                        <?php if (!empty($funcionalidade['operacoes'])) { ?>
                                            <span class="toggle-icon-operacao">&#9660;</span>
                                        <?php } ?>
                                    </div>
                                    
                                    <!-- Container das operações (oculto por padrão) -->
                                    <?php if (!empty($funcionalidade['operacoes'])) { ?>
                                        <div class="operacoes-container" style="display: none; margin-left: 20px;">
                                            <?php foreach ($funcionalidade['operacoes'] as $operacao) { ?>
                                                <div class="form-check checkbox-permissao">
                                                    <input 
                                                        class="form-check-input check-operacao"
                                                        type="checkbox"
                                                        name="permissions[]" 
                                                        value="<?= $funcionalidade['id_funcionalidade'] ?>|<?= $operacao['id_operacao'] ?>"
                                                        id="check_operacao_<?= $operacao['id_funcionalidade_oper'] ?>"
                                                        data-funcionalidade-id="<?= $funcionalidade['id_funcionalidade'] ?>"
                                                        <?php
                                                            foreach ($permissoesUser as $perm) {
                                                                if ($perm['id_funcionalidade'] == $funcionalidade['id_funcionalidade'] && $perm['id_operacao'] == $operacao['id_operacao']) {
                                                                    echo 'checked';
                                                                    break;
                                                                }
                                                            }
                                                        ?>
                                                    >
                                                    <label class="form-check-label" for="check_operacao_<?= $operacao['id_funcionalidade_oper'] ?>">
                                                        <?= $operacao['nm_operacao'] ?>
                                                    </label>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                    <!-- Botões -->
                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary">Salvar</button>
                        <a href="<?= base_url('admin/usuariosControllers') ?>" class="btn btn-secondary">Voltar</a>
                    </div>
                <?php } else { ?>
                    <div class="alert alert-warning" role="alert">
                        Nenhum usuário encontrado.
                    </div>
                <?php } ?>
            </form>
            <!-- Fim do Form -->
        </div>
    </div>
</div>

<script>
$(document).ready(function() {

    // Dispara o evento change em cada checkbox de operação para atualizar o estado da funcionalidade
    $('.check-operacao').trigger('change');

    // Itera sobre cada checkbox de funcionalidade para garantir o estado indeterminado
    $('.check-funcionalidade').each(function(){
        var funcId = $(this).data('funcionalidade-id');
        var $ops = $('.check-operacao[data-funcionalidade-id="' + funcId + '"]');
        var total = $ops.length;
        var checked = $ops.filter(':checked').length;
        // Se houver operações marcadas, mas não todas, define o estado indeterminado
        if (checked > 0 && checked < total) {
            $(this).prop('checked', false).prop('indeterminate', true);
        }
    });

    // Toggle para exibir/ocultar as funcionalidades do módulo
    $('.modulo-titulo').click(function(e) {
        if ($(e.target).is('input')) return;
        var $container = $(this).closest('.modulo-container').find('.funcionalidades-container');
        var $toggleIcon = $(this).find('.toggle-icon');
        $container.slideToggle();
        $toggleIcon.toggleClass('rotate');
    });

    // Ao marcar/desmarcar uma funcionalidade, seleciona/desmarca todas as operações associadas
    $('.check-funcionalidade').change(function() {
        var funcId = $(this).data('funcionalidade-id');
        $('.check-operacao[data-funcionalidade-id="' + funcId + '"]').prop('checked', $(this).prop('checked')).trigger('change');
    });

    // Quando uma operação é marcada/desmarcada, atualiza o estado da funcionalidade (checked, unchecked ou indeterminado)
    $('.check-operacao').change(function() {
        var funcId = $(this).data('funcionalidade-id');
        var $ops = $('.check-operacao[data-funcionalidade-id="' + funcId + '"]');
        var total = $ops.length;
        var checked = $ops.filter(':checked').length;
        var $funcCheckbox = $('.check-funcionalidade[data-funcionalidade-id="' + funcId + '"]');
        if (checked === 0) {
            $funcCheckbox.prop('checked', false).prop('indeterminate', false);
        } else if (checked === total) {
            $funcCheckbox.prop('checked', true).prop('indeterminate', false);
        } else {
            $funcCheckbox.prop('checked', false).prop('indeterminate', true);
        }
    });

    // Toggle para exibir/ocultar as operações de uma funcionalidade
    $('.toggle-icon-operacao').click(function(e) {
        e.stopPropagation(); // Evita que o clique afete elementos pais
        var $opsContainer = $(this).closest('.checkbox-permissao').next('.operacoes-container');
        $opsContainer.slideToggle();
        $(this).toggleClass('rotate');
    });
});
</script>
