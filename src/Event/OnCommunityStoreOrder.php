<?php

namespace SlifeCommunityStore\Event;

use Slife\Integration\BasicEvent;

class OnCommunityStoreOrder extends BasicEvent
{
    /**
     * Return the handle of the event.
     *
     * @return string
     */
    public function getEventHandle()
    {
        return 'on_community_store_order';
    }

    public function install()
    {
        $this->getOrCreateEvent();
        $this->getOrCreatePlaceholders([
            'order_id',
            'order_total',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultMessage()
    {
        return t("Order {order_id} has been placed with a value of {order_total}");
    }

    /**
     * @param \Concrete\Package\CommunityStore\Src\CommunityStore\Order\OrderEvent $event
     * @param string $message
     *
     * @return string
     */
    protected function replaceOrderId(\Concrete\Core\File\Event\DeleteFile $event, $message)
    {
        $order = $event->getOrder();

        return str_replace('{order_id}', '#'.$order->getOrderID(), $message);
    }

    /**
     * @param \Concrete\Package\CommunityStore\Src\CommunityStore\Order\OrderEvent $event
     * @param string $message
     *
     * @return string
     */
    protected function replaceOrderTotal(\Concrete\Core\File\Event\DeleteFile $event, $message)
    {
        $order = $event->getOrder();

        return str_replace('{order_total}', $order->getTotal(), $message);
    }
}
