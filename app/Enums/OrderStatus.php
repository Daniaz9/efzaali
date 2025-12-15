<?php

namespace App\Enums;

enum OrderStatus: string
{
//    case PROCESSING  = 'processing';
    case CANCELLED   = 'ملغي';
    case PENDING     = 'اختر الفزاع ';
    case ASSIGNED     = 'تم اختيار الفزاع';
    case PICKEDUP     = 'نجهز الفزعة';
    case ON_THE_WAY  = 'في الطريق';
    case DELIVERED   = 'تم التسليم';

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'اختر الفزاع',
            self::ASSIGNED => 'تم اختيار الفزاع',
            self::PICKEDUP => 'نجهز الفزعة',
            self::ON_THE_WAY => 'في الطريق',
            self::DELIVERED => 'تم التسليم',
            self::CANCELLED => 'ملغي',
        };
    }
//'ready_for_pickup',
//'driver_assigned',
//'cancelled'

}
