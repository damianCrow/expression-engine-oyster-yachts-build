<?php namespace Zenbu\librairies\platform\ee;

class Convert
{
	public static function col($col)
	{
		$cols = array(
			'fieldId'      => 'fieldId',
			'fieldType'    => 'fieldType',
			'sectionId'    => 'sectionId',
			'subSectionId' => 'subSectionId',
			);
		if(isset($cols[$col]))
		{
        	return $cols[$col];
		}
		else
		{
			return $col;
		}
	}

	public static function string($str)
	{
		$strings = array(
			'subSubsections' => 'subSubsections',
			'sections'       => 'channels',
			'section'        => 'channel',
			'sectionId'        => 'channel_id',
			);
		if(isset($strings[$str]))
		{
        	return $strings[$str];
		}
		else
		{
			return $strings;
		}
	}
}