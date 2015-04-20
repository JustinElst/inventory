<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Quote\Model\GuestCart;

use Magento\Quote\Api\GuestCartManagementInterface;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Model\QuoteManagement;
use Magento\Quote\Model\QuoteRepository;

/**
 * Cart Management class for guest carts.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GuestCartManagement implements GuestCartManagementInterface
{
    /**
     * @var CartManagementInterface
     */
    protected $quoteManagement;

    /**
     * @var QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @var QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * Initialize dependencies.
     *
     * @param CartManagementInterface $quoteManagement
     * @param QuoteRepository $quoteRepository
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        CartManagementInterface $quoteManagement,
        QuoteRepository $quoteRepository,
        QuoteIdMaskFactory $quoteIdMaskFactory
    ) {
        $this->quoteManagement = $quoteManagement;
        $this->quoteRepository = $quoteRepository;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createEmptyCart($customerId = null)
    {
        /** @var $quoteIdMask \Magento\Quote\Model\QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create();
        $cartId = $this->quoteManagement->createEmptyCart($customerId);
        $quoteIdMask->setId($cartId)->save();
        return $quoteIdMask->getMaskedId();
    }

    /**
     * {@inheritdoc}
     */
    public function assignCustomer($cartId, $customerId, $storeId)
    {
        /** @var $quoteIdMask QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this->quoteManagement->assignCustomer($quoteIdMask->getId(), $customerId, $storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function placeOrder($cartId)
    {
        /** @var $quoteIdMask QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this->quoteManagement->placeOrder($quoteIdMask->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function getCartForCustomer($customerId)
    {
        $cart = $this->quoteRepository->getActiveForCustomer($customerId);
        /** @var $quoteIdMask QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cart->getId(), 'masked_id');
        $cart->setId($quoteIdMask->getId());
        return $cart;
    }
}
