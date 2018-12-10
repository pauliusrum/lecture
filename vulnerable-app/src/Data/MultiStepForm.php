<?php declare(strict_types=1);

namespace App\Data;

class MultiStepForm
{
    /**
     * @var bool
     */
    private $vipDiscount;

    /**
     * @var ?string
     */
    private $address;

    /**
     * @param bool $vipDiscount
     * @param ?string $address
     */
    public function __construct(bool $vipDiscount, ?string $address)
    {
        $this->vipDiscount = $vipDiscount;
        $this->address = $address;
    }

    public function isVipDiscount(): bool
    {
        return $this->vipDiscount;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }
}
