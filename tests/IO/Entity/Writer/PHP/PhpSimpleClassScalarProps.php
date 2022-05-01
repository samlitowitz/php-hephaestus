<?php

namespace PhpHephaestus\Tests\IO\Entity\Writer\PHP;

final class PhpSimpleClassScalarProps
{
    /** @var ?string $prop_binary */
    private $propBinary;
    /** @var ?int $prop_currency */
    private $propCurrency;
    /** @var ?\DateTime $prop_date */
    private $propDate;
    /** @var ?\DateTime $prop_datetime */
    private $propDatetime;
    /** @var ?string $prop_enumeration */
    private $propEnumeration;
    /** @var ?float $prop_float */
    private $propFloat;
    /** @var ?int $prop_integer */
    private $propInteger;
    /** @var ?string $prop_string */
    private $propString;
    /** @var ?\DateTime $prop_time */
    private $propTime;
    public function getPropBinary() : ?string
    {
        return $this->propBinary;
    }
    public function setPropBinary(?string $propBinary) : void
    {
        $this->propBinary = $propBinary;
    }
    public function getPropCurrency() : ?int
    {
        return $this->propCurrency;
    }
    public function setPropCurrency(?int $propCurrency) : void
    {
        $this->propCurrency = $propCurrency;
    }
    public function getPropDate() : ?\DateTime
    {
        return $this->propDate;
    }
    public function setPropDate(?\DateTime $propDate) : void
    {
        $this->propDate = $propDate;
    }
    public function getPropDatetime() : ?\DateTime
    {
        return $this->propDatetime;
    }
    public function setPropDatetime(?\DateTime $propDatetime) : void
    {
        $this->propDatetime = $propDatetime;
    }
    public function getPropEnumeration() : ?string
    {
        return $this->propEnumeration;
    }
    public function setPropEnumeration(?string $propEnumeration) : void
    {
        $this->propEnumeration = $propEnumeration;
    }
    public function getPropFloat() : ?float
    {
        return $this->propFloat;
    }
    public function setPropFloat(?float $propFloat) : void
    {
        $this->propFloat = $propFloat;
    }
    public function getPropInteger() : ?int
    {
        return $this->propInteger;
    }
    public function setPropInteger(?int $propInteger) : void
    {
        $this->propInteger = $propInteger;
    }
    public function getPropString() : ?string
    {
        return $this->propString;
    }
    public function setPropString(?string $propString) : void
    {
        $this->propString = $propString;
    }
    public function getPropTime() : ?\DateTime
    {
        return $this->propTime;
    }
    public function setPropTime(?\DateTime $propTime) : void
    {
        $this->propTime = $propTime;
    }
}