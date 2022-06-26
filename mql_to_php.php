<?php






class MqlObj {



	// CONNECT TO THE DATABASE INSTANCE
	function __construct($db_host = 'localhost', $db_name = '', $db_user = '', $db_pass = '', $db_port = 27017) {

		$this->manager = new MongoDB\Driver\Manager("mongodb://".$db_user.":".$this->percent_encode_password_special_chars($db_pass)."@".$db_host.":".$db_port);

	}






	// ROUTE THE QUERY TO THE CORRESPONDING FUNCTION
	public function mql($command) {



		$this->command = $command;



		switch(true) {

			// MATCH THE `use` KEYWORD
			case (preg_match('~^use (.*)~', $this->command, $matches_use)):
				$this->db = $matches_use[1];
				$output = true;
				break;

			// MATCH A `find` QUERY
			case (preg_match('~db\.[^.]+\.find~', $this->command)):
				$cursor = $this->mql_find();
				$output = $cursor->toArray();
				break;

			// MATCH A `remove` QUERY
			case (preg_match('~db\.[^.]+\.remove~', $this->command)):
				$output = $this->mql_remove();
				break;

			default:
				$output = "Unable to match a command with that name.";

		}



		return $output;



	}






	public function mql_find() {



		preg_match('~^[^.]+\.(?<COLLECTION>[^.]+)\.find\((?<PATTERN>(?:(?!(?:\)\.|\);|\)$)).)*)\)(?<OPERATIONS>.*)~i', $this->command, $matches_find);

		$this->collection = $matches_find['COLLECTION'];
		$this->pattern = $matches_find['PATTERN'];
		$this->operations = $matches_find['OPERATIONS'];
		
		$manager = $this->manager;




		$query = new MongoDB\Driver\Query($this->mql_find_helper_pattern(), array('limit' => 10));
		$cursor = $manager->executeQuery($this->db.'.'.$this->collection, $query);



		return $cursor;



	}






	public function mql_remove() {



		preg_match('~^[^.]+\.(?<COLLECTION>[^.]+)\.remove\((?<PATTERN>(?:(?!(?:\)\.|\);|\)$)).)*)\)(?<OPERATIONS>.*)~i', $this->command, $matches_remove);

		$this->collection = $matches_remove['COLLECTION'];
		$this->pattern = $matches_remove['PATTERN'];
		$this->operations = $matches_remove['OPERATIONS'];

		$manager = $this->manager;






		$query = new MongoDB\Driver\BulkWrite;
		$query->delete($this->mql_find_helper_pattern(), array('limit' => 10));

		$cursor = $manager->executeBulkWrite($this->db.'.'.$this->collection, $query);



		return $cursor;



	}






	public function mql_find_helper_pattern() {



		$pattern_array = array();

		if (empty($this->pattern)) return $pattern_array;

		print $this->pattern;
		/*
		* PARSE THE PATTERN STRING
		*/
		$pattern_array = json_decode($this->pattern, true);
		

		if (preg_match_all('~(?<=(\{|,))\h*"?(?<KEY>[^:"]+)"?\h*:\h*"?(?<VALUE>[^",}]+)"?~', $this->pattern, $matches_pattern)) {
			

			foreach ($matches_pattern['KEY'] AS $key => $val) {
				
				$matched_key = $matches_pattern['KEY'][$key];
				$matched_val = $matches_pattern['VALUE'][$key];
				
				print '<br>KEY: '.$matched_key;
				print '<br>VAL: '.$matched_val;
				
				if (is_numeric($matched_val)) $matched_val = (int) $matched_val;
				
				$pattern_array[$matched_key] = $matched_val;
				

			}
			
			
		}



		return $pattern_array;



	}






	public function mql_find_helper_operations() {
		
	}






	// PASSWORD REQUIRES PERCENT-ENCODED REPLACEMENTS
	// ??? CAN WE JUST USE `url_encode` HERE ???
	public function percent_encode_password_special_chars($db_password) {



		$db_password = preg_replace('~:~',  '%3A', $db_password);
		$db_password = preg_replace('~/~',  '%2F', $db_password);
		$db_password = preg_replace('~\?~', '%3F', $db_password);
		$db_password = preg_replace('~#~',  '%23', $db_password);
		$db_password = preg_replace('~\[~', '%5B', $db_password);
		$db_password = preg_replace('~\]~', '%5D', $db_password);
		$db_password = preg_replace('~@~',  '%40', $db_password);



		return $db_password;



	}



}