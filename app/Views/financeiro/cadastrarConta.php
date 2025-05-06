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
                <h2 class="mb-0">Cadastro de Contas</h2>
            </div>
            <div class="card-body">
                <form id="formCadastro" action="<?= base_url('financeiro/financeiroControllers/saveLancamentos') ?>" method="post" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <!-- Tipo da Conta -->
                        <div class="col-md-3">
                            <label for="tp_lancamento" class="form-label">Tipo da Conta</label>
                            <select name="tp_lancamento" id="tp_lancamento" class="form-control" required>
                                <option value="">Selecione</option>
                                <option value="R">Contas a Receber</option>
                                <option value="D">Contas a Pagar</option>
                            </select>
                        </div>

                        <!-- Número Doc -->
                        <div class="col-md-3">
                            <label for="num_doc" class="form-label">Número Doc.</label>
                            <input type="text" name="num_doc" id="num_doc" class="form-control">
                        </div>

                        <!-- NDI -->
                        <div class="col-md-3">
                            <label for="id_ndi" class="form-label">NDI:</label>
                            <select name="id_ndi" id="id_ndi" class="form-control">
                                <option value="">Selecione</option>
                                <?php foreach ($ndis as $ndi): ?>
                                    <option value="<?= $ndi->id_ndi ?>"><?= $ndi->id_ndi . ' - ' . $ndi->assunto ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Cliente -->
                        <div class="col-md-3">
                            <label for="id_pessoa" class="form-label">Cliente:</label>
                            <select name="id_pessoa" id="id_pessoa" class="form-control" required>
                                <option value="">Selecione</option>
                                <?php foreach ($pessoas as $pessoa) : ?>
                                    <option value="<?= $pessoa->id_pessoa ?>"><?= $pessoa->nm_pessoa ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <!-- Descrição -->
                        <div class="col-md-3">
                            <label for="descricao" class="form-label">Descrição:</label>
                            <input type="text" name="descricao" id="descricao" class="form-control" placeholder="Informe uma descrição do lançamento">
                        </div>

                        <!-- Valor da Conta -->
                        <div class="col-md-3">
                            <label for="vl_original" class="form-label">Valor da Conta:</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" name="vl_original" id="vl_original" class="form-control" required>
                            </div>
                        </div>

                        <!-- Data de Vencimento -->
                        <div class="col-md-3">
                            <label for="dt_vencimento" class="form-label">Data de Vencimento:</label>
                            <input type="text" name="dt_vencimento" id="dt_vencimento" class="form-control datepicker" required placeholder="dd/mm/yyyy">
                        </div>

                        <!-- Data da Competência -->
                        <div class="col-md-3">
                            <label for="dt_competencia" class="form-label">Data da Competência:</label>
                            <input type="text" name="dt_competencia" id="dt_competencia" class="form-control datepicker" required placeholder="dd/mm/yyyy">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <!-- Forma de Pagamento -->
                        <div class="col-md-3">
                            <label for="id_forma_pagto" class="form-label">Forma de Pagamento:</label>
                            <select name="id_forma_pagto" id="id_forma_pagto" class="form-control" required>
                                <option value="">Selecione</option>
                                <?php foreach ($formasPagamento as $forma) : ?>
                                    <option value="<?= $forma->id_formapagto ?>"><?= $forma->dsc_forma_pagto ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Tipo de Pagamento -->
                        <div class="col-md-3">
                            <label for="tp_conta" class="form-label">Tipo de Pagamento:</label>
                            <select name="tp_conta" id="tp_conta" class="form-control" required>
                                <option value="">Selecione</option>
                                <option value="A">À vista</option>
                                <option value="P">Parcelado</option>
                                <option value="R">Recorrente</option>
                            </select>
                        </div>

                        <!-- Campo para Parcelamento (aparece se selecionado "Parcelado") -->
                        <div class="col-md-3" id="parcelas_container" style="display: none;">
                            <label for="num_parcelas" class="form-label">Número de Parcelas:</label>
                            <select name="num_parcelas" id="num_parcelas" class="form-control">
                                <?php for ($i = 2; $i <= 24; $i++) : ?>
                                    <option value="<?= $i ?>"><?= $i ?> parcelas</option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <!-- Campo para Recorrência (aparece se selecionado "Recorrente") -->
                        <div class="col-md-3" id="lancamentos_container" style="display: none;">
                            <label for="num_lancamentos" class="form-label">Número de Lançamentos:</label>
                            <select name="num_lancamentos" id="num_lancamentos" class="form-control">
                                <?php for ($i = 2; $i <= 24; $i++) : ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <!-- Campo para exibir o valor de cada parcela (somente para parcelado) -->
                        <div class="col-md-3" id="valor_parcelas_container" style="display: none;">
                            <label for="val_parcela" class="form-label">Valor da Parcela:</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" name="val_parcela" id="val_parcela" class="form-control" disabled readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <!-- Complemento -->
                        <div class="col-md-12">
                            <label for="complemento" class="form-label">Complemento:</label>
                            <textarea name="complemento" id="complemento" class="form-control" rows="2"></textarea>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <!-- Upload de Documentos -->
                        <div class="col-md-12">
                            <label for="documentos" class="form-label">Anexar Documentos:</label>
                            <input type="file" name="documentos[]" id="documentos" class="form-control" multiple>
                        </div>
                    </div>

                    <!-- Campo Criado Por -->
                    <input type="hidden" name="criado_por" value="<?= session()->get('username') ?>">

                    <div class="mb-2 text-end">
                        <button type="submit" id="btnCadastrar" class="btn btn-success">Cadastrar</button>
                        <a href="<?= base_url('financeiro/financeiroControllers') ?>" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {

        $('#id_ndi').change(function() {
            var id_ndi = $(this).val();
            if (id_ndi !== "") {
                $.ajax({
                    url: '<?= base_url('pessoas/pessoasControllers/ajax/buscaClienteNDI') ?>',
                    type: 'POST',
                    data: { 
                        id_ndi: id_ndi 
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#id_pessoa').val(response.id_pessoa).trigger('change');
                        } else {
                            $('#id_pessoa').val('').trigger('change');
                            showCustomAlert(response.message, 'warning');
                        }
                    },
                    error: function() {
                        showCustomAlert('Erro ao buscar cliente. Tente novamente.', 'danger');
                    }
                });
            } else {
                $('#id_pessoa').val('').trigger('change');
            }
        });

        inicializarSelect2('#id_pessoa', 'Digite para pesquisar um cliente.');
        inicializarSelect2('#id_ndi', 'Digite para pesquisar um cliente.');
        inicializarSelect2('#id_forma_pagto', 'Digite para pesquisar uma forma de pagamento.');
        inicializarSelect2('#tp_conta', 'Selecione o tipo de pagamento.');

        $('#vl_original').mask('#.##0,00', { reverse: true });

        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-bottom-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        // Gerenciar exibição dos campos conforme o tipo de pagamento selecionado
        $('#tp_conta').change(function(){
            var tipo = $(this).val();
            if (tipo === 'P') {
                $('#parcelas_container').show();
                $('#lancamentos_container').hide();
                $('#valor_parcelas_container').show();
            } else if (tipo === 'R') {
                $('#lancamentos_container').show();
                $('#parcelas_container').hide();
                $('#valor_parcelas_container').hide(); // Esconde o cálculo para recorrente
            } else {
                $('#parcelas_container').hide();
                $('#lancamentos_container').hide();
                $('#valor_parcelas_container').hide();
            }
        });

        // Função para calcular e exibir o valor da parcela apenas para o pagamento parcelado
        function calcularValorParcela() {
            var tipo = $('#tp_conta').val();
            if (tipo !== 'P') {
                return;
            }
            let valorConta = parseFloat($('#vl_original').val().replace(/\./g, '').replace(',', '.')) || 0;
            let num = parseInt($('#num_parcelas').val()) || 1;
            if (valorConta === 0 || num === 0) {
                $('#val_parcela').val('');
            } else {
                let valorParcela = (valorConta / num).toFixed(2);
                $('#val_parcela').val(valorParcela.replace('.', ','));
            }
        }

        // Atualiza o valor sempre que o valor da conta ou número de parcelas mudar (apenas para parcelado)
        $('#vl_original, #num_parcelas').on('change keyup', calcularValorParcela);

        // Validação do formulário
        $('#btnCadastrar').on('click', function (e) {
            let formValid = true;
            toastr.clear();

            $('#formCadastro [required]').each(function () {
                if ($(this).val() === '' || $(this).val() === '0') {
                    formValid = false;
                    let label = $(this).closest('.mb-2, .mb-3').find('label').text();
                    toastr.error('O campo ' + label + ' é obrigatório.', 'Erro!');
                }
            });

            var tipo = $('#tp_conta').val();
            if (tipo === 'P') {
                if (!$('#num_parcelas').val() || $('#num_parcelas').val() === '0') {
                    formValid = false;
                    toastr.error('O campo Número de Parcelas é obrigatório.', 'Erro!');
                }
            } else if (tipo === 'R') {
                if (!$('#num_lancamentos').val() || $('#num_lancamentos').val() === '0') {
                    formValid = false;
                    toastr.error('O campo Número de Lançamentos é obrigatório.', 'Erro!');
                }
            }

            if (!formValid) {
                e.preventDefault();
            }
        });
    });
</script>
