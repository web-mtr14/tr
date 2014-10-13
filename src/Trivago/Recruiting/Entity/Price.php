<?php

namespace Trivago\Recruiting\Entity;

/**
 * Represents a single price from a search result
 * related to a single partner.
 * 
 * @author mmueller
 */
class Price
{
    /**
     * Description text for the rate/price
     * 
     * @var string
     */
    public $sDescription;

    /**
     * Price in euro
     * 
     * @var float
     */
    public $fAmount;

    /**
     * Arrival date, represented by a DateTime obj
     * which needs to be converted from a string on 
     * write of the property.
     *
     * @var \DateTime
     */
    public $oFromDate;

    /**
     * Departure date, represented by a DateTime obj
     * which needs to be converted from a string on 
     * write of the property
     *
     * @var \DateTime
     */
    public $oToDate;
}
