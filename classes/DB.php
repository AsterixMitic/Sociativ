<?php
class DB{
	private static function connect(){
		//in next line there should be personal informations about MySQL database login
		$pdo = new PDO('database_conenction', 'username', 'password');
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $pdo;
	}

	public static function query($query, $params = array()){
		$statement=self::connect()->prepare($query);
		$statement->execute($params);

		if(explode(' ',$query)[0] == 'SELECT'){

			$data = $statement->fetchAll();
			return $data;
		}
	}
}
