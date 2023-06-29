<?php 
require_once __DIR__ . '/user/Nutzerverwaltung.php';

class ErsparnisBerechner {

	private $stromPreis;
	
	public function __construct() {
		$nv = new Nutzerverwaltung();
		$this->stromPreis = $nv->getUser()['Strompreis'];
	}
	
	public function berechneErsparnis($analyse) {
		$ersparnis = 0;
		foreach ($analyse['steckdosen'] as &$sd) {
			foreach($sd['Verbrauch'] as &$v) {
				if($v <= 0.1){
					$ersparnis += $v;
				}
			}
		}
		return round($ersparnis, 2);
	}
		
	public function berechneErsparnisInEuro($ersparnisInkWh) {
		return round($ersparnisInkWh * $this->stromPreis, 2);
	}
}

?>