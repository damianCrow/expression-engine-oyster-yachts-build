<?php

namespace EllisLab\ExpressionEngine\Service\Model\Query;

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2003 - 2014, EllisLab, Inc.
 * @license		https://ellislab.com/expressionengine/user-guide/license.html
 * @link		http://ellislab.com
 * @since		Version 3.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * ExpressionEngine Update Query
 *
 * @package		ExpressionEngine
 * @subpackage	Model
 * @category	Service
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */
class Update extends Query {

	public function run()
	{
		$builder = $this->builder;
		$object  = $builder->getExisting();

		if ( ! $object)
		{
			$object = $this->store->make(
				$builder->getFrom(),
				$builder->getFacade()
			);
		}

		$backup = $object->getOriginal();
		$this->prepObject($object);
		
		// Do not save if the object isn't dirty. We cannot do this in the model
		// because the query builder can accept set() calls. Plus, we always want
		// to cascade to children.
		if ( ! $object->getDirty())
		{
			return;
		}

		$object->emit('beforeUpdate', $backup);
		$object->emit('beforeSave');

		$this->doWork($object);
		$object->markAsClean();

		$object->emit('afterSave');
		$object->emit('afterUpdate', $backup);
	}
	
	/**
	 * Add any set() calls that were on the query builder to the object.
	 */
	protected function prepObject($object)
	{
		foreach ($this->builder->getSet() as $field => $value)
		{
			$object->$field = $value;
		}
	}

	/**
	 * Distribute the data amongs the gateways and save it
	 */
	protected function doWork($object)
	{
		foreach ($this->builder->getSet() as $field => $value)
		{
			$object->$field = $value;
		}

		/*
		$result = $object->validate();

		if ( ! $result->isValid())
		{
			throw new \Exception('Validation failed');
		}
		*/

		// todo this is yucky
		$gateways = $this->store->getMetaDataReader($object->getName())->getGateways();

		$dirty = $object->getDirty();

		if (empty($dirty))
		{
			return;
		}

		foreach ($gateways as $gateway)
		{
			$gateway->fill($dirty);

			$this->actOnGateway($gateway, $object);
		}
	}

	protected function actOnGateway($gateway, $object)
	{
		$values = $gateway->getValues();

		if (empty($values))
		{
			return;
		}

		$query = $this->store
			->rawQuery()
			->set($values);

		if ( ! $object->isNew())
		{
			$query->where($gateway->getPrimaryKey(), $object->getId());

			if ($object->getName() == 'ee:MemberGroup')
			{
				$query->where('site_id', $object->site_id);
			}
		}

		$query->update($gateway->getTableName());
	}
}
