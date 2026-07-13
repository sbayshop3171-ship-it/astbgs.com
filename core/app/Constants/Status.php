<?php

namespace App\Constants;

class Status
{

    const ENABLE  = 1;
    const DISABLE = 0;
    const EXPIRE  = 2;

    const YES = 1;
    const NO  = 0;

    const VERIFIED   = 1;
    const UNVERIFIED = 0;

    const PAYMENT_INITIATE = 0;
    const PAYMENT_SUCCESS  = 1;
    const PAYMENT_PENDING  = 2;
    const PAYMENT_REJECT   = 3;

    const TICKET_OPEN   = 0;
    const TICKET_ANSWER = 1;
    const TICKET_REPLY  = 2;
    const TICKET_CLOSE  = 3;

    const PRIORITY_LOW    = 1;
    const PRIORITY_MEDIUM = 2;
    const PRIORITY_HIGH   = 3;

    const USER_ACTIVE = 1;
    const USER_BAN    = 0;

    const KYC_UNVERIFIED = 0;
    const KYC_PENDING    = 2;
    const KYC_VERIFIED   = 1;

    const GOOGLE_PAY = 5001;

    const CUR_BOTH = 1;
    const CUR_TEXT = 2;
    const CUR_SYM  = 3;

    const PRODUCT_PENDING        = 0;
    const PRODUCT_APPROVED       = 1;
    const PRODUCT_SOFT_REJECTED  = 2;
    const PRODUCT_HARD_REJECTED  = 3;
    const PRODUCT_DOWN           = 4;
    const PRODUCT_PERMANENT_DOWN = 5;

    const PERSONAL_LICENSE   = 1;
    const COMMERCIAL_LICENSE = 2;

    const PRODUCT_TYPE_DOWNLOADABLE   = 'downloadable';
    const PRODUCT_TYPE_OPTION_REQUEST = 'option_request';

    const PRODUCT_AVAILABILITY_AVAILABLE   = 'available';
    const PRODUCT_AVAILABILITY_LIMITED     = 'limited';
    const PRODUCT_AVAILABILITY_UNAVAILABLE = 'unavailable';

    const ORDER_PAID     = 1;
    const ORDER_UNPAID   = 2;
    const ORDER_REFUNDED = 1;

    const CATALOG_ORDER_PENDING_PAYMENT = 'pending_payment';
    const CATALOG_ORDER_PAID            = 'paid';
    const CATALOG_ORDER_PROCESSING      = 'processing';
    const CATALOG_ORDER_COMPLETED       = 'completed';
    const CATALOG_ORDER_CANCELLED       = 'cancelled';

    const PRODUCT_NO_UPDATE          = 0;
    const PRODUCT_UPDATE_PENDING     = 1;
    const PRODUCT_UPDATE_APPROVED    = 2;
    const PRODUCT_UPDATE_SOFT_REJECT = 3;
    const PRODUCT_UPDATE_HARD_REJECT = 4;

    const ACCOUNT_BALANCE = 1;
    const GATEWAY         = 2;

    const APPROVED_REFUND = 1;
    const REJECTED_REFUND = 2;

    const SERVER_CURRENT       = 1;
    const SERVER_FTP           = 2;
    const SERVER_WASABI        = 3;
    const SERVER_DIGITAL_OCEAN = 4;
    const SERVER_BACKBLAZE     = 5;

    const ACTIVE_KEY   = 1;
    const INACTIVE_KEY = 2;

    const AD_ACTIVE   = 1;
    const AD_INACTIVE = Null;

    const AUTHOR_FEATURED     = 1;
    const AUTHOR_NOT_FEATURED = 0;

    const DISCOUNT_FIXED   = 1;
    const DISCOUNT_PERCENT = 2;

    const AUTHOR = 1;

   
    const UNPAID_SUBSCRIPTION= 0;
    const PAID_SUBSCRIPTION= 1;

    const PLAN_PENDING   = 0;
    const PLAN_ACTIVE   = 1;
    const PLAN_EXPIRED    = 3;

    const MONTHLY_PLAN   = 1;
    const YEARLY_PLAN  = 2;
}
