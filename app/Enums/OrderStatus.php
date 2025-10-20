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


//'ready_for_pickup',
//'driver_assigned',
//'cancelled'

}
