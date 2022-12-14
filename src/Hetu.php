<?php

namespace Devsmo;

use DateTimeImmutable;
use DateTimeInterface;
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

    public DateTimeImmutable $dateOfBirth;

    public const CENTURY_CODES_1800 = [
        '+'
    ];

    public const CENTURY_CODES_1900 = [
        '-', 'Y', 'X', 'W', 'V', 'U'
    ];

    public const CENTURY_CODES_2000 = [
        'A', 'B', 'C', 'D', 'E', 'F'
    ];

    /**
     * create()
     * Shortcut for initializing the Hetu class
     * @param String $hetu a hetu string
     * @return Hetu|null object or null if invalid
     */
	public static function create(string $hetu): ?self {
		try {
			$hetuObject = new self($hetu);
		} catch (\InvalidArgumentException $e) {
			return null;
		}
		return $hetuObject;
	}


	public function __construct(string $hetu) {
		$this->hetu = $hetu;

		if ( strlen($this->hetu) !== 11 ) {
			throw new InvalidLenghtException();
		}

		// Split hetu into it's building blocks
		$this->parts = new \stdClass();
		$this->parts->dd = substr($hetu, 0, 2); // we dont type-cast to int,
		$this->parts->mm = substr($hetu, 2, 2); // we need the leading zero
		$this->parts->yy = substr($hetu, 4, 2); // php deals with numeric strings fine
		$this->parts->century_code = strtoupper($hetu[6]);
		$this->parts->id = (int) ($this->parts->dd . $this->parts->mm . $this->parts->yy . substr($hetu, 7, 3));
		$this->parts->checksum = strtoupper($hetu[10]);

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

		if ( $this->getValidationKey() !== $this->parts->checksum ) {
			throw new InvalidControllerCharacterException();
		}


        $this->dateOfBirth = DateTimeImmutable::createFromFormat('Y-m-d',($this->getCentury()+(int)$this->parts->yy).'-'. $this->parts->mm.'-'.$this->parts->dd);
	}


    /**
     * getValidationKey
     * Calculate the validation key for this hetu
     * @return string|null , 0-9A-Y or null on failure
     */
	public function getValidationKey(): ?string {
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
     * @param DateTimeInterface|bool|null $date Optional date for comparison.
     * @return int The person's age in years
     */
	public function getAge(DateTimeInterface|bool|null $date = null): int {
        if (is_null($date)) {
            $date = DateTimeImmutable::createFromFormat('Y-m-d', date('Y-m-d'));
        }
		return $this->dateOfBirth->diff($date)->y;
	}

    /**
     * getCentury
     * Get century based on the century char in the hetu
     * @return int|null Century in four digit format
     */
	public function getCentury(): ?int {
        if (in_array($this->parts->century_code, self::CENTURY_CODES_1800, true)){
            return 1800;
        }
        if (in_array($this->parts->century_code, self::CENTURY_CODES_1900, true)){
            return 1900;
        }
        if (in_array($this->parts->century_code, self::CENTURY_CODES_2000, true)){
            return 2000;
        }
        return null;
	}

	/**
	 * getGender
	 * Get the sex based on the hetu
	 * @return String 'male' or 'female'
	 */
	public function getGender(): string
    {
		switch ( $this->parts->id & 1 ) {
			case 0: return self::FEMALE;
			case 1: return self::MALE;
		}
	}
}
