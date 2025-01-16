<style>
    .readonly-field {
        background-color: #e9ecef;
        opacity: 1;
        pointer-events: none;
        cursor: not-allowed;
    }
</style>

<div class="main">
    <div class="container-fluid mt-3">
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h2 class="mb-0">Visualizar Pessoa</h2>
            </div>
            <div class="card-body">
                <form>
                    <div class="row g-3">
                        <!-- Tipo de Pessoa -->
                        <div class="col-md-3">
                            <label for="tp_pessoa" class="form-label">Tipo de Pessoa:</label>
                            <select name="tp_pessoa" id="tp_pessoa" class="form-control readonly-field" disabled>
                                <option value="PF" <?= $pessoas->tp_pessoa == 'PF' ? 'selected' : '' ?>>Pessoa Física</option>
                                <option value="PJ" <?= $pessoas->tp_pessoa == 'PJ' ? 'selected' : '' ?>>Pessoa Jurídica</option>
                            </select>
                        </div>
                                
                        <!-- Documento -->
                        <div class="col-md-3">
                            <label for="documento" class="form-label">CPF/CNPJ:</label>
                            <input type="text" name="documento" id="documento" class="form-control readonly-field" value="<?= htmlspecialchars($pessoas->documento) ?>" readonly>
                        </div>
                                
                        <!-- Tipo de Cadastro -->
                        <div class="col-md-6">
                            <label class="form-label">Tipo de Cadastro:</label>
                            <div class="d-flex align-items-center">
                                <div class="form-check me-3">
                                    <input class="form-check-input readonly-field" type="checkbox" id="tp_cad_cli" disabled
                                        <?= $pessoas->tp_cad_cli == '1' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="tipo_cadastro_cliente">Cliente</label>
                                </div>
                                <div class="form-check me-3">
                                    <input class="form-check-input readonly-field" type="checkbox" id="tp_cad_for" disabled
                                        <?= $pessoas->tp_cad_for == '1' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="tipo_cadastro_fornecedor">Fornecedor</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input readonly-field" type="checkbox" id="tp_cad_parte" disabled
                                        <?= $pessoas->tp_cad_parte == '1' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="tipo_cadastro_parte">Parte</label>
                                </div>
                            </div>
                        </div>
                    </div>
                                
                    <div class="row g-3 mt-3">
                        <!-- Nome -->
                        <div class="col-md-4">
                            <label for="nm_pessoa" class="form-label">Nome:</label>
                            <input type="text" name="nm_pessoa" id="nm_pessoa" class="form-control readonly-field" value="<?= htmlspecialchars($pessoas->nm_pessoa) ?>" readonly>
                        </div>
                                
                        <!-- Telefone 1 -->
                        <div class="col-md-4">
                            <label for="tel_1" class="form-label">Telefone principal:</label>
                            <input type="text" name="tel_1" id="tel_1" class="form-control readonly-field" value="<?= htmlspecialchars($pessoas->tel_1) ?>" readonly>
                        </div>
                                
                        <!-- Telefone 2 -->
                        <div class="col-md-4">
                            <label for="tel_2" class="form-label">Telefone secundário:</label>
                            <input type="text" name="tel_2" id="tel_2" class="form-control readonly-field" value="<?= htmlspecialchars($pessoas->tel_2) ?>" readonly>
                        </div>
                    </div>
                                
                    <div class="row g-3 mt-3">
                        <!-- Email -->
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" name="email" id="email" class="form-control readonly-field" value="<?= htmlspecialchars($pessoas->email) ?>" readonly>
                        </div>
                                
                        <!-- CEP -->
                        <div class="col-md-3">
                            <label for="cep" class="form-label">CEP:</label>
                            <input type="text" name="cep" id="cep" class="form-control readonly-field" value="<?= htmlspecialchars($enderecos->cep) ?>" readonly>
                        </div>
                                
                        <!-- Número -->
                        <div class="col-md-3">
                            <label for="numero" class="form-label">Número:</label>
                            <input type="text" name="numero" id="numero" class="form-control readonly-field" value="<?= htmlspecialchars($enderecos->numero) ?>" readonly>
                        </div>
                    </div>
                                
                    <div class="row g-3 mt-3">
                        <!-- Rua -->
                        <div class="col-md-6">
                            <label for="rua" class="form-label">Rua:</label>
                            <input type="text" name="rua" id="rua" class="form-control readonly-field" value="<?= htmlspecialchars($enderecos->rua) ?>" readonly>
                        </div>
                                
                        <!-- Complemento -->
                        <div class="col-md-6">
                            <label for="complemento" class="form-label">Complemento:</label>
                            <input type="text" name="complemento" id="complemento" class="form-control readonly-field" value="<?= htmlspecialchars($enderecos->complemento) ?>" readonly>
                        </div>
                    </div>
                                
                    <div class="row g-3 mt-3">
                        <!-- Bairro -->
                        <div class="col-md-4">
                            <label for="bairro" class="form-label">Bairro:</label>
                            <input type="text" name="bairro" id="bairro" class="form-control readonly-field" value="<?= htmlspecialchars($enderecos->bairro) ?>" readonly>
                        </div>
                                
                        <!-- Cidade -->
                        <div class="col-md-4">
                            <label for="cidade" class="form-label">Cidade:</label>
                            <input type="text" name="cidade" id="cidade" class="form-control readonly-field" value="<?= htmlspecialchars($enderecos->cidade) ?>" readonly>
                        </div>
                                
                        <!-- Estado -->
                        <div class="col-md-4">
                            <label for="estado" class="form-label">Estado:</label>
                            <select name="estado" id="estado" class="form-control readonly-field" disabled>
                                <option value="">Selecione o Estado</option>
                                <?php foreach ($estados as $estado): ?>
                                    <option value="<?= $estado['sigla'] ?>" <?= $enderecos->estado == $estado['sigla'] ? 'selected' : '' ?>>
                                        <?= $estado['nome'] ?> (<?= $estado['sigla'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                                
                    <div class="row g-3 mt-3">
                        <!-- Observação -->
                        <div class="col-md-12">
                            <label for="observacao" class="form-label">Observação:</label>
                            <textarea name="observacao" id="observacao" class="form-control readonly-field" rows="2" readonly><?= htmlspecialchars($pessoas->observacao) ?></textarea>
                        </div>
                    </div>
                                
                    <div class="mt-4 text-end">
                        <a href="<?= base_url('pessoas/pessoasControllers/visualizarPessoas') ?>" class="btn btn-secondary">Voltar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

        $('#cep').mask('00000-000');

        $('#documento').inputmask({
            mask: ['999.999.999-99', '99.999.999/9999-99'],
            keepStatic: true
        });

        $('#tel_1, #tel_2').inputmask({
            mask: ['(99) 9999-9999', '(99) 99999-9999'],
            keepStatic: true
        });

    });
</script>