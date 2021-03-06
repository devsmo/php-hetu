<?php
namespace devsmo;

class Hetu {

	public $hetu = null;
	public $parts = null;


	/**
	 * create()
	 * Shortcut for initializing the Hetu class
	 * @param String a hetu string
	 * @return Hetu object or null if invalid
	 */
	static public function create($hetu) {
		try {
			$hetu = new self($hetu);
		} catch (\InvalidArgumentException $e) {
			return null;
		}
		return $hetu;
	}


	public function __construct($hetu) {
		$this->hetu = $hetu;

		if ( strlen($this->hetu) != 11 ) {
			throw new \InvalidArgumentException("A hetu is always 11 characters long");
		}

		// Split hetu into it's building blocks
		$this->parts = new \stdClass();
		$this->parts->dd = substr($hetu, 0, 2); // we dont type-cast to int,
		$this->parts->mm = substr($hetu, 2, 2); // we need the leading zero
		$this->parts->yy = substr($hetu, 4, 2); // php deals with numeric strings fine
		$this->parts->century_code = strtoupper(substr($hetu, 6, 1));
		$this->parts->id = (int) ($this->parts->dd . $this->parts->mm . $this->parts->yy . substr($hetu, 7, 3));
		$this->parts->checksum = strtoupper(substr($hetu, 10, 1));

		if ( $this->parts->dd < 1 || $this->parts->dd > 31 ) {
			throw new \InvalidArgumentException("Invalid day of the month");
		}
		
		if ( $this->parts->mm < 1 || $this->parts->mm > 12 ) {
			throw new \InvalidArgumentException("Invalid month");
		}
		
		if ( !is_numeric($this->parts->yy) ) {
			throw new \InvalidArgumentException("Invalid year");
		}

		if ( !$this->getCentury() ) {
			throw new \InvalidArgumentException("Invalid century character");
		}

		if ( $this->getValidationKey() != $this->parts->checksum ) {
			throw new \InvalidArgumentException("Invalid hetu, the control character is wrong");
		}
	}
	

	/** 
	 * getValidationKey
	 * Calculate the validation key for this hetu
	 * @return Char, 0-9A-Y or null on failure
	 */
	public function getValidationKey() {
		$validation_keys = str_split('0123456789ABCDEFHJKLMNPRSTUVWXY');
		$hetu_key = $this->parts->id % 31;

		if ( isset($validation_keys[$hetu_key]) ) {
			return $validation_keys[$hetu_key]; 
		}
		return null;
	}

	/** 
	 * getDateStr
	 * Get date string. 
	 * @todo Should this be a date object instead?
	 * @return String yyyy-mm-dd
	 */
	public function getDateStr() {
		return ($this->getCentury()+$this->parts->yy) ."-". str_pad($this->parts->mm, 2, "0", STR_PAD_LEFT) ."-". str_pad($this->parts->dd, 2, "0", STR_PAD_LEFT) ;
	}

	/** 
	 * getAge
	 * Get the age of the person based on the hetu. 
	 * @param String, A date formated YYYY-MM-DD
	 * @return Int, age
	 */
	public function getAge($date='today') {
		$birthday =new \DateTime($this->getDateStr());
		$today = new \DateTime($date);

		return $birthday->diff($today)->y;
	}

	/**
	 * getCentury
	 * Get century based on the century char in the hetu
	 * @return Int, Century in four digit format
	 */
	public function getCentury() {
		switch( $this->parts->century_code ) {
			case '+': return 1800;
			case '-': return 1900;
			case 'A': return 2000;
			default: return null;
		}
	}

	/**
	 * getGender
	 * Get the sex based on the hetu
	 * @return String, 'male' or 'female'
	 */
	public function getGender() {
		switch ( $this->parts->id & 1 ) {
			case 0: return "female";
			case 1: return "male";
		}
	}
}
