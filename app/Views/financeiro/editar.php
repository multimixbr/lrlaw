<?php
// Define se o campo 'val_parcela' é somente leitura
$val_parcela_readonly = 'readonly'; // Agora não é mais utilizado
?>
<style>
    .form-switch .form-check-input {
        width: 3rem;
        height: 1.5rem;
        margin-left: -2.5rem;
    }
    .form-switch .form-check-input:checked {
        background-color: #198754;
    }
    .form-switch .form-check-input::before {
        width: 1.5rem;
        height: 1.5rem;
        transform: translateX(0.2rem);
    }
    .form-switch .form-check-input:checked::before {
        transform: translateX(1.5rem);
    }
</style>

<div>
    <div class="container-fluid mt-3">
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h2 class="mb-0">Editar Lançamento</h2>
            </div>
            <div class="card-body">
                <!-- Updated enctype to support file uploads -->
                <form action="<?= base_url('financeiro/financeiroControllers/atualizar/' . $lancamento['id_lan']) ?>" method="post" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <!-- Tipo da Conta -->
                        <div class="col-md-3">
                            <label for="tp_lancamento" id="tp_lancamento_label" class="form-label">Tipo da conta</label>
                            <select name="tp_lancamento" id="tp_lancamento" class="form-control" required>
                                <option value="">----Selecione----</option>
                                <option value="R" <?= $lancamento['tp_lancamento'] == 'R' ? 'selected' : '' ?>>Contas a Receber</option>
                                <option value="D" <?= $lancamento['tp_lancamento'] == 'D' ? 'selected' : '' ?>>Contas a Pagar</option>
                            </select>
                        </div>

                        <!-- Número Doc -->
                        <div class="col-md-3">
                            <label for="num_doc" class="form-label">Número Doc.</label>
                            <input type="text" name="num_doc" id="num_doc" class="form-control" value="<?= htmlspecialchars($lancamento['num_doc']) ?>">
                        </div>

                        <!-- NDI -->
                        <div class="col-md-3">
                            <label for="id_ndi" class="form-label">NDI:</label>
                            <select name="id_ndi" id="id_ndi" class="form-control">
                                <option value="">----Selecione----</option>
                                <?php foreach ($ndis as $ndi) {
                                    $selected = ($lancamento['id_ndi'] == $ndi->id_ndi) ? 'selected' : '';
                                    echo '<option value="' . $ndi->id_ndi . '" ' . $selected . '>' . $ndi->id_ndi . ' - ' .  $ndi->assunto . '</option>';
                                } ?>
                            </select>
                        </div>

                        <!-- Cliente -->
                        <div class="col-md-3">
                            <label for="id_pessoa" class="form-label">Cliente:</label>
                            <select name="id_pessoa" id="id_pessoa" class="form-control" required>
                                <?php foreach ($pessoas as $pessoa) {
                                    $selected = ($lancamento['id_pessoa'] == $pessoa->id_pessoa) ? 'selected' : '';
                                    echo '<option value="' . $pessoa->id_pessoa . '" ' . $selected . '>' . $pessoa->nm_pessoa . '</option>';
                                } ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <!-- Descrição -->
                        <div class="col-md-3">
                            <label for="descricao" class="form-label">Descrição:</label>
                            <input type="text" name="descricao" id="descricao" class="form-control" value="<?= htmlspecialchars($lancamento['descricao']) ?>">
                        </div>
                        
                        <!-- Valor da Conta -->
                        <div class="col-md-3">
                            <label for="vl_original" class="form-label">Valor da Conta:</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" name="vl_original" id="vl_original" class="form-control" value="<?= number_format($lancamento['vl_original'], 2, ',', '.') ?>" required>
                            </div>
                        </div>

                        <!-- Data de Vencimento -->
                        <div class="col-md-3">
                            <label for="dt_vencimento" class="form-label">Data de Vencimento:</label>
                            <input type="text" name="dt_vencimento" id="dt_vencimento" class="form-control datepicker" value="<?= date('d/m/Y', strtotime($lancamento['dt_vencimento'])) ?>" required>
                        </div>

                        <!-- Data da Competência -->
                        <div class="col-md-3">
                            <label for="dt_competencia" class="form-label">Data da Competência:</label>
                            <input type="text" name="dt_competencia" id="dt_competencia" class="form-control datepicker" value="<?= date('d/m/Y', strtotime($lancamento['dt_competencia'])) ?>" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <!-- Forma de Pagamento -->
                        <div class="col-md-4">
                            <label for="id_forma_pagto" class="form-label">Forma de Pagamento:</label>
                            <select name="id_forma_pagto" id="id_forma_pagto" class="form-control" required>
                                <?php foreach ($formasPagamento as $forma) : ?>
                                    <option value="<?= $forma->id_formapagto; ?>" <?= $forma->id_formapagto == $lancamento['id_forma_pagto'] ? 'selected' : '' ?>>
                                        <?= $forma->dsc_forma_pagto; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Os campos de "Número de Parcelas" e "Valor das Parcelas" foram removidos -->
                    </div>

                    <!-- Complemento -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="complemento" class="form-label">Complemento:</label>
                            <textarea name="complemento" id="complemento" class="form-control" rows="2"><?= htmlspecialchars($lancamento['complemento']) ?></textarea>
                        </div>
                    </div>

                    <!-- Upload de Documentos -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="documentos" class="form-label">Anexar Documentos:</label>
                            <input type="file" name="documentos[]" id="documentos" class="form-control" multiple accept="image/*">
                        </div>
                    </div>

                    <div class="mb-2 text-end">
                        <a href="<?= base_url('financeiro/financeiroControllers/visualizar/' . $lancamento['id_lan']) ?>" class="btn btn-secondary">Voltar</a>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                        <a href="javascript:void(0);" class="btn btn-danger" onclick="confirmarExclusao('<?= base_url('financeiro/financeiroControllers/cancelarLan/' . $lancamento['id_lan']) ?>')">Cancelar Lançamento</a>
                    </div>

                    <!-- Anexos -->
                    <?php if (!empty($anexos)) : ?>
                        <div class="card mt-4">
                            <div class="card-header bg-secondary text-white">
                                <h3 class="mb-0">Anexos</h3>
                            </div>
                            <div class="card-body">
                                <ul class="list-group">
                                    <?php foreach ($anexos as $anexo) : ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?= $anexo->nome_arquivo ?>
                                        <div>
                                            <a href="<?= base_url($anexo->caminho_arquivo) ?>" class="btn btn-sm btn-primary" target="_blank"><i class="fas fa-eye"></i> Visualizar</a>
                                            <button type="button" class="btn btn-sm btn-danger delete-anexo-btn" data-anexo-id="<?= $anexo->id_anexo ?>">Excluir</button>
                                        </div>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmarExclusao(url) {
        Swal.fire({
            title: 'Tem certeza?',
            text: "Você não poderá reverter esta ação!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }

    $(document).ready(function() {
        inicializarSelect2('#id_pessoa', 'Digite para pesquisar um cliente.');
        inicializarSelect2('#id_ndi', 'Digite para pesquisar um ndi.');
        inicializarSelect2('#id_forma_pagto', 'Digite para pesquisar uma forma de pagamento.');

        // Máscara para o campo de valor
        $('#vl_original').mask('#.##0,00', { reverse: true });

        // Lidar com a exclusão de anexos
        $('.delete-anexo-btn').on('click', function() {
            var anexoId = $(this).data('anexo-id');
            var $this = $(this);
            Swal.fire({
                title: 'Tem certeza que deseja excluir este anexo?',
                text: 'Esta ação não pode ser desfeita!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, excluir',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('financeiro/financeiroControllers/excluirAnexo') ?>',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            id_anexo: anexoId,
                            <?= csrf_token() ?>: '<?= csrf_hash() ?>' // Proteção CSRF
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire(
                                    'Excluído!',
                                    response.message,
                                    'success'
                                );
                                // Remove o anexo da lista
                                $this.closest('li').remove();
                            } else {
                                Swal.fire(
                                    'Erro!',
                                    response.message,
                                    'error'
                                );
                            }
                        },
                        error: function() {
                            Swal.fire(
                                'Erro!',
                                'Ocorreu um erro ao excluir o anexo.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    });
</script>
