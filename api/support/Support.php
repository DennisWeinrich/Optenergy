<?php 
require_once __DIR__ . '/../user/Nutzerverwaltung.php';
use PHPMailer\PHPMailer\PHPMailer;

class Support {
	
	private $mailer;
	private $nv;
	
	public function __construct() {
		$this->nv = new Nutzerverwaltung();
		//initialisere SMTP Mail
		$this->mailer= new PHPMailer();
		
		$this->mailer->IsSMTP(true);
		$this->mailer->Host = "mail.your-server.de";
		$this->mailer->SMTPAuth = true;
		$this->mailer->Username = "optenergy@immotickety.de";
		$this->mailer->Password = "passwort";
		$this->mailer->SetFrom("optenergy@immotickety.de", "OPTENERGY");
		$this->mailer->CharSet = "UTF-8";
	}
	
	public function sendMail($empfaenger, $betreff, $text) {
		$user = $this->nv->getUser();
		$this->mailer->AddAddress("optenergy@immotickety.de", "OPTENERGY");
		
		$this->mailer->Subject = "[SUPPORT ANFRAGE] " . $betreff;
		$this->mailer->Body = "Anfrage von: " . $user['Vorname'] . " " . $user['Nachname'] . ", E-Mail: " . (($empfaenger == null) ? $user['Email']:$empfaenger) . ", Text: " . $text;
		
		if(!$this->mailer->Send()) {
			http_response_code(500);
			die();
		}  
		
		$this->mailer->clearAddresses(); 
		$this->mailer->AddAddress($empfaenger == null ? $user['Email']:$empfaenger, $user['Vorname'] . " " . $user['Nachname'] );
		$this->mailer->isHTML(true);
		
		$this->mailer->Subject = "[OPTENERGY SUPPORT] " . $betreff;
		$text = "Hallo " . $user['Vorname'] . ", <br><p>
								vielen Dank für deine Support-Anfrage. Wir haben deine Anfrage erhalten und möchten dir versichern, dass wir uns so schnell wie möglich um dein Anliegen kümmern werden.
								Wir legen großen Wert auf die Zufriedenheit unserer Kunden und werden alles tun, um dir bestmöglich weiterzuhelfen. </p>
								<p>Bitte habe etwas Geduld, während wir dein Anfrage bearbeiten. Je nach Komplexität kann die Bearbeitungszeit variieren.
								Wir werden uns jedoch schnellstmöglich bei dir melden, um weitere Informationen zu erfragen oder dir eine Lösung anzubieten.</p>
								<p>In der Zwischenzeit bitten wir dich, Ihre E-Mails im Auge zu behalten, da wir möglicherweise Rückfragen haben oder dir Updates zu deinem Anliegen senden wollen.
								Vielen Dank für dein Verständnis und deine Geduld. </p>
								<br>
								Freundliche Grüße
								<p>
								Dein OPTENERGY Support-Team </p> <hr> <br>" . $text;
		
		if(!$this->mailer->Send()) {
			http_response_code(500);
			die();
		} else {
			http_response_code(200);
		} 
	}
	
}

?>