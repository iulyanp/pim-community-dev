<?php

namespace Pim\Component\Catalog\Updater\Setter;

use Akeneo\Component\StorageUtils\Exception\InvalidPropertyTypeException;
use Pim\Component\Catalog\Builder\ProductBuilderInterface;
use Pim\Component\Catalog\Factory\MetricFactory;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Catalog\Model\ProductInterface;
use Pim\Component\Catalog\Validator\AttributeValidatorHelper;

/**
 * Sets a metric value in many products
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2014 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MetricAttributeSetter extends AbstractAttributeSetter
{
    /** @var MetricFactory */
    protected $metricFactory;

    /**
     * @param ProductBuilderInterface  $productBuilder
     * @param AttributeValidatorHelper $attrValidatorHelper
     * @param MetricFactory            $metricFactory
     * @param array                    $supportedTypes
     */
    public function __construct(
        ProductBuilderInterface $productBuilder,
        AttributeValidatorHelper $attrValidatorHelper,
        MetricFactory $metricFactory,
        array $supportedTypes
    ) {
        parent::__construct($productBuilder, $attrValidatorHelper);
        $this->metricFactory = $metricFactory;
        $this->supportedTypes = $supportedTypes;
    }

    /**
     * {@inheritdoc}
     *
     * Expected data input format:
     * {
     *     "amount": "12.0"|"12"|12|12.3,
     *     "unit": "KILOGRAM"
     * }
     */
    public function setAttributeData(
        ProductInterface $product,
        AttributeInterface $attribute,
        $data,
        array $options = []
    ) {
        $options = $this->resolver->resolve($options);
        $this->checkLocaleAndScope($attribute, $options['locale'], $options['scope']);

        if (null === $data) {
            $data = ['amount' => null, 'unit' => null];
        }

        $this->checkData($attribute, $data);

        $unit = $data['unit'];
        $amount = $data['amount'];

        $this->setData($product, $attribute, $amount, $unit, $options['locale'], $options['scope']);
    }

    /**
     * Check if data is valid
     *
     * @param AttributeInterface $attribute
     * @param mixed              $data
     *
     * @throws InvalidPropertyTypeException
     */
    protected function checkData(AttributeInterface $attribute, $data)
    {
        if (!is_array($data)) {
            throw InvalidPropertyTypeException::arrayExpected($attribute->getCode(), static::class, $data);
        }

        if (!array_key_exists('amount', $data)) {
            throw InvalidPropertyTypeException::arrayKeyExpected(
                $attribute->getCode(),
                'amount',
                static::class,
                $data
            );
        }

        if (!array_key_exists('unit', $data)) {
            throw InvalidPropertyTypeException::arrayKeyExpected(
                $attribute->getCode(),
                'unit',
                static::class,
                $data
            );
        }
    }

    /**
     * Set the data into the product value
     *
     * @param ProductInterface   $product
     * @param AttributeInterface $attribute
     * @param mixed              $amount
     * @param string             $unit
     * @param string             $locale
     * @param string             $scope
     */
    protected function setData(
        ProductInterface $product,
        AttributeInterface $attribute,
        $amount,
        $unit,
        $locale,
        $scope
    ) {
        $value = $product->getValue($attribute->getCode(), $locale, $scope);
        if (null === $value) {
            $value = $this->productBuilder->addOrReplaceProductValue($product, $attribute, $locale, $scope);
        }

        if (null === $metric = $value->getMetric()) {
            $metric = $this->metricFactory->createMetric($attribute->getMetricFamily());
        }

        $value->setMetric($metric);
        $metric->setUnit($unit);
        $metric->setData($amount);
    }
}
