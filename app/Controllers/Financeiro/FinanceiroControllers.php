<?php

namespace App\Controllers\Financeiro;

use App\Controllers\BaseController;
use App\Models\Pessoas\PessoasModels;
use App\Models\Financeiro\LancamentosModels;
use App\Models\Juridico\NDIModels;
use App\Models\Financeiro\AnexosModel;

class FinanceiroControllers extends BaseController
{

    protected $lancamentosModels;
    protected $pessoasModel;
    protected $anexosModel;
    protected $ndiModels;

    public function __construct()
    {
        $this->lancamentosModels = new LancamentosModels();
        $this->pessoasModel = new PessoasModels();
        $this->anexosModel = new AnexosModel();
        $this->ndiModels = new NDIModels();
    }

    public function index(): string
    {
        $session = session()->get();
    
        $dados['session'] = $session;
        $dados['formasPagamento'] = $this->lancamentosModels->getFormaPgto();
    
        // Captura os filtros da requisição GET, incluindo o novo filtro por ID
        $dados['filtros'] = [
            'id_lan' => $this->request->getGet('id_lan') ?? '',
            'tp_lancamento' => $this->request->getGet('tp_lancamento') ?? '',
            'num_doc' => $this->request->getGet('num_doc') ?? '',
            'id_ndi' => $this->request->getGet('id_ndi') ?? '',
            'nm_pessoa' => $this->request->getGet('nm_pessoa') ?? '',
            'dt_vencimento' => $this->request->getGet('dt_vencimento') ? $this->formatarDataParaAmericano($this->request->getGet('dt_vencimento')) : '',
            'id_forma_pagto' => $this->request->getGet('id_forma_pagto') ?? '',
            'vl_conta' => $this->request->getGet('vl_conta') ? $this->convertToDecimal($this->request->getGet('vl_conta')) : '',
            'is_conferido' => $this->request->getGet('is_conferido') ?? '',
            'is_aprovado' => $this->request->getGet('is_aprovado') ?? '',
            'situacao' => $this->request->getGet('situacao') ?? ''
        ];
    
        // Busca apenas se houver filtros aplicados
        $dados['lancamentos'] = array_filter($dados['filtros'])
            ? $this->lancamentosModels->getLancamentosFiltro($dados['filtros'])
            : [];
    
        return $this->render('financeiro/visualizarContas', $dados);
    }

    public function novo(): string
    {
        $session = session()->get();

        $dados['session'] = $session;
        $dados['ndis'] = $this->ndiModels->getAllNDIS();
        $dados['pessoas'] = $this->pessoasModel->getAllPessoas();
        $dados['formasPagamento'] = $this->lancamentosModels->getFormaPgto();

        return $this->render('financeiro/cadastrarConta', $dados);
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
        return $this->render('financeiro/controleContas', $dados);
    }

