<?php

namespace AntonioPrimera\ChangeDocuments\Casts;

use AntonioPrimera\ChangeDocuments\Models\AttributeChanges;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class CastsAttributeChanges implements CastsAttributes
{
	
	/**
	 * Cast the given value.
	 */
	public function get(Model $model, string $key, mixed $value, array $attributes): AttributeChanges
	{
		if (is_string($value))
			return new AttributeChanges(json_decode($value, true));
		
		return new AttributeChanges(is_array($value) ? $value : []);
	}
	
	/**
	 * Prepare the given value for storage.
	 * @throws \Exception
	 */
	public function set(Model $model, string $key, mixed $value, array $attributes): string
	{
		if ($value instanceof AttributeChanges)
			return json_encode($value->getChanges());
		
		if (is_array($value))
			return json_encode($value);
		
		throw new \Exception('Invalid value for attribute changes');
	}
}
