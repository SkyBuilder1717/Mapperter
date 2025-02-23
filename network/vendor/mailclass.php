<?php
class SendMail {
    public $smtp_username;
    public $smtp_password;
    public $smtp_host;
    public $smtp_from;
    public $smtp_port;
    public $smtp_charset;
	public $boundary;
    public $addFile = false;
    public $multipart;

    public function __construct($smtp_username, $smtp_password, $smtp_host, $smtp_port = 25, $smtp_charset = "utf-8") {
        $this->smtp_username = $smtp_username;
        $this->smtp_password = $smtp_password;
        $this->smtp_host = $smtp_host;
        $this->smtp_port = $smtp_port;
        $this->smtp_charset = $smtp_charset;

		$this->boundary = "--".md5(uniqid(time()));
		$this->multipart = "";
    }
    function send($mailTo, $subject, $message, $smtp_from) {
		$contentMail = $this->getContentMail($subject, $message, $smtp_from,$mailTo);

        try {
            if(!$socket = @fsockopen($this->smtp_host, $this->smtp_port, $errorNumber, $errorDescription, 30)){
                throw new Exception($errorNumber.".".$errorDescription);
            }
            if (!$this->_parseServer($socket, "220")){
                throw new Exception('Connection error');
            }

			$server_name = $_SERVER["SERVER_NAME"];
            fputs($socket, "EHLO $server_name\r\n");
			if(!$this->_parseServer($socket, "250")){
				fputs($socket, "HELO $server_name\r\n");
				if (!$this->_parseServer($socket, "250")) {
					fclose($socket);
					throw new Exception('Error of command sending: HELO');
				}
			}

            fputs($socket, "AUTH LOGIN\r\n");
            if (!$this->_parseServer($socket, "334")) {
                fclose($socket);
                throw new Exception('Autorization error 1');
            }

            fputs($socket, base64_encode($this->smtp_username) . "\r\n");
            if (!$this->_parseServer($socket, "334")) {
                fclose($socket);
                throw new Exception('Autorization error 2');
            }

            fputs($socket, base64_encode($this->smtp_password) . "\r\n");
            if (!$this->_parseServer($socket, "235")) {
                fclose($socket);
                throw new Exception('Autorization error 3');
            }

            fputs($socket, "MAIL FROM: <".$this->smtp_username.">\r\n");
            if (!$this->_parseServer($socket, "250")) {
                fclose($socket);
                throw new Exception('Error of command sending: MAIL FROM');
            }

			$mailTo = str_replace(" ", "", $mailTo);
			$emails_to_array = explode(',', $mailTo);


			foreach($emails_to_array as $email) {
				fputs($socket, "RCPT TO: <{$mailTo}>\r\n");
				if (!$this->_parseServer($socket, "250")) {
					fclose($socket);
					throw new Exception('Error of command sending: RCPT TO');
				}
			}


            fputs($socket, "DATA\r\n");
            if (!$this->_parseServer($socket, "354")) {
                fclose($socket);
                throw new Exception('Error of command sending: DATA');
            }

            fputs($socket, $contentMail."\r\n.\r\n");
            if (!$this->_parseServer($socket, "250")) {
                fclose($socket);
                throw new Exception("E-mail didn't sent");
            }

            fputs($socket, "QUIT\r\n");
            fclose($socket);
        } catch (Exception $e) {
            return  $e->getMessage();
        }
        return true;
    }

	public function addFile($path){
		$file = @fopen($path, "rb");
		if(!$file) {
			throw new Exception("File `{$path}` didn't open");
		}
		$data = fread($file,  filesize( $path ) );
		fclose($file);
		$filename = basename($path);
		$multipart .=  "\r\n--{$this->boundary}\r\n";
		$multipart .= "Content-Type: application/octet-stream; name=\"$filename\"\r\n";
		$multipart .= "Content-Transfer-Encoding: base64\r\n";
		$multipart .= "Content-Disposition: attachment; filename=\"$filename\"\r\n";
		$multipart .= "\r\n";
		$multipart .= chunk_split(base64_encode($data));

		$this->multipart .= $multipart;
		$this->addFile = true;
	}

    private function _parseServer($socket, $response) {
        while (@substr($responseServer, 3, 1) != ' ') {
            if (!($responseServer = fgets($socket, 256))) {
                return false;
            }
        }
        if (!(substr($responseServer, 0, 3) == $response)) {
            return false;
        }
        return true;
    }

	private function getContentMail($subject, $message, $smtp_from,$mailTo){
		if( strtolower($this->smtp_charset) == "windows-1251" ){
			$subject = iconv('utf-8', 'windows-1251', $subject);
		}
        $contentMail = "Date: " . date("D, d M Y H:i:s") . " UT\r\n";
        $contentMail .= 'Subject: =?' . $this->smtp_charset . '?B?'  . base64_encode($subject) . "=?=\r\n";

		$headers = "MIME-Version: 1.0\r\n";

		if($this->addFile){
			$headers .= "Content-Type: multipart/mixed; boundary=\"{$this->boundary}\"\r\n";
		}else{
			$headers .= "Content-type: text/html; charset={$this->smtp_charset}\r\n";
		}
		$headers .= "From: {$smtp_from[0]} <{$smtp_from[1]}>\r\n";
        $headers.= "To: ".$mailTo."\r\n";
        $contentMail .= $headers . "\r\n";

		if($this->addFile){
			$multipart  = "--{$this->boundary}\r\n";
			$multipart .= "Content-Type: text/html; charset=utf-8\r\n";
			$multipart .= "Content-Transfer-Encoding: base64\r\n";
			$multipart .= "\r\n";
			$multipart .= chunk_split(base64_encode($message));

			$multipart .= $this->multipart;
			$multipart .= "\r\n--{$this->boundary}--\r\n";

			$contentMail .= $multipart;
		}else{
			$contentMail .= $message ."\r\n";
		}

		if( strtolower($this->smtp_charset) == "windows-1251" ){
			$contentMail = iconv('utf-8', 'windows-1251', $contentMail);
		}

		return $contentMail;
	}

}
?>