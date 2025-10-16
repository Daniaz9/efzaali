<?php

namespace App\Enums;

enum OrderStatus: string
{
    case DELIVERED   = 'delivered';
    case PROCESSING  = 'processing';
    case ON_THE_WAY  = 'on_the_way';
    case CANCELLED   = 'cancelled';
    case PENDING     = 'pending';
//    case PICKEDUP     = 'picked_up';
//'ready_for_pickup',
//'driver_assigned',
//'cancelled'

}
