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

<div class="main">
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
                            <input type="date" class="form-control" id="dt_abertura" name="dt_abertura" required>
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
                            <label for="id_promovente" class="form-label">Autor</label>
                            <select class="form-control" id="id_promovente" name="id_promovente">
                                <option value="">Selecione</option>
                                <?php foreach ($pessoasParte as $pessoasAutor) : ?>
                                    <option value="<?= $pessoasAutor->id_pessoa ?>"><?= $pessoasAutor->nm_pessoa ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Advogado do Autor -->
                        <div class="col-md-4">
                            <label for="id_advogado_autor" class="form-label">Advogado do Autor</label>
                            <select class="form-control" id="id_advogado_autor" name="id_advogado_autor" disabled>
                                <option value="">Selecione</option>
                                <?php foreach ($pessoasAdv as $pessoasAutorAdv) : ?>
                                    <option value="<?= $pessoasAutorAdv->id_pessoa ?>"><?= $pessoasAutorAdv->nm_pessoa ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Escritório do Advogado do Autor -->
                        <!-- <div class="col-md-4">
                            <label for="id_escritorio_autor" class="form-label">Escritório do Advogado do Autor</label>
                            <input type="text" class="form-control" id="escritorio_autor" name="escritorio_autor" placeholder="Digite o escritório." disabled>
                            <input type="hidden" id="id_escritorio_autor" name="id_escritorio_autor">
                        </div> -->
                    </div>
                    <div class="row mb-3">
                        <!-- Réu -->
                        <div class="col-md-4">
                            <label for="id_promovido" class="form-label">Réu</label>
                            <select class="form-control" id="id_promovido" name="id_promovido">
                                <option value="">Selecione</option>
                                <?php foreach ($pessoasParte as $pessoasReu) : ?>
                                    <option value="<?= $pessoasReu->id_pessoa ?>"><?= $pessoasReu->nm_pessoa ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Advogado do Réu -->
                        <div class="col-md-4">
                            <label for="id_advogado_reu" class="form-label">Advogado do Réu</label>
                            <select class="form-control" id="id_advogado_reu" name="id_advogado_reu" disabled>
                                <option value="">Selecione</option>
                                <?php foreach ($pessoasAdv as $pessoasReuAdv) : ?>
                                    <option value="<?= $pessoasReuAdv->id_pessoa ?>"><?= $pessoasReuAdv->nm_pessoa ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Escritório do Advogado do Réu -->
                        <!-- <div class="col-md-4">
                            <label for="id_escritorio_reu" class="form-label">Escritório do Advogado do Réu</label>
                            <input type="text" class="form-control" id="escritorio_reu" name="escritorio_reu" placeholder="Digite o escritório." disabled>
                            <input type="hidden" id="id_escritorio_reu" name="id_escritorio_reu">
                        </div> -->
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

        // Habilitar advogado do autor ao selecionar autor
        $('#id_promovente').change(function () {
            const isSelected = $(this).val() !== '';
            $('#id_advogado_autor').prop('disabled', !isSelected);
            if (!isSelected) {
                $('#id_advogado_autor').val('');
                $('#id_escritorio_autor').prop('disabled', true).val('');
            }
        });

        // Buscar escritório do autor via AJAX ao selecionar advogado
        // $('#id_advogado_autor').change(function () {
        //     const advogadoId = $(this).val();
        //     if (advogadoId) {
        //         $.ajax({
        //             url: `<?= base_url('juridico/ndiControllers/buscarEscritorio/') ?>${advogadoId}`,
        //             type: 'GET',
        //             dataType: 'json',
        //             success: function (data) {
        //                 if (data && data.escritorio) {
        //                     $('#id_escritorio_autor').val(data.id_endereco).prop('disabled', false);
        //                     $('#escritorio_autor').val(data.escritorio).prop('disabled', false);
        //                 } else {
        //                     $('#id_escritorio_autor').val('').prop('disabled', false);
        //                     alert('Nenhum escritório encontrado para o advogado selecionado.');
        //                 }
        //             },
        //             error: function () {
        //                 $('#id_escritorio_autor').val('').prop('disabled', false);
        //                 alert('Erro ao buscar o escritório do advogado. Tente novamente.');
        //             }
        //         });
        //     } else {
        //         $('#id_escritorio_autor').val('').prop('disabled', true);
        //     }
        // });

        // Habilitar advogado do réu ao selecionar réu
        $('#id_promovido').change(function () {
            const isSelected = $(this).val() !== '';
            $('#id_advogado_reu').prop('disabled', !isSelected);
            if (!isSelected) {
                $('#id_advogado_reu').val('');
                $('#id_escritorio_reu').prop('disabled', true).val('');
            }
        });

        // Buscar escritório do réu via AJAX ao selecionar advogado
        // $('#id_advogado_reu').change(function () {
        //     const advogadoId = $(this).val();
        //     if (advogadoId) {
        //         $.ajax({
        //             url: `<?= base_url('juridico/ndiControllers/buscarEscritorio/') ?>${advogadoId}`,
        //             type: 'GET',
        //             dataType: 'json',
        //             success: function (data) {

        //                 if (data && data.escritorio) {
        //                     $('#id_escritorio_reu').val(data.id_endereco).prop('disabled', false);
        //                     $('#escritorio_reu').val(data.escritorio).prop('disabled', false);
        //                 } else {
        //                     $('#id_escritorio_reu').val('').prop('disabled', false);
        //                     alert('Nenhum escritório encontrado para o advogado selecionado.');
        //                 }
        //             },
        //             error: function () {
        //                 $('#id_escritorio_reu').val('').prop('disabled', false);
        //                 alert('Erro ao buscar o escritório do advogado. Tente novamente.');
        //             }
        //         });
        //     } else {
        //         $('#id_escritorio_reu').val('').prop('disabled', true);
        //     }
        // });

        $('#dt_abertura').on('click focus', function() {
            this.showPicker(); // Método para abrir o calendário
        });

        $('#uf').select2({
            placeholder: 'Selecione o estado.',
            language: 'pt-BR',
            minimumInputLength: 0, // Se necessário para AJAX
            theme: 'bootstrap-5', // Aplica o tema do Bootstrap 5
            width: '100%' // Garante que use a largura total
        });

        $('#cidade').select2({
            placeholder: 'selecione uma cidade.',
            language: 'pt-BR',
            minimumInputLength: 0, // Se necessário para AJAX
            theme: 'bootstrap-5', // Aplica o tema do Bootstrap 5
            width: '100%' // Garante que use a largura total
        });

        $('#id_responsavel').select2({
            placeholder: 'selecione um responsável.',
            language: 'pt-BR',
            minimumInputLength: 0, // Se necessário para AJAX
            theme: 'bootstrap-5', // Aplica o tema do Bootstrap 5
            width: '100%' // Garante que use a largura total
        });

        $('#id_cliente').select2({
            placeholder: 'selecione um cliente.',
            language: 'pt-BR',
            minimumInputLength: 0, // Se necessário para AJAX
            theme: 'bootstrap-5', // Aplica o tema do Bootstrap 5
            width: '100%' // Garante que use a largura total
        });

        $('#id_servico').select2({
            placeholder: 'selecione um serviço.',
            language: 'pt-BR',
            minimumInputLength: 0, // Se necessário para AJAX
            theme: 'bootstrap-5', // Aplica o tema do Bootstrap 5
            width: '100%' // Garante que use a largura total
        });

        $('#id_promovente').select2({
            placeholder: 'selecione um autor.',
            language: 'pt-BR',
            minimumInputLength: 0, // Se necessário para AJAX
            theme: 'bootstrap-5', // Aplica o tema do Bootstrap 5
            width: '100%' // Garante que use a largura total
        });

        $('#id_promovido').select2({
            placeholder: 'selecione um reu.',
            language: 'pt-BR',
            minimumInputLength: 0, // Se necessário para AJAX
            theme: 'bootstrap-5', // Aplica o tema do Bootstrap 5
            width: '100%' // Garante que use a largura total
        });

        $('#id_advogado_autor').select2({
            placeholder: 'selecione um advogado.',
            language: 'pt-BR',
            minimumInputLength: 0, // Se necessário para AJAX
            theme: 'bootstrap-5', // Aplica o tema do Bootstrap 5
            width: '100%' // Garante que use a largura total
        });

        $('#id_advogado_reu').select2({
            placeholder: 'selecione um advogado.',
            language: 'pt-BR',
            minimumInputLength: 0, // Se necessário para AJAX
            theme: 'bootstrap-5', // Aplica o tema do Bootstrap 5
            width: '100%' // Garante que use a largura total
        });

        $('#uf').change(function () {
            var estadoUF = $(this).val();
            $('#cidade').empty().append('<option value="">Carregando.</option>');

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