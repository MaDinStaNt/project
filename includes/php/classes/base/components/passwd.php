<?php
define("CLASS_VOWELS","aoueiy");
define("CLASS_CONSONANTS","bcdfghjklmnpqrstvwxz");

define("CLASS_PW_CHARS","0123456789" . CLASS_VOWELS . CLASS_CONSONANTS . strtoupper(CLASS_VOWELS) . strtoupper(CLASS_CONSONANTS) . "./");
define("CLASS_SALT_CHARS","8"); // Change this to 2 if you want to make DES encrypted passwords

class Passwd {
	
	function makeSalt() {
		mt_srand ((double) microtime() * 10000000);
		
		$class_pw_chars = CLASS_PW_CHARS;
		$class_salt_chars = CLASS_SALT_CHARS;
		
		$i = 0;
		$salt = "";
		while ($i != $class_salt_chars) {
			$rand = mt_rand(0, (strlen($class_pw_chars) - 1));
			$rand_keys = $class_pw_chars{$rand};
			
			if (($rand % 2) == 1) {
				$salt .= strtoupper($rand_keys);
			} else {
				$salt .= $rand_keys;
			}
			
			$i++;
		}
		
		return $salt;
	}
	
	function createPasswd($data) {
		// Generate an 8 character long password.
		// The password should be pronouncable but yet not a dictionary word.
		
		$class_vowels = CLASS_VOWELS;
		$class_consonants = CLASS_CONSONANTS;
		$pwlength = 8;
		$i = 0;
		
		mt_srand ((double) microtime() * 10000000);
		
		$vowelNext = 0;
		
		while ($i != $pwlength) {
			if ($vowelNext) {
				$rand = mt_rand(0, (strlen($class_vowels) - 1));

				if ($data == "nice") {
					$password .= $class_vowels{$rand};
					$vowelNext = 0;
				} else {
					if (($rand % 2) == 1) {
						$password .= strtoupper($class_vowels{$rand});
					} else {
						$password .= $class_vowels{$rand};
					}
					$vowelNext = rand(0,1);
				}
			} else {
				$rand = mt_rand(0, (strlen($class_consonants) - 1));
				if ($data == "nice") {
					$password .= $class_consonants{$rand};
					$vowelNext = 1;
				} else {
					if (($rand % 2) == 1) {
						$password .= strtoupper($class_consonants{$rand});
					} else {
						$password .= $class_consonants{$rand};
					}
					$vowelNext = rand(0,1);
				}
			}
			$i++;
		}
		
		return $password;
	}
	
	function encryptPasswd($passwd) {
		if (CLASS_SALT_CHARS == 8) {
			$salt = "$1$" . $this->makeSalt() . "\$";
		} else {
			$salt = $this->makeSalt();
		}

		return crypt( $passwd, $salt );
	}
		
	function getSalt($encPasswd) {
		if (strstr($encPasswd, "$1$")) {
			$tmp = substr( $encPasswd , 3 , 8 );
			$salt = "$1$" . $tmp . "$"; 
		} else {
			$salt = substr( $encPasswd , 0 , 2 ); 
		}
		
		return $salt;
	}

	function checkPasswd($passwd, $encPasswd) {
		$salt = $this->getSalt($encPasswd);
		$enc_pw = crypt( $passwd, $salt ); 
		        
		if ( $encPasswd == $enc_pw ) { 
			// The user is authenticated
			return true; 
		} 
	
		// The password didn't match, so send a negative response
		return false;
	}

}
?>