<!-- editarParcela.php -->

<style>
    /* Estilos necessários */
    .link-form-style {
        display: block;
        width: 100%;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
        color: #495057;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        text-decoration: none;
    }

    .btn-group .btn {
        padding: 2px 4px;
        font-size: 12px;
        margin-right: 2px;
    }

    td.acao-col {
        width: 150px;
        text-align: center;
    }

    .alert-success {
        background-color: #28a745;
        color: white;
        font-size: 1.2rem;
        font-weight: bold;
    }

    .alert-success i {
        margin-right: 8px;
    }

    .readonly-field {
        background-color: #e9ecef;
        opacity: 1;
        pointer-events: none;
    }
</style>

<div class="main">
    <div class="container-fluid mt-3">
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h2 class="mb-0">Editando Parcela <?= $parcela->id_parcela ?></h2>
            </div>
            <div class="card-body">

                <form method="post" action="<?= base_url('financeiro/parcelasControllers/atualizarParcela/') . $parcela->id_parcela ?>" enctype="multipart/form-data">
                    <!-- Editar informações da parcela -->
                    <div class="row">
                        <!-- Número da Parcela -->
                        <div class="col-md-4 mb-2">
                            <label for="num_parcela" class="form-label">Parcela de Número:</label>
                            <input type="text" name="num_parcela" id="num_parcela" class="form-control readonly-field" value="<?= $parcela->num_parcela ?>" readonly>
                        </div>

                        <!-- Valor da Parcela -->
                        <div class="col-md-4 mb-2">
                            <label for="vl_parcela" class="form-label">Valor da Parcela:</label>
                            <input type="text" name="vl_parcela" id="vl_parcela" class="form-control" value="<?= number_format($parcela->vl_parcela, 2, ',', '.') ?>">
                        </div>

                        <!-- Data de Vencimento -->
                        <div class="col-md-4 mb-2">
                            <label for="dt_vencimento" class="form-label">Data de Vencimento:</label>
                            <input type="date" name="dt_vencimento" id="dt_vencimento" class="form-control" value="<?= date('Y-m-d', strtotime($parcela->dt_vencimento)) ?>">
                        </div>
                    </div>

                    <!-- Complemento -->
                    <div class="mb-2">
                        <label for="complemento" class="form-label">Complemento:</label>
                        <textarea name="complemento" id="complemento" class="form-control" rows="2"><?= htmlspecialchars($parcela->observacao) ?></textarea>
                    </div>

                    <!-- Anexos -->
                    <div class="mb-3">
                        <label for="anexo" class="form-label">Anexar Arquivos:</label>
                        <input type="file" class="form-control" name="anexo[]" id="anexo" multiple>
                    </div>

                    <!-- Botões -->
                    <div class="mb-2 text-end">
                        <a href="<?= base_url('financeiro/financeiroControllers/visualizar/') . $parcela->id_lan ?>" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    </div>
                </form>

                <!-- Exibir e deletar anexos -->
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
                                    <a href="<?= base_url($anexo->caminho_arquivo) ?>" class="btn btn-sm btn-primary" target="_blank">Visualizar</a>
                                    <form method="post" action="<?= base_url('financeiro/parcelasControllers/deletarAnexo/') . $anexo->id_anexo ?>" style="display:inline-block;" onsubmit="return confirm('Tem certeza que deseja deletar este anexo?');">
                                        <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                                    </form>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<!-- Script para máscaras e validações -->
<script>
    $(document).ready(function() {
        // Máscara para o campo de valor
        $('#vl_parcela').mask('#.##0,00', {
            reverse: true
        });
    });

    $('#dt_vencimento').on('click focus', function() {
        this.showPicker(); // Método para abrir o calendário
    });
</script>
