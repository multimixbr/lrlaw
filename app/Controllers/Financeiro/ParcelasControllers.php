<?php

namespace App\Controllers\Financeiro;

use App\Controllers\BaseController;
use App\Models\Pessoas\PessoasModels;
use App\Models\Financeiro\LancamentosModels;
use App\Models\Financeiro\LanParcelaModels;
use App\Models\Financeiro\AnexosModel;
use App\Models\UserModel;

class ParcelasControllers extends BaseController
{

    protected $lancamentosModels;
    protected $lanParcelaModels;
    protected $pessoasModel;
    protected $anexosModel;

    public function __construct()
    {
        $this->lancamentosModels = new LancamentosModels();
        $this->lanParcelaModels = new LanParcelaModels();
        $this->pessoasModel = new PessoasModels();
        $this->anexosModel = new AnexosModel();
    }

    private function loadDashboardView($viewName, $data = [])
    {
        echo view('dashboard/dashboard');
        return view($viewName, $data);
    }

    public function visualizarParcela($id_parcela)
    {
        $dados['parcela'] = $this->lanParcelaModels->getParcelasByID($id_parcela)[0];
        $dados['anexos'] = $this->anexosModel->getAnexosParcelasID($id_parcela);
        return $this->loadDashboardView('financeiro/parcela/visualizarParcela', $dados);
    }
    
    public function editarParcela($id_parcela)
    {
        $dados['parcela'] = $this->lanParcelaModels->getParcelasByID($id_parcela)[0];
        $dados['anexos'] = $this->anexosModel->getAnexosParcelasID($id_parcela);
        return $this->loadDashboardView('financeiro/parcela/editarParcela', $dados);
    }

    public function conferirParcela()
    {
        $session = session()->get();

        $id_parcela = $this->request->getPost('id_parcela');
        $valor_conferido = $this->request->getPost('valor_conferido');
        $data_vencimento_conferida = $this->request->getPost('data_vencimento_conferida');

        // Verifica se os dados necessários estão presentes
        if (!$id_parcela || !$valor_conferido || !$data_vencimento_conferida) {
            // Registro de auditoria para tentativa de conferência com dados incompletos
            $this->audit([
                'descricao'       => "Tentativa de conferir parcela ID: $id_parcela com dados incompletos.",
                'usuario'         => $session['username'],
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'CONFERIR',
                'modulo'          => 'Financeiro',
                'funcionalidade'  => 'Conferir Parcela',
                'operacao'        => 'Erro',
            ]);

            return $this->response->setJSON(['status' => 'error', 'message' => 'Dados incompletos']);
        }

        // Prepara os dados para atualização
        $data = [
            'vl_parcela'      => str_replace(['.', ','], ['', '.'], $valor_conferido),
            'is_conferido'    => 1,
            'conferido_por'   => $session['username'],
            'dt_alteracao'    => date('Y-m-d H:i:s')
        ];

        // Atualiza a parcela no banco de dados
        if ($this->lanParcelaModels->update($id_parcela, $data)) {
            // Registro de auditoria para conferência bem-sucedida
            $this->audit([
                'descricao'       => "Parcela ID: $id_parcela conferida com sucesso. Valor conferido: $valor_conferido, Data de vencimento conferida: $data_vencimento_conferida.",
                'usuario'         => $session['username'],
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'CONFERIR',
                'modulo'          => 'Financeiro',
                'funcionalidade'  => 'Conferir Parcela',
                'operacao'        => 'Atualizar',
            ]);

            return $this->response->setJSON(['status' => 'success', 'message' => 'Parcela conferida com sucesso!']);
        } else {
            // Registro de auditoria para erro ao conferir a parcela
            $this->audit([
                'descricao'       => "Erro ao conferir parcela ID: $id_parcela. Valor conferido: $valor_conferido, Data de vencimento conferida: $data_vencimento_conferida.",
                'usuario'         => $session['username'],
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'CONFERIR',
                'modulo'          => 'Financeiro',
                'funcionalidade'  => 'Conferir Parcela',
                'operacao'        => 'Erro',
            ]);

            return $this->response->setJSON(['status' => 'error', 'message' => 'Erro ao conferir a parcela']);
        }
    }

