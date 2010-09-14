<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package  Benchmarking
 * @author   Geert De Deckere <geert@idoe.be>
 */
class Bench_LtrimDigits extends Codebench {

	public $description = 'Chopping off leading digits: regex vs ltrim.';

	public $loops = 100000;

	public $subjects = array
	(
		'123digits',
		'no-digits',
	);

	public function bench_regex($subject)
	{
		return preg_replace('/^\d+/', '', $subject);
	}

	public function bench_ltrim($subject)
	{
		return ltrim($subject, '0..9');
	}
}