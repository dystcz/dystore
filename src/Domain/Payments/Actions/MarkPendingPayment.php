<?php

namespace Dystcz\LunarApi\Domain\Payments\Actions;

use Dystcz\LunarApi\Domain\Orders\Actions\ChangeOrderStatus;
use Dystcz\LunarApi\Domain\Orders\Enums\OrderStatus;
use Lunar\Models\Order;

class MarkPendingPayment
{
    /**
     * Change order status to pending payment.
     */
    public function __invoke(Order $order): Order
    {
        $disabledStatuses = [
            'payment-received',
            'dispatched',
            'delivered',
            'cancelled',
            'on-hold',
        ];

        if (in_array($order->status, $disabledStatuses)) {
            return $order;
        }

        $order = (new ChangeOrderStatus)($order, OrderStatus::PENDING_PAYMENT);

        return $order;
    }
}