    public function desconferirParcela()
    {
        $session = session()->get();

        $id_parcela = $this->request->getPost('id_parcela');

        // Verifica se o ID da parcela foi fornecido
        if (!$id_parcela) {
            // Registro de auditoria para tentativa de desconferência sem ID
            $this->audit([
                'descricao'       => "Tentativa de desconferir parcela sem ID fornecido.",
                'usuario'         => $session['username'],
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'DESCONFERIR',
                'modulo'          => 'Financeiro',
                'funcionalidade'  => 'Desconferir Parcela',
                'operacao'        => 'Erro',
            ]);

            return $this->response->setJSON(['status' => 'error', 'message' => 'ID da parcela não fornecido']);
        }

        // Prepara os dados para atualização
        $data = [
            'is_conferido'  => 0,
            'conferido_por' => null,
            'dt_alteracao'  => date('Y-m-d H:i:s'),
        ];

        // Atualiza a parcela no banco de dados
        if ($this->lanParcelaModels->update($id_parcela, $data)) {
            // Registro de auditoria para desconferência bem-sucedida
            $this->audit([
                'descricao'       => "Parcela ID: $id_parcela desconferida com sucesso.",
                'usuario'         => $session['username'],
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'DESCONFERIR',
                'modulo'          => 'Financeiro',
                'funcionalidade'  => 'Desconferir Parcela',
                'operacao'        => 'Atualizar',
            ]);

            return $this->response->setJSON(['status' => 'success', 'message' => 'Parcela desconferida com sucesso!']);
        } else {
            // Registro de auditoria para erro ao desconferir a parcela
            $this->audit([
                'descricao'       => "Erro ao desconferir parcela ID: $id_parcela.",
                'usuario'         => $session['username'],
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'DESCONFERIR',
                'modulo'          => 'Financeiro',
                'funcionalidade'  => 'Desconferir Parcela',
                'operacao'        => 'Erro',
            ]);

            return $this->response->setJSON(['status' => 'error', 'message' => 'Erro ao desconferir a parcela']);
        }
    }

    public function aprovarParcela()
    {
        $session = session()->get();

        $id_parcela = $this->request->getPost('id_parcela');

        // Verifica se o ID da parcela foi fornecido
        if (!$id_parcela) {
            // Registro de auditoria para tentativa com ID ausente
            $this->audit([
                'descricao'       => "Tentativa de aprovar parcela sem ID fornecido.",
                'usuario'         => $session['username'],
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'APROVAR',
                'modulo'          => 'Financeiro',
                'funcionalidade'  => 'Aprovar Parcela',
                'operacao'        => 'Erro',
            ]);

            return $this->response->setJSON(['status' => 'error', 'message' => 'ID da parcela não encontrado']);
        }

        $parcela = $this->lanParcelaModels->getParcelasByID($id_parcela)[0];

        // Verifica se a parcela foi conferida antes de aprovar
        if ($parcela->is_conferido == 1) {
            $data = [
                'is_aprovado'   => 1,
                'aprovado_por'  => $session['username'],
                'dt_alteracao'  => date('Y-m-d H:i:s')
            ];

            // Atualiza a parcela para aprovada
            if ($this->lanParcelaModels->update($id_parcela, $data)) {
                // Registro de auditoria para aprovação bem-sucedida
                $this->audit([
                    'descricao'       => "Parcela ID: $id_parcela aprovada com sucesso.",
                    'usuario'         => $session['username'],
                    'ip_user'         => $this->request->getIPAddress(),
                    'acao'            => 'APROVAR',
                    'modulo'          => 'Financeiro',
                    'funcionalidade'  => 'Aprovar Parcela',
                    'operacao'        => 'Atualizar',
                ]);

                return $this->response->setJSON(['status' => 'success', 'message' => 'Parcela aprovada com sucesso!']);
            } else {
                // Registro de auditoria para erro ao aprovar
                $this->audit([
                    'descricao'       => "Erro ao aprovar parcela ID: $id_parcela.",
                    'usuario'         => $session['username'],
                    'ip_user'         => $this->request->getIPAddress(),
                    'acao'            => 'APROVAR',
                    'modulo'          => 'Financeiro',
                    'funcionalidade'  => 'Aprovar Parcela',
                    'operacao'        => 'Erro',
                ]);

                return $this->response->setJSON(['status' => 'error', 'message' => 'Erro ao aprovar a parcela']);
            }
        } else {
            // Registro de auditoria para tentativa de aprovar parcela não conferida
            $this->audit([
                'descricao'       => "Tentativa de aprovar parcela ID: $id_parcela sem conferência prévia.",
                'usuario'         => $session['username'],
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'APROVAR',
                'modulo'          => 'Financeiro',
                'funcionalidade'  => 'Aprovar Parcela',
                'operacao'        => 'Erro',
            ]);

            return $this->response->setJSON(['status' => 'error', 'message' => 'Precisa conferir antes de aprovar a parcela']);
        }
    }

