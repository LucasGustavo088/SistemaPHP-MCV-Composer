<?php

class User extends \HXPHP\System\Model
{	

	//validação de dados com o active record
	static $validates_presence_of = array(
		array(
			'name',
			'message' => 'O nome é um campo obrigatório.'
		),
		array(
			'email',
			'message' => 'O e-mail é um campo obrigatório.'
		),
		array(
			'username',
			'message' => 'O nome de usuário é um campo obrigatório.'
		),
		array(
			'password',
			'message' => 'A senha é um campo obrigatório.'
		)
	);

	//veririficando se ja consta o username ou email inserido no banco de dados com o active record
	static $validates_uniqueness_of = array(
		array(
			array('username', 'email'),
			'message' => 'Já existe um usuário com este e-mail e/ou nome de usuário cadastrado.'
		)
	);

	//método pra cadastrar o usuário através do cadastro controller
	public static function cadastrar(array $post)
	{
		//criando um objeto nulo para cadastrar o úsuário
		$callbackObj = new \stdClass;
		$callbackObj->user = null;
		$callbackObj->status = false;
		$callbackObj->errors = array();
		//encontrar atributo(user) de uma tabela
		$role = Role::find_by_role('user');

		//caso não ache o atributo, retornar a mensagem
		if(is_null($role)){
			array_push($callbackObj->errors, 'A role user não existe. Contate o administrador');
			return $callbackObj;
		}

		//concatenando os dados do usuário com dados extras pra formar a tupla completa numa variavel
		//de dados do usuário
		$user_data = array_merge($post, array(
			'role_id' => $role->id,
			'status' => 1
		));
		//criprografando a senha
		$password = \HXPHP\System\Tools::hashHX($post['password']);
		//concatenando a variável $post com os dados com a senha pra inserir no banco de dados
		$post = array_merge($user_data, $password);
		//inserindo o usuário no banco de dados
		$cadastrar = self::create($post);
		//verificando se o cadastro foi valido
		if($cadastrar->is_valid()){
			$callbackObj->user = $cadastrar;
			$callbackObj->status = true;
			return $callbackObj;
		}

		$errors = $cadastrar->errors->get_raw_errors();
		//criando um array list pra retornar todos os erros que ocorreram na inserção dos dados do usuário no banco
		foreach($errors as $field => $message){
			array_push($callbackObj->errors, $message[0]);
		}

		return $callbackObj;
	}
}