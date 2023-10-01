<?php

namespace AntonioPrimera\ChangeDocuments;

use AntonioPrimera\ChangeDocuments\Models\ChangeDocument;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class DocumentChange
{
	public ChangeDocument $document;
	
	public function __construct()
	{
		$this->document = new ChangeDocument();
	}
	
	//--- API ---------------------------------------------------------------------------------------------------------
	
	public function model(Model $model): static
	{
		$this->document->forModel($model);
		
		return $this;
	}
	
	public function type(string $changeType): static
	{
		$this->document->change_type = $changeType;
		
		return $this;
	}
	
	public function by(Model $user): static
	{
		$this->document->changed_by = $user->getKey();
		return $this;
	}
	
	public function medium(string $medium): static
	{
		$this->document->medium = $medium;
		return $this;
	}
	
	public function ipAddress(string $ipAddress): static
	{
		$this->document->ip_address = $ipAddress;
		return $this;
	}
	
	public function userAgent(string $userAgent): static
	{
		$this->document->user_agent = $userAgent;
		return $this;
	}
	
	public function url(string $url): static
	{
		$this->document->url = $url;
		return $this;
	}
	
	public function method(string $method): static
	{
		$this->document->method = $method;
		return $this;
	}
	
	public function route(string $route): static
	{
		$this->document->route = $route;
		return $this;
	}
	
	public function command(string $command): static
	{
		$this->document->command = $command;
		return $this;
	}
	
	public function comment(string $comment): static
	{
		$this->document->comment = $comment;
		return $this;
	}
	
	public function save(): static
	{
		$this->document->save();
		return $this;
	}
	
	//--- Helper methods ----------------------------------------------------------------------------------------------
	
	public function created(): static
	{
		$this->document->change_type = ChangeDocument::CHANGE_TYPE_CREATED;
		return $this;
	}
	
	public function updated(): static
	{
		$this->document->change_type = ChangeDocument::CHANGE_TYPE_UPDATED;
		return $this;
	}
	
	public function deleted(): static
	{
		$this->document->change_type = ChangeDocument::CHANGE_TYPE_DELETED;
		return $this;
	}
	
	public function web(): static
	{
		$this->document->medium = ChangeDocument::MEDIUM_WEB;
		return $this;
	}
	
	public function api(): static
	{
		$this->document->medium = ChangeDocument::MEDIUM_API;
		return $this;
	}
	
	public function cli(): static
	{
		$this->document->medium = ChangeDocument::MEDIUM_CLI;
		return $this;
	}
	
	public function fillMissing(): static
	{
		if (!$this->document->changed_by)
			$this->document->changed_by = Auth::id();
		
		if (!$this->document->medium)
			$this->document->medium = App::runningInConsole() ? ChangeDocument::MEDIUM_CLI : ChangeDocument::MEDIUM_WEB;
		
		//assuming the app runs in a web environment, we can fill the request specific fields
		if (!$this->document->ip_address)
			$this->document->ip_address = Request::ip();
		
		if (!$this->document->user_agent)
			$this->document->user_agent = Request::userAgent();
		
		if (!$this->document->url)
			$this->document->url = Request::url();
		
		if (!$this->document->method)
			$this->document->method = Request::method();
		
		if (!$this->document->route)
			$this->document->route = Request::route()?->getName();
		
		return $this;
	}
	
	//--- Static API --------------------------------------------------------------------------------------------------
	
	public static function create(bool $autoFill = true): static
	{
		$change = new self();
		
		if ($autoFill)
			$change->fillMissing();
		
		return $change;
	}
	
	public static function forModel(Model $model, bool $autoFill = true): static
	{
		return static::create($autoFill)->model($model);
	}
}