    public function desaprovarParcela()
    {
        $session = session()->get();
    
        $id_parcela = $this->request->getPost('id_parcela');
    
        // Verifica se o ID da parcela foi fornecido
        if (!$id_parcela) {
            // Registro de auditoria para tentativa de desaprovação sem ID
            $this->audit([
                'descricao'       => "Tentativa de desaprovar parcela sem ID fornecido.",
                'usuario'         => $session['username'],
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'DESAPROVAR',
                'modulo'          => 'Financeiro',
                'funcionalidade'  => 'Desaprovar Parcela',
                'operacao'        => 'Erro',
            ]);
        
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID da parcela não encontrado']);
        }
    
        $parcela = $this->lanParcelaModels->getParcelasByID($id_parcela)[0];
    
        // Verifica se a parcela está aprovada antes de desaprovar
        if ($parcela->is_aprovado == 1) {
            $data = [
                'is_aprovado'   => 0,
                'aprovado_por'  => null,
                'dt_alteracao'  => date('Y-m-d H:i:s')
            ];
        
            // Atualiza a parcela para desaprovada
            if ($this->lanParcelaModels->update($id_parcela, $data)) {
                // Registro de auditoria para desaprovação bem-sucedida
                $this->audit([
                    'descricao'       => "Parcela ID: $id_parcela desaprovada com sucesso.",
                    'usuario'         => $session['username'],
                    'ip_user'         => $this->request->getIPAddress(),
                    'acao'            => 'DESAPROVAR',
                    'modulo'          => 'Financeiro',
                    'funcionalidade'  => 'Desaprovar Parcela',
                    'operacao'        => 'Atualizar',
                ]);
            
                return $this->response->setJSON(['status' => 'success', 'message' => 'Parcela desaprovada com sucesso!']);
            } else {
                // Registro de auditoria para erro ao desaprovar
                $this->audit([
                    'descricao'       => "Erro ao desaprovar parcela ID: $id_parcela.",
                    'usuario'         => $session['username'],
                    'ip_user'         => $this->request->getIPAddress(),
                    'acao'            => 'DESAPROVAR',
                    'modulo'          => 'Financeiro',
                    'funcionalidade'  => 'Desaprovar Parcela',
                    'operacao'        => 'Erro',
                ]);
            
                return $this->response->setJSON(['status' => 'error', 'message' => 'Erro ao desaprovar a parcela']);
            }
        } else {
            // Registro de auditoria para tentativa de desaprovar parcela não aprovada
            $this->audit([
                'descricao'       => "Tentativa de desaprovar parcela ID: $id_parcela que não está aprovada.",
                'usuario'         => $session['username'],
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'DESAPROVAR',
                'modulo'          => 'Financeiro',
                'funcionalidade'  => 'Desaprovar Parcela',
                'operacao'        => 'Erro',
            ]);
        
            return $this->response->setJSON(['status' => 'error', 'message' => 'Parcela não está aprovada para ser desaprovada']);
        }
    }

