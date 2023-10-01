<?php
namespace AntonioPrimera\ChangeDocuments\Models;

use AntonioPrimera\ChangeDocuments\Casts\CastsAttributeChanges;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

/**
 * Class ChangeDocument
 *
 * @package AntonioPrimera\ChangeDocuments\Models
 *
 * @property int $id
 * @property string|null $model_type
 * @property int|null $model_id
 * @property string $change_type
 * @property AttributeChanges|null $changes
 * @property int|null $changed_by
 * @property string|null $medium
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $url
 * @property string|null $method
 * @property string|null $route
 * @property string|null $command
 * @property string|null $comment
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * //Relations
 * @property Model $model
 * @property Authenticatable|null $user
 */
class ChangeDocument extends Model
{
	const CHANGE_TYPE_CREATED = 'created';
	const CHANGE_TYPE_UPDATED = 'updated';
	const CHANGE_TYPE_DELETED = 'deleted';
	
	const MEDIUM_WEB = 'web';
	const MEDIUM_API = 'api';
	const MEDIUM_CLI = 'cli';
	const MEDIUM_OTHER = 'other';
	
	protected $guarded = [];
	
	protected $casts = [
		'changes' => CastsAttributeChanges::class,
	];
	
	public function model(): MorphTo
	{
		return $this->morphTo('model', 'model_type', 'model_id');
	}
	
	public function actor(): ?Authenticatable
	{
		if (!$this->changed_by) {
			return null;
		}
		
		$userClass = Config::get('change-documents.user_model', 'App\Models\User');
		
		return $userClass::find($this->changed_by);
	}
	
	public function forModel(Model $model): static
	{
		$this->model_type = $model->getMorphClass();
		$this->model_id = $model->getKey();
		$this->getAttribute('changes')->fromModel($model);
		$this->change_type = $this->determineModelChangeType($model);
		
		return $this;
	}
	
	//--- Protected helpers -------------------------------------------------------------------------------------------
	
	protected function determineModelChangeType(Model $model): string
	{
		if ($model->wasRecentlyCreated)
			return static::CHANGE_TYPE_CREATED;
		
		if (!$model->exists)
			return $model->id ? static::CHANGE_TYPE_DELETED : static::CHANGE_TYPE_CREATED;
		
		return static::CHANGE_TYPE_UPDATED;
	}
}