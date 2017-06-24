<?php  

class CadastroController extends \HXPHP\System\Controller
{
	//configurando o login do usuário
	public function __construct($configs){
		parent::__construct($configs);

		//carregando a autenticação do usuário da classe auth em services
		$this->load(
			'Services\Auth',
			$configs->auth->after_login,
			$configs->auth->after_logout,
			true
		);
		//true: permite acessar cadastro mesmo logado | false: só permite cadastrar estando logado		
		$this->auth->redirectCheck(true);
	}

	public function cadastrarAction()
	{
		//colocando este arquivo como o index
		$this->view->setFile('index');

		//validando o campo email e filtrando
		$this->request->setCustomFilters(array(
			'email' => FILTER_VALIDATE_EMAIL
		));
		//recebendo na variável $post um array de dados a serem inseridos no banco de dados
		$post = $this->request->post();
		//caso esteja com dados na variável $post, cadastre o usuário
		if(!empty($post)){
			//cadastrando o usuário com a classe user do active record
			$cadastrarUsuario = User::cadastrar($post);
			//caso ocorra algum erro de cadastro, exibir os erros através do framework HXPHP
			if($cadastrarUsuario->status === false){
				$this->load('Helpers\Alert', array(
					'danger',
					'Ops! Não foi possível efetuar o seu cadastro. Verifique os erros abaixo: ',
					$cadastrarUsuario->errors
				));
			} else {
				//logar o usuário
				$this->auth->login($cadastrarUsuario->user->id, $cadastrarUsuario->user->username);
			}
		}
	}
}