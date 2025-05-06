<?php 
use App\Models\Financeiro\LancamentosModels;

$financeiroModel = new LancamentosModels();

// Recupera os dados do lançamento pai, se existir
$lancamentoPai = !empty($lancamento['id_lan_pai']) ? $financeiroModel->find($lancamento['id_lan_pai']) : null;

// Define o título do card de acordo com o tp_conta do lançamento atual
if ($lancamento['tp_conta'] == 'P') {
    $tituloLancamentos = "Lançamentos Parcelados";
} elseif ($lancamento['tp_conta'] == 'R') {
    $tituloLancamentos = "Lançamentos Recorrentes";
} else {
    $tituloLancamentos = "Lançamentos Filhos";
}
?>
<style>
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
        margin-right: 2px; /* Pequeno espaçamento entre os botões */
    }
    td.acao-col {
        width: 150px; /* Largura fixa para a coluna de ações */
        text-align: center;
    }
</style>

<div>
    <div class="container-fluid mt-3">
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h2 class="mb-0">Lançamento <?= $lancamento['id_lan'] ?> - <?= $lancamento['descricao'] ?></h2>
            </div>
            <div class="card-body">
                <!-- Informações Principais -->
                <div class="mb-3">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>Criado por:</strong>
                            <p><?= htmlspecialchars($lancamento['criado_por'] ?? 'N/A') ?></p>
                        </div>
                        <?php if (!isset($parcelas) || empty($parcelas)) { ?>
                            <div class="col-md-3">
                                <strong>Conferido por:</strong>
                                <p><?= htmlspecialchars($lancamento['conferido_por'] ?? 'N/A') ?></p>
                            </div>
                            <div class="col-md-3">
                                <strong>Aprovado por:</strong>
                                <p><?= htmlspecialchars($lancamento['aprovado_por'] ?? 'N/A') ?></p>
                            </div>
                        <?php } ?>
                        <div class="col-md-3">
                            <strong>NDI:</strong>
                            <p><?= ($lancamento['id_ndi'] == 0 || $lancamento['id_ndi'] == null) ? 'N/A' : htmlspecialchars($lancamento['ndi_assunto']) ?></p>
                        </div>
                    </div>
                </div>
                <form>
                    <!-- Exibição do Lançamento Pai (se existir) -->
                    <?php if (!empty($lancamento['id_lan_pai'])) : ?>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Lançamento Pai:</label>
                                <a href="<?= base_url('financeiro/financeiroControllers/visualizar/') . $lancamento['id_lan_pai'] ?>" 
                                   class="form-control" target="_blank" 
                                   style="display: block; text-decoration: none; background-color: #e9ecef; border: 1px solid #ced4da; pointer-events: all;"> 
                                    <?= "Lançamento #" . $lancamentoPai['id_lan'] . " - " . $lancamentoPai['complemento'] ?>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="tp_lancamento" class="form-label">Tipo da Conta:</label>
                                    <select name="tp_lancamento" id="tp_lancamento" class="form-control" disabled>
                                        <option value="R" <?= $lancamento['tp_lancamento'] == 'R' ? 'selected' : '' ?>>Contas a Receber</option>
                                        <option value="D" <?= $lancamento['tp_lancamento'] == 'D' ? 'selected' : '' ?>>Contas a Pagar</option>
                                    </select>
                                </div>

                                <!-- Número Doc -->
                                <div class="col-md-3">
                                    <label for="num_doc" class="form-label">Número Doc.</label>
                                    <input type="text" name="num_doc" id="num_doc" class="form-control" value="<?= htmlspecialchars($lancamento['num_doc']) ?>" disabled>
                                </div>

                                <!-- Cliente -->
                                <div class="col-md-6">
                                    <label for="id_pessoa" class="form-label">Cliente:</label>
                                    <select name="id_pessoa" id="id_pessoa" class="form-control" disabled>
                                        <option value="<?= $lancamento['id_pessoa'] ?>"><?= $cliente ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <!-- Valor da Conta -->
                                <div class="col-md-3">
                                    <label for="vl_original" class="form-label">Valor da Conta:</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="text" name="vl_original" id="vl_original" class="form-control" value="<?= $lancamento['vl_original'] ?>" disabled>
                                    </div>
                                </div>

                                <!-- Forma de Pagamento -->
                                <div class="col-md-3">
                                    <label for="id_forma_pagto" class="form-label">Forma de Pagamento:</label>
                                    <select name="id_forma_pagto" id="id_forma_pagto" class="form-control" disabled>
                                        <?php foreach ($formasPagamento as $forma) : ?>
                                            <option value="<?= $forma->id_formapagto; ?>" <?= $forma->id_formapagto == $lancamento['id_forma_pagto'] ? 'selected' : '' ?>>
                                                <?= $forma->dsc_forma_pagto; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Data de Vencimento -->
                                <div class="col-md-3">
                                    <label for="dt_vencimento" class="form-label">Data de Vencimento:</label>
                                    <input type="text" name="dt_vencimento" id="dt_vencimento" class="form-control" value="<?= date('d/m/Y', strtotime($lancamento['dt_vencimento'])) ?>" disabled>
                                </div>

                                <!-- Data da Competência -->
                                <div class="col-md-3">
                                    <label for="dt_competencia" class="form-label">Data da Competência:</label>
                                    <input type="date" name="dt_competencia" id="dt_competencia" class="form-control" value="<?= date('Y-m-d', strtotime($lancamento['dt_competencia'])) ?>" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Complemento -->
                    <div class="mb-2">
                        <label for="complemento" class="form-label">Complemento:</label>
                        <textarea name="complemento" id="complemento" class="form-control" rows="4" disabled><?= htmlspecialchars($lancamento['complemento']) ?></textarea>
                    </div>

                    <div class="mb-2 text-end">
                        <a href="<?= base_url('financeiro/financeiroControllers') ?>" class="btn btn-secondary">Voltar</a>

                        <?php if (empty($lancamento['baixado_por']) && $lancamento['is_aprovado'] != 1) { ?>
                            <a href="<?= base_url('financeiro/financeiroControllers/editar/') . $lancamento['id_lan'] ?>" id="editarBtn" class="btn btn-warning">Editar</a>
                        <?php } ?>

                        <?php if (empty($parcelas)) { ?>
                            <button type="button" class="btn btn-info" id="conferirBtn" data-bs-toggle="modal" data-bs-target="#conferirModal">Conferir</button>
                            <button type="button" class="btn btn-info" id="desconferirBtn" style="display: none;">Desconferir</button>
                            <button type="button" class="btn btn-success" id="aprovarBtn" style="display: none;" disabled>Aprovar</button>
                            <button type="button" class="btn btn-success" id="desaprovarBtn" style="display: none;">Desaprovar</button>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#baixarModal" id="baixarBtn" <?= ($lancamento['is_conferido'] == 1 && $lancamento['is_aprovado'] == 1) ? '' : 'disabled' ?>>Baixar</button>
                        <?php } ?>
                        <!-- Mensagem de Lançamento Encerrado -->
                        <div id="encerradaMsg" class="alert alert-success text-center mt-4" role="alert" style="<?= empty($lancamento['baixado_por']) ? 'display: none;' : '' ?>">
                            <i class="fas fa-lock"></i> <strong>Lançamento Encerrado!</strong> Este lançamento foi baixado e finalizado.
                        </div>
                    </div>
                </form>

                <!-- Card: Documentos / Anexos -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h4 class="mb-0">Documentos / Anexos</h4>
                    </div>
                    <div class="card-body">
                        <!-- Formulário para Upload de Anexos -->
                        <form method="post" 
                              action="<?= base_url('financeiro/financeiroControllers/uploadAnexo/') . $lancamento['id_lan'] ?>" 
                              enctype="multipart/form-data" class="mb-3">
                            <div class="mb-3">
                                <label for="documentos" class="form-label">Selecione o(s) arquivo(s)</label>
                                <input type="file" class="form-control" name="documentos[]" id="documentos" multiple>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Enviar Arquivo(s)
                            </button>
                        </form>
                                    
                        <!-- Lista de Anexos -->
                        <?php if (!empty($anexos)) : ?>
                            <ul class="list-group">
                                <?php foreach ($anexos as $anexo) : ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?= htmlspecialchars($anexo->nome_arquivo) ?>
                                        <a href="<?= base_url($anexo->caminho_arquivo) ?>" class="btn btn-sm btn-primary" target="_blank">
                                            <i class="fas fa-eye"></i> 
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else : ?>
                            <div class="text-muted">Nenhum anexo disponível para este lançamento.</div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Tabela de Lançamentos Filhos -->
                <?php if (!empty($lancamentoFilhos)) : ?>
                    <div class="card mt-4">
                        <div class="card-header bg-secondary text-white">
                            <h3 class="mb-0"><?= $tituloLancamentos ?></h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Tipo</th>
                                            <th>Data de Vencimento</th>
                                            <th>Valor da Conta</th>
                                            <th>Descrição</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($lancamentoFilhos as $filho): ?>
                                            <tr>
                                                <td><?= $filho['id_lan'] ?></td>
                                                <td>
                                                    <?php 
                                                        if ($filho['id_lan'] == $lancamento['id_lan']) {
                                                            // Essa linha corresponde ao lançamento que está sendo visualizado
                                                            if (empty($lancamento['id_lan_pai'])) {
                                                                echo "<strong>Principal (Visualizando)</strong>";
                                                            } else {
                                                                echo "<strong>Parcelado (Visualizando)</strong>";
                                                            }
                                                        } else {
                                                            // Para os demais registros
                                                            if (empty($filho['id_lan_pai'])) {
                                                                echo "Principal";
                                                            } else {
                                                                echo "Parcelado";
                                                            }
                                                        }
                                                    ?>
                                                </td>
                                                <td><?= date('d/m/Y', strtotime($filho['dt_vencimento'])) ?></td>
                                                <td>R$ <?= number_format($filho['vl_original'], 2, ',', '.') ?></td>
                                                <td><?= htmlspecialchars($filho['descricao']) ?></td>
                                                <td class="acao-col">
                                                    <div class="btn-group" role="group">
                                                        <a href="<?= base_url('financeiro/financeiroControllers/visualizar/') . $filho['id_lan'] ?>" class="btn btn-warning">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Modal Conferir -->
                <div class="modal fade" id="conferirModal" tabindex="-1" aria-labelledby="conferirModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="conferirModalLabel">Conferir Lançamento</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Confira e edite os dados do lançamento, se necessário:</p>
                                <div class="form-group">
                                    <label for="valor_conferido">Valor Conferido:</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="text" name="valor_conferido" id="valor_conferido" class="form-control" value="<?= $lancamento['vl_original'] ?>">
                                    </div>
                                </div>
                                <div class="form-group mt-3">
                                    <label for="data_vencimento_conferida">Data de Vencimento:</label>
                                    <input type="text" id="data_vencimento_conferida" class="form-control" disabled value="<?= date('d/m/Y', strtotime($lancamento['dt_vencimento'])) ?>">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                <button type="button" class="btn btn-info" id="confirmarConferenciaBtn">Conferido</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Baixar -->
                <div class="modal fade" id="baixarModal" tabindex="-1" aria-labelledby="baixarModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="baixarModalLabel">Baixar Lançamento</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class='row mb-3'>
                                    <div class="col-md-6">
                                        <label for="valor_baixa">Valor da Baixa:</label>
                                        <div class="input-group">
                                            <span class="input-group-text">R$</span>
                                            <input type="text" id="valor_baixa" class="form-control" value="<?= $lancamento['vl_original'] ?>" placeholder="Digite o valor da baixa">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="banco_baixa">Conta baixada:</label>
                                        <select name="id_conta" id="id_conta" class="form-control" required>
                                            <option value="">----Selecione----</option>
                                            <option value="1">Banco Inter</option>
                                            <option value="2">Banco Nubank</option>
                                        </select>
                                    </div>
                                </div>
                                <div class='row mb-3'>
                                    <div class="col-md-12">
                                        <div class="form-group mt-3">
                                            <label for="data_baixa">Data da Baixa:</label>
                                            <input type="text" id="data_baixa" class="form-control" max="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                <button type="button" class="btn btn-primary" id="confirmarBaixaBtn">Confirmar Baixa</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div> <!-- Fim do card-body -->
        </div> <!-- Fim do card -->
    </div> <!-- Fim do container-fluid -->
