<?php

namespace Trivago\Recruiting\Service;

use Trivago\Recruiting\Entity as Entity;

use \RecursiveIteratorIterator;
use \RecursiveDirectoryIterator;
use \DateTime;

/**
 * This class is an (unfinished) example implementation of an unordered hotel service.
 *
 * @author mmueller
 */
class UnorderedPartnerService implements PartnerServiceInterface
{

    /**
     * Maps from city id to the hotels data.
     *  
     * @var array
     */
    private $aCityToHotelsMapping = array();

    /**
     * @param $pathToJsonStorage - string
     */
    public function __construct($pathToJsonStorage)
    {
        $objects = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($pathToJsonStorage),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($objects as $index => $object) {
            if (!$object->isDir() && $object->isFile() && $object->isReadable() && $object->getExtension() == 'json') {
                if ($data = @json_decode(file_get_contents($object->getRealPath()), true)) {
                    foreach ($data['hotels'] as &$hotel) {
                        $entity = new Entity\Hotel();
                        $vars   = get_object_vars($entity);
                        foreach ($vars as $var => $val) {
                            $key = strtolower(substr($var, 1));
                            if (isset($hotel[$key])) {
                                if ($key == 'partners') {
                                    foreach ($hotel[$key] as &$partner) {
                                        $entityPartner = new Entity\Partner;
                                        $varsPartner   = get_object_vars($entityPartner);
                                        foreach ($varsPartner as $varPartner => $valPartner) {
                                            $keyPartner = strtolower(substr($varPartner, 1));
                                            /**
                                             * @internal alias for url
                                             */
                                            if ($keyPartner == 'homepage') {
                                                $keyPartner = 'url';
                                            }
                                            if (isset($partner[$keyPartner])) {
                                                if ($keyPartner == 'prices') {
                                                    foreach ($partner[$keyPartner] as &$price) {
                                                        $entityPrice = new Entity\Price;
                                                        $varsPrice   = get_object_vars($entityPrice);
                                                        foreach ($varsPrice as $varPrice => $valPrice) {
                                                            $firstKeyPrice = substr($varPrice, 0, 1);
                                                            $keyPrice = strtolower(substr($varPrice, 1));
                                                            if (!isset($price[$keyPrice])) {
                                                                $keyPrice = str_replace('date', '', $keyPrice);
                                                            }
                                                            if (isset($price[$keyPrice])) {
                                                                switch ($firstKeyPrice) {
                                                                    case 's':
                                                                        $price[$keyPrice] = (string) $price[$keyPrice];
                                                                        break;
                                                                    case 'f':
                                                                        $price[$keyPrice] = (float) $price[$keyPrice];
                                                                        break;
                                                                    case 'o':
                                                                        $price[$keyPrice] = new DateTime($price[$keyPrice]);
                                                                        break;
                                                                }
                                                                $entityPrice->{$varPrice} = $price[$keyPrice];
                                                            }
                                                        }
                                                        $price = $entityPrice;
                                                    }
                                                    unset($price);
                                                }
                                                $entityPartner->{$varPartner} = $partner[$keyPartner];
                                            }
                                        }
                                        $partner = $entityPartner;
                                    }
                                    unset($partner);
                                }
                                $entity->{$var} = $hotel[$key];
                            }
                        }
                        $hotel = $entity;
                    }
                    unset($hotel);
                    
                    $this->aCityToHotelsMapping[$data['id']] = $data['hotels'];
                }
            }
        }
    }

    /**
     * @inherited
     */
    public function getResultForCityId($iCityId)
    {
        if (!isset($this->aCityToHotelsMapping[$iCityId])) {
            throw new \InvalidArgumentException(sprintf('Given city id [%s] is not mapped.', $iCityId));
        }

        return $this->aCityToHotelsMapping[$iCityId];
    }
    
}
