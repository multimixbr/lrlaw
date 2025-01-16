<?php

namespace App\Controllers\Financeiro;

use App\Controllers\BaseController;
use App\Models\Pessoas\PessoasModels;
use App\Models\Financeiro\LancamentosModels;
use App\Models\Juridico\NDIModels;
use App\Models\Financeiro\LanParcelaModels;
use App\Models\Financeiro\AnexosModel;

class FinanceiroControllers extends BaseController
{

    protected $lancamentosModels;
    protected $lanParcelaModels;
    protected $pessoasModel;
    protected $anexosModel;
    protected $ndiModels;

    public function __construct()
    {
        $this->lancamentosModels = new LancamentosModels();
        $this->lanParcelaModels = new LanParcelaModels();
        $this->pessoasModel = new PessoasModels();
        $this->anexosModel = new AnexosModel();
        $this->ndiModels = new NDIModels();
    }

    private function loadDashboardView($viewName, $data = [])
    {
        echo view('dashboard/dashboard');
        return view($viewName, $data);
    }

    public function index(): string
    {
        return $this->loadDashboardView('financeiro/financeiro');
    }

    public function filtrar()
    {
        $session = session()->get();

        $dados['session'] = $session;

        // Captura os filtros da requisição GET, incluindo o novo filtro por ID
        $dados['filtros'] = [
            'id_lan' => $this->request->getGet('id_lan'), // Filtro por ID do lançamento
            'tp_lancamento' => $this->request->getGet('tp_lancamento'),
            'num_doc' => $this->request->getGet('num_doc'),
            'id_ndi' => $this->request->getGet('id_ndi'),
            'nm_pessoa' => $this->request->getGet('nm_pessoa'),
            'dt_vencimento' => $this->request->getGet('dt_vencimento'),
            'id_forma_pagto' => $this->request->getGet('id_forma_pagto'),
            'vl_conta' => $this->request->getGet('vl_conta'),
            'is_conferido' => $this->request->getGet('is_conferido'),
            'is_aprovado' => $this->request->getGet('is_aprovado'),
            'situacao' => $this->request->getGet('situacao')
        ];

        $dados['formasPagamento'] = $this->lancamentosModels->getFormaPgto();

        // Passa os filtros para o método da model
        $dados['lancamentos'] = $this->lancamentosModels->getLancamentosFiltro($dados['filtros']);

        // Passa os dados para a view
        return $this->loadDashboardView('financeiro/visualizarContas', $dados);
    }

    public function cadastrarContas(): string
    {
        $session = session()->get();

        $dados['session'] = $session;
        $dados['ndis'] = $this->ndiModels->getAllNDIS();
        $dados['pessoas'] = $this->pessoasModel->getAllPessoas();
        $dados['formasPagamento'] = $this->lancamentosModels->getFormaPgto();

        return $this->loadDashboardView('financeiro/cadastrarConta', $dados);
    }

    public function visualizarContas(): string
    {
        $session = session()->get();

        $dados['session'] = $session;
        $dados['lancamentos'] = $this->lancamentosModels->getAllLancamentos();
        $dados['formasPagamento'] = $this->lancamentosModels->getFormaPgto();

        // Filtros com valores padrão, incluindo o filtro por ID
        $dados['filtros'] = [
            'id_lan' => '', // Adicionando o filtro por ID no formulário de visualização
            'tp_lancamento' => '',
            'num_doc' => '',
            'id_ndi' => '',
            'nm_pessoa' => '',
            'dt_vencimento' => '',
            'id_forma_pagto' => '',
            'vl_conta' => '',
            'is_conferido' => '',
            'is_aprovado' => '',
            'situacao' => ''
        ];

        return $this->loadDashboardView('financeiro/visualizarContas', $dados);
    }

    public function controleContas(): string
    {
        $session = session()->get();
        $dados['session'] = $session;
    
        // Totais Gerais (sem filtro por conta)
        $dados['total_a_receber'] = $this->lancamentosModels->getTotalByTypeAndStatus('R', 'A'); // Total a receber em aberto
        $dados['total_recebido'] = $this->lancamentosModels->getTotalByTypeAndStatus('R', 'B');  // Total recebido
        $dados['total_a_pagar'] = $this->lancamentosModels->getTotalByTypeAndStatus('D', 'A');   // Total a pagar em aberto
        $dados['total_pago'] = $this->lancamentosModels->getTotalByTypeAndStatus('D', 'B');      // Total pago
        $dados['total_aberto'] = $this->lancamentosModels->getTotalByStatus('A');                // Total em aberto
        $dados['total_baixado'] = $this->lancamentosModels->getTotalByStatus('B');               // Total baixado
    
        // Totais Banco Inter (id_conta = 1)
        $dados['total_baixado_inter'] = $this->lancamentosModels->getTotalByStatusAndConta('B', 1);
    
        // Totais Nubank (id_conta = 2)
        $dados['total_baixado_nubank'] = $this->lancamentosModels->getTotalByStatusAndConta('B', 2);
    
        // Carregar a view com os dados
        return $this->loadDashboardView('financeiro/controleContas', $dados);
    }

