<!-- Estilo customizado para aumentar o tamanho do switch -->
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
                <h2 class="mb-0">Cadastro de NDI</h2>
            </div>
            <div class="card-body">
                <form id="formCadastroNDI" action="<?= base_url('juridico/ndiControllers/saveNDI') ?>" method="post">
                    <div class="row mb-3">
                        <!-- Assunto -->
                        <div class="col-md-4">
                            <label for="assunto" class="form-label">Assunto</label>
                            <input type="text" class="form-control" id="assunto" name="assunto" placeholder="Digite o assunto." required>
                        </div>
                        <!-- Nº Processo -->
                        <div class="col-md-4">
                            <label for="processo" class="form-label">Nº Processo</label>
                            <input type="text" class="form-control" id="processo" name="processo" placeholder="Digite o número do processo." required>
                        </div>
                        <!-- Responsável -->
                        <div class="col-md-4">
                            <label for="id_responsavel" class="form-label">Responsável</label>
                            <select class="form-control" id="id_responsavel" name="id_responsavel" required>
                                <option value="">Selecione o responsável</option>
                                <?php foreach ($usersAtivos as $users) : ?>
                                    <option value="<?= $users->id_usuario ?>"><?= $users->username ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <!-- Cliente -->
                        <div class="col-md-4">
                            <label for="id_cliente" class="form-label">Cliente</label>
                            <select class="form-control" id="id_cliente" name="id_cliente" required>
                                <option value="">Selecione o cliente</option>
                                <?php foreach ($clientes as $cliente) : ?>
                                    <option value="<?= $cliente->id_pessoa ?>"><?= $cliente->nm_pessoa ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- UF -->
                        <div class="col-md-2">
                            <label for="uf" class="form-label">UF</label>
                            <select class="form-control" id="uf" name="uf">
                                <option value="">Selecione</option>
                                <?php foreach ($estados as $estado) : ?>
                                    <option value="<?= $estado['sigla'] ?>"><?= $estado['nome'] ?> (<?= $estado['sigla'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Cidade -->
                        <div class="col-md-2">
                            <label for="cidade" class="form-label">Cidade</label>
                            <select name="cidade" id="cidade" class="form-control" required>
                                <option value="">Selecione uma cidade</option>
                            </select>
                        </div>
                        <!-- Serviços -->
                        <div class="col-md-4">
                            <label for="id_servico" class="form-label">Serviços</label>
                            <select class="form-control" id="id_servico" name="id_servico">
                                <option value="">Selecione</option>
                                <?php foreach ($servicos as $servico) : ?>
                                    <option value="<?= $servico->id_servico ?>"><?= $servico->nm_servico ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <!-- Data de Abertura -->
                        <div class="col-md-4">
                            <label for="dt_abertura" class="form-label">Data de Abertura</label>
                            <input type="text" class="form-control datepicker" id="dt_abertura" name="dt_abertura" required>
                        </div>
                        <!-- Complexidade -->
                        <div class="col-md-4">
                            <label for="complexidade" class="form-label">Complexidade</label>
                            <select class="form-control" id="complexidade" name="complexidade">
                                <option value="">Selecione</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                        <!-- Prioridade -->
                        <div class="col-md-4">
                            <label for="prioridade" class="form-label">Prioridade</label>
                            <select class="form-control" id="prioridade" name="prioridade">
                                <option value="N">Normal</option>
                                <option value="U">Urgente</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <!-- Autor -->
                        <div class="col-md-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="id_promovente" class="form-label">
                                    Autor
                                </label>
                                <a href="#" id="cadastrarParte" class="cadastrarParte" target="_blank" style="font-size: 0.9rem; color: #007bff; text-decoration: none;">
                                    (Cadastrar novo Autor)
                                </a>
                            </div>
                            <select class="form-control mt-1" id="id_promovente" name="id_promovente">
                                <option value="">Selecione</option>
                                <?php foreach ($pessoasParte as $pessoasAutor) : ?>
                                    <option value="<?= $pessoasAutor->id_pessoa ?>"><?= $pessoasAutor->nm_pessoa ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Advogado do Autor -->
                        <div class="col-md-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="id_advogado_autor" class="form-label">
                                    Advogado do Autor
                                </label>
                                <a href="#" id="cadastrarAdv" target="_blank" style="font-size: 0.9rem; color: #007bff; text-decoration: none;">
                                    (Cadastrar novo Advogado)
                                </a>
                            </div>
                            <select class="form-control mt-1" id="id_advogado_autor" name="id_advogado_autor" disabled>
                                <option value="">Selecione</option>
                                <?php foreach ($pessoasAdv as $pessoasAutorAdv) : ?>
                                    <option value="<?= $pessoasAutorAdv->id_pessoa ?>"><?= $pessoasAutorAdv->nm_pessoa ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <!-- Réu -->
                        <div class="col-md-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="id_promovido" class="form-label">
                                    Réu
                                </label>
                                <a href="#" id="cadastrarParte" class="cadastrarParte" target="_blank" style="font-size: 0.9rem; color: #007bff; text-decoration: none;">
                                    (Cadastrar novo Réu)
                                </a>
                            </div>
                            <select class="form-control mt-1" id="id_promovido" name="id_promovido">
                                <option value="">Selecione</option>
                                <?php foreach ($pessoasParte as $pessoasReu) : ?>
                                    <option value="<?= $pessoasReu->id_pessoa ?>"><?= $pessoasReu->nm_pessoa ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Advogado do Réu -->
                        <div class="col-md-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="id_advogado_reu" class="form-label">
                                    Advogado do Réu
                                </label>
                                <a href="#" id="cadastrarAdv" target="_blank" style="font-size: 0.9rem; color: #007bff; text-decoration: none;">
                                    (Cadastrar novo Advogado)
                                </a>
                            </div>
                            <select class="form-control mt-1" id="id_advogado_reu" name="id_advogado_reu" disabled>
                                <option value="">Selecione</option>
                                <?php foreach ($pessoasAdv as $pessoasReuAdv) : ?>
                                    <option value="<?= $pessoasReuAdv->id_pessoa ?>"><?= $pessoasReuAdv->nm_pessoa ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-2 text-end">
                        <button type="submit" class="btn btn-success">Cadastrar</button>
                        <a href="<?= base_url('juridico/ndiControllers') ?>" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

        function redirecionarCadastro(tipoPessoa) {
            var baseUrl = "<?= base_url('pessoas/pessoasControllers/novo') ?>";

            window.open(baseUrl + "?tipo=" + encodeURIComponent(tipoPessoa), '_blank');
        }

        $(".cadastrarParte").on("click", function(e) {
            e.preventDefault();
            redirecionarCadastro("parte");
        });

        $("#cadastrarAdv").on("click", function(e) {
            e.preventDefault();
            redirecionarCadastro("adv");
        });

        $('#processo').mask('0000000-00.0000.0.00.0000');

        $('#id_promovente').change(function () {
            const isSelected = $(this).val() !== '';
            $('#id_advogado_autor').prop('disabled', !isSelected);
            if (!isSelected) {
                $('#id_advogado_autor').val('').trigger('change');
            }

            let autor = $(this).val();
            let reu = $('#id_promovido').val();
            if (autor && reu && autor === reu) {
                showCustomAlert('Não é possível cadastrar o mesmo autor e réu!', 'danger');
                $('#id_promovente').val('').trigger('change');
            }
        });

        // Habilitar advogado do réu ao selecionar réu
        $('#id_promovido').change(function () {
            const isSelected = $(this).val() !== '';
            $('#id_advogado_reu').prop('disabled', !isSelected);
            if (!isSelected) {
                $('#id_advogado_reu').val('').trigger('change');
            }

            // VERIFICA SE RÉU E AUTOR SÃO IGUAIS
            let reu = $(this).val();
            let autor = $('#id_promovente').val();
            if (reu && autor && reu === autor) {
                showCustomAlert('Não é possível cadastrar o mesmo autor e réu!', 'danger');
                // Limpa o campo de Réu para evitar duplicidade
                $('#id_promovido').val('').trigger('change');
            }
        });

        inicializarSelect2('#uf', 'Selecione o estado.');
        inicializarSelect2('#cidade', 'Selecione uma cidade.');
        inicializarSelect2('#id_responsavel', 'Selecione um responsável.');
        inicializarSelect2('#id_cliente', 'Selecione um cliente.');
        inicializarSelect2('#id_servico', 'Selecione um serviço.');
        inicializarSelect2('#id_promovente', 'Selecione um autor.');
        inicializarSelect2('#id_promovido', 'Selecione um réu.');
        inicializarSelect2('#id_advogado_autor', 'Selecione um advogado.');
        inicializarSelect2('#id_advogado_reu', 'Selecione um advogado.');

        // Carregar cidades ao selecionar UF (usando API do IBGE)
        $('#uf').change(function () {
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
    });
</script>