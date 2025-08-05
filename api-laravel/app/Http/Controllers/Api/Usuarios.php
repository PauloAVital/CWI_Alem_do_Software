<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ControleUsuarios;
use Exception;
use Illuminate\Support\Facades\DB;

class Usuarios extends Controller
{
    private $usuarios;

    public function __construct()
    {
        $this->usuarios = new ControleUsuarios();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $dataUsuarios = ControleUsuarios::all();

            if(!$dataUsuarios->isEmpty()) {
                return response()->json($dataUsuarios);
            } else {
                return response()->json(['error'=> 'Nada Encontrado', 404]);
            }

        } catch (Exception $e) {
            return response()->json(['error'=> $e->getMessage(), 400]);
        }
    }    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $dataUsuarios =  $request->all();

            $retornoValida = $this->validarApi($dataUsuarios);               
            
            if ($retornoValida['success']) {
                $dataValidoUsuarios = [                            
                    'name' => $dataUsuarios['nome'],
                    'email' => $dataUsuarios['email'],
                    'senha' => md5($dataUsuarios['senha'])
                ];
                try {
                    $data = $this->usuarios->create($dataValidoUsuarios);
                    return response()->json($data, 200);
                } catch (\Illuminate\Database\QueryException $exception) {
                    $errorInfo = $exception->errorInfo;
                    return response()->json([
                        'success'=> false,
                        'error'=> 'Erro ao cadastrar', 
                        'message' => $errorInfo,
                        400
                    ]);
                }             
            }
        } catch (Exception $e) {
            return response()->json(['error'=> $e->getMessage(), 400]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            
            if (!$dataUsuarios = $this->usuarios->find($id)) {
                return response()->json(['error'=> 'Nada Encontrado', 404]);
            } else {
                return response()->json($dataUsuarios);
            }
        } catch (Exception $e) {
            return response()->json(['error'=> $e->getMessage(), 400]);
        }
    }    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            if (!$dataUpdateUsuario = $this->usuarios->find($id)) {
                return response()->json(['error'=> 'Nada Encontrado', 404]);
            } else {
                $dataUsuarios =  $request->all();

                $retornoValida = $this->validarApi($dataUsuarios);               
            
                if ($retornoValida['success']) {

                    $dataValidoUsuarios = [                            
                        'name' => $dataUsuarios['nome'],
                        'email' => $dataUsuarios['email'],
                        'senha' => md5($dataUsuarios['senha'])
                    ];
                    try {
                        $dataUpdateUsuario->update($dataValidoUsuarios);
                        return response()->json($dataUpdateUsuario, 200);
                    } catch (\Illuminate\Database\QueryException $exception) {
                        $errorInfo = $exception->errorInfo;
                        return response()->json([
                            'success'=> false,
                            'error'=> 'Erro ao atualizar', 
                            'message' => $errorInfo,
                            400
                        ]);
                    }
                }
            }
        } catch (Exception $e) {
            return response()->json(['error'=> $e->getMessage(), 400]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            if (!$dataDeleteUsuario = $this->usuarios->find($id)){
                return response()->json(['error'=> 'Nada Encontrado', 404]);
            } else {
                try {
                    $dataDeleteUsuario->delete();
                    
                    return response()->json([
                        'success'=> 'Deletado com Sucesso',
                        $dataDeleteUsuario, 
                        200
                    ]);
                } catch (\Illuminate\Database\QueryException $exception) {
                    $errorInfo = $exception->errorInfo;
                    return response()->json([
                        'success'=> false,
                        'error'=> 'Erro ao deletar', 
                        'message' => $errorInfo,
                        400
                    ]);
                }
            }
        } catch (Exception $e) {
            return response()->json(['error'=> $e->getMessage(), 400]);
        }
    }

    private function validarApi($dataApi) {
       
        #validar campos vazios
        $error = [];
        if ($dataApi['nome'] == '') {
            $error_nome = ['nome' => 'campo nome está vazio'];
            array_push(
                $error, 
                $error_nome
            );        
        }

        if ($dataApi['email'] == '') {
            $error_email = ['email' => 'campo email está vazio'];
            array_push(
                $error, 
                $error_email
            );
        }

        if ($dataApi['senha'] == '') {
            $error_senha = ['senha' => 'campo senha está vazio'];
            array_push(
                $error, 
                $error_senha
            );
        }

        if (empty($error)) {
            # Validar e-mail
            if ($this->validaEmail($dataApi['email'])) {
                $validEmail = DB::select("
                        SELECT email 
                            FROM usuarios 
                        WHERE email = '{$dataApi['email']}'"
                );

                if (!empty($validEmail)) {
                    return [
                        'success'=> false,
                        'error'=> 'e-mail ja existe',
                        400
                    ];
                }
            } else {
                return [
                    'success'=> false,
                    'error'=> 'e-mail inválido',
                    400
                ];
            }

            #validar senha
            $retorno_valida_senha = $this->senhaValida($dataApi['senha']);
            if ($retorno_valida_senha == 0) {
                return [
                    'success'=> false,
                    'error'=> 'A SENHA INVÁLIDA',
                    'mensagem'=> [
                            'uma letra minúscula',
                            'uma letra maiúscula',
                            'um número', 
                            '6 ou mais caracteres'
                        ],                     
                    400
                ];
            }

            return [
                'success'=> true,
                200
            ];

        } else {
            return [
                'success'=> false,
                'error'=> $error,
                400
            ];
        }
        
        

    }

    private function validaEmail($email) {
        $conta = "/^[a-zA-Z0-9\._-]+@";
        $domino = "[a-zA-Z0-9\._-]+.";
        $extensao = "([a-zA-Z]{2,4})$/";
        $pattern = $conta.$domino.$extensao;
        if (preg_match($pattern, $email, $check))
            return true;
        else
            return false;
    }

    private function senhaValida($senha) {
        return 
            preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[\w$@]{6,}$/', $senha);
    }
}
