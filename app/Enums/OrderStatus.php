<?php

namespace App\Enums;

enum OrderStatus: string
{
//    case PROCESSING  = 'processing';
    case CANCELLED   = 'cancelled';
    case PENDING     = 'pending';
    case ASSIGNED     = 'assigned';
    case PICKEDUP     = 'picked_up';
    case ON_THE_WAY  = 'on_the_way';
    case DELIVERED   = 'delivered';

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'pending',
            self::ASSIGNED => 'assigned',
            self::PICKEDUP => 'picked_up',
            self::ON_THE_WAY => 'on_the_way',
            self::DELIVERED => 'delivered',
            self::CANCELLED => 'cancelled',
        };
    }
//'ready_for_pickup',
//'driver_assigned',
//'cancelled'

}
