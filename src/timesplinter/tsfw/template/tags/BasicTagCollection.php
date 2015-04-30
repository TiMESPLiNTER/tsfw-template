<?php

namespace timesplinter\tsfw\template\tags;

use timesplinter\tsfw\template\common\TagCollection;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2015, TiMESPLiNTER Webdevelopment
 */
class BasicTagCollection implements TagCollection
{

	/**
	 * @return string
	 */
	public function getPrefix()
	{
		return 'tst';
	}

	/**
	 * @return string[]
	 */
	public function getAvailableTags()
	{
		return array(
			'text' => PrintTag::class,
			'print' => PrintTag::class,
			'checkboxOptions' => CheckboxOptionsTag::class,
			'checkbox' => CheckboxTag::class,
			'date' => DateTag::class,
			'else' => ElseTag::class,
			'for' => ForTag::class,
			'if' => IfTag::class,
			'elseif' => ElseifTag::class,
			'loadSubTpl' => LoadSubTplTag::class,
			'options' => OptionsTag::class,
			'option' => OptionTag::class,
			'radioOptions' => RadioOptionsTag::class,
			'radio' => RadioTag::class
		);
	}
}

/* EOF */