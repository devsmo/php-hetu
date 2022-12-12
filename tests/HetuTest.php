<?php

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Devsmo\Hetu;

class HetuTest extends TestCase
{

	public function validTestSet()
	{
		// Valid sets (all values are valid)
		yield '211097-9476' => ['211097-9476', Hetu::MALE, '1997-10-21', 20];
		yield '210202A992N' => ['210202A992N', Hetu::FEMALE, '2002-02-21', 15];
		// https://dvv.fi/hetu-uudistus
		yield '010594Y9032' => ['010594Y9032' , Hetu::MALE, '1994-05-01', 23]; // Antti - kuollut
		yield '010594Y9021' => ['010594Y9021' , Hetu::FEMALE, '1994-05-01', 23]; // Ëìñî Úllãstiina
		yield '020594X903P' => ['020594X903P' , Hetu::MALE, '1994-05-02', 23]; // Eppu
		yield '020594X902N' => ['020594X902N' , Hetu::FEMALE, '1994-05-02', 23]; // Eppu
		yield '030594W903B' => ['030594W903B' , Hetu::MALE, '1994-05-03', 23]; // Eikka
		yield '030694W9024' => ['030694W9024' , Hetu::FEMALE, '1994-06-03', 23]; // Erika
		yield '040594V9030' => ['040594V9030' , Hetu::MALE, '1994-05-04', 23]; // Arska
		yield '040594V902Y' => ['040594V902Y' , Hetu::FEMALE, '1994-05-04', 23]; // Pike
		yield '050594U903M' => ['050594U903M' , Hetu::MALE, '1994-05-05', 23]; // Luca
		yield '050594U902L' => ['050594U902L' , Hetu::FEMALE, '1994-05-05', 23]; // Siru
		yield '010516B903X' => ['010516B903X' , Hetu::MALE, '2016-05-01', 1]; // Elias
		yield '010516B902W' => ['010516B902W' , Hetu::FEMALE, '2016-05-01', 1]; // Siiri
		yield '020516C903K' => ['020516C903K' , Hetu::MALE, '2016-05-02', 1]; // Erkki
		yield '020516C902J' => ['020516C902J' , Hetu::FEMALE, '2016-05-02', 1]; // Elina
		yield '030516D9037' => ['030516D9037' , Hetu::MALE, '2016-05-03', 1]; // Antti
		yield '030516D9026' => ['030516D9026' , Hetu::FEMALE, '2016-05-03', 1]; // Saimi
		yield '010501E9032' => ['010501E9032' , Hetu::MALE, '2001-05-01', 16]; // Jean
		yield '020502E902X' => ['020502E902X' , Hetu::FEMALE, '2002-05-02', 15]; // Susanna
		yield '020503F9037' => ['020503F9037' , Hetu::MALE, '2003-05-02', 14]; // Hannes
		yield '020504A902E' => ['020504A902E' , Hetu::FEMALE, '2004-05-02', 13]; // (passiivi)
		yield '020504B904H' => ['020504B904H' , Hetu::FEMALE, '2004-05-02', 13]; // Anne
		
	}
	
	/**
	 * @dataProvider validTestSet
	 */
	public function testValidity($hetu, $gender, $birthday, $age)
	{
		$instance = Hetu::create($hetu);
		$this->assertNotNull($instance);
	}

	/**
	 * @dataProvider validTestSet
	 */
	public function testGender($hetu, $gender, $birthday, $age)
	{
		$instance = Hetu::create($hetu);
		$this->assertEquals($gender, $instance->getGender());
	}

	/**
	 * @dataProvider validTestSet
	 */
	public function testBirthday($hetu, $gender, $birthday, $age)
	{
		$instance = Hetu::create($hetu);
		$this->assertEquals($birthday, $instance->getDateStr());
	}

	/**
	 * @dataProvider validTestSet
	 */
	public function testAge($hetu, $gender, $birthday, $age)
	{
		$instance = Hetu::create($hetu);
		$this->assertEquals($age, $instance->getAge(Carbon::parse('2018-02-02')));
	}


	public function invalidTestSet()
	{
		return array(
			// Valid sets (none of the values are valid)
			array('211096-9476'),
			array('010202-992N'),
			array('210202-992Nsd'),
		);
	}
	
	/**
	 * @dataProvider invalidTestSet
	 */
	public function testInvalidHetu($hetu)
	{
		$instance = Hetu::create($hetu);
		$this->assertNull($instance);
	}





}