    public function saveLancamentos()
    {
        // Variável para acumular as descrições das auditorias
        $auditoriaDescricao = '';

        // Obtém os dados do formulário
        $idFormaPagto = $this->request->getPost('id_forma_pagto');
        $numParcelas = max(1, (int)$this->request->getPost('num_parcelas'));
        $valorOriginal = $this->convertToDecimal($this->request->getPost('vl_original'));
        $valorParcela = round($valorOriginal / $numParcelas, 2);
        
        // Dados para o lançamento principal
        $dataLancamentoPrincipal = [
            'tp_lancamento'  => $this->request->getPost('tp_lancamento'),
            'num_doc'        => $this->request->getPost('num_doc'),
            'id_conta'       => $this->request->getPost('id_conta'),
            'id_pessoa'      => $this->request->getPost('id_pessoa'),
            'id_ndi'         => $this->request->getPost('id_ndi'),
            'vl_original'    => $valorOriginal,
            'complemento'    => $this->request->getPost('complemento'),
            'dt_vencimento'  => $this->request->getPost('dt_vencimento'),
            'dt_competencia' => $this->request->getPost('dt_competencia'),
            'id_forma_pagto' => $idFormaPagto,
            'criado_por'     => $this->request->getPost('criado_por'),
            'dt_criacao'     => date('Y-m-d H:i:s'),
            'is_ativo'       => 1,
            'dt_alteracao'   => date('Y-m-d H:i:s'),
            'alterado_por'   => $this->request->getPost('criado_por'),
            'situacao'       => 'A'
        ];

        // Salva o lançamento principal
        if (!$this->lancamentosModels->save($dataLancamentoPrincipal)) {
            $errors = $this->lancamentosModels->errors();
            $this->db->transRollback();
            session()->setFlashdata('error', 'Falha ao cadastrar o usuário. Tente novamente.');
            return redirect()->back();
        }

        // Obtém o ID do lançamento principal
        $idLancamentoPrincipal = $this->lancamentosModels->getInsertID();
        $auditoriaDescricao .= "Lançamento principal inserido com ID: $idLancamentoPrincipal. ";

        // Se houver parcelas, salva cada uma
        if ($numParcelas > 1) {
            $dataVencimentoInicial = $this->request->getPost('dt_vencimento');

            for ($parcela = 1; $parcela <= $numParcelas; $parcela++) {
                $dataVencimentoParcela = date('Y-m-d', strtotime("+".($parcela - 1)." month", strtotime($dataVencimentoInicial)));

                $dataParcela = [
                    'id_lan'          => $idLancamentoPrincipal,
                    'vl_parcela'      => $valorParcela,
                    'num_parcela'     => $parcela,
                    'is_ativo'        => 1,
                    'dt_vencimento'   => $dataVencimentoParcela,
                    'observacao'      => "Parcela $parcela de $numParcelas",
                    'situacao'        => 'A'
                ];

                if (!$this->lanParcelaModels->save($dataParcela)) {
                    $errors = $this->lanParcelaModels->errors();
                    $this->db->transRollback();
                    session()->setFlashdata('error', 'Falha ao cadastrar o usuário. Tente novamente.');
                    return redirect()->back();
                }

                $auditoriaDescricao .= "Parcela $parcela de $numParcelas salva. ";
            }
        }

        // Tratamento dos anexos
        $files = $this->request->getFiles();

        if ($files && isset($files['documentos'])) {
            $uploadPath = ROOTPATH . 'public/storage/uploads/lancamento/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            foreach ($files['documentos'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move($uploadPath, $newName);

                    $dataAnexo = [
                        'id_lancamento'   => $idLancamentoPrincipal,
                        'nome_arquivo'    => $file->getClientName(),
                        'caminho_arquivo' => 'storage/uploads/lancamento/' . $newName,
                        'tipo'            => 1,
                        'enviado_por'     => $this->request->getPost('criado_por'),
                        'data_envio'      => date('Y-m-d H:i:s')
                    ];

                    if (!$this->anexosModel->save($dataAnexo)) {
                        $errors = $this->anexosModel->errors();
                        $this->db->transRollback();
                        session()->setFlashdata('error', 'Falha ao cadastrar o usuário. Tente novamente.');
                        return redirect()->back();
                    }

                    $auditoriaDescricao .= "Anexo {$file->getClientName()} salvo. ";
                }
            }
        }

        // Finaliza a transação
        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            session()->setFlashdata('error', 'Falha ao cadastrar o usuário. Tente novamente.');
            return redirect()->back();
        } else {
            // Registra a auditoria consolidada
            $this->audit([
                'descricao' => $auditoriaDescricao,
                'usuario' => $this->request->getPost('criado_por'),
                'ip_user' => $this->request->getIPAddress(),
                'acao' => 'INSERT',
                'modulo' => 'Financeiro',
                'funcionalidade' => 'Cadastrar Lancamento Completo',
                'operacao' => 'Criar',
            ]);

            session()->setFlashdata('success', 'Usuário cadastrado com sucesso.');
            return redirect()->to(base_url('config/configControllers'));
        }
    }

    public function uploadAnexo($idLancamento)
    {
        // Tratamento dos anexos
        $files = $this->request->getFiles();

        // Variável para acumular descrições de auditoria
        $auditDescription = "Tentativa de upload de anexos para o lançamento ID: $idLancamento. ";

        if ($files && isset($files['documentos'])) {
            $uploadPath = ROOTPATH . 'public/storage/uploads/lancamento/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            foreach ($files['documentos'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move($uploadPath, $newName);

                    // Prepara os dados do anexo para salvar no banco de dados
                    $dataAnexo = [
                        'id_lancamento'   => $idLancamento,
                        'nome_arquivo'    => $file->getClientName(),
                        'caminho_arquivo' => 'storage/uploads/lancamento/' . $newName,
                        'tipo'            => 1,
                        'enviado_por'     => session()->get()['username'],
                        'data_envio'      => date('Y-m-d H:i:s'),
                    ];

                    // Salva os dados do anexo no banco de dados
                    if ($this->anexosModel->save($dataAnexo)) {
                        $auditDescription .= "Arquivo '{$file->getClientName()}' enviado com sucesso. ";
                    } else {
                        // Registro de auditoria para erro ao salvar o anexo
                        $this->audit([
                            'descricao'       => "Erro ao salvar anexo '{$file->getClientName()}' para o lançamento ID: $idLancamento.",
                            'usuario'         => session()->get()['username'],
                            'ip_user'         => $this->request->getIPAddress(),
                            'acao'            => 'UPLOAD',
                            'modulo'          => 'Financeiro',
                            'funcionalidade'  => 'Upload de Anexo',
                            'operacao'        => 'Erro',
                        ]);

                        // Define as mensagens de erro
                        $errors = $this->anexosModel->errors();
                        session()->setFlashdata('error', 'Falha ao cadastrar o usuário. Tente novamente.');
                        return redirect()->back();
                    }
                } else {
                    // Registro de auditoria para erro ao validar/mover o arquivo
                    $this->audit([
                        'descricao'       => "Erro ao validar/mover o arquivo '{$file->getClientName()}' para o lançamento ID: $idLancamento.",
                        'usuario'         => session()->get()['username'],
                        'ip_user'         => $this->request->getIPAddress(),
                        'acao'            => 'UPLOAD',
                        'modulo'          => 'Financeiro',
                        'funcionalidade'  => 'Upload de Anexo',
                        'operacao'        => 'Erro',
                    ]);

                    session()->setFlashdata('error', 'Falha ao cadastrar o usuário. Tente novamente.');
                    return redirect()->back();
                }
            }

            // Registro de auditoria para upload bem-sucedido
            $this->audit([
                'descricao'       => $auditDescription,
                'usuario'         => session()->get()['username'],
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'UPLOAD',
                'modulo'          => 'Financeiro',
                'funcionalidade'  => 'Upload de Anexo',
                'operacao'        => 'Criar',
            ]);

            // Retorna sucesso caso o upload tenha sido bem-sucedido
            session()->setFlashdata('success', 'Anexos enviados com sucesso!');
            return redirect()->back();
        } else {
            // Registro de auditoria para tentativa sem arquivos enviados
            $this->audit([
                'descricao'       => "Tentativa de upload sem anexos enviados para o lançamento ID: $idLancamento.",
                'usuario'         => session()->get()['username'],
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'UPLOAD',
                'modulo'          => 'Financeiro',
                'funcionalidade'  => 'Upload de Anexo',
                'operacao'        => 'Erro',
            ]);

            session()->setFlashdata('error', 'Nenhum arquivo foi enviado.');
            return redirect()->back()->withInput()->with('errors', 'Nenhum arquivo foi enviado.');
        }
    }

    public function visualizar($id)
    {
        $lancamento = $this->lancamentosModels->find($id);

        if ($lancamento) {
            // Registro de auditoria
            $this->audit([
                'descricao' => "Visualização de lançamento ID: $id",
                'usuario' => session()->get('username'), // ID do usuário logado
                'ip_user' => $this->request->getIPAddress(),
                'acao' => 'VIEW',
                'modulo' => 'Financeiro',
                'funcionalidade' => 'Visualizar Lançamento',
                'operacao' => 'Visualizar',
            ]);

            $dados['lancamento'] = $lancamento;
            $dados['cliente'] = $this->pessoasModel->getNomePessoaByID($lancamento['id_pessoa']);
            $dados['formasPagamento'] = $this->lancamentosModels->getFormaPgto();
            $dados['anexos'] = $this->anexosModel->getAnexosLancamentosID($id);
            $dados['parcelas'] = $this->lanParcelaModels->getParcelasLan($id);

            return $this->loadDashboardView('financeiro/visualizar', $dados);
        } else {
            // Registro de tentativa de visualização com ID inválido
            $this->audit([
                'descricao' => "Tentativa de visualização de lançamento com ID inexistente: $id",
                'usuario' => session()->get('username'), // ID do usuário logado
                'ip_user' => $this->request->getIPAddress(),
                'acao' => 'VIEW',
                'modulo' => 'Financeiro',
                'funcionalidade' => 'Visualizar Lançamento',
                'operacao' => 'Erro',
            ]);

            return redirect()->to(base_url('financeiro/financeiroControllers'))->with('error', 'Lançamento não encontrado.');
        }
    }


    public function editar($id)
    {
        $lancamento = $this->lancamentosModels->find($id);
        if ($lancamento) {
            $dados['lancamento'] = $lancamento;
            $dados['pessoas'] = $this->pessoasModel->getAllPessoas();
            $dados['ndis'] = $this->ndiModels->getAllNDIS();
            $dados['formasPagamento'] = $this->lancamentosModels->getFormaPgto();
            $dados['anexos'] = $this->anexosModel->getAnexosLancamentosID($id);
            $dados['totalNumParcela'] = count($this->lanParcelaModels->getParcelasLan($id));
            return $this->loadDashboardView('financeiro/editar', $dados);
        } else {
            return redirect()->to(base_url('financeiro/financeiroControllers'))->with('error', 'Lançamento não encontrado.');
        }
    }

    public function atualizar($id)
    {
        // Inicia a transação (caso ainda não esteja iniciada)
        $this->db->transStart();

        // Variável para acumular a descrição da auditoria
        $auditDescription = "Atualização do lançamento ID: $id. ";
    
        // Recupera o lançamento existente
        $lancamento = $this->lancamentosModels->find($id);
        if (!$lancamento) {
            session()->setFlashdata('error', 'Lançamento não encontrado.');
            return redirect()->back();
        }
    
        // Recupera as parcelas existentes
        $parcelas = $this->lanParcelaModels->where('id_lan', $id)->findAll();
    
        // Conta as parcelas pagas ou canceladas
        $parcelasPagasOuCanceladas = $this->lanParcelaModels
            ->where('id_lan', $id)
            ->whereIn('situacao', ['B', 'C']) // 'B' para pago, 'C' para cancelado
            ->countAllResults();
    
        // Obtém a forma de pagamento atual e o número de parcelas
        $currentFormaPagto   = $lancamento['id_forma_pagto'];
        $currentNumParcelas  = count($parcelas) > 0 ? count($parcelas) : 1;
    
        // Obtém os novos dados do request
        $dados = $this->request->getPost();
    
        // Corrige o formato do valor se estiver presente
        if (isset($dados['vl_original'])) {
            $dados['vl_original'] = $this->convertToDecimal($dados['vl_original']);
        }
    
        // Obtém a nova forma de pagamento e o novo número de parcelas
        $newFormaPagto   = $dados['id_forma_pagto'];
        $newNumParcelas  = isset($dados['num_parcelas']) ? (int)$dados['num_parcelas'] : 1;
    
        // Verifica se é possível alterar a forma de pagamento ou o número de parcelas
        if ($parcelasPagasOuCanceladas > 0) {
            // Existem parcelas pagas ou canceladas, não pode alterar dados sensíveis
            if ($newFormaPagto != $currentFormaPagto) {
                session()->setFlashdata('error', 'Não é permitido alterar a forma de pagamento pois existem parcelas pagas ou canceladas.');
                return redirect()->back()->withInput();
            }
            if ($newNumParcelas != $currentNumParcelas) {
                session()->setFlashdata('error', 'Não é permitido alterar o número de parcelas pois existem parcelas pagas ou canceladas.');
                return redirect()->back()->withInput();
            }
            if ($lancamento['vl_original'] != $dados['vl_original']) {
                session()->setFlashdata('error', 'Não é permitido alterar o valor original pois existem parcelas pagas ou canceladas.');
                return redirect()->back()->withInput();
            }
            
            $auditDescription .= "Tentativa de alterar campos bloqueados por ter parcelas pagas/canceladas. ";
        } else {
            // Não há parcelas pagas ou canceladas, então podemos alterar a forma de pagamento e o número de parcelas
            if ($newFormaPagto != $currentFormaPagto || $newNumParcelas != $currentNumParcelas) {
                $auditDescription .= "Forma de pagamento ou número de parcelas foi alterado. ";
            
                // Deleta as parcelas existentes
                $this->lanParcelaModels->where('id_lan', $id)->delete();
                $auditDescription .= "Parcelas antigas removidas. ";
            
                // Se o número de parcelas > 1, cria novas parcelas
                if ($newNumParcelas > 1) {
                    $valorOriginal  = $dados['vl_original'];
                    $valorParcela   = round($valorOriginal / $newNumParcelas, 2);
                
                    // Ajusta a última parcela para cobrir possíveis diferenças de arredondamento
                    $valorTotalParcelas = $valorParcela * $newNumParcelas;
                    $diferenca         = $valorOriginal - $valorTotalParcelas;
                    $valorParcelaUltima = ($diferenca != 0)
                        ? $valorParcela + $diferenca
                        : $valorParcela;
                
                    // Cria as novas parcelas
                    $dataVencimentoInicial = $dados['dt_vencimento'];
                    for ($parcela = 1; $parcela <= $newNumParcelas; $parcela++) {
                        $dataVencimentoParcela = date('Y-m-d', strtotime("+".($parcela - 1)." month", strtotime($dataVencimentoInicial)));
                        
                        // Usa o valor ajustado para a última parcela
                        $valorParcelaAtual = ($parcela == $newNumParcelas)
                            ? $valorParcelaUltima
                            : $valorParcela;
                    
                        $dataParcela = [
                            'id_lan'        => $id,
                            'vl_parcela'    => $valorParcelaAtual,
                            'num_parcela'   => $parcela,
                            'is_ativo'      => 1,
                            'dt_vencimento' => $dataVencimentoParcela,
                            'observacao'    => "Parcela $parcela de $newNumParcelas",
                            'situacao'      => 'A',
                        ];
                    
                        if (!$this->lanParcelaModels->insert($dataParcela)) {
                            $errors = $this->lanParcelaModels->errors();
                            $this->db->transRollback();
                            session()->setFlashdata('error', 'Falha ao atualizar o lançamento. Tente novamente.');
                            return redirect()->back();
                        }
                    }
                    $auditDescription .= "Novas parcelas criadas: $newNumParcelas. ";
                } else {
                    // Se o número de parcelas é 1, cria uma única parcela
                    $dataParcela = [
                        'id_lan'        => $id,
                        'vl_parcela'    => $dados['vl_original'],
                        'num_parcela'   => 1,
                        'is_ativo'      => 1,
                        'dt_vencimento' => $dados['dt_vencimento'],
                        'observacao'    => "Parcela única",
                        'situacao'      => 'A',
                    ];
                
                    if (!$this->lanParcelaModels->insert($dataParcela)) {
                        $errors = $this->lanParcelaModels->errors();
                        $this->db->transRollback();
                        session()->setFlashdata('error', 'Falha ao atualizar o lançamento. Tente novamente.');
                        return redirect()->back();
                    }
                    $auditDescription .= "Criada parcela única. ";
                }
            }
        }
    
        // Manipula o upload de arquivos
        $files = $this->request->getFiles();
        if ($files && isset($files['documentos'])) {
            $uploadPath = ROOTPATH . 'public/storage/uploads/lancamento/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
        
            foreach ($files['documentos'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move($uploadPath, $newName);
                
                    // Prepara os dados para salvar no banco de dados
                    $dataAnexo = [
                        'id_lancamento'   => $id,
                        'nome_arquivo'    => $file->getClientName(),
                        'caminho_arquivo' => 'storage/uploads/lancamento/' . $newName,
                        'tipo'            => 1,
                        'enviado_por'     => session()->get('username'),
                        'data_envio'      => date('Y-m-d H:i:s')
                    ];
                
                    // Salva o anexo no banco de dados
                    if (!$this->anexosModel->save($dataAnexo)) {
                        $errors = $this->anexosModel->errors();
                        $this->db->transRollback();
                        session()->setFlashdata('error', 'Falha ao atualizar o lançamento. Tente novamente.');
                        return redirect()->back();
                    }
                
                    $auditDescription .= "Anexo '{$file->getClientName()}' adicionado. ";
                }
            }
        }
    
        // Atualiza o lançamento com os novos dados
        if (!$this->lancamentosModels->update($id, $dados)) {
            $errors = $this->lancamentosModels->errors();
            $this->db->transRollback();
            session()->setFlashdata('error', 'Falha ao atualizar o lançamento. Tente novamente.');
            return redirect()->back();
        }
    
        // Completa a transação
        $this->db->transComplete();
    
        if ($this->db->transStatus() === FALSE) {
            session()->setFlashdata('error', 'Falha ao atualizar o lançamento. Tente novamente.');
            return redirect()->back();
        } else {
            // Registro único na auditoria, consolidando toda a operação
            $this->audit([
                'descricao'       => $auditDescription,
                'usuario'         => session()->get('username'), // Ajuste ao seu sistema de autenticação
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'UPDATE',
                'modulo'          => 'Financeiro',
                'funcionalidade'  => 'Atualizar Lançamento',
                'operacao'        => 'Atualizar',
            ]);
        
            session()->setFlashdata('success', "Lançamento: {$id} atualizado com sucesso!");
            return redirect()->to(base_url('financeiro/financeiroControllers'));
        }
    }

    public function cancelarLan($id)
    {
        // Verifica se o lançamento existe
        $lancamento = $this->lancamentosModels->find($id);

        if ($lancamento) {
            // Inicia a transação para garantir a integridade
            $this->db->transStart();

            // Variável para acumular a descrição da auditoria
            $auditDescription = "Cancelamento do lançamento ID: $id. ";

            // Atualiza o campo 'is_ativo' para 0 e a situação para 'C'
            $data = [
                'is_ativo' => 0,
                'situacao' => 'C',
                'dt_alteracao' => date('Y-m-d H:i:s'), // Atualiza a data de alteração
                'alterado_por' => session()->get('username') // Define quem alterou (supondo que o username está na sessão)
            ];

            if ($this->lancamentosModels->update($id, $data)) {
                $auditDescription .= "Lançamento marcado como inativo e situação alterada para 'C'. ";

                // Completa a transação
                $this->db->transComplete();

                if ($this->db->transStatus() === FALSE) {
                    session()->setFlashdata('error', 'Erro ao cancelar o lançamento.');
                    return redirect()->to(base_url('financeiro/financeiroControllers'))->with('error', 'Erro ao cancelar o lançamento.');
                } else {
                    // Registro de auditoria
                    $this->audit([
                        'descricao'       => $auditDescription,
                        'usuario'         => session()->get('username'), // Ajuste conforme necessário
                        'ip_user'         => $this->request->getIPAddress(),
                        'acao'            => 'CANCEL',
                        'modulo'          => 'Financeiro',
                        'funcionalidade'  => 'Cancelar Lançamento',
                        'operacao'        => 'Cancelar',
                    ]);

                    return redirect()->to(base_url('financeiro/financeiroControllers'))->with('success', 'Lançamento inativado com sucesso.');
                }
            } else {
                // Erro ao atualizar o lançamento
                $errors = $this->lancamentosModels->errors();
                $this->db->transRollback();
                session()->setFlashdata('error', 'Erro ao cancelar o lançamento.');
                return redirect()->to(base_url('financeiro/financeiroControllers'))->with('error', 'Erro ao cancelar o lançamento.');
            }
        } else {
            // Registro de tentativa de cancelamento de um ID inválido
            $this->audit([
                'descricao'       => "Tentativa de cancelamento de lançamento com ID inexistente: $id.",
                'usuario'         => session()->get('username'),
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'CANCEL',
                'modulo'          => 'Financeiro',
                'funcionalidade'  => 'Cancelar Lançamento',
                'operacao'        => 'Erro',
            ]);

            return redirect()->to(base_url('financeiro/financeiroControllers'))->with('error', 'Lançamento não encontrado.');
        }
    }
    
    public function excluirAnexo()
    {
        $session = session()->get();
        $id_anexo = $this->request->getPost('id_anexo');

        // Valida o ID do anexo
        if (!$id_anexo) {
            // Registro de auditoria para tentativa sem ID fornecido
            $this->audit([
                'descricao'       => "Tentativa de exclusão de anexo sem ID fornecido.",
                'usuario'         => $session['username'],
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'EXCLUIR',
                'modulo'          => 'Financeiro',
                'funcionalidade'  => 'Excluir Anexo',
                'operacao'        => 'Erro',
            ]);

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ID do anexo não fornecido.'
            ]);
        }

        // Recupera o anexo do banco de dados
        $anexo = $this->anexosModel->find($id_anexo);

        if (!$anexo) {
            // Registro de auditoria para anexo não encontrado
            $this->audit([
                'descricao'       => "Tentativa de exclusão de anexo não encontrado. ID: $id_anexo.",
                'usuario'         => $session['username'],
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'EXCLUIR',
                'modulo'          => 'Financeiro',
                'funcionalidade'  => 'Excluir Anexo',
                'operacao'        => 'Erro',
            ]);

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Anexo não encontrado.'
            ]);
        }

        // Exclui o arquivo físico
        $filePath = FCPATH . $anexo['caminho_arquivo']; // Ajuste o caminho conforme necessário
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Exclui o registro do banco de dados
        if ($this->anexosModel->delete($id_anexo)) {
            // Registro de auditoria para exclusão bem-sucedida
            $this->audit([
                'descricao'       => "Anexo excluído com sucesso. ID: $id_anexo, Nome: {$anexo['nome_arquivo']}.",
                'usuario'         => $session['username'],
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'EXCLUIR',
                'modulo'          => 'Financeiro',
                'funcionalidade'  => 'Excluir Anexo',
                'operacao'        => 'Deletar',
            ]);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Anexo excluído com sucesso.'
            ]);
        } else {
            // Registro de auditoria para erro ao excluir o registro
            $this->audit([
                'descricao'       => "Erro ao excluir registro do anexo. ID: $id_anexo, Nome: {$anexo['nome_arquivo']}.",
                'usuario'         => $session['username'],
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'EXCLUIR',
                'modulo'          => 'Financeiro',
                'funcionalidade'  => 'Excluir Anexo',
                'operacao'        => 'Erro',
            ]);

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Erro ao excluir o anexo.'
            ]);
        }
    }

    public function conferirLancamento()
    {
        $session = session()->get();

        $id_lancamento = $this->request->getPost('id_lancamento');
        $valor_conferido = $this->request->getPost('valor_conferido');
        $data_vencimento_conferida = $this->request->getPost('data_vencimento_conferida');

        // Verifica se os dados necessários foram fornecidos
        if (!$id_lancamento || !$valor_conferido || !$data_vencimento_conferida) {
            // Registro de auditoria para tentativa com dados incompletos
            $this->audit([
                'descricao'       => "Tentativa de conferir lançamento com dados incompletos. ID: $id_lancamento.",
                'usuario'         => $session['username'],
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'CONFERIR',
                'modulo'          => 'Financeiro',
                'funcionalidade'  => 'Conferir Lançamento',
                'operacao'        => 'Erro',
            ]);

            return $this->response->setJSON(['status' => 'error', 'message' => 'Dados incompletos']);
        }

        // Prepara os dados para atualização
        $data = [
            'vl_original'   => str_replace(['.', ','], ['', '.'], $valor_conferido),
            'is_conferido'  => 1,
            'conferido_por' => $session['username'],
            'dt_alteracao'  => date('Y-m-d H:i:s')
        ];

        // Atualiza o lançamento para conferido
        if ($this->lancamentosModels->update($id_lancamento, $data)) {
            // Registro de auditoria para conferência bem-sucedida
            $this->audit([
                'descricao'       => "Lançamento ID: $id_lancamento conferido com sucesso. Valor conferido: $valor_conferido, Data de vencimento conferida: $data_vencimento_conferida.",
                'usuario'         => $session['username'],
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'CONFERIR',
                'modulo'          => 'Financeiro',
                'funcionalidade'  => 'Conferir Lançamento',
                'operacao'        => 'Atualizar',
            ]);

            return $this->response->setJSON(['status' => 'success', 'message' => 'Lançamento conferido com sucesso!']);
        } else {
            // Registro de auditoria para erro na conferência
            $this->audit([
                'descricao'       => "Erro ao conferir lançamento ID: $id_lancamento. Valor conferido: $valor_conferido, Data de vencimento conferida: $data_vencimento_conferida.",
                'usuario'         => $session['username'],
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'CONFERIR',
                'modulo'          => 'Financeiro',
                'funcionalidade'  => 'Conferir Lançamento',
                'operacao'        => 'Erro',
            ]);

            return $this->response->setJSON(['status' => 'error', 'message' => 'Erro ao conferir o lançamento']);
        }
    }

    public function desconferirLancamento()
    {
        $session = session()->get();

        $id_lancamento = $this->request->getPost('id_lancamento');

        // Verifica se o ID do lançamento foi fornecido
        if (!$id_lancamento) {
            // Registro de auditoria para tentativa sem ID fornecido
            $this->audit([
                'descricao'       => "Tentativa de desconferir lançamento sem ID fornecido.",
                'usuario'         => $session['username'],
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'DESCONFERIR',
                'modulo'          => 'Financeiro',
                'funcionalidade'  => 'Desconferir Lançamento',
                'operacao'        => 'Erro',
            ]);

            return $this->response->setJSON(['status' => 'error', 'message' => 'ID do lançamento não encontrado']);
        }

        // Prepara os dados para reverter a conferência
        $data = [
            'is_conferido'  => 0,
            'conferido_por' => null, // Remove o usuário que conferiu
            'dt_alteracao'  => date('Y-m-d H:i:s')
        ];

        // Atualiza o lançamento para não conferido
        if ($this->lancamentosModels->update($id_lancamento, $data)) {
            // Registro de auditoria para desconferência bem-sucedida
            $this->audit([
                'descricao'       => "Lançamento ID: $id_lancamento desconferido com sucesso.",
                'usuario'         => $session['username'],
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'DESCONFERIR',
                'modulo'          => 'Financeiro',
                'funcionalidade'  => 'Desconferir Lançamento',
                'operacao'        => 'Atualizar',
            ]);

            return $this->response->setJSON(['status' => 'success', 'message' => 'Lançamento desconferido com sucesso!']);
        } else {
            // Registro de auditoria para erro na desconferência
            $this->audit([
                'descricao'       => "Erro ao desconferir lançamento ID: $id_lancamento.",
                'usuario'         => $session['username'],
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'DESCONFERIR',
                'modulo'          => 'Financeiro',
                'funcionalidade'  => 'Desconferir Lançamento',
                'operacao'        => 'Erro',
            ]);

            return $this->response->setJSON(['status' => 'error', 'message' => 'Erro ao desconferir o lançamento']);
        }
    }

    public function aprovarLancamento()
    {
        $session = session()->get();

        $id_lancamento = $this->request->getPost('id_lancamento');

        // Verifica se o ID do lançamento foi fornecido
        if (!$id_lancamento) {
            // Registro de auditoria para tentativa com ID ausente
            $this->audit([
                'descricao'       => "Tentativa de aprovar lançamento sem ID fornecido.",
                'usuario'         => $session['username'],
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'APROVAR',
                'modulo'          => 'Financeiro',
                'funcionalidade'  => 'Aprovar Lançamento',
                'operacao'        => 'Erro',
            ]);

            return $this->response->setJSON(['status' => 'error', 'message' => 'ID do lançamento não encontrado']);
        }

        $lancamento = $this->lancamentosModels->find($id_lancamento);

        // Verifica se o lançamento foi conferido antes de aprovar
        if ($lancamento['is_conferido'] == 1) {
            $data = [
                'is_aprovado'  => 1,
                'aprovado_por' => $session['username'],
                'dt_alteracao' => date('Y-m-d H:i:s'),
            ];

            if ($this->lancamentosModels->update($id_lancamento, $data)) {
                // Registro de auditoria para aprovação bem-sucedida
                $this->audit([
                    'descricao'       => "Lançamento ID: $id_lancamento aprovado com sucesso.",
                    'usuario'         => $session['username'],
                    'ip_user'         => $this->request->getIPAddress(),
                    'acao'            => 'APROVAR',
                    'modulo'          => 'Financeiro',
                    'funcionalidade'  => 'Aprovar Lançamento',
                    'operacao'        => 'Atualizar',
                ]);

                return $this->response->setJSON(['status' => 'success', 'message' => 'Lançamento aprovado com sucesso!']);
            } else {
                // Registro de auditoria para erro na aprovação
                $this->audit([
                    'descricao'       => "Erro ao aprovar lançamento ID: $id_lancamento.",
                    'usuario'         => $session['username'],
                    'ip_user'         => $this->request->getIPAddress(),
                    'acao'            => 'APROVAR',
                    'modulo'          => 'Financeiro',
                    'funcionalidade'  => 'Aprovar Lançamento',
                    'operacao'        => 'Erro',
                ]);

                return $this->response->setJSON(['status' => 'error', 'message' => 'Erro ao aprovar o lançamento']);
            }
        } else {
            // Registro de auditoria para tentativa de aprovar sem conferência
            $this->audit([
                'descricao'       => "Tentativa de aprovar lançamento ID: $id_lancamento sem conferência prévia.",
                'usuario'         => $session['username'],
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'APROVAR',
                'modulo'          => 'Financeiro',
                'funcionalidade'  => 'Aprovar Lançamento',
                'operacao'        => 'Erro',
            ]);

            return $this->response->setJSON(['status' => 'error', 'message' => 'Precisa conferir antes de aprovar o lançamento']);
        }
    }

    public function desaprovarLancamento()
    {
        $session = session()->get();

        $id_lancamento = $this->request->getPost('id_lancamento');

        // Verifica se o ID do lançamento foi fornecido
        if (!$id_lancamento) {
            // Registro de auditoria para tentativa com ID ausente
            $this->audit([
                'descricao'       => "Tentativa de desaprovar lançamento sem ID fornecido.",
                'usuario'         => $session['username'],
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'DESAPROVAR',
                'modulo'          => 'Financeiro',
                'funcionalidade'  => 'Desaprovar Lançamento',
                'operacao'        => 'Erro',
            ]);

            return $this->response->setJSON(['status' => 'error', 'message' => 'ID do lançamento não encontrado']);
        }

        $lancamento = $this->lancamentosModels->find($id_lancamento);

        // Verifica se o lançamento está aprovado antes de desaprovar
        if ($lancamento['is_aprovado'] == 1) {
            $data = [
                'is_aprovado'  => 0,
                'aprovado_por' => null, // Remove o usuário que aprovou
                'dt_alteracao' => date('Y-m-d H:i:s'),
            ];

            if ($this->lancamentosModels->update($id_lancamento, $data)) {
                // Registro de auditoria para desaprovação bem-sucedida
                $this->audit([
                    'descricao'       => "Lançamento ID: $id_lancamento desaprovado com sucesso.",
                    'usuario'         => $session['username'],
                    'ip_user'         => $this->request->getIPAddress(),
                    'acao'            => 'DESAPROVAR',
                    'modulo'          => 'Financeiro',
                    'funcionalidade'  => 'Desaprovar Lançamento',
                    'operacao'        => 'Atualizar',
                ]);

                return $this->response->setJSON(['status' => 'success', 'message' => 'Lançamento desaprovado com sucesso!']);
            } else {
                // Registro de auditoria para erro na desaprovação
                $this->audit([
                    'descricao'       => "Erro ao desaprovar lançamento ID: $id_lancamento.",
                    'usuario'         => $session['username'],
                    'ip_user'         => $this->request->getIPAddress(),
                    'acao'            => 'DESAPROVAR',
                    'modulo'          => 'Financeiro',
                    'funcionalidade'  => 'Desaprovar Lançamento',
                    'operacao'        => 'Erro',
                ]);

                return $this->response->setJSON(['status' => 'error', 'message' => 'Erro ao desaprovar o lançamento']);
            }
        } else {
            // Registro de auditoria para tentativa de desaprovar lançamento não aprovado
            $this->audit([
                'descricao'       => "Tentativa de desaprovar lançamento ID: $id_lancamento que não está aprovado.",
                'usuario'         => $session['username'],
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'DESAPROVAR',
                'modulo'          => 'Financeiro',
                'funcionalidade'  => 'Desaprovar Lançamento',
                'operacao'        => 'Erro',
            ]);

            return $this->response->setJSON(['status' => 'error', 'message' => 'Lançamento não está aprovado.']);
        }
    }

    public function baixarLancamento()
    {
        $session = session()->get();

        $id_lancamento = $this->request->getPost('id_lancamento');
        $valor_baixa = $this->request->getPost('valor_baixa');
        $data_baixa = $this->request->getPost('data_baixa');
        $id_conta = $this->request->getPost('id_conta');

        // Verifica se os dados necessários foram fornecidos
        if (!$id_lancamento || !$valor_baixa || !$data_baixa) {
            // Registro de auditoria para tentativa com dados incompletos
            $this->audit([
                'descricao'       => "Tentativa de baixar lançamento com dados incompletos. ID: $id_lancamento.",
                'usuario'         => $session['username'],
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'BAIXAR',
                'modulo'          => 'Financeiro',
                'funcionalidade'  => 'Baixar Lançamento',
                'operacao'        => 'Erro',
            ]);

            return $this->response->setJSON(['status' => 'error', 'message' => 'Dados incompletos']);
        }

        $lancamento = $this->lancamentosModels->find($id_lancamento);

        // Verifica se o lançamento foi conferido ou aprovado
        if ($lancamento['is_conferido'] == 1 || $lancamento['is_aprovado'] == 1) {
            $data = [
                'vl_baixado'   => str_replace(['.', ','], ['', '.'], $valor_baixa),
                'dt_baixa'     => $data_baixa,
                'id_conta'     => $id_conta,
                'baixado_por'  => $session['username'],
                'situacao'     => 'B',
                'dt_alteracao' => date('Y-m-d H:i:s'),
            ];

            if ($this->lancamentosModels->update($id_lancamento, $data)) {
                // Registro de auditoria para baixa bem-sucedida
                $this->audit([
                    'descricao'       => "Lançamento ID: $id_lancamento baixado com sucesso. Valor baixado: $valor_baixa, Data: $data_baixa.",
                    'usuario'         => $session['username'],
                    'ip_user'         => $this->request->getIPAddress(),
                    'acao'            => 'BAIXAR',
                    'modulo'          => 'Financeiro',
                    'funcionalidade'  => 'Baixar Lançamento',
                    'operacao'        => 'Atualizar',
                ]);

                return $this->response->setJSON(['status' => 'success', 'message' => 'Lançamento baixado com sucesso!']);
            } else {
                // Registro de auditoria para erro na baixa
                $this->audit([
                    'descricao'       => "Erro ao baixar lançamento ID: $id_lancamento. Valor baixado: $valor_baixa, Data: $data_baixa.",
                    'usuario'         => $session['username'],
                    'ip_user'         => $this->request->getIPAddress(),
                    'acao'            => 'BAIXAR',
                    'modulo'          => 'Financeiro',
                    'funcionalidade'  => 'Baixar Lançamento',
                    'operacao'        => 'Erro',
                ]);

                return $this->response->setJSON(['status' => 'error', 'message' => 'Erro ao baixar o lançamento']);
            }
        } else {
            // Registro de auditoria para tentativa de baixa sem conferência ou aprovação
            $this->audit([
                'descricao'       => "Tentativa de baixar lançamento ID: $id_lancamento sem aprovação ou conferência prévia.",
                'usuario'         => $session['username'],
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'BAIXAR',
                'modulo'          => 'Financeiro',
                'funcionalidade'  => 'Baixar Lançamento',
                'operacao'        => 'Erro',
            ]);

            return $this->response->setJSON(['status' => 'error', 'message' => 'Precisa aprovar e conferir antes de baixar o lançamento']);
        }
    }
}