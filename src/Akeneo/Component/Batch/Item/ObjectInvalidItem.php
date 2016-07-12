<?php

namespace Akeneo\Component\Batch\Item;

/**
 * This class handle invalid items that could be raised by Reader or Processor. This invalid item class will handle
 * object invalid items (for example items coming from DB)
 *
 * @author    Olivier Soulet <olivier.soulet@akeneo.com>
 * @copyright 2016 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ObjectInvalidItem implements InvalidItemInterface
{
    /** @var Object */
    protected $invalidData;

    /**
     * @param Object $invalidData
     */
    public function __construct($invalidData)
    {
        $this->invalidData = $invalidData;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->invalidData;
    }
}