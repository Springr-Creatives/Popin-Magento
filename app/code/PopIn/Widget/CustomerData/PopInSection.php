<?php

namespace PopIn\Widget\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\Api\AddressRepositoryInterface;

class PopInSection implements SectionSourceInterface
{
    private CurrentCustomer $currentCustomer;
    private AddressRepositoryInterface $addressRepository;

    public function __construct(
        CurrentCustomer $currentCustomer,
        AddressRepositoryInterface $addressRepository
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->addressRepository = $addressRepository;
    }

    public function getSectionData(): array
    {
        try {
            $customer = $this->currentCustomer->getCustomer();
        } catch (\Exception $e) {
            return [];
        }

        $data = [
            'name' => trim($customer->getFirstname() . ' ' . $customer->getLastname()),
            'email' => $customer->getEmail(),
            'mobile' => '',
            'pincode' => '',
        ];

        $defaultBillingId = $customer->getDefaultBilling();
        if ($defaultBillingId) {
            try {
                $address = $this->addressRepository->getById($defaultBillingId);
                $data['mobile'] = $address->getTelephone() ?: '';
                $data['pincode'] = $address->getPostcode() ?: '';
            } catch (\Exception $e) {
                // Address not found, leave fields empty
            }
        }

        return $data;
    }
}
