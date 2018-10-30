<?php

namespace Shisun\LaravelElasticsearchQueryBuilder;

use Illuminate\Database\Eloquent\Model;

trait EsTrait {
	protected function es(){
		return new LaravelElasticsearchQueryBuilder($this);
	}

	public function __call($method, $parameters) {
		if(strpos($method, 'es') === 0) {
			return $this->{$method}();
		}
		return parent::__call($method, $parameters);
	}
}