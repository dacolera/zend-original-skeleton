<?php

class App_Funcoes_UTF8
{
	/**
	 * Codifica uma variável (str|arr|obj) para UTF-8
	 *
	 * @param mix $encode
	 * @return void
	 */
	public static function encode(&$encode)
	{
		if (is_array($encode) || is_object($encode))
		{
			foreach ($encode as $index => $value)
			{
				if (is_array($value))
				{
					self::encode($encode[$index]);
				} elseif (is_object($value)) {
					self::encode($value);
				} elseif (is_string($value)) {
					if (is_array($encode))
					{
						$encode[$index] = utf8_encode($value);
					} elseif (is_object($encode)) {
						$encode->{$index} = utf8_encode($value);
					}
				} else {
					$encode[$index] = $value;
				}
			}
		} else {
			$encode = utf8_encode($encode);
		}
	}

	/**
	 * Codifica uma variável (str|arr|obj) para UTF-8
	 *
	 * @param mix $encode
	 * @return void
	 */
	public static function decode(&$encode)
	{
		if (is_array($encode) || is_object($encode))
		{
			foreach ($encode as $index => $value)
			{
				if (is_array($value))
				{
					self::decode($encode[$index]);
				} elseif (is_object($value)) {
					self::decode($value);
				} elseif (is_string($value)) {
					if (is_array($encode))
					{
						$encode[$index] = utf8_decode($value);
					} elseif (is_object($encode)) {
						$encode->{$index} = utf8_decode($value);
					}
				} else {
					$encode[$index] = $value;
				}
			}
		} else {
			$encode = utf8_decode($encode);
		}
	}
}