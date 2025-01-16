<?php 
use App\Models\Financeiro\LancamentosModels;

$financeiroModel = new LancamentosModels();

$lancamentoPai = $financeiroModel->find($lancamento['id_lan_pai']);

if ($lancamento['vl_original'] == 0 && !empty($lancamento['id_lan_pai'])) {
    $vl_conta = $lancamento['vl_parcela'] * count($parcelas);
} else if ($lancamento['vl_original'] > 0 && empty($lancamento['id_lan_pai'])){
    $vl_conta = $lancamento['vl_original'];
}
$vl_conta = number_format($vl_conta, 2, ',', '.');

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
        width: 150px; /* Definir uma largura fixa para a coluna dos botões */
        text-align: center; /* Centralizar os botões na célula */
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
</style>

<div class="main">
    <div class="container-fluid mt-3">
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h2 class="mb-0">Visualizando Lançamento <?= $lancamento['id_lan'] ?></h2>
            </div>
            <div class="card-body">
                <!-- Campos de informações importantes -->
                <div class="mb-3">
                    <div class="row">
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
                            <p><?= ($lancamento['id_ndi'] == 0 || $lancamento['id_ndi'] == null) ? 'N/A' : htmlspecialchars($lancamento['id_ndi']) ?></p>
                        </div>
                    </div>
                </div>
                <form>
                    <!-- Exibir informações do lançamento pai, se existir -->
                    <?php if (!empty($lancamento['id_lan_pai'])) : ?>
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Lançamento Pai:</label>
                                <a href="<?= base_url('financeiro/financeiroControllers/visualizar/') . $lancamento['id_lan_pai'] ?>" 
                                   class="form-control" target="_blank" 
                                   style="display: block; text-decoration: none; background-color: #e9ecef; border: 1px solid #ced4da; pointer-events: all;"> 
                                    <?= "Lançamento #" . $lancamentoPai['id_lan'] . " - " . $lancamentoPai['complemento'] ?>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <!-- Tipo da Conta -->
                        <div class="col-md-4 mb-2">
                            <label for="tp_lancamento" class="form-label">Tipo da Conta:</label>
                            <select name="tp_lancamento" id="tp_lancamento" class="form-control" disabled>
                                <option value="R" <?= $lancamento['tp_lancamento'] == 'R' ? 'selected' : '' ?>>Contas a Receber</option>
                                <option value="D" <?= $lancamento['tp_lancamento'] == 'D' ? 'selected' : '' ?>>Contas a Pagar</option>
                            </select>
                        </div>

                        <!-- Cliente -->
                        <div class="col-md-8 mb-2">
                            <label for="id_pessoa" class="form-label">Cliente:</label>
                            <select name="id_pessoa" id="id_pessoa" class="form-control" disabled>
                                <option value="<?= $lancamento['id_pessoa'] ?>"><?= $cliente ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Valor da Conta -->
                        <div class="col-md-4 mb-2">
                            <label for="vl_original" class="form-label">Valor da Conta:</label>
                            <input type="text" name="vl_original" id="vl_original" class="form-control" value="<?= $vl_conta ?>" disabled>
                        </div>

                        <!-- Data de Vencimento -->
                        <div class="col-md-4 mb-2">
                            <label for="dt_vencimento" class="form-label">Data de Vencimento:</label>
                            <input type="date" name="dt_vencimento" id="dt_vencimento" class="form-control" value="<?= date('Y-m-d', strtotime($lancamento['dt_vencimento'])) ?>" disabled>
                        </div>

                        <!-- Data de Competência -->
                        <div class="col-md-4 mb-2">
                        <label for="dt_competencia" class="form-label">Data da Competência:</label>
                            <input type="date" name="dt_competencia" id="dt_competencia" class="form-control" value="<?= date('Y-m-d', strtotime($lancamento['dt_competencia'])) ?>" disabled>
                        </div>

                        <!-- Forma de Pagamento -->
                        <div class="col-md-4 mb-2">
                            <label for="id_forma_pagto" class="form-label">Forma de Pagamento:</label>
                            <select name="id_forma_pagto" id="id_forma_pagto" class="form-control" disabled>
                                <?php foreach ($formasPagamento as $forma) : ?>
                                    <option value="<?= $forma->id_formapagto; ?>" <?= $forma->id_formapagto == $lancamento['id_forma_pagto'] ? 'selected' : '' ?>>
                                        <?= $forma->dsc_forma_pagto; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <?php if (!empty($parcelas)): ?>
                            <!-- Total de Parcelas -->
                            <div class="col-md-4 mb-2">
                                <label for="total_parcelas" class="form-label">Total de Parcelas:</label>
                                <input type="text" name="total_parcelas" id="total_parcelas" class="form-control" value="<?= !empty($parcelas) ? count($parcelas) : 'N/A' ?>" disabled>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Complemento -->
                    <div class="mb-2">
                        <label for="complemento" class="form-label">Complemento:</label>
                        <textarea name="complemento" id="complemento" class="form-control" rows="4" disabled><?= htmlspecialchars($lancamento['complemento']) ?></textarea>
                    </div>

                    <div class="mb-2 text-end">
                        <a href="<?= base_url('financeiro/financeiroControllers/visualizarContas') ?>" class="btn btn-secondary">Voltar</a>

                        <?php if (empty($lancamento['baixado_por']) && $lancamento['is_aprovado'] != 1) { ?>
                            <a href="<?= base_url('financeiro/financeiroControllers/editar/') . $lancamento['id_lan'] ?>" id="editarBtn" class="btn btn-warning">Editar</a>
                        <?php } ?>

                        <?php if (!isset($parcelas) || empty($parcelas)) { ?>
                        
                            <button type="button" class="btn btn-info" id="conferirBtn" data-bs-toggle="modal" data-bs-target="#conferirModal">Conferir</button>
                            <button type="button" class="btn btn-info" id="desconferirBtn" style="display: none;">Desconferir</button>
                            <button type="button" class="btn btn-success" id="aprovarBtn" style="display: none;">Aprovar</button>
                            <button type="button" class="btn btn-success" id="desaprovarBtn" style="display: none;">Desaprovar</button>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#baixarModal" id="baixarBtn" <?= ($lancamento['is_conferido'] == 1 && $lancamento['is_aprovado'] == 1) ? '' : 'disabled' ?>>Baixar</button>

                        <?php } ?>
                        <!-- Exibir mensagem de encerramento quando o lançamento for baixado -->
                        <div id="encerradaMsg" class="alert alert-success text-center mt-4" role="alert" style="<?= empty($lancamento['baixado_por']) ? 'display: none;' : '' ?>">
                            <i class="fas fa-lock"></i> <strong>Lançamento Encerrado!</strong> Este lançamento foi baixado e finalizado.
                        </div>
                    </div>
                </form>

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
                                    <input type="text" id="valor_conferido" class="form-control" value="<?= $vl_conta ?>">
                                </div>
                                <div class="form-group mt-3">
                                    <label for="data_vencimento_conferida">Data de Vencimento:</label>
                                    <input type="date" id="data_vencimento_conferida" class="form-control" value="<?= date('Y-m-d', strtotime($lancamento['dt_vencimento'])) ?>">
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
                                <div class='row'>
                                        <div class="col-md-6 mb-2">
                                            <label for="valor_baixa">Valor da Baixa:</label>
                                            <input type="text" id="valor_baixa" class="form-control" placeholder="Digite o valor da baixa">
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label for="banco_baixa">Conta baixada:</label>
                                            <select name="id_conta" id="id_conta" class="form-control" required>
                                                <option value="">----Selecione----</option>
                                                <option value="1">Banco Inter</option>
                                                <option value="2">Banco Nubank</option>
                                            </select>
                                        </div>
                                </div>
                                <div class='row'>
                                    <div class="col-md-12 mb-2">
                                        <div class="form-group mt-3">
                                            <label for="data_baixa">Data da Baixa:</label>
                                            <input type="date" id="data_baixa" class="form-control" max="">
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

                <!-- Adicionar Anexos -->
                <div class="card mt-4">
                    <div class="card-header bg-secondary text-white">
                        <h3 class="mb-0">Adicionar Anexos</h3>
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?= base_url('financeiro/financeiroControllers/uploadAnexo/') . $lancamento['id_lan'] ?>" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="anexo" class="form-label">Selecione o arquivo</label>
                                <input type="file" class="form-control" name="documentos[]" id="documentos" multiple>
                            </div>
                            <button type="submit" class="btn btn-primary">Enviar Arquivo</button>
                        </form>
                    </div>
                </div>

                <!-- Exibir tabela de parcelas, se houver -->
                <?php if (!empty($parcelas)) : ?>
                <div class="card mt-4">
                    <div class="card-header bg-secondary text-white">
                        <h3 class="mb-0">Parcelas</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Número da Parcela</th>
                                    <th>Valor</th>
                                    <th>Data de Vencimento</th>
                                    <th>Conferido</th>
                                    <th>Aprovado</th>
                                    <th>Baixado</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($parcelas as $parcela) : ?>
                                    <tr>
                                        <td><?= $parcela->id_parcela ?></td>
                                        <td><?= $parcela->num_parcela ?></td>
                                        <td><?= number_format($parcela->vl_parcela, 2, ',', '.') ?></td>
                                        <td><?= date('d/m/Y', strtotime($parcela->dt_vencimento)) ?></td>
                                
                                        <!-- Conferido Column with Icon -->
                                        <td>
                                            <?php if ($parcela->is_conferido) : ?>
                                                <span style="color: green;">
                                                    <i class="fas fa-check-circle"></i> Sim
                                                </span>
                                            <?php else : ?>
                                                <span style="color: red;">
                                                    <i class="fas fa-times-circle"></i> Não
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                            
                                        <!-- Aprovado Column with Icon -->
                                        <td>
                                            <?php if ($parcela->is_aprovado) : ?>
                                                <span style="color: green;">
                                                    <i class="fas fa-check-circle"></i> Sim
                                                </span>
                                            <?php else : ?>
                                                <span style="color: red;">
                                                    <i class="fas fa-times-circle"></i> Não
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                            
                                        <!-- Baixado Column with Icon -->
                                        <td>
                                            <?php if ($parcela->dt_baixa) : ?>
                                                <span style="color: green;">
                                                    <i class="fas fa-check-circle"></i> Sim
                                                </span>
                                            <?php else : ?>
                                                <span style="color: red;">
                                                    <i class="fas fa-times-circle"></i> Não
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                            
                                        <td class="acao-col">
                                            <div class="btn-group" role="group">
                                                <a href="<?= base_url('financeiro/parcelasControllers/visualizarParcela/') . $parcela->id_parcela ?>" class="btn btn-warning">Visualizar</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Exibir anexos, se houver -->
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
                                    <a href="<?= base_url($anexo->caminho_arquivo) ?>" class="btn btn-sm btn-primary" target="_blank">Visualizar</a>
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

