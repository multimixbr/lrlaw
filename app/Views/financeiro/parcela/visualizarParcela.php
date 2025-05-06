<style>
    .readonly-field {
        background-color: #e9ecef;
        opacity: 1;
        pointer-events: none;
    }

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
        padding: 2px 4px; /* Ajuste o padding para deixar os botões menores */
        font-size: 12px;  /* Tamanho da fonte menor */
        margin-right: 2px; /* Pequeno espaçamento entre os botões */
    }

    td.acao-col {
        width: 150px; /* Definir uma largura fixa para a coluna dos botões */
        text-align: center; /* Centralizar os botões na célula */
    }

</style>

<div>
    <div class="container-fluid mt-3">
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h2 class="mb-0">Visualizando Parcela <?= $parcela->id_parcela ?></h2>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Criado por:</strong>
                            <p><?= htmlspecialchars($parcela->criado_por ?? 'N/A') ?></p>
                        </div>
                        <div class="col-md-3">
                            <strong>Conferido por:</strong>
                            <p><?= htmlspecialchars($parcela->conferido_por ?? 'N/A') ?></p>
                        </div>
                        <div class="col-md-3">
                            <strong>Aprovado por:</strong>
                            <p><?= htmlspecialchars($parcela->aprovado_por ?? 'N/A') ?></p>
                        </div>
                        <div class="col-md-3">
                            <strong>NDI:</strong>
                            <p><?= ($parcela->id_ndi == 0 || $parcela->id_ndi == null) ? 'N/A' : htmlspecialchars($parcela->ndi_assunto) ?></p>
                        </div>
                    </div>
                </div>
                <form>
                    <!-- Exibir informações da parcela -->
                    <div class="row">
                        <!-- Número da Parcela -->
                        <div class="col-md-4 mb-2">
                            <label for="num_parcela" class="form-label">Parcela de Número:</label>
                            <input type="text" name="num_parcela" id="num_parcela" class="form-control readonly-field" value="<?= $parcela->num_parcela ?>" readonly>
                        </div>

                        <!-- Valor da Parcela -->
                        <div class="col-md-4 mb-2">
                            <label for="vl_parcela" class="form-label">Valor da Parcela:</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" name="vl_parcela" id="vl_parcela" class="form-control readonly-field" value="<?= number_format($parcela->vl_parcela, 2, ',', '.') ?>" readonly>
                            </div>
                        </div>

                        <!-- Data de Vencimento -->
                        <div class="col-md-4 mb-2">
                            <label for="dt_vencimento" class="form-label">Data de Vencimento:</label>
                            <input type="text" name="dt_vencimento" id="dt_vencimento" class="form-control readonly-field datepicker" value="<?= date('d/m/Y', strtotime($parcela->dt_vencimento)) ?>" readonly>
                        </div>
                    </div>

                    <!-- Complemento -->
                    <div class="mb-2">
                        <label for="complemento" class="form-label">Complemento:</label>
                        <textarea name="complemento" id="complemento" class="form-control readonly-field" rows="2" readonly><?= htmlspecialchars($parcela->observacao) ?></textarea>
                    </div>

                    <div class="mb-2 text-end">
                        <a href="<?= base_url('financeiro/financeiroControllers/visualizar/') . $parcela->id_lan ?>" class="btn btn-secondary">Voltar</a>
                        <a href="<?= base_url('financeiro/parcelasControllers/editarParcela/') . $parcela->id_parcela ?>" id="editarBtn" class="btn btn-warning">Editar</a>
                        <button type="button" class="btn btn-info" id="conferirBtn" data-bs-toggle="modal" data-bs-target="#conferirModal" style="display: none;">Conferir</button>
                        <button type="button" class="btn btn-info" id="desconferirBtn" style="display: none;">Desconferir</button>
                        <button type="button" class="btn btn-success" id="aprovarBtn" style="display: none;">Aprovar</button>
                        <button type="button" class="btn btn-success" id="desaprovarBtn" style="display: none;">Desaprovar</button>
                        <button type="button" class="btn btn-primary" id="baixarBtn" data-bs-toggle="modal" data-bs-target="#baixarModal" <?= ($parcela->is_conferido == 1 && $parcela->is_aprovado == 1) ? '' : 'disabled' ?>>Baixar</button>
                    </div>

                    <div id="parcelaEncerradaMsg" style="<?= empty($parcela->baixado_por) ? 'display: none;' : '' ?>">
                        <div class="alert alert-success text-center mt-4" role="alert">
                            <i class="fas fa-lock"></i> <strong>Parcela Encerrada!</strong> Esta parcela foi baixada e finalizada.
                        </div>
                    </div>
                </form>

                <!-- Modal Conferir -->
                <div class="modal fade" id="conferirModal" tabindex="-1" aria-labelledby="conferirModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="conferirModalLabel">Conferir Parcela</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Confira e edite os dados da parcela, se necessário:</p>
                                <div class="form-group">
                                    <label for="valor_conferido">Valor Conferido:</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="text" name="valor_conferido" id="valor_conferido" class="form-control" value="<?= number_format($parcela->vl_parcela, 2, ',', '.') ?>">
                                    </div>
                                </div>
                                <div class="form-group mt-3">
                                    <label for="data_vencimento_conferida">Data de Vencimento:</label>
                                    <input type="text" id="data_vencimento_conferida" class="form-control readonly-field" readonly value="<?= date('d/m/Y', strtotime($parcela->dt_vencimento)) ?>">
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
                                <h5 class="modal-title" id="baixarModalLabel">Baixar Parcela</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class='row'>
                                    <div class="col-md-6 mb-2">
                                        <label for="valor_baixa">Valor da Baixa:</label>
                                        <div class="input-group">
                                            <span class="input-group-text">R$</span>
                                            <input type="text" id="valor_baixa" class="form-control" placeholder="Valor da baixa" value="<?= number_format($parcela->vl_parcela, 2, ',', '.') ?>">
                                        </div>
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

                <!-- Card: Documentos / Anexos -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h4 class="mb-0">Documentos / Anexos</h4>
                    </div>
                    <div class="card-body">
                        <!-- Form de Upload de Anexos -->
                        <form method="post" 
                              action="<?= base_url('financeiro/parcelasControllers/uploadAnexo/') . $parcela->id_parcela ?>"
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
                                            <i class="fas fa-eye"></i> Visualizar
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else : ?>
                            <div class="text-muted">Nenhum anexo disponível para este lançamento.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
        let isAprovado = <?= $parcela->is_aprovado ? 'true' : 'false' ?>;
        let isConferido = <?= $parcela->is_conferido ? 'true' : 'false' ?>;
        let isBaixado = <?= empty($parcela->baixado_por) ? 'false' : 'true' ?>;

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
                $('#desconferirBtn').show(); // Esconde o botão Desconferir
            } else {
                $('#aprovarBtn').prop('disabled', true); // Desabilita o botão Baixar
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
            $('#parcelaEncerradaMsg').css('display', 'block'); // Mostra a mensagem de encerramento
        }

        // Máscara para o campo de valor
        $('#vl_parcela').mask('#.##0,00', {
            reverse: true
        });

        let valorConferido = "<?= number_format($parcela->vl_parcela, 2, ',', '.') ?>";
        let dataVencimentoConferida = "<?= date('Y-m-d', strtotime($parcela->dt_vencimento)) ?>";
        let parcelaId = "<?= $parcela->id_parcela ?>";  // Pegando o ID da parcela

        // Conferir Parcela
        $('#confirmarConferenciaBtn').on('click', function() {
            valorConferido = $('#valor_conferido').val();
            dataVencimentoConferida = $('#data_vencimento_conferida').val();

            $('#vl_parcela').val(valorConferido);
            $('#dt_vencimento').val(dataVencimentoConferida);

            $('#conferirModal').modal('hide');

            $.ajax({
                url: '<?= base_url('financeiro/parcelasControllers/conferirParcela') ?>', 
                type: 'POST',
                dataType: 'json',
                data: {
                    id_parcela: parcelaId,
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
                        text: 'Erro ao conferir a parcela.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

        // Desconferir Parcela
        $('#desconferirBtn').on('click', function() {
            Swal.fire({
                title: 'Deseja desconferir a parcela?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, desconferir',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('financeiro/parcelasControllers/desconferirParcela') ?>', 
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            id_parcela: parcelaId
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    title: 'Desconferência Realizada!',
                                    text: response.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                });
                                $('#desconferirBtn').hide(); // Esconde o botão Desconferir
                                $('#conferirBtn').css('display', 'inline-block'); // Mostra o botão Conferir
                                $('#aprovarBtn').prop('disabled', true); // Desabilita o botão Aprovar
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
                                text: 'Erro ao desconferir a parcela.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        });

        // Aprovar Parcela
        $('#aprovarBtn').on('click', function() {
            Swal.fire({
                title: 'Deseja aprovar a parcela?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, aprovar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('financeiro/parcelasControllers/aprovarParcela') ?>', 
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            id_parcela: parcelaId
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
                                text: 'Erro ao aprovar a parcela.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        });

        // Desaprovar Parcela
        $('#desaprovarBtn').on('click', function() {
            Swal.fire({
                title: 'Deseja desaprovar a parcela?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, desaprovar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('financeiro/parcelasControllers/desaprovarParcela') ?>', 
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            id_parcela: parcelaId
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
                                text: 'Erro ao desaprovar a parcela.',
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
            let bancoBaixa = $('#id_conta').val();

            if (valorBaixa && dataBaixa) {
                $.ajax({
                    url: '<?= base_url('financeiro/parcelasControllers/baixarParcela') ?>', 
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        id_parcela: parcelaId,
                        valor_baixa: valorBaixa,
                        data_baixa: dataBaixa,
                        banco_baixa: bancoBaixa
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
                            $('#parcelaEncerradaMsg').show();
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
                            text: 'Erro ao realizar a baixa da parcela.',
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