    public function baixarParcela()
    {
        $session = session()->get();

        $id_parcela = $this->request->getPost('id_parcela');
        $valor_baixa = $this->request->getPost('valor_baixa');
        $data_baixa = $this->request->getPost('data_baixa');
        $banco_baixa = $this->request->getPost('banco_baixa');

        // Verifica se os dados necessários foram fornecidos
        if (!$id_parcela || !$valor_baixa || !$data_baixa) {
            // Registro de auditoria para tentativa com dados incompletos
            $this->audit([
                'descricao'       => "Tentativa de baixar parcela com dados incompletos. ID: $id_parcela.",
                'usuario'         => $session['username'],
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'BAIXAR',
                'modulo'          => 'Financeiro',
                'funcionalidade'  => 'Baixar Parcela',
                'operacao'        => 'Erro',
            ]);

            return $this->response->setJSON(['status' => 'error', 'message' => 'Dados incompletos']);
        }

        // Obtem a parcela a ser baixada
        $parcela = $this->lanParcelaModels->getParcelasByID($id_parcela)[0];

        // Verifica se a parcela já foi conferida ou aprovada
        if ($parcela->is_conferido == 1 && $parcela->is_aprovado == 1 && !empty($banco_baixa)) {
            // Prepara os dados para a baixa
            $data = [
                'id_conta'      => $banco_baixa,
                'vl_baixa'      => str_replace(['.', ','], ['', '.'], $valor_baixa),
                'dt_baixa'      => $data_baixa,
                'baixado_por'   => $session['username'],
                'status'        => 'baixada',
                'situacao'      => 'B'
            ];

            if ($this->lanParcelaModels->update($id_parcela, $data)) {
                $auditDescription = "Parcela ID: $id_parcela baixada com sucesso. Valor: $valor_baixa, Data: $data_baixa.";

                // Obtem todas as parcelas relacionadas ao lançamento
                $parcelas = $this->lanParcelaModels->getParcelasLan($parcela->id_lan);
                $parcela = $this->lanParcelaModels->getParcelasByID($id_parcela)[0];

                // Verifica se todas as parcelas do lançamento foram baixadas
                $todasParcelasBaixadas = true;
                $valorTotalBaixado = 0;

                foreach ($parcelas as $parc) {
                    if (is_null($parc->dt_baixa)) {
                        $todasParcelasBaixadas = false;
                        break;
                    }
                    $valorTotalBaixado += $parc->vl_parcela;
                }

                // Se todas as parcelas foram baixadas, atualiza o lançamento
                if ($todasParcelasBaixadas) {
                    $dadosLancamento = [
                        'is_aprovado'     => $parcela->is_aprovado,
                        'is_conferido'    => $parcela->is_conferido,
                        'dt_baixa'        => $data_baixa,  // Data da última parcela baixada
                        'dt_alteracao'    => date("Y-m-d H:i:s"),  // Data da última parcela baixada
                        'vl_baixado'      => $valorTotalBaixado,
                        'conferido_por'   => $parcela->conferido_por,
                        'baixado_por'     => $parcela->baixado_por,
                        'situacao'        => 'B'
                    ];

                    $this->lancamentosModels->update($parcela->id_lan, $dadosLancamento);

                    $auditDescription .= " Todas as parcelas do lançamento ID: {$parcela->id_lan} baixadas com sucesso. Valor total baixado: $valorTotalBaixado.";
                }

                // Registro de auditoria para baixa bem-sucedida
                $this->audit([
                    'descricao'       => $auditDescription,
                    'usuario'         => $session['username'],
                    'ip_user'         => $this->request->getIPAddress(),
                    'acao'            => 'BAIXAR',
                    'modulo'          => 'Financeiro',
                    'funcionalidade'  => 'Baixar Parcela',
                    'operacao'        => 'Atualizar',
                ]);

                return $this->response->setJSON(['status' => 'success', 'message' => 'Parcela baixada com sucesso!']);
            } else {
                // Registro de auditoria para erro na baixa
                $this->audit([
                    'descricao'       => "Erro ao baixar parcela ID: $id_parcela. Valor: $valor_baixa, Data: $data_baixa.",
                    'usuario'         => $session['username'],
                    'ip_user'         => $this->request->getIPAddress(),
                    'acao'            => 'BAIXAR',
                    'modulo'          => 'Financeiro',
                    'funcionalidade'  => 'Baixar Parcela',
                    'operacao'        => 'Erro',
                ]);

                return $this->response->setJSON(['status' => 'error', 'message' => 'Erro ao baixar a parcela']);
            }
        } else {
            // Registro de auditoria para tentativa de baixar parcela não conferida ou aprovada
            $this->audit([
                'descricao'       => "Tentativa de baixar parcela ID: $id_parcela sem aprovação ou conferência prévia.",
                'usuario'         => $session['username'],
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'BAIXAR',
                'modulo'          => 'Financeiro',
                'funcionalidade'  => 'Baixar Parcela',
                'operacao'        => 'Erro',
            ]);

            return $this->response->setJSON(['status' => 'error', 'message' => 'Precisa aprovar e conferir antes de baixar a parcela']);
        }
    }