<script>
    $(document).ready(function() {

        // Obtém os valores do lançamento diretamente do PHP
        let isAprovado = <?= $lancamento['is_aprovado'] ? 'true' : 'false' ?>;
        let isConferido = <?= $lancamento['is_conferido'] ? 'true' : 'false' ?>;
        let isBaixado = <?= empty($lancamento['baixado_por']) ? 'false' : 'true' ?>;

        // Configuração inicial dos botões com base no estado do lançamento
        if (isAprovado) {
            $('#aprovarBtn').hide();
            $('#desaprovarBtn').css('display', 'inline-block');
            $('#baixarBtn').prop('disabled', false); // Habilita o botão Baixar
            $('#editarBtn').hide(); // Esconde o botão Editar
            $('#conferirBtn').hide(); // Esconde o botão Conferir
            $('#desconferirBtn').hide(); // Esconde o botão Desconferir
        } else {
            $('#aprovarBtn').css('display', 'inline-block');
            $('#desaprovarBtn').hide();
            $('#baixarBtn').prop('disabled', true); // Desabilita o botão Baixar

            if (isConferido) {
                $('#conferirBtn').hide(); // Esconde o botão Conferir
                $('#desconferirBtn').css('display', 'inline-block'); // Mostra o botão Desconferir
            } else {
                $('#conferirBtn').css('display', 'inline-block'); // Mostra o botão Conferir
                $('#desconferirBtn').hide(); // Esconde o botão Desconferir
            }
        }

        if (isBaixado) {
            $('#aprovarBtn').hide(); // Esconde Aprovar
            $('#desaprovarBtn').hide(); // Esconde Desaprovar
            $('#conferirBtn').hide(); // Esconde Conferir
            $('#desconferirBtn').hide(); // Esconde Desconferir
            $('#editarBtn').hide(); // Esconde Editar
            $('#baixarBtn').hide(); // Esconde Baixar
            $('#encerradaMsg').css('display', 'block'); // Mostra a mensagem de encerramento
        }

        // Máscara para o campo de valor
        $('#vl_parcela').mask('#.##0,00', {
            reverse: true
        });

        $('#data_baixa').on('click focus', function() {
            this.showPicker(); // Método para abrir o calendário
        });

        let lancamentoId = "<?= $lancamento['id_lan'] ?>";  // Pegando o ID do lançamento

        // Definir o valor máximo do campo de data como a data de hoje
        $('#data_baixa').attr('max', today);

        // Conferir Lançamento
        $('#confirmarConferenciaBtn').on('click', function() {
            let valorConferido = $('#valor_conferido').val();
            let dataVencimentoConferida = $('#data_vencimento_conferida').val();
        
            $('#vl_parcela').val(valorConferido);
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
                        $('#aprovarBtn').prop('disabled', false); // Habilita o botão Aprovar
                        $('#conferirBtn').hide();
                        $('#desconferirBtn').css('display', 'inline-block'); // Mostra o botão Desconferir
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
                        data: {
                            id_lancamento: lancamentoId
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    title: 'Desconferência Realizada!',
                                    text: response.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                });
                                $('#aprovarBtn').prop('disabled', true); // Desabilita o botão Aprovar
                                $('#desconferirBtn').hide();
                                $('#conferirBtn').css('display', 'inline-block'); // Mostra o botão Conferir
                                $('#editarBtn').prop('disabled', false).css('display', 'inline-block'); // Habilita o botão Editar
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
                        data: {
                            id_lancamento: lancamentoId
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire('Aprovado!', response.message, 'success');
                                $('#aprovarBtn').hide();
                                $('#editarBtn').hide();
                                $('#desconferirBtn').hide();
                                $('#conferirBtn').hide();
                                $('#desaprovarBtn').css('display', 'inline-block');
                                $('#baixarBtn').prop('disabled', false); // Habilita o botão Baixar
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
                        data: {
                            id_lancamento: lancamentoId
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire('Desaprovado!', response.message, 'success');
                                $('#desaprovarBtn').hide();
                                $('#editarBtn').show();
                                $('#desconferirBtn').show();
                                $('#aprovarBtn').css('display', 'inline-block');
                                $('#baixarBtn').prop('disabled', true); // Desabilita o botão Baixar
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
                            $('#baixarBtn').prop('disabled', true).text('Baixado');
                            $('#baixarBtn').hide();
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
