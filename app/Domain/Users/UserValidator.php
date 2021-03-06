<?php namespace Zeropingheroes\Lanager\Domain\Users;

use Fadion\ValidatorAssistant\ValidatorAssistant;
use Zeropingheroes\Lanager\Domain\InputValidatorContract;

class UserValidator extends ValidatorAssistant implements InputValidatorContract {

	/**
	 * Validation rules to enforce for each field
	 * @var array
	 */
	protected $rules = [
		'username'			=> 'required|max:32',
		'steam_id_64'		=> 'required|max:17',
		'steam_visibility'	=> 'required|in:0,1,2,3',
		'ip'				=> 'ip',
		'avatar'			=> 'url',
		'visible'			=> 'boolean',
	];

}