    public function cancelarParcela()
    {
        $id_parcela = $this->request->getPost('id_parcela');

        if (!$id_parcela) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID da parcela não encontrado']);
        }

        $data = [
            'status' => 'cancelada',
        ];

        if ($this->lanParcelaModels->update($id_parcela, $data)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Parcela cancelada com sucesso!']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Erro ao cancelar a parcela']);
        }
    }

    public function uploadAnexo($idParcela)
    {
        $session = session()->get();

        // Tratamento dos anexos
        $files = $this->request->getFiles();

        // Variável para acumular a descrição da auditoria
        $auditDescription = "Tentativa de upload de anexo para a parcela ID: $idParcela. ";

        // Verifica se algum arquivo foi enviado
        if (!empty($files['anexo'])) {
            $uploadPath = ROOTPATH . 'public/storage/uploads/parcela/';

            // Cria o diretório se não existir
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Percorre os arquivos enviados (o campo 'anexo' suporta múltiplos arquivos)
            foreach ($files['anexo'] as $file) {
                // Verifica se o arquivo é válido e não foi movido ainda
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName(); // Gera um nome aleatório para o arquivo
                    $file->move($uploadPath, $newName); // Move o arquivo para o diretório de uploads

                    // Prepara os dados do anexo para salvar no banco de dados
                    $dataAnexo = [
                        'id_parcela'      => $idParcela,
                        'nome_arquivo'    => $file->getClientName(), // Nome original do arquivo
                        'caminho_arquivo' => 'storage/uploads/parcela/' . $newName, // Caminho no servidor
                        'enviado_por'     => $session['username'],
                        'data_envio'      => date('Y-m-d H:i:s'),
                    ];

                    // Tenta salvar os dados do anexo no banco de dados
                    if (!$this->anexosModel->save($dataAnexo)) {
                        // Registro de auditoria para erro ao salvar o anexo
                        $this->audit([
                            'descricao'       => "Erro ao salvar anexo '{$file->getClientName()}' para a parcela ID: $idParcela.",
                            'usuario'         => $session['username'],
                            'ip_user'         => $this->request->getIPAddress(),
                            'acao'            => 'UPLOAD',
                            'modulo'          => 'Financeiro',
                            'funcionalidade'  => 'Upload de Anexo',
                            'operacao'        => 'Erro',
                        ]);

                        // Define as mensagens de erro
                        $errors = $this->anexosModel->errors();
                        session()->setFlashdata('errors', $errors);
                        return redirect()->back()->withInput()->with('errors', $errors);
                    }

                    // Adiciona informações ao log de auditoria em caso de sucesso
                    $auditDescription .= "Arquivo '{$file->getClientName()}' enviado com sucesso. ";
                } else {
                    // Registro de auditoria para erro ao validar/mover o arquivo
                    $this->audit([
                        'descricao'       => "Erro ao validar/mover o arquivo '{$file->getClientName()}' para a parcela ID: $idParcela.",
                        'usuario'         => $session['username'],
                        'ip_user'         => $this->request->getIPAddress(),
                        'acao'            => 'UPLOAD',
                        'modulo'          => 'Financeiro',
                        'funcionalidade'  => 'Upload de Anexo',
                        'operacao'        => 'Erro',
                    ]);

                    session()->setFlashdata('error', "Erro ao enviar o arquivo '{$file->getClientName()}'.");
                    return redirect()->back()->withInput()->with('errors', "Erro ao enviar o arquivo '{$file->getClientName()}'.");
                }
            }

            // Registro de auditoria para upload bem-sucedido
            $this->audit([
                'descricao'       => $auditDescription,
                'usuario'         => $session['username'],
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
                'descricao'       => "Tentativa de upload sem arquivos enviados para a parcela ID: $idParcela.",
                'usuario'         => $session['username'],
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

    public function atualizarParcela($id_parcela)
    {
        $data = $this->request->getPost();

        // Inicia uma transação para garantir a integridade
        $this->db->transStart();

        // Variável para acumular a descrição da auditoria
        $auditDescription = "Atualização da parcela ID: $id_parcela. ";

        // Prepare os dados para atualização
        $updateData = [];

        if (isset($data['vl_parcela'])) {
            $updateData['vl_parcela'] = $this->convertToDecimal($data['vl_parcela']);
            $auditDescription .= "Valor da parcela atualizado para {$updateData['vl_parcela']}. ";
        }

        if (isset($data['dt_vencimento'])) {
            $updateData['dt_vencimento'] = $data['dt_vencimento'];
            $auditDescription .= "Data de vencimento atualizada para {$updateData['dt_vencimento']}. ";
        }

        if (isset($data['complemento'])) {
            $updateData['observacao'] = $data['complemento'];
            $auditDescription .= "Complemento atualizado para '{$updateData['observacao']}'. ";
        }

        // Atualiza a parcela no banco de dados
        if ($this->lanParcelaModels->update($id_parcela, $updateData)) {
            $auditDescription .= "Dados da parcela atualizados com sucesso. ";

            // Manipula o upload de arquivos
            $session = session()->get();
            $files = $this->request->getFiles();

            if ($files && isset($files['anexo'])) {
                $uploadPath = ROOTPATH . 'public/storage/uploads/parcela/';

                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                foreach ($files['anexo'] as $file) {
                    if ($file->isValid() && !$file->hasMoved()) {
                        $newName = $file->getRandomName();
                        $file->move($uploadPath, $newName);

                        $dataAnexo = [
                            'id_parcela'      => $id_parcela,
                            'nome_arquivo'    => $file->getClientName(),
                            'caminho_arquivo' => 'storage/uploads/parcela/' . $newName,
                            'enviado_por'     => $session['username'],
                            'data_envio'      => date('Y-m-d H:i:s'),
                        ];

                        if ($this->anexosModel->save($dataAnexo)) {
                            $auditDescription .= "Anexo '{$file->getClientName()}' adicionado. ";
                        } else {
                            // Se houver erro ao salvar o anexo, realiza o rollback e retorna
                            $errors = $this->anexosModel->errors();
                            $this->db->transRollback();
                            return redirect()->back()->withInput()->with('errors', $errors);
                        }
                    }
                }
            }

            // Completa a transação
            $this->db->transComplete();

            if ($this->db->transStatus() === FALSE) {
                session()->setFlashdata('error', 'Ocorreu um erro ao atualizar a parcela.');
                return redirect()->back()->withInput();
            } else {
                // Registro de auditoria único
                $this->audit([
                    'descricao'       => $auditDescription,
                    'usuario'         => $session['username'],
                    'ip_user'         => $this->request->getIPAddress(),
                    'acao'            => 'UPDATE',
                    'modulo'          => 'Financeiro',
                    'funcionalidade'  => 'Atualizar Parcela',
                    'operacao'        => 'Atualizar',
                ]);

                return redirect()->to(base_url('financeiro/parcelasControllers/visualizarParcela/' . $id_parcela))->with('success', 'Parcela atualizada com sucesso.');
            }
        } else {
            // Erro ao atualizar a parcela
            $errors = $this->lanParcelaModels->errors();
            return redirect()->back()->withInput()->with('errors', $errors);
        }
    }

    public function deletarAnexo($id_anexo)
    {
        // Find the anexo in the database
        $anexo = $this->anexosModel->find($id_anexo);

        if (!$anexo) {
            return redirect()->back()->with('error', 'Anexo não encontrado.');
        }

        // Get the id_parcela to redirect back
        $id_parcela = $anexo['id_parcela'];

        // Delete the physical file
        $filePath = FCPATH . $anexo['caminho_arquivo'];

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete the anexo record from the database
        if ($this->anexosModel->delete($id_anexo)) {
            return redirect()->to(base_url('financeiro/parcelasControllers/editarParcela/' . $id_parcela))->with('success', 'Anexo excluído com sucesso.');
        } else {
            $errors = $this->anexosModel->errors();
            return redirect()->back()->with('error', 'Erro ao excluir o anexo.');
        }
    }
}