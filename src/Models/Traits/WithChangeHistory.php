<?php

namespace AntonioPrimera\ChangeDocuments\Models\Traits;

use AntonioPrimera\ChangeDocuments\Models\ChangeDocument;
use Illuminate\Support\Collection;

trait WithChangeHistory
{
	protected Collection $changeDocuments;
	
	public function getChangeDocuments(): Collection
	{
		return $this->changeDocuments;
	}
	
	protected function changeVersion(int $version, mixed $value, ChangeDocument $changeDocument): array
	{
		return [
			'version' => $version,
			
			'change_type' => $changeDocument->change_type,
			'changed_by' => $changeDocument->changed_by,
			'changed_at' => $changeDocument->created_at,
			
			'value' => $value,
			
			'changeDocument' => $changeDocument,
		];
	}
	
	protected function createChangeHistory(callable $value, callable|null $initialVersionValue = null): array
	{
		$history = [];
		
		if ($this->changeDocuments->isEmpty())
			return $history;
		
		$version = 0;
		
		//if we have an initial version value, we add it to the history as the initial version
		if ($initialVersionValue) {
			$firstChangeDocument = $this->changeDocuments->first();
			
			$history[] = $this->changeVersion(
				$version,
				call_user_func($value, $firstChangeDocument),	//$firstChangeDocument->changes->getOldValue($attributeName),
				$firstChangeDocument
			);
			
			$version++;
		}
		
		//run through all the change documents and add them to the history
		foreach ($this->changeDocuments as $changeDocument) {
			$history[] = $this->changeVersion(
				$version + 1,
				call_user_func($value, $changeDocument), 		//$changeDocument->changes->getNewValue($attributeName),
				$changeDocument
			);
			
			$version++;
		}
		
		return $history;
	}
}