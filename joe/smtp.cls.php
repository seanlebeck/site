<?php


class Smtp {

    var $host = "localhost";
    var $port = 25;
    var $authenticate = FALSE;
    var $username = "";
    var $password = "";
    
    var $smtp = null;
    var $connected = false;
    var $timeout = 30;
    var $error;
    
    var $MAX_CHARS_PER_LINE = 100;
    var $NEW_LINE = "\r\n";
    var $DEBUG = false;
    
    function send_mail($to, $subject, $message, $from, $charset = "UTF-8", $html = false, $attach = array(), $headers = array()) {
        $success = true;
        $disconnect_on_exit = false;
		$mime_boundary = $this->_generate_boundary();
		
		if (empty($headers)) {
			$headers = array();
		}

        $to = $this->_parse_address($to);
        $headers[] = "To: {$to[1]} <{$to[0]}>";
        $to_address = $to[0];
        
        $from = $this->_parse_address($from);
        $headers[] = "From: {$from[1]} <{$from[0]}>";
        $from_address = $from[0];

        $headers[] = "Subject: {$subject}";
		$headers[] = "Date: " . date("r");
		$headers[] = "Message-ID: " . $this->_generate_id();
		$headers[] = "MIME-Version: 1.0";
		$headers[] = "Content-Type: multipart/mixed;\n\tboundary=\"" . $mime_boundary . "\"";
		
        $message_data = $this->_compose($headers, $message, $charset, $html, $attach, $mime_boundary);
        
        # Connect.
        if (!$this->connected) {
            $success &= $this->connect();
            $disconnect_on_exit = true;
        }
        
        if ($this->connected) {
            
            # Send the SMTP commands in sequence.
            $success &= $this->_helo();
            
            if ($this->authenticate) {
                $success &= $this->_auth();
            }
            
            $success &= $this->_mail($from_address);
            $success &= $this->_rcpt($to_address);
            $success &= $this->_data($message_data);
            
            # All done.
            $error = $this->error;
            $this->_quit();
            
            if ($disconnect_on_exit) {
                $this->disconnect();
            }   
        }
        
        if (!$success) {
            $this->error = $error;
        }
        
        return $success;
    }
        
    function connect() {
        $this->smtp = fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout);
        
        if (!empty($this->smtp)) {
            $reply = $this->_get_reply();
            $this->connected = true;
        
        } else {
            $this->connected = false;
            $this->error = array("Failed to connect.", $errno, $errstr);
            $this->_debug($this->error);            
        }
        
