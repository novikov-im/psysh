<?php

namespace Psy\Formatter\Signature;

use Psy\Formatter\Signature\SignatureFormatter;

/**
 * Property signature representation.
 */
class FunctionSignatureFormatter extends SignatureFormatter
{
    /**
     * {@inheritdoc}
     */
    public static function format(\Reflector $reflector)
    {
        return sprintf(
            '<info>function</info> %s<strong>%s</strong>(%s)',
            self::formatReturnsReference($reflector),
            self::formatName($reflector),
            implode(', ', self::formatParams($reflector))
        );
    }

    /**
     * Print an `&` if this function returns by reference.
     *
     * @return string
     */
    protected static function formatReturnsReference(\Reflector $reflector)
    {
        if ($reflector->returnsReference()) {
            return '&';
        }
    }

    /**
     * Print the function params.
     *
     * @return string
     */
    protected static function formatParams(\Reflector $reflector)
    {
        return array_map(function($param) {
            if ($param->isArray()) {
                $hint = '<info>array</info> ';
            } elseif ($class = $param->getClass()) {
                $hint = sprintf('<info>%s</info> ', $class->getName());
            } else {
                $hint = '';
            }

            if ($param->isOptional()) {
                if (!$param->isDefaultValueAvailable()) {
                    $value = 'unknown';
                } else {
                    $value = $param->getDefaultValue();
                    $value = is_array($value) ? 'array()' : is_null($value) ? 'null' : var_export($value, true);
                }
                $default = sprintf(' = <return>%s</return>', $value);
            } else {
                $default = '';
            }

            return sprintf(
                '%s%s<strong>$%s</strong>%s',
                $param->isPassedByReference() ? '&' : '',
                $hint,
                $param->getName(),
                $default
            );
        }, $reflector->getParameters());
    }
}