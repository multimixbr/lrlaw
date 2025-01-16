<style>
    .styled-checkbox {
        width: 20px;
        height: 20px;
    }

    .form-check-label {
        font-weight: bold;
        margin-left: 5px;
    }
</style>

<div class="main">
    <div class="container-fluid mt-3">
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h2 class="mb-0">Cadastro de Pessoas</h2>
            </div>
            <div class="card-body">
                <form id="formCadastroPessoa" action="<?= base_url('pessoas/pessoasControllers/savePessoas') ?>" method="post">
                    <div class="row mb-3">
                        <!-- Tipo de Pessoa -->
                        <div class="col-md-4">
                            <label for="tp_pessoa" class="form-label">Tipo de Pessoa:</label>
                            <select name="tp_pessoa" id="tp_pessoa" class="form-control" required>
                                <option value="">Selecione</option>
                                <option value="PF">Pessoa Física</option>
                                <option value="PJ">Pessoa Jurídica</option>
                            </select>
                        </div>

                        <!-- Documento (CPF/CNPJ) -->
                        <div class="col-md-4">
                            <label for="documento" class="form-label">CPF/CNPJ:</label>
                            <input type="text" name="documento" id="documento" class="form-control" required disabled>
                        </div>

                        <!-- Tipo de Cadastro -->
                        <div class="col-md-4">
                            <label class="form-label">Tipo de Cadastro:</label>
                            <div class="d-flex align-items-center">
                                <div class="form-check me-3">
                                    <input class="form-check-input styled-checkbox" type="checkbox" name="tp_cad_cli" value="1" id="tp_cad_cli">
                                    <label class="form-check-label" for="tp_cad_cli">Cliente</label>
                                </div>
                                <div class="form-check me-3">
                                    <input class="form-check-input styled-checkbox" type="checkbox" name="tp_cad_for" value="1" id="tp_cad_for">
                                    <label class="form-check-label" for="tp_cad_for">Fornecedor</label>
                                </div>
                                <div class="form-check me-3">
                                    <input class="form-check-input styled-checkbox" type="checkbox" name="tp_cad_parte" value="1" id="tp_cad_parte">
                                    <label class="form-check-label" for="tp_cad_parte">Parte</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input styled-checkbox" type="checkbox" name="tp_cad_adv" value="1" id="tp_cad_adv">
                                    <label class="form-check-label" for="tp_cad_adv">Advogado</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <!-- Nome -->
                        <div class="col-md-4">
                            <label for="nm_pessoa" class="form-label">Nome:</label>
                            <input type="text" name="nm_pessoa" id="nm_pessoa" class="form-control" required>
                        </div>

                        <!-- Telefone 1 -->
                        <div class="col-md-4">
                            <label for="tel_1" class="form-label">Telefone principal:</label>
                            <input type="text" name="tel_1" id="tel_1" class="form-control" required>
                        </div>

                        <!-- Telefone 2 -->
                        <div class="col-md-4">
                            <label for="tel_2" class="form-label">Telefone secundário:</label>
                            <input type="text" name="tel_2" id="tel_2" class="form-control">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <!-- Email -->
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>

                        <!-- CEP -->
                        <div class="col-md-3">
                            <label for="cep" class="form-label">CEP:</label>
                            <input type="text" name="cep" id="cep" class="form-control" required>
                        </div>

                        <!-- Número -->
                        <div class="col-md-3">
                            <label for="numero" class="form-label">Número:</label>
                            <input type="text" name="numero" id="numero" class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <!-- Rua -->
                        <div class="col-md-6">
                            <label for="rua" class="form-label">Rua:</label>
                            <input type="text" name="rua" id="rua" class="form-control" required>
                        </div>

                        <!-- Complemento -->
                        <div class="col-md-6">
                            <label for="complemento" class="form-label">Complemento:</label>
                            <input type="text" name="complemento" id="complemento" class="form-control">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <!-- Bairro -->
                        <div class="col-md-4">
                            <label for="bairro" class="form-label">Bairro:</label>
                            <input type="text" name="bairro" id="bairro" class="form-control" required>
                        </div>

                        <!-- Estado -->
                        <div class="col-md-4">
                            <label for="estado" class="form-label">Estado:</label>
                            <select name="estado" id="estado" class="form-control" required>
                                <option value="">Selecione o Estado</option>
                                <?php foreach ($estados as $estado): ?>
                                    <option value="<?= $estado['sigla'] ?>"><?= $estado['nome'] ?> (<?= $estado['sigla'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Cidade -->
                        <div class="col-md-4">
                            <label for="cidade" class="form-label">Cidade:</label>
                            <select name="cidade" id="cidade" class="form-control" required>
                                <option value="">Selecione uma cidade</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <!-- Observação -->
                        <div class="col-md-12">
                            <label for="observacao" class="form-label">Observação:</label>
                            <textarea name="observacao" id="observacao" class="form-control" rows="2"></textarea>
                        </div>
                    </div>

                    <!-- Campo Criado Por -->
                    <input type="hidden" name="criado_por" value="<?= session()->get('username') ?>">

                    <div class="mb-2 text-end">
                        <button type="submit" id="btnCadastrarPessoa" class="btn btn-success">Cadastrar</button>
                        <a href="<?= base_url('pessoas/pessoasControllers') ?>" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
    
<script>
    $(document).ready(function() {

        $('#estado').select2({
            placeholder: 'Digite para pesquisar um estado.',
            language: 'pt-BR',
            minimumInputLength: 0, // Se necessário para AJAX
            theme: 'bootstrap-5', // Aplica o tema do Bootstrap 5
            width: '100%' // Garante que use a largura total
        });

        $('#cidade').select2({
            placeholder: 'Digite para pesquisar uma cidade.',
            language: 'pt-BR',
            minimumInputLength: 0, // Se necessário para AJAX
            theme: 'bootstrap-5', // Aplica o tema do Bootstrap 5
            width: '100%' // Garante que use a largura total
        });

        $('#estado').change(function () {
            var estadoUF = $(this).val();
            $('#cidade').empty().append('<option value="">Carregando...</option>');

            if (estadoUF) {
                $.ajax({
                    url: `https://servicodados.ibge.gov.br/api/v1/localidades/estados/${estadoUF}/municipios`,
                    type: 'GET',
                    success: function (data) {
                        $('#cidade').empty().append('<option value="">Selecione uma cidade</option>');
                        data.forEach(function (municipio) {
                            $('#cidade').append(`<option value="${municipio.nome}">${municipio.nome}</option>`);
                        });
                    },
                    error: function () {
                        $('#cidade').empty().append('<option value="">Erro ao carregar cidades</option>');
                    }
                });
            } else {
                $('#cidade').empty().append('<option value="">Selecione uma cidade</option>');
            }
        });

        // Máscara para CEP
        $('#cep').inputmask('99999-999');
        
        // Máscaras para telefones
        $('#tel_1, #tel_2').inputmask({
            mask: ['(99) 9999-9999', '(99) 99999-9999'],
            keepStatic: true
        });

        // Inicialmente, o campo 'documento' está sem máscara
        $('#documento').inputmask('remove');

        // Quando o tipo de pessoa mudar
        $('#tp_pessoa').change(function () {
            var tipoPessoa = $(this).val();
            $('#documento').val(''); // Limpa o campo documento
            $('#documento').prop('disabled', false);
            if (tipoPessoa == 'PF') {
                $('#documento').inputmask('999.999.999-99');
            } else if (tipoPessoa == 'PJ') {
                $('#documento').inputmask('99.999.999/9999-99');
            } else {
                $('#documento').inputmask('remove');
            }
        });

        // Chamar a função quando o campo documento perder o foco
        $('#documento').blur(function () {
            attemptFetchDocumentoData();
        });

        // Função para tentar buscar dados do documento
        function attemptFetchDocumentoData() {
            var documento = $('#documento').val().replace(/\D/g, '');
            var tipoPessoa = $('#tp_pessoa').val();

            if (documento !== "" && tipoPessoa !== "") {
                if (tipoPessoa === "PJ" && documento.length === 14) {
                    fetchCNPJData(documento);
                } else if (tipoPessoa === "PF" && documento.length === 11) {
                    // Valida CPF antes de prosseguir
                    if (!isValidCPF(documento)) {
                        toastr.error("CPF inválido.", "Erro!");
                        return; // Interrompe o fluxo caso o CPF não seja válido
                    }
                    // Aqui você poderia implementar alguma lógica adicional caso deseje.
                    // Por exemplo: fetchCPFData(documento); se você tiver um endpoint para isso.
                } else {
                    toastr.error("Documento inválido para o tipo de pessoa selecionado.", "Erro!");
                }
            }
        }

        // Função para validar CPF
        function isValidCPF(cpf) {
            // Elimina CPFs inválidos conhecidos
            if (cpf.length !== 11 || 
                cpf === "00000000000" || 
                cpf === "11111111111" || 
                cpf === "22222222222" || 
                cpf === "33333333333" ||
                cpf === "44444444444" || 
                cpf === "55555555555" || 
                cpf === "66666666666" || 
                cpf === "77777777777" ||
                cpf === "88888888888" || 
                cpf === "99999999999") {
                    return false;
            }

            let soma = 0;
            let resto;

            for (let i = 1; i <= 9; i++) {
                soma = soma + parseInt(cpf.substring(i-1, i)) * (11 - i);
            }
            resto = (soma * 10) % 11;

            if ((resto === 10) || (resto === 11)) resto = 0;
            if (resto !== parseInt(cpf.substring(9, 10))) return false;

            soma = 0;
            for (let i = 1; i <= 10; i++) {
                soma = soma + parseInt(cpf.substring(i-1, i)) * (12 - i);
            }
            resto = (soma * 10) % 11;

            if ((resto === 10) || (resto === 11)) resto = 0;
            if (resto !== parseInt(cpf.substring(10, 11))) return false;
            
            return true;
        }

        // Função para buscar dados do CNPJ
        function fetchCNPJData(cnpj) {
            $.ajax({
                url: `https://www.receitaws.com.br/v1/cnpj/${cnpj}`,
                type: 'GET',
                dataType: 'jsonp',
                success: function (data) {
                    if (data.status === "OK") {
                        $("#nm_pessoa").val(data.nome);
                        $("#email").val(data.email);
                        $("#rua").val(data.logradouro);
                        $("#bairro").val(data.bairro);
                        $("#cidade").val(data.municipio);
                        $("#estado").val(data.uf).trigger('change');;
                        $("#numero").val(data.numero);
                        $("#cep").val(data.cep.replace(/\D/g, ''));
                    } else {
                        toastr.error("CNPJ não encontrado ou inválido.", "Erro!");
                    }
                },
                error: function () {
                    toastr.error("Erro ao consultar CNPJ. Tente novamente.", "Erro!");
                }
            });
        }

        // Consulta ao ViaCEP
        $('#cep').blur(function () {
            var cep = $(this).val().replace(/\D/g, '');
            if (cep !== "") {
                var validacep = /^[0-9]{8}$/;

                if (validacep.test(cep)) {
                    $("#rua").val("...");
                    $("#bairro").val("...");
                    $("#cidade").val("...");
                    $("#estado").val("...");

                    $.getJSON("https://viacep.com.br/ws/" + cep + "/json/?callback=?", function (dados) {
                        if (!("erro" in dados)) {
                            $("#rua").val(dados.logradouro);
                            $("#bairro").val(dados.bairro);
                            $("#cidade").val(dados.localidade);
                            // Atualiza o estado no Select2
                            $("#estado").val(dados.uf).trigger('change');
                        } else {
                            limpa_formulario_cep();
                            toastr.error("CEP não encontrado.", "Erro!");
                        }
                    });
                } else {
                    limpa_formulario_cep();
                    toastr.error("Formato de CEP inválido.", "Erro!");
                }
            } else {
                limpa_formulario_cep();
            }
        });

        function limpa_formulario_cep() {
            $("#rua").val("");
            $("#bairro").val("");
            $("#cidade").val("");
            $("#estado").val("");
        }

        // Validação de e-mail no front-end
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        // Validação e submissão do formulário
        $('#btnCadastrarPessoa').on('click', function() {
            let formValid = true;

            // Verificar todos os campos obrigatórios
            $('#formCadastroPessoa [required]').each(function() {
                if ($(this).val() === '') {
                    formValid = false;
                    let label = $(this).closest('.mb-2').find('label').text();
                    toastr.error('O campo ' + label + ' é obrigatório.', 'Erro!');
                }
            });

            // Verificar se o e-mail é válido
            const email = $('#email').val();
            if (!isValidEmail(email)) {
                formValid = false;
                toastr.error('O endereço de e-mail fornecido é inválido.', 'Erro!');
            }

            // Se o formulário for válido, submeta-o
            if (formValid) {
                $('#formCadastroPessoa').submit();
            }
        });
    });
</script>