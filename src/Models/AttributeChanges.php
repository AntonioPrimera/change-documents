<?php

namespace AntonioPrimera\ChangeDocuments\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeChanges
{
	const VALUE_UNKNOWN = '#U!';
	
	public array $changes;
	
	public function __construct(array $changes = [])
	{
		$this->changes = $changes;
	}
	
	//--- API ---------------------------------------------------------------------------------------------------------
	
	public function addChange(string $attribute, mixed $oldValue, mixed $newValue): static
	{
		if ($oldValue !== $newValue)
			$this->changes[$attribute] = [
				'o' => $oldValue,
				'n' => $newValue
			];
		
		return $this;
	}
	
	public function fromModel(Model $model): static
	{
		//clean and new models don't have any changes
		if (!$model->exists)
			return $this;
		
		//if the model was just created, we can get all the attributes as new values
		if ($model->wasRecentlyCreated) {
			foreach ($model->getAttributes() as $attribute => $value)
				if (!in_array($attribute, ['created_at', 'updated_at', 'deleted_at', 'id']))
					$this->addChange($attribute, null, $value);
			
			return $this;
		}
		
		//if the model is dirty, we can get the changed attributes and their old and new values
		if ($model->isDirty()) {
			foreach ($model->getDirty() as $attribute => $newValue)
				$this->addChange($attribute, $model->getOriginal($attribute), $newValue);
			
			return $this;
		}
		
		//if the model was changed, we can only get the changed attributes and their new values
		if ($model->wasChanged()) {
			foreach ($model->getChanges() as $attribute => $newValue)
				if ($attribute !== 'updated_at')
					$this->addChange($attribute, static::VALUE_UNKNOWN, $newValue);
			
			return $this;
		}
		
		return $this;
	}
	
	public function hasChanged(string $attribute): bool
	{
		return isset($this->changes[$attribute]);
	}
	
	public function getOldValue(string $attribute): mixed
	{
		return $this->changes[$attribute]['o'] ?? null;
	}
	
	public function getNewValue(string $attribute): mixed
	{
		return $this->changes[$attribute]['n'] ?? null;
	}
	
	public function getChanges(): array
	{
		return $this->changes;
	}
	
	public function getChangedAttributes(): array
	{
		return array_keys($this->changes);
	}
}