<?php

namespace Devsmo;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Devsmo\Exceptions\InvalidCenturyCharacterException;
use Devsmo\Exceptions\InvalidControllerCharacterException;
use Devsmo\Exceptions\InvalidDayException;
use Devsmo\Exceptions\InvalidLenghtException;
use Devsmo\Exceptions\InvalidMonthException;
use Devsmo\Exceptions\InvalidYearException;

class Hetu {


    public const FEMALE = 'female';
    public const MALE = 'male';

	public string $hetu;
	public $parts = null;

    public Carbon $dateOfBirth;

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
			throw new InvalidLenghtException();
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
			throw new InvalidDayException();
		}
		
		if ( $this->parts->mm < 1 || $this->parts->mm > 12 ) {
			throw new InvalidMonthException();
		}
		
		if ( !is_numeric($this->parts->yy) ) {
			throw new InvalidYearException();
		}

		if ( !$this->getCentury() ) {
			throw new InvalidCenturyCharacterException();
		}

		if ( $this->getValidationKey() != $this->parts->checksum ) {
			throw new InvalidControllerCharacterException();
		}


        $this->dateOfBirth = Carbon::create(($this->getCentury()+(int)$this->parts->yy), $this->parts->mm, $this->parts->dd);
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
	 * @return String yyyy-mm-dd
	 */
	public function getDateStr(): string {
		return $this->dateOfBirth->format('Y-m-d');
	}

    /**
     * @param CarbonInterface|null $date Optional date for comparison.
     * @return int The person's age in years
     */
	public function getAge(?CarbonInterface $date = null): int {
        if (is_null($date)) {
            $date = Carbon::today()->toImmutable();
        }
		return $this->dateOfBirth->diff($date)->y;
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
			case 0: return self::FEMALE;
			case 1: return self::MALE;
		}
	}
}