</div>

<script>
    $(document).ready(function() {

        $('#data_baixa').datepicker({
            dateFormat: 'dd/mm/yy',
            maxDate: today,
            showAnim: 'slideDown',
            changeMonth: true,
            changeYear: true,
            yearRange: '1900:2100',
        });

        $('#data_baixa').mask('00/00/0000', { placeholder: 'dd/mm/yyyy' });

        $('#data_baixa').on('blur', function () {
            const regex = /^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0-2])\/\d{4}$/;
            const valor = $('#data_baixa').val();
                
            if (!regex.test(valor) && valor !== '') {
                showCustomAlert('Por favor, insira uma data válida no formato dd/mm/yyyy.', 'danger');
                $('#data_baixa').val(''); 
            }
        });

        // Obtém os valores do lançamento diretamente do PHP
        let isAprovado = <?= $lancamento['is_aprovado'] ? 'true' : 'false' ?>;
        let isConferido = <?= $lancamento['is_conferido'] ? 'true' : 'false' ?>;
        let isBaixado = <?= empty($lancamento['baixado_por']) ? 'false' : 'true' ?>;

        // Configuração inicial dos botões com base no estado do lançamento
        if (isAprovado) {
            $('#aprovarBtn').hide();
            $('#desaprovarBtn').css('display', 'inline-block');
            $('#baixarBtn').prop('disabled', false); 
            $('#editarBtn').hide();
            $('#conferirBtn').hide();
            $('#desconferirBtn').hide(); 
        } else {
            $('#aprovarBtn').css('display', 'inline-block');
            $('#desaprovarBtn').hide();
            $('#baixarBtn').prop('disabled', true);

            if (isConferido) {
                $('#conferirBtn').hide();
                $('#desconferirBtn').css('display', 'inline-block'); 
                $('#aprovarBtn').prop('disabled', false).css('display', 'inline-block');
            } else {
                $('#conferirBtn').css('display', 'inline-block');
                $('#desconferirBtn').hide();
            }
        }

        if (isBaixado) {
            $('#aprovarBtn').hide();
            $('#desaprovarBtn').hide();
            $('#conferirBtn').hide();
            $('#desconferirBtn').hide();
            $('#editarBtn').hide();
            $('#baixarBtn').hide();
            $('#encerradaMsg').css('display', 'block');
        }

        // Máscara para os campos de valor
        $('#valor_conferido, #valor_baixa').mask('#.##0,00', { reverse: true });

        let lancamentoId = "<?= $lancamento['id_lan'] ?>";

        // Conferir Lançamento
        $('#confirmarConferenciaBtn').on('click', function() {
            let valorConferido = $('#valor_conferido').val();
            let dataVencimentoConferida = $('#data_vencimento_conferida').val();
        
            $('#dt_vencimento').val(dataVencimentoConferida);
        
            $('#conferirModal').modal('hide');
        
            $.ajax({
                url: '<?= base_url('financeiro/financeiroControllers/conferirLancamento') ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    id_lancamento: lancamentoId,
                    valor_conferido: valorConferido,
                    data_vencimento_conferida: dataVencimentoConferida
                },
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            title: 'Conferência Realizada!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                        $('#aprovarBtn').prop('disabled', false);
                        $('#conferirBtn').hide();
                        $('#desconferirBtn').css('display', 'inline-block');
                    } else {
                        Swal.fire({
                            title: 'Erro!',
                            text: response.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Erro!',
                        text: 'Erro ao conferir o lançamento.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

        // Desconferir Lançamento
        $('#desconferirBtn').on('click', function() {
            Swal.fire({
                title: 'Deseja desconferir o lançamento?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, desconferir',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('financeiro/financeiroControllers/desconferirLancamento') ?>',
                        type: 'POST',
                        dataType: 'json',
                        data: { id_lancamento: lancamentoId },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    title: 'Desconferência Realizada!',
                                    text: response.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                });
                                $('#aprovarBtn').prop('disabled', true);
                                $('#desconferirBtn').hide();
                                $('#conferirBtn').css('display', 'inline-block');
                                $('#editarBtn').prop('disabled', false).css('display', 'inline-block');
                            } else {
                                Swal.fire({
                                    title: 'Erro!',
                                    text: response.message,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                title: 'Erro!',
                                text: 'Erro ao desconferir o lançamento.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        });
    
        // Aprovar Lançamento
        $('#aprovarBtn').on('click', function() {
            Swal.fire({
                title: 'Deseja aprovar o lançamento?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, aprovar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('financeiro/financeiroControllers/aprovarLancamento') ?>',
                        type: 'POST',
                        dataType: 'json',
                        data: { id_lancamento: lancamentoId },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire('Aprovado!', response.message, 'success');
                                $('#aprovarBtn').hide();
                                $('#editarBtn').hide();
                                $('#desconferirBtn').hide();
                                $('#conferirBtn').hide();
                                $('#desaprovarBtn').css('display', 'inline-block');
                                $('#baixarBtn').prop('disabled', false);
                            } else {
                                Swal.fire('Erro!', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire({
                                title: 'Erro!',
                                text: 'Erro ao aprovar o lançamento.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        });

        // Desaprovar Lançamento
        $('#desaprovarBtn').on('click', function() {
            Swal.fire({
                title: 'Deseja desaprovar o lançamento?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, desaprovar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('financeiro/financeiroControllers/desaprovarLancamento') ?>',
                        type: 'POST',
                        dataType: 'json',
                        data: { id_lancamento: lancamentoId },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire('Desaprovado!', response.message, 'success');
                                $('#desaprovarBtn').hide();
                                $('#editarBtn').show();
                                $('#desconferirBtn').show();
                                $('#aprovarBtn').css('display', 'inline-block');
                                $('#baixarBtn').prop('disabled', true);
                            } else {
                                Swal.fire('Erro!', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire({
                                title: 'Erro!',
                                text: 'Erro ao desaprovar o lançamento.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        });
    
        // Confirmar Baixa
        $('#confirmarBaixaBtn').on('click', function() {
            let valorBaixa = $('#valor_baixa').val();
            let dataBaixa = $('#data_baixa').val();
            let id_conta = $('#id_conta').val();
        
            if (valorBaixa && dataBaixa) {
                $.ajax({
                    url: '<?= base_url('financeiro/financeiroControllers/baixarLancamento') ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        id_lancamento: lancamentoId,
                        valor_baixa: valorBaixa,
                        data_baixa: dataBaixa,
                        id_conta: id_conta
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                title: 'Baixa Realizada!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            });
                            $('#baixarModal').modal('hide');
                            $('#editarBtn').hide();
                            $('#desaprovarBtn').hide();
                            $('#baixarBtn').prop('disabled', true).text('Baixado').hide();
                            $('#encerradaMsg').show();
                        } else {
                            Swal.fire({
                                title: 'Erro!',
                                text: response.message,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Erro!',
                            text: 'Erro ao realizar a baixa do lançamento.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            } else {
                Swal.fire({
                    title: 'Erro!',
                    text: 'Por favor, preencha todos os campos.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
</script>
