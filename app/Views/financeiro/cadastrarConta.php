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

<div class="main">
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
                            <input type="text" name="num_doc" id="num_doc" class="form-control" required>
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

                        <!-- NDI -->
                        <div class="col-md-3">
                            <label for="id_ndi" class="form-label">NDI:</label>
                            <select name="id_ndi" id="id_ndi" class="form-control" required>
                                <option value="">Selecione</option>
                                <?php foreach ($ndis as $ndi): ?>
                                    <option value="<?= $ndi->id_ndi ?>"><?= $ndi->id_ndi . ' - ' . $ndi->assunto ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <!-- Valor da Conta -->
                        <div class="col-md-4">
                            <label for="vl_original" class="form-label">Valor da Conta:</label>
                            <input type="text" name="vl_original" id="vl_original" class="form-control" required>
                        </div>

                        <!-- Data de Vencimento -->
                        <div class="col-md-4">
                            <label for="dt_vencimento" class="form-label">Data de Vencimento:</label>
                            <input type="date" name="dt_vencimento" id="dt_vencimento" class="form-control" required>
                        </div>

                        <!-- Data da Competência -->
                        <div class="col-md-4">
                            <label for="dt_competencia" class="form-label">Data da Competência:</label>
                            <input type="date" name="dt_competencia" id="dt_competencia" class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <!-- Forma de Pagamento -->
                        <div class="col-md-4">
                            <label for="id_forma_pagto" class="form-label">Forma de Pagamento:</label>
                            <select name="id_forma_pagto" id="id_forma_pagto" class="form-control" required>
                                <option value="">Selecione</option>
                                <?php foreach ($formasPagamento as $forma) : ?>
                                    <option value="<?= $forma->id_formapagto ?>"><?= $forma->dsc_forma_pagto ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Número de Parcelas -->
                        <div class="col-md-4">
                            <label for="num_parcelas" class="form-label">Número de Parcelas:</label>
                            <select name="num_parcelas" id="num_parcelas" class="form-control" disabled>
                                <?php for ($i = 1; $i <= 12; $i++) : ?>
                                    <option value="<?= $i ?>"><?= $i ?> parcelas</option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <!-- Valor das Parcelas -->
                        <div class="col-md-4">
                            <label for="val_parcela" class="form-label">Valor das Parcelas:</label>
                            <input type="text" name="val_parcela" id="val_parcela" class="form-control" disabled readonly>
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

        $('#id_pessoa').select2({
            placeholder: 'Digite para pesquisar um cliente.',
            language: 'pt-BR',
            minimumInputLength: 0, // Se necessário para AJAX
            theme: 'bootstrap-5', // Aplica o tema do Bootstrap 5
            width: '100%' // Garante que use a largura total
        });

        $('#id_ndi').select2({
            placeholder: 'Digite para pesquisar um cliente.',
            language: 'pt-BR',
            minimumInputLength: 0, // Se necessário para AJAX
            theme: 'bootstrap-5', // Aplica o tema do Bootstrap 5
            width: '100%' // Garante que use a largura total
        });

        $('#id_forma_pagto').select2({
            placeholder: 'Digite para pesquisar uma forma de pagamento.',
            language: 'pt-BR',
            minimumInputLength: 0, // Se necessário para AJAX
            theme: 'bootstrap-5', // Aplica o tema do Bootstrap 5
            width: '100%' // Garante que use a largura total
        });

        // Máscara para o campo de valor
        $('#vl_original').mask('#.##0,00', { reverse: true });

        $('#dt_vencimento').on('click focus', function () {
            this.showPicker(); // Método para abrir o calendário
        });

        $('#dt_competencia').on('click focus', function () {
            this.showPicker(); // Método para abrir o calendário
        });
    
        // Configuração do Toastr
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
    
        // Mostrar ou ocultar a seção de parcelamento baseado na Forma de Pagamento
        $('#id_forma_pagto').on('change', function () {
            $('#num_parcelas').prop('disabled', false); // Habilitar o campo de número de parcelas
            $('#val_parcela').prop('disabled', false);  // Habilitar o campo de valor das parcelas
        });
    
        // Função para calcular o valor das parcelas
        $('#num_parcelas, #vl_original').on('change keyup', function () {
            let valorConta = parseFloat($('#vl_original').val().replace(/\./g, '').replace(',', '.')) || 0;
            let numeroParcelas = parseInt($('#num_parcelas').val()) || 1;
        
            // Se o valor da conta estiver vazio ou 0, zerar o campo de valor das parcelas
            if (valorConta === 0) {
                $('#val_parcela').val('');
            } else if (numeroParcelas > 0 && valorConta > 0) {
                let valorParcela = (valorConta / numeroParcelas).toFixed(2); // Calcula o valor das parcelas
                $('#val_parcela').val(valorParcela.replace('.', ',')); // Atualiza o campo de valor das parcelas
            }
        });
    
        $('#btnCadastrar').on('click', function () {
            let formValid = true;
            
            // Remover mensagens anteriores do Toastr
            toastr.clear();
        
            // Verificar todos os campos do formulário
            $('#formCadastro [required]').each(function () {
                if ($(this).val() === '' || $(this).val() === '0') {
                    formValid = false;
                    let label = $(this).closest('.mb-2, .mb-3').find('label').text();
                    toastr.error('O campo ' + label + ' é obrigatório.', 'Erro!');
                }
            });
        
            // Verificar se Cartão de Crédito está selecionado e o número de parcelas foi selecionado
            if ($('#num_parcelas').val() === '') {
                formValid = false;
                toastr.error('O campo Número de Parcelas é obrigatório.', 'Erro!');
            }
        
            // Se o formulário for válido, submeta-o
            if (formValid) {
                $('#formCadastro').submit();
            }
        });
    });
</script>