        return $this->connected;
    }
    
    function disconnect() {
        if (!empty($smtp)) {
            fclose($smtp);
            $this->connected = false;
        }
    }
    
    function _compose($headers, $body, $charset, $html, $attach, $mime_boundary) {
        $data = "";
        
        # Process headers.
        if (is_array($headers) && count($headers) > 0) {

            foreach ($headers as $header) {
                $data .= $header;
 				$data .= $this->NEW_LINE;
            }
            
            $data .= $this->NEW_LINE;
        }
        
        # Normalize new lines.
        $body_data = $body;
        $body_data = str_replace("\r\n", "\n", $body_data);
        $body_data = str_replace("\r", "\n", $body_data);
        
        # Now wrap lines.
        $body_data = wordwrap($body_data, $this->MAX_CHARS_PER_LINE, "\n", true);
        $body_data = str_replace("\n.", "\n..", $body_data);
        
        # Change new lines back to the required format.
        $body_data = str_replace("\n", $this->NEW_LINE, $body_data);
        
		# Add the non-MIME text content.
        $data .= "This is a multi-part message in MIME format.";
		$data .= $this->NEW_LINE;
					
		# Now add the MIME body part.
		$mime_type = $html ? "text/html" : "text/plain";
		$data .= $this->NEW_LINE;
		$data .= "--{$mime_boundary}";
		$data .= $this->NEW_LINE;
		$data .= "Content-Type: {$mime_type}; charset=\"{$charset}\"";
		$data .= $this->NEW_LINE;
		$data .= "Content-Transfer-Encoding: 8bit";
		$data .= $this->NEW_LINE;
		$data .= $this->NEW_LINE;
		$data .= $body_data;
		$data .= $this->NEW_LINE;
				
		# Attach the files.
		if (is_array($attach)) {
			foreach ($attach as $attach_file) {
			
				$attach_filename = str_replace(array("\"", "\r", "\n"), array("-", "-", "-"), 
						$attach_file['name']);
				$attach_mime_type = $attach_file['mime_type'] ? $attach_file['mime_type'] : 
				        "application/octet-stream";
				$attach_contents = chunk_split(base64_encode(file_get_contents($attach_file['tmp_name'])));
			
				$data .= $this->NEW_LINE;
				$data .= "--{$mime_boundary}";
				$data .= $this->NEW_LINE;
				$data .= "Content-Type: {$attach_mime_type}; name=\"{$attach_filename}\"";
				$data .= $this->NEW_LINE;
				$data .= "Content-Transfer-Encoding: base64";
				$data .= $this->NEW_LINE;
				$data .= "Content-Disposition: attachment; filename=\"{$attach_filename}\"";
				$data .= $this->NEW_LINE;
				$data .= $this->NEW_LINE;
				$data .= $attach_contents;
				$data .= $this->NEW_LINE;
			}
		}
		
		$data .= $this->NEW_LINE;
		$data .= "--{$mime_boundary}--";
		$data .= $this->NEW_LINE;
		
        return $data;
    }
        
    function _helo() {
        $result = false;
        
        if ($this->connected) {
            $reply = $this->_send_command("HELO {$this->host}", 250);
            $result = empty($this->error);
        }
        
        return $result;
    }
    
    function _auth() {
        $result = false;
        
        if ($this->connected) {
        
            $reply = $this->_send_command("AUTH LOGIN", 334);

            if (empty($this->error)) {
                $reply = $this->_send_command(base64_encode($this->username), 334);
            }
            
            if (empty($this->error)) {
                $reply = $this->_send_command(base64_encode($this->password), 235);
            }
            
            if (!empty($this->error)) {
                $this->error[0] = "SMTP error in AUTH";
            }
            
            $result = empty($this->error);
        }
        
        return $result;
    }
    
    function _mail($from) {
        $result = false;
        
        if ($this->connected) {
            $reply = $this->_send_command("MAIL FROM:<{$from}>", 250);
            $result = empty($this->error);
        }
        
        return $result;
    }
    
    function _rcpt($to) {
        $result = false;
        
        if ($this->connected) {
            $reply = $this->_send_command("RCPT TO:<{$to}>", array(250, 251));
            $result = empty($this->error);
        }
        
        return $result;
    }
    
    function _data($data) {
        $result = false;
        
        if ($this->connected) {
            $reply = $this->_send_command("DATA", 354);

            if (empty($this->error)) {
                $command = "{$data}{$this->NEW_LINE}{$this->NEW_LINE}.";
                $reply = $this->_send_command($command, 250);
            }
            
            if (!empty($this->error)) {
                $this->error[0] = "SMTP error in DATA";
            }
            
            $result = empty($this->error);
        }
        
        return $result;
    }
    
    function _quit() {
        $result = false;
        
        if ($this->connected) {
            $reply = $this->_send_command("QUIT", 221);
            $result = empty($this->error);
        }
        
        return $result;
    }
    
    function _send_command($command, $success_code = null) {

        $error = null;
                
        if ($this->connected) {
        
            $this->_debug($command);    
            fputs($this->smtp, "{$command}{$this->NEW_LINE}");
            
            $reply = $this->_get_reply();
            $code = substr($reply, 0, 3);
            $message = substr($reply, 3);
            
            if (!empty($success_code)) {
            
                $success = (is_array($success_code)) 
                    ? in_array($code, $success_code) 
                    : ($code == $success_code);
                
                if (!$success) {             
                    $command_name = $this->_first_word($command);
                    $error = array("SMTP error in {$command_name}", $code, $reply);
                }
            }
        }
        
        $this->error = $error;
        return array("code" => $code, "message" => $message, "error" => $error);
    }
    
    function _get_reply() {
        $reply = "";
        
        while ($line = fgets($this->smtp)) {
            $reply .= $line;
            if ($line{3} == " ") break;
        }
        
        $this->_debug($reply);
        return $reply;
    }
    
    function _first_word($str) {
        list($line) = explode("\n", $str);
        list($word) = explode(" ", $line);
        return $word;
    }
    
    function _debug($message) {
        if ($this->DEBUG) {
            print_r($message);
            echo "\n";
        }
    }

	function _generate_id() {
		return generateMessageID();
	}
	
	function _generate_boundary() {
		return "<<<-=-=-[snet.clf.".md5(time())."]-=-=->>>";
	}
	
	function _parse_address($address) {
	    $parsed_address = null;
	    
	    if (is_array($address)) {
	        $parsed_address = $address;
	    
	    } else {
    	    $match = preg_match("/(.*)(\s+)?<(.*)>/U", $address, $parts);
    	    $parsed_address = $match ? array($parts[3], $parts[1]) : array($address, $address);
    	    
    	}
    	
	    return $parsed_address;
	}
}


if (!function_exists("generateMessageID"))
{
	function generateMessageID($prefix="40ftrq") {
	    $message_id = "<$prefix." 
	        . base_convert((double)microtime(), 10, 36) 
	        . "." . base_convert(time(), 10, 36) 
	        . "@" . $_SERVER['HTTP_HOST'] . ">";
	    return $message_id;
	}
}

