<?php

namespace AntonioPrimera\ChangeDocuments\Models;

use AntonioPrimera\ChangeDocuments\Models\Traits\WithChangeHistory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ModelChangeHistory
{
	use WithChangeHistory;
	
	protected Model|null $model;
	protected array $modelIdentifier;
	
	public function __construct(Model|array $model)
	{
		$this->model = $model instanceof Model ? $model : null;
		
	}
	
	public function fetch(): static
	{
		$this->changeDocuments = ChangeDocument::where('model_type', $this->model->getMorphClass())
			->where('model_id', $this->model->getKey())
			->get();
		
		$this->changeDocuments->sortBy('created_at');
		
		return $this;
	}
	
	//--- Getters -----------------------------------------------------------------------------------------------------
	
	public function getModel(): Model
	{
		return $this->model;
	}
	
	public function getAttributeHistory(string $attributeName): array
	{
		return $this->createChangeHistory(
			fn(ChangeDocument $changeDocument) => $changeDocument->changes->getNewValue($attributeName),
			fn(ChangeDocument $changeDocument) => $changeDocument->changes->getOldValue($attributeName)
		);
	}
}