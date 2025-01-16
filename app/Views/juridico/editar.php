<div class="main">
    <div class="container-fluid mt-3">
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h2 class="mb-0">Editar NDI</h2>
            </div>
            <div class="card-body">
                <form id="formEditarNDI" action="<?= base_url('juridico/ndiControllers/updateNDI/' . $ndi->id_ndi) ?>" method="post">
                    <div class="row mb-3">
                        <!-- Assunto -->
                        <div class="col-md-4">
                            <label for="assunto" class="form-label">Assunto</label>
                            <input type="text" class="form-control" id="assunto" name="assunto" placeholder="Digite o assunto." value="<?= $ndi->assunto ?>" required>
                        </div>
                        <!-- Nº Processo -->
                        <div class="col-md-4">
                            <label for="processo" class="form-label">Nº Processo</label>
                            <input type="text" class="form-control" id="processo" name="processo" placeholder="Digite o número do processo." value="<?= $ndi->processo ?>" required>
                        </div>
                        <!-- Responsável -->
                        <div class="col-md-4">
                            <label for="id_responsavel" class="form-label">Responsável</label>
                            <select class="form-control" id="id_responsavel" name="id_responsavel" required>
                                <option value="">Selecione o responsável</option>
                                <?php foreach ($usersAtivos as $users) : ?>
                                    <option value="<?= $users->id_usuario ?>" <?= $ndi->id_responsavel == $users->id_usuario ? 'selected' : '' ?>>
                                        <?= $users->username ?>
                                    </option>
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
                                    <option value="<?= $cliente->id_pessoa ?>" <?= $ndi->id_cliente == $cliente->id_pessoa ? 'selected' : '' ?>>
                                        <?= $cliente->nm_pessoa ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- UF -->
                        <div class="col-md-2">
                            <label for="uf" class="form-label">UF</label>
                            <select class="form-control" id="uf" name="uf">
                                <option value="">Selecione</option>
                                <?php foreach ($estados as $estado) : ?>
                                    <option value="<?= $estado['sigla'] ?>" <?= $ndi->uf == $estado['sigla'] ? 'selected' : '' ?>>
                                        <?= $estado['nome'] ?> (<?= $estado['sigla'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Cidade -->
                        <div class="col-md-2">
                            <label for="cidade" class="form-label">Cidade</label>
                            <select name="cidade" id="cidade" class="form-control" required>
                                <option value="<?= $ndi->cidade ?>"><?= $ndi->cidade ?></option>
                            </select>
                        </div>
                        <!-- Serviços -->
                        <div class="col-md-4">
                            <label for="id_servico" class="form-label">Serviços</label>
                            <select class="form-control" id="id_servico" name="id_servico">
                                <option value="">Selecione</option>
                                <?php foreach ($servicos as $servico) : ?>
                                    <option value="<?= $servico->id_servico ?>" <?= $ndi->id_servico == $servico->id_servico ? 'selected' : '' ?>>
                                        <?= $servico->nm_servico ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <!-- Data de Abertura -->
                        <div class="col-md-4">
                            <label for="dt_abertura" class="form-label">Data de Abertura</label>
                            <input type="date" class="form-control" id="dt_abertura" name="dt_abertura" value="<?= $ndi->dt_abertura ?>" required>
                        </div>
                        <!-- Complexidade -->
                        <div class="col-md-4">
                            <label for="complexidade" class="form-label">Complexidade</label>
                            <select class="form-control" id="complexidade" name="complexidade">
                                <option value="">Selecione</option>
                                <?php for ($i = 1; $i <= 5; $i++) : ?>
                                    <option value="<?= $i ?>" <?= @$ndi->complexidade == $i ? 'selected' : '' ?>><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <!-- Prioridade -->
                        <div class="col-md-4">
                            <label for="prioridade" class="form-label">Prioridade</label>
                            <select class="form-control" id="prioridade" name="prioridade">
                                <option value="N" <?= $ndi->prioridade == 'N' ? 'selected' : '' ?>>Normal</option>
                                <option value="U" <?= $ndi->prioridade == 'U' ? 'selected' : '' ?>>Urgente</option>
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
                                    <option value="<?= $pessoasAutor->id_pessoa ?>" <?= @$ndi->id_promovente == $pessoasAutor->id_pessoa ? 'selected' : '' ?>><?= $pessoasAutor->nm_pessoa ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Advogado do Autor -->
                        <div class="col-md-4">
                            <label for="id_advogado_autor" class="form-label">Advogado do Autor</label>
                            <select class="form-control" id="id_advogado_autor" name="id_advogado_autor" disabled>
                                <option value="">Selecione</option>
                                <?php foreach ($pessoasAdv as $pessoasAutorAdv) : ?>
                                    <option value="<?= $pessoasAutorAdv->id_pessoa ?>" <?= @$ndi->id_advogado_autor == $pessoasAutorAdv->id_pessoa ? 'selected' : '' ?>><?= $pessoasAutorAdv->nm_pessoa ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Escritório do Advogado do Autor -->
                        <!-- <div class="col-md-4">
                            <label for="id_escritorio_autor" class="form-label">Escritório do Advogado do Autor</label>
                            <input type="text" class="form-control" id="id_escritorio_autor" name="id_escritorio_autor" placeholder="Digite o escritório." disabled>
                        </div> -->
                    </div>
                    <div class="row mb-3">
                        <!-- Réu -->
                        <div class="col-md-4">
                            <label for="id_promovido" class="form-label">Réu</label>
                            <select class="form-control" id="id_promovido" name="id_promovido">
                                <option value="">Selecione</option>
                                <?php foreach ($pessoasParte as $pessoasReu) : ?>
                                    <option value="<?= $pessoasReu->id_pessoa ?>" <?= @$ndi->id_promovido == $pessoasReu->id_pessoa ? 'selected' : '' ?>><?= $pessoasReu->nm_pessoa ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Advogado do Réu -->
                        <div class="col-md-4">
                            <label for="id_advogado_reu" class="form-label">Advogado do Réu</label>
                            <select class="form-control" id="id_advogado_reu" name="id_advogado_reu" disabled>
                                <option value="">Selecione</option>
                                <?php foreach ($pessoasAdv as $pessoasReuAdv) : ?>
                                    <option value="<?= $pessoasReuAdv->id_pessoa ?>" <?= @$ndi->id_advogado_reu == $pessoasReuAdv->id_pessoa ? 'selected' : '' ?>><?= $pessoasReuAdv->nm_pessoa ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Escritório do Advogado do Réu -->
                        <!-- <div class="col-md-4">
                            <label for="id_escritorio_reu" class="form-label">Escritório do Advogado do Réu</label>
                            <input type="text" class="form-control" id="id_escritorio_reu" name="id_escritorio_reu" placeholder="Digite o escritório." disabled>
                        </div> -->
                    </div>
                    <!-- Mais campos como Autor, Advogado, Escritório, Réu, etc., com base no padrão acima -->
                    <div class="mb-2 text-end">
                        <button type="submit" class="btn btn-success">Salvar Alterações</button>
                        <a href="<?= base_url('juridico/ndiControllers') ?>" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
   $(document).ready(function () {
        // Inicializar campos Select2
        initSelect2();

        // Verifica e carrega os dados existentes para edição
        const ufSelecionada = "<?= $ndi->uf ?>";
        const cidadeSelecionada = "<?= $ndi->cidade ?>";
        const autorSelecionado = "<?= $ndi->id_promovente ?>";
        const advogadoAutorSelecionado = "<?= $ndi->id_advogado_autor ?>";
        // const escritorioAutor = "<?= $ndi->id_escritorio_autor ?>";
        const reuSelecionado = "<?= $ndi->id_promovido ?>";
        const advogadoReuSelecionado = "<?= $ndi->id_advogado_reu ?>";
        // const escritorioReu = "<?= $ndi->id_escritorio_reu ?>";

        // Preencher as cidades com base na UF selecionada
        if (ufSelecionada) {
            carregarCidades(ufSelecionada, cidadeSelecionada);
        }

        // Configurar advogado do autor
        if (autorSelecionado) {
            $('#id_advogado_autor').prop('disabled', false);
            if (advogadoAutorSelecionado) {
                $('#id_advogado_autor').val(advogadoAutorSelecionado).trigger('change');
            }
            // if (escritorioAutor) {
            //     $('#id_escritorio_autor').val(escritorioAutor).prop('disabled', false);
            // }
        }

        // Configurar advogado do réu
        if (reuSelecionado) {
            $('#id_advogado_reu').prop('disabled', false);
            if (advogadoReuSelecionado) {
                $('#id_advogado_reu').val(advogadoReuSelecionado).trigger('change');
            }
            // if (escritorioReu) {
            //     $('#id_escritorio_reu').val(escritorioReu).prop('disabled', false);
            // }
        }

        // Evento para alterar cidades ao selecionar UF
        $('#uf').change(function () {
            const estadoUF = $(this).val();
            $('#cidade').empty().append('<option value="">Carregando...</option>');
            if (estadoUF) {
                carregarCidades(estadoUF);
            } else {
                $('#cidade').empty().append('<option value="">Selecione uma cidade</option>');
            }
        });

        // Evento para habilitar advogado do autor
        $('#id_promovente').change(function () {
            const isSelected = $(this).val() !== '';
            $('#id_advogado_autor').prop('disabled', !isSelected);
            if (!isSelected) {
                $('#id_advogado_autor').val('');
                $('#id_escritorio_autor').val('').prop('disabled', true);
            }
        });

        // Evento para buscar escritório do advogado do autor
        // $('#id_advogado_autor').change(function () {
        //     buscarEscritorio($(this).val(), '#id_escritorio_autor');
        // });

        // Evento para habilitar advogado do réu
        $('#id_promovido').change(function () {
            const isSelected = $(this).val() !== '';
            $('#id_advogado_reu').prop('disabled', !isSelected);
            if (!isSelected) {
                $('#id_advogado_reu').val('');
                $('#id_escritorio_reu').val('').prop('disabled', true);
            }
        });

        // Evento para buscar escritório do advogado do réu
        $('#id_advogado_reu').change(function () {
            buscarEscritorio($(this).val(), '#id_escritorio_reu');
        });

        // Função para carregar cidades com base na UF
        function carregarCidades(uf, cidadeSelecionada = '') {
            $.ajax({
                url: `https://servicodados.ibge.gov.br/api/v1/localidades/estados/${uf}/municipios`,
                type: 'GET',
                success: function (data) {
                    $('#cidade').empty().append('<option value="">Selecione uma cidade</option>');
                    data.forEach(function (municipio) {
                        const isSelected = municipio.nome === cidadeSelecionada ? 'selected' : '';
                        $('#cidade').append(`<option value="${municipio.nome}" ${isSelected}>${municipio.nome}</option>`);
                    });
                },
                error: function () {
                    $('#cidade').empty().append('<option value="">Erro ao carregar cidades</option>');
                }
            });
        }

        // Função para buscar escritório do advogado
        // function buscarEscritorio(advogadoId, campoEscritorio) {
        //     if (advogadoId) {
        //         $.ajax({
        //             url: `<?= base_url('juridico/ndiControllers/buscarEscritorio/') ?>${advogadoId}`,
        //             type: 'GET',
        //             dataType: 'json',
        //             success: function (data) {
        //                 if (data && data.escritorio) {
        //                     $(campoEscritorio).val(data.escritorio).prop('disabled', false);
        //                 } else {
        //                     $(campoEscritorio).val('').prop('disabled', false);
        //                     alert('Nenhum escritório encontrado para o advogado selecionado.');
        //                 }
        //             },
        //             error: function () {
        //                 $(campoEscritorio).val('').prop('disabled', false);
        //                 alert('Erro ao buscar o escritório do advogado. Tente novamente.');
        //             }
        //         });
        //     } else {
        //         $(campoEscritorio).val('').prop('disabled', true);
        //     }
        // }

        // Inicializar campos Select2
        function initSelect2() {
            $('#uf, #cidade, #id_responsavel, #id_cliente, #id_servico, #id_promovente, #id_promovido, #id_advogado_autor, #id_advogado_reu').select2({
                placeholder: function () {
                    return $(this).attr('placeholder') || 'Selecione uma opção';
                },
                language: 'pt-BR',
                theme: 'bootstrap-5',
                width: '100%'
            });
        }
    });

</script>