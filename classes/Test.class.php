<?php
	class Test {
		//muutujuad ehk properties
		private $secret = 7;
		public $non_secret = 3;
		private $received_secret;
		
		//funktsioonid ehk methods
		function __construct() {
			echo "Klass on laetud! Konstrukotr töötab!";
			$this->received_secret = $recived;
			echo "Saabunud salajane number on " .$this.received_secret .". ";
		}
		
		function __destruct(){
			echo "Klass lõpetas!";
		}
		
		public function reveal(){
			echo "Täiesti salajane number on ". $this->secret .". ";
		}
		private function multiplay(){
			echo " Korrutis on: " .$this->secret * $this->non_secret .". ";
		}
	} // class lõppeb