    public function saveLancamentos()
    {
        $auditoriaDescricao = '';
        $idFormaPagto   = $this->request->getPost('id_forma_pagto');
        $valorOriginal  = $this->convertToDecimal($this->request->getPost('vl_original'));
        $dataVencimentoInicial = $this->formatarDataParaAmericano($this->request->getPost('dt_vencimento'));
        $tipoPagamentoInput = $this->request->getPost('tp_conta'); 

        if ($tipoPagamentoInput == 'A') {
             $tpConta = 'A';
        } elseif ($tipoPagamentoInput == 'P') {
             $tpConta = 'P';
        } elseif ($tipoPagamentoInput == 'R') {
             $tpConta = 'R';
        } else {
             $tpConta = 'A';
        }

        $dataLancamentoPrincipal = [
             'tp_lancamento'  => $this->request->getPost('tp_lancamento'),
             'num_doc'        => $this->request->getPost('num_doc'),
             'id_conta'       => $this->request->getPost('id_conta'),
             'id_pessoa'      => $this->request->getPost('id_pessoa'),
             'id_ndi'         => $this->request->getPost('id_ndi') ?: null,
             'vl_original'    => $valorOriginal,
             'descricao'      => $this->request->getPost('descricao'),
             'complemento'    => $this->request->getPost('complemento'),
             'dt_vencimento'  => $dataVencimentoInicial,
             'dt_competencia' => $this->formatarDataParaAmericano($this->request->getPost('dt_competencia')),
             'id_forma_pagto' => $idFormaPagto,
             'criado_por'     => $this->request->getPost('criado_por'),
             'dt_criacao'     => date('Y-m-d H:i:s'),
             'is_ativo'       => 1,
             'dt_alteracao'   => date('Y-m-d H:i:s'),
             'alterado_por'   => $this->request->getPost('criado_por'),
             'situacao'       => 'A',
             'tp_conta'       => $tpConta
        ];

        if ($tpConta == 'A') {
            // À vista: lançamento único
            if (!$this->lancamentosModels->save($dataLancamentoPrincipal)) {
                $errors = $this->lancamentosModels->errors();
                $this->db->transRollback();
                session()->setFlashdata('error', 'Falha ao cadastrar o lançamento à vista. Tente novamente.');
                return redirect()->back();
            }
            $idLancamentoPrincipal = $this->lancamentosModels->getInsertID();
            $auditoriaDescricao .= "Lançamento à vista inserido com ID: $idLancamentoPrincipal. ";
        }
        elseif ($tpConta == 'P') {
            // Parcelado: divide o valor total pelo número de parcelas e insere todos os registros na mesma tabela
            $numParcelas = max(1, (int)$this->request->getPost('num_parcelas'));
            $valorParcela = round($valorOriginal / $numParcelas, 2);

            // Define o valor da parcela no registro principal
            $dataLancamentoPrincipal['vl_original'] = $valorParcela;
            if (!$this->lancamentosModels->save($dataLancamentoPrincipal)) {
                $errors = $this->lancamentosModels->errors();
                $this->db->transRollback();
                session()->setFlashdata('error', 'Falha ao cadastrar o lançamento parcelado. Tente novamente.');
                return redirect()->back();
            }
            $idLancamentoPrincipal = $this->lancamentosModels->getInsertID();
            $auditoriaDescricao .= "Lançamento parcelado principal inserido com ID: $idLancamentoPrincipal. ";

            if ($numParcelas > 1) {
                for ($i = 2; $i <= $numParcelas; $i++) {
                    $dataVencimento = date('Y-m-d', strtotime("+" . ($i - 1) . " month", strtotime($dataVencimentoInicial)));
                    $dataLancamentoParcelado = $dataLancamentoPrincipal;
                    $dataLancamentoParcelado['dt_vencimento'] = $dataVencimento;
                    $dataLancamentoParcelado['id_lan_pai'] = $idLancamentoPrincipal;
                    if (!$this->lancamentosModels->save($dataLancamentoParcelado)) {
                        $errors = $this->lancamentosModels->errors();
                        $this->db->transRollback();
                        session()->setFlashdata('error', 'Falha ao cadastrar os lançamentos parcelados. Tente novamente.');
                        return redirect()->back();
                    }
                    $auditoriaDescricao .= "Parcela $i de $numParcelas salva com vencimento em $dataVencimento. ";
                }
            }
        }
        elseif ($tpConta == 'R') {
            // Recorrente: insere o número selecionado de lançamentos com vencimento acrescido de 1 mês a cada registro
            $numLancamentos = max(1, (int)$this->request->getPost('num_lancamentos'));
            if (!$this->lancamentosModels->save($dataLancamentoPrincipal)) {
                $errors = $this->lancamentosModels->errors();
                $this->db->transRollback();
                session()->setFlashdata('error', 'Falha ao cadastrar o lançamento recorrente. Tente novamente.');
                return redirect()->back();
            }
            $idLancamentoPrincipal = $this->lancamentosModels->getInsertID();
            $auditoriaDescricao .= "Lançamento recorrente principal inserido com ID: $idLancamentoPrincipal. ";

            if ($numLancamentos > 1) {
                for ($i = 2; $i <= $numLancamentos; $i++) {
                    $dataVencimento = date('Y-m-d', strtotime("+" . ($i - 1) . " month", strtotime($dataVencimentoInicial)));
                    $dataLancamentoRecorrente = $dataLancamentoPrincipal;
                    $dataLancamentoRecorrente['dt_vencimento'] = $dataVencimento;
                    $dataLancamentoRecorrente['id_lan_pai'] = $idLancamentoPrincipal;
                    if (!$this->lancamentosModels->save($dataLancamentoRecorrente)) {
                        $errors = $this->lancamentosModels->errors();
                        $this->db->transRollback();
                        session()->setFlashdata('error', 'Falha ao cadastrar os lançamentos recorrentes. Tente novamente.');
                        return redirect()->back();
                    }
                    $auditoriaDescricao .= "Lançamento recorrente $i de $numLancamentos salvo com vencimento em $dataVencimento. ";
                }
            }
        }
        else {
            // Caso não seja identificado, insere como lançamento único (padrão)
            if (!$this->lancamentosModels->save($dataLancamentoPrincipal)) {
                $errors = $this->lancamentosModels->errors();
                $this->db->transRollback();
                session()->setFlashdata('error', 'Falha ao cadastrar o lançamento. Tente novamente.');
                return redirect()->back();
            }
            $idLancamentoPrincipal = $this->lancamentosModels->getInsertID();
            $auditoriaDescricao .= "Lançamento inserido com ID: $idLancamentoPrincipal. ";
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
                        session()->setFlashdata('error', 'Falha ao cadastrar o anexo. Tente novamente.');
                        return redirect()->back();
                    }
                    $auditoriaDescricao .= "Anexo {$file->getClientName()} salvo. ";
                }
            }
        }

        // Finaliza a transação
        $this->db->transComplete();
        if ($this->db->transStatus() === FALSE) {
            session()->setFlashdata('error', 'Falha ao cadastrar o lançamento. Tente novamente.');
            return redirect()->back();
        } else {
            $this->audit([
                'descricao'       => $auditoriaDescricao,
                'usuario'         => $this->request->getPost('criado_por'),
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'INSERT',
                'modulo'          => 'Financeiro',
                'funcionalidade'=> 'Cadastrar Lancamento Completo',
                'operacao'      => 'Criar',
            ]);
            session()->setFlashdata('success', "Lançamento: {$idLancamentoPrincipal}. Cadastrado com sucesso.");
            return redirect()->to(base_url('financeiro/financeiroControllers/visualizar/' . $idLancamentoPrincipal));
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
        $lancamentoFilhos = $this->lancamentosModels->getLanFilho($id);

        if ($lancamento) {
            $this->audit([
                'descricao' => "Visualização de lançamento ID: $id",
                'usuario' => session()->get('username'), 
                'ip_user' => $this->request->getIPAddress(),
                'acao' => 'VIEW',
                'modulo' => 'Financeiro',
                'funcionalidade' => 'Visualizar Lançamento',
                'operacao' => 'Visualizar',
            ]);

            if ($lancamento['id_ndi'] != null) {
                $lancamento['ndi_assunto'] = $lancamento['id_ndi'] . ' - ' . $this->ndiModels->getNDIByID($lancamento['id_ndi'])->assunto;
            }
            $dados['lancamento'] = $lancamento;
            $dados['lancamentoFilhos'] = $lancamentoFilhos;
            $dados['cliente'] = $this->pessoasModel->getNomePessoaByID($lancamento['id_pessoa']);
            $dados['formasPagamento'] = $this->lancamentosModels->getFormaPgto();
            $dados['anexos'] = $this->anexosModel->getAnexosLancamentosID($id);

            return $this->render('financeiro/visualizar', $dados);
        } else {
            $this->audit([
                'descricao' => "Tentativa de visualização de lançamento com ID inexistente: $id",
                'usuario' => session()->get('username'), 
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
            return $this->render('financeiro/editar', $dados);
        } else {
            return redirect()->to(base_url('financeiro/financeiroControllers'))->with('error', 'Lançamento não encontrado.');
        }
    }

    public function atualizar($id)
    {
        $this->db->transStart();
        $auditDescription = "Atualização do lançamento ID: $id. ";
    
        $lancamento = $this->lancamentosModels->find($id);
        if (!$lancamento) {
            session()->setFlashdata('error', 'Lançamento não encontrado.');
            return redirect()->back();
        }
    
        $dados = $this->request->getPost();
    
        if (empty($dados['id_ndi'])) {
            unset($dados['id_ndi']);
        }

        $dados['dt_vencimento'] = $this->formatarDataParaAmericano($dados['dt_vencimento']);
        $dados['dt_competencia'] = $this->formatarDataParaAmericano($dados['dt_competencia']);

        if (isset($dados['vl_original'])) {
            $dados['vl_original'] = $this->convertToDecimal($dados['vl_original']);
        }

        // Atualiza o lançamento com os novos dados
        if (!$this->lancamentosModels->update($id, $dados)) {
            $this->db->transRollback();
            session()->setFlashdata('error', 'Falha ao atualizar o lançamento. Tente novamente.');
            return redirect()->back();
        }

        $auditDescription .= "Lançamento atualizado com novos dados.";

        // Finaliza a transação
        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            session()->setFlashdata('error', 'Falha ao atualizar o lançamento. Tente novamente.');
            return redirect()->back();
        } else {
            $this->audit([
                'descricao'       => $auditDescription,
                'usuario'         => session()->get('username'),
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'UPDATE',
                'modulo'          => 'Financeiro',
                'funcionalidade'  => 'Atualizar Lançamento',
                'operacao'        => 'Atualizar',
            ]);

            session()->setFlashdata('success', "Lançamento: {$id} atualizado com sucesso!");
            return redirect()->to(base_url('financeiro/financeiroControllers/visualizar/' . $id));
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
                'dt_baixa'     => $this->formatarDataParaAmericano($data_baixa),
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