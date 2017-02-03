<?php

namespace Akeneo\Component\StorageUtils\Exception;

/**
 * Exception and updater can throw when updating a property which is invalid.
 *
 * @author    Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class InvalidPropertyException extends ObjectUpdaterException
{
    const EXPECTED_CODE = 100;
    const DATE_EXPECTED_CODE = 101;
    const BOOLEAN_EXPECTED_CODE = 102;
    const FLOAT_EXPECTED_CODE = 103;
    const INTEGER_EXPECTED_CODE = 104;
    const NUMERIC_EXPECTED_CODE = 105;
    const STRING_EXPECTED_CODE = 106;
    const ARRAY_EXPECTED_CODE = 108;
    const ARRAY_OF_ARRAYS_EXPECTED_CODE = 109;

    const NOT_EMPTY_VALUE_EXPECTED_CODE = 200;

    const VALID_ENTITY_CODE_EXPECTED_CODE = 300;
    const VALID_GROUP_TYPE_EXPECTED_CODE = 301;

    /** @var string */
    protected $propertyName;

    /** @var string */
    protected $propertyValue;

    /** @var string */
    protected $className;

    /**
     * @param string          $propertyName
     * @param string          $propertyValue
     * @param string          $className
     * @param string          $message
     * @param int             $code
     * @param \Exception|null $previous
     */
    public function __construct(
        $propertyName,
        $propertyValue,
        $className,
        $message = '',
        $code = 0,
        \Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->propertyName  = $propertyName;
        $this->propertyValue = $propertyValue;
        $this->className = $className;
    }

    /**
     * Build an exception when the data is empty and should not.
     *
     * @param string $propertyName
     * @param string $className
     *
     * @return InvalidPropertyException
     */
    public static function valueNotEmptyExpected($propertyName, $className)
    {
        $message = 'Property "%s" does not expect an empty value.';

        return new self(
            $propertyName,
            null,
            $className,
            sprintf($message, $propertyName),
            self::NOT_EMPTY_VALUE_EXPECTED_CODE
        );
    }

    /**
     * Build an exception when the data is an invalid entity code.
     *
     * @param string $propertyName
     * @param string $key
     * @param string $because
     * @param string $className
     * @param string $propertyValue
     *
     * @return InvalidPropertyException
     */
    public static function validEntityCodeExpected($propertyName, $key, $because, $className, $propertyValue)
    {
        $message = 'Property "%s" expects a valid %s. %s, "%s" given.';

        return new self(
            $propertyName,
            $propertyValue,
            $className,
            sprintf($message, $propertyName, $key, $because, $propertyValue),
            self::VALID_ENTITY_CODE_EXPECTED_CODE
        );
    }

    /**
     * Build an exception when the date is invalid.
     *
     * @param string $propertyName
     * @param string $format
     * @param string $className
     * @param string $propertyValue
     *
     * @return InvalidPropertyException
     */
    public static function dateExpected($propertyName, $format, $className, $propertyValue)
    {
        $message = 'Property "%s" expects a string with the format "%s" as data, "%s" given.';

        return new self(
            $propertyName,
            $propertyValue,
            $className,
            sprintf($message, $propertyName, $format, $propertyValue),
            self::DATE_EXPECTED_CODE
        );
    }

    /**
     * Build an exception when the group type is invalid or is not allowed.
     *
     * @param string $propertyName
     * @param string $because
     * @param string $className
     * @param string $propertyValue
     *
     * @return InvalidPropertyException
     */
    public static function validGroupTypeExpected($propertyName, $because, $className, $propertyValue)
    {
        $message = 'Property "%s" expects a valid group type. %s, "%s" given.';

        return new self(
            $propertyName,
            $propertyValue,
            $className,
            sprintf($message, $propertyName, $because, $propertyValue),
            self::VALID_GROUP_TYPE_EXPECTED_CODE
        );
    }

    /**
     * @return string
     */
    public function getPropertyName()
    {
        return $this->propertyName;
    }

    /**
     * @return string
     */
    public function getPropertyValue()
    {
        return $this->propertyValue;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }
}
