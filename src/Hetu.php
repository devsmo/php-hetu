<?php
namespace devsmo;

class Hetu {
	/** Parse hetu string to an object */
	public $hetu = null;
	public $parts = null;


	public function __construct($hetu) {

		$this->reset();

		if ( strlen($hetu) != 11 ) {
			return;
		}

		$this->parts->dd = substr($hetu, 0, 2);
		$this->parts->mm = substr($hetu, 2, 2);
		$this->parts->yy = substr($hetu, 4, 2);
		$this->parts->century_code = strtoupper(substr($hetu, 6, 1));

		$this->parts->id = (int) ($this->parts->dd . $this->parts->mm . $this->parts->yy . substr($hetu, 7, 3));
		$this->parts->checksum = strtoupper(substr($hetu, 10, 1));
		$this->hetu = $hetu;

		if ( $this->parts->dd < 1 || $this->parts->dd > 31 ) {
			$this->reset();
			return;
		}
		
		if ( $this->parts->mm < 1 || $this->parts->mm > 12 ) {
			$this->reset();
			return;
		}
		
		if ( !is_numeric($this->parts->yy) ) {
			$this->reset();
			return;
		}

		if ( !$this->getpartsCentury() ) {
			$this->reset();
			return;
		}

		// FIXME: We need to validate the "ID" and make sure CHECKSUM is set
	}
	

	public function reset() {
		$this->hetu = null; 
		$this->parts = (object) [];
	}



	/** Check hetu from a string */
	public function isValid() {

		if( !$this->hetu || !$this->parts->checksum ) {
			return false;
		}

		return $this->getValidationKey() == $this->parts->checksum;
	}

	/** Calculate check sum for finnish hetu ID object */
	public function getValidationKey() {
		$validation_keys = str_split('0123456789ABCDEFHJKLMNPRSTUVWXY');
		$hetu_key = $this->parts->id % 31;

		if ( isset($validation_keys[$hetu_key]) ) {
			return $validation_keys[$hetu_key]; 
		}
		return null;
	}

	/** 
	 * Get date string. 
	 * @todo Should this be a date object instead?
	 * @return String yyyy-mm-dd
	 */
	public function getDateStr() {

		if ( !$this->hetu ) {
			return null;
		}

		return ($this->getpartsCentury()+$this->parts->yy) ."-". str_pad($this->parts->mm, 2, "0", STR_PAD_LEFT) ."-". str_pad($this->parts->dd, 2, "0", STR_PAD_LEFT) ;
	}


	/** 
	 * Get date string. 
	 * @todo Should this be a date object instead?
	 * @return String yyyy-mm-dd
	 */
	public function getAge() {

		if ( !$this->hetu ) {
			return null;
		}


		$birthday =new \DateTime($this->getDateStr());
		$today = new \DateTime('today');

		return $birthday->diff($today)->y;
	}



	public function getpartsCentury() {
		if ( !$this->hetu ) {
			return null;
		}

		switch( $this->parts->century_code ) {
			case '+': return 1800;
			case '-': return 1900;
			case 'A': return 2000;
			default: return null;
		}
	}

	/** Parse sex */
	public function getGender() {

		if ( !$this->hetu ) {
			return null;
		}

		//$geneder_code = (int) substr($this->hetu, 9, 1);
		
		switch ( $this->parts->id & 1 ) {
			case 0: return "female";
			case 1: return "male";
		}
	}
}
