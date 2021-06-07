<?php
	class Test {
		//muutujad ehk properties
		private $secret = 7;
		public $non_secret = 3;
		private $received_secret;
		
		//funktsioonid ehk methods
		function __construct($received){
			echo "Klass on laetud! Konstruktor töötab!";
			$this->received_secret = $received; //ees pool olev this ütleb, et tegemist on selle klassi muutujaga
			echo "Saabunud salajane number on ". $this->received_secret . ". ";
			$this->multiplay();
		
		}
		
		function __destruct(){
			echo "Klass lõpetas!";
			
		
		}
		
		public function reveal (){
			echo "Täiesti salajane number on ". $this->secret ." .";
		}
		
		private function multiplay(){
			echo " Korrutis on: " .$this->secret * $this->non_secret * $this->received_secret;
		}
		
		
	}//class lõppeb