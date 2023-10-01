<?php

namespace AntonioPrimera\ChangeDocuments\Models;

use AntonioPrimera\ChangeDocuments\Models\Traits\WithChangeHistory;
use Illuminate\Contracts\Auth\Authenticatable;

class ActorChangeHistory
{
	use WithChangeHistory;
	
	protected Authenticatable $user;
	
	public function __construct(Authenticatable $user)
	{
		$this->user = $user;
	}
	
	public function fetch(): static
	{
		$this->changeDocuments = ChangeDocument::where('changed_by', $this->user->getAuthIdentifier())->get();
		$this->changeDocuments->sortBy('created_at');
		return $this;
	}
	
	//--- Getters -----------------------------------------------------------------------------------------------------
	
	public function getUser(): Authenticatable
	{
		return $this->user;
	}
	
	public function getModelHistory(): array
	{
		return $this->createChangeHistory(
			fn(ChangeDocument $changeDocument) => [
				'model_id' => $changeDocument->model_id,
				'model_type' => $changeDocument->model_type,
				'attribute_changes' => $changeDocument->changes,
			]
		);
	}
}