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

<div>
    <div class="container-fluid mt-3">
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h2 class="mb-0">Editar Pessoa</h2>
            </div>
            <div class="card-body">
                <form id="formEditarPessoa" action="<?= base_url('pessoas/pessoasControllers/atualizar/' . $pessoas->id_pessoa) ?>" method="post">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="tp_pessoa" class="form-label">Tipo de Pessoa:</label>
                            <select name="tp_pessoa" id="tp_pessoa" class="form-control" disabled>
                                <option value="PF" <?= $pessoas->tp_pessoa == 'PF' ? 'selected' : '' ?>>Pessoa Física</option>
                                <option value="PJ" <?= $pessoas->tp_pessoa == 'PJ' ? 'selected' : '' ?>>Pessoa Jurídica</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="documento" class="form-label">CPF/CNPJ:</label>
                            <input type="text" name="documento" id="documento" class="form-control"
                                   value="<?= htmlspecialchars($pessoas->documento) ?>" disabled>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tipo de Cadastro:</label>
                            <div class="d-flex align-items-center">
                                <div class="form-check me-3">
                                    <input class="form-check-input styled-checkbox" type="checkbox" name="tp_cad_cli" value="1" id="tp_cad_cli"
                                        <?= $pessoas->tp_cad_cli == '1' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="tp_cad_cli">Cliente</label>
                                </div>
                                <div class="form-check me-3">
                                    <input class="form-check-input styled-checkbox" type="checkbox" name="tp_cad_for" value="1" id="tp_cad_for"
                                        <?= $pessoas->tp_cad_for == '1' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="tp_cad_for">Fornecedor</label>
                                </div>
                                <div class="form-check me-3">
                                    <input class="form-check-input styled-checkbox" type="checkbox" name="tp_cad_parte" value="1" id="tp_cad_parte"
                                        <?= $pessoas->tp_cad_parte == '1' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="tp_cad_parte">Parte</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input styled-checkbox" type="checkbox" name="tp_cad_adv" value="1" id="tp_cad_adv"
                                        <?= $pessoas->tp_cad_adv == '1' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="tp_cad_adv">Advogado</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <!-- Nome -->
                        <div class="col-md-4">
                            <label for="nm_pessoa" class="form-label">Nome:</label>
                            <input type="text" name="nm_pessoa" id="nm_pessoa" class="form-control"
                                   value="<?= htmlspecialchars($pessoas->nm_pessoa) ?>" required>
                        </div>

                        <!-- Telefone principal -->
                        <div class="col-md-4">
                            <label for="tel_1" class="form-label">Telefone principal:</label>
                            <input type="text" name="tel_1" id="tel_1" class="form-control"
                                   value="<?= htmlspecialchars($pessoas->tel_1) ?>" required>
                        </div>

                        <!-- Telefone secundário -->
                        <div class="col-md-4">
                            <label for="tel_2" class="form-label">Telefone secundário:</label>
                            <input type="text" name="tel_2" id="tel_2" class="form-control"
                                   value="<?= htmlspecialchars($pessoas->tel_2) ?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <!-- Email -->
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" name="email" id="email" class="form-control"
                                   value="<?= htmlspecialchars($pessoas->email) ?>" required>
                        </div>

                        <!-- CEP -->
                        <div class="col-md-3">
                            <label for="cep" class="form-label">CEP:</label>
                            <input type="text" name="cep" id="cep" class="form-control"
                                   value="<?= htmlspecialchars($enderecos->cep) ?>" required>
                        </div>

                        <!-- Número -->
                        <div class="col-md-3">
                            <label for="numero" class="form-label">Número:</label>
                            <input type="text" name="numero" id="numero" class="form-control"
                                   value="<?= htmlspecialchars($enderecos->numero) ?>" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <!-- Rua -->
                        <div class="col-md-6">
                            <label for="rua" class="form-label">Rua:</label>
                            <input type="text" name="rua" id="rua" class="form-control"
                                   value="<?= htmlspecialchars($enderecos->rua) ?>" required>
                        </div>

                        <!-- Complemento -->
                        <div class="col-md-6">
                            <label for="complemento" class="form-label">Complemento:</label>
                            <input type="text" name="complemento" id="complemento" class="form-control"
                                   value="<?= htmlspecialchars($enderecos->complemento) ?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <!-- Bairro -->
                        <div class="col-md-4">
                            <label for="bairro" class="form-label">Bairro:</label>
                            <input type="text" name="bairro" id="bairro" class="form-control"
                                   value="<?= htmlspecialchars($enderecos->bairro) ?>" required>
                        </div>

                        <!-- Estado (Select2 + Carregamento de cidades) -->
                        <div class="col-md-4">
                            <label for="estado" class="form-label">Estado:</label>
                            <select name="estado" id="estado" class="form-control" required>
                                <option value="">Selecione o Estado</option>
                                <?php foreach ($estados as $estado): ?>
                                    <option value="<?= $estado['sigla'] ?>"
                                        <?= $enderecos->estado == $estado['sigla'] ? 'selected' : '' ?>>
                                        <?= $estado['nome'] ?> (<?= $estado['sigla'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="cidade" class="form-label">Cidade:</label>
                            <select name="cidade" id="cidade" class="form-control" required>
                                <option value="">Selecione uma cidade</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="observacao" class="form-label">Observação:</label>
                            <textarea name="observacao" id="observacao" class="form-control" rows="2"><?= htmlspecialchars($pessoas->observacao) ?></textarea>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="button" id="btnSalvarPessoa" class="btn btn-primary">Salvar</button>
                        <a href="<?= base_url('pessoas/pessoasControllers/visualizarPessoas') ?>" class="btn btn-secondary">Voltar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

        inicializarSelect2('#estado', 'Selecione um estado.');
        inicializarSelect2('#cidade', 'Selecione uma cidade.');

        function carregarCidades(estadoUF, cidadeSelecionada = '') {
            if (estadoUF) {
                $('#cidade').empty().append('<option value="">Carregando...</option>');

                $.ajax({
                    url: `https://servicodados.ibge.gov.br/api/v1/localidades/estados/${estadoUF}/municipios`,
                    type: 'GET',
                    success: function (data) {
                        $('#cidade').empty().append('<option value="">Selecione uma cidade</option>');
                        data.forEach(function (municipio) {
                            let selected = (municipio.nome === cidadeSelecionada) ? 'selected' : '';
                            $('#cidade').append(`<option value="${municipio.nome}" ${selected}>${municipio.nome}</option>`);
                        });

                        // Força o Select2 a atualizar
                        $('#cidade').trigger('change.select2');
                    },
                    error: function () {
                        $('#cidade').empty().append('<option value="">Erro ao carregar cidades</option>');
                    }
                });
            } else {
                $('#cidade').empty().append('<option value="">Selecione uma cidade</option>');
            }
        }

        $('#estado').on('change', function () {
            carregarCidades($(this).val());
        });

        let ufInicial = $('#estado').val();
        let cidadeInicial = "<?= $enderecos->cidade ?>";
        if (ufInicial) {
            carregarCidades(ufInicial, cidadeInicial);
        }

        $('#cep').inputmask('99999-999');
        
        $('#tel_1, #tel_2').inputmask({
            mask: ['(99) 9999-9999', '(99) 99999-9999'],
            keepStatic: true
        });

        $('#documento').inputmask({
            mask: ['999.999.999-99', '99.999.999/9999-99'],
            keepStatic: true
        });

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
                            $("#estado").val(dados.uf).trigger('change');

                        } else {
                            limpa_formulario_cep();
                            exibirErro("CEP não encontrado.");
                        }
                    });
                } else {
                    limpa_formulario_cep();
                    exibirErro("Formato de CEP inválido.");
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

        function exibirErro(mensagem) {
            alert(mensagem);
        }

        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        $('#btnSalvarPessoa').on('click', function() {
            let formValido = true;

            $('#formEditarPessoa [required]').each(function() {
                if ($(this).val().trim() === '') {
                    formValido = false;
                    let label = $(this).closest('.mb-3').find('label').text();
                    exibirErro('O campo "' + label + '" é obrigatório.');
                }
            });

            const email = $('#email').val().trim();
            if (!isValidEmail(email)) {
                formValido = false;
                exibirErro('O endereço de e-mail fornecido é inválido.');
            }

            if (formValido) {
                $('#formEditarPessoa').submit();
            }
        });
    });
</script>
