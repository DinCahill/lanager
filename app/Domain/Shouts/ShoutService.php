<?php namespace Zeropingheroes\Lanager\Domain\Shouts;

use Zeropingheroes\Lanager\Domain\ResourceService;
use Zeropingheroes\Lanager\Domain\ServiceFilters\FilterableByCreatedAt;
use Zeropingheroes\Lanager\Domain\ServiceFilters\FilterableByUser;
use Zeropingheroes\Lanager\Domain\AuthorisationException;
use DomainException;
use Carbon\Carbon;

class ShoutService extends ResourceService {

	protected $orderBy = [ [ 'pinned', 'desc' ], [ 'created_at', 'desc' ] ];

	protected $eagerLoad = [ 'user.roles', 'user.state.application' ];

	use FilterableByCreatedAt;

	use FilterableByUser;

	public function __construct()
	{
		parent::__construct(
			new Shout,
			new ShoutValidator
		);
	}

	public function store( $input )
	{
		$input['user_id'] = $this->user->id();

		return parent::store( $input );
	}

	protected function readAuthorised()
	{
		return true;
	}

	protected function storeAuthorised()
	{
		return $this->user->isAuthenticated();
	}

	protected function updateAuthorised()
	{
		return $this->user->isAuthenticated();
	}

	protected function destroyAuthorised()
	{
		return $this->user->isAuthenticated();
	}

	protected function rulesOnStore( $input )
	{
		if ( ! $this->user->hasRole( 'Shouts Admin' ) )
		{
			if ( isset( $input['pinned'] ) )
				throw new AuthorisationException( 'You must be a shouts admin to pin shouts' );
		}

		$past = (new Carbon)->subSeconds(15);

		$recentShouts = ( new self )->filterCreatedAfter( $past )->filterByUser( $input['user_id'] )->all();

		if ( $recentShouts->count() != 0 )
			throw new DomainException( 'You have posted too recently - please wait a while and try again' );
	}

	protected function rulesOnUpdate( $input, $original )
	{
		if ( ! $this->user->hasRole( 'Shouts Admin' ) )
		{
			if ( $input['user_id'] != $this->user->id() )
				throw new AuthorisationException( 'You may only edit your own shouts' );

			if ( $input['pinned'] != $original['pinned'] )
				throw new AuthorisationException( 'You must be a shouts admin to pin shouts' );
		}
	}

	protected function rulesOnDestroy( $input )
	{
		if ( ! $this->user->hasRole( 'Shouts Admin' ) )
		{
			if ( $input['user_id'] != $this->user->id() )
				throw new AuthorisationException( 'You may only delete your own shouts' );
		}
	}

}