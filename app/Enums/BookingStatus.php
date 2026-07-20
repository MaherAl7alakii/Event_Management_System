<?php

namespace App\Enums;

enum BookingStatus: string
{
    case DRAFT = 'draft';
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case DEPOSIT_PAID = 'deposit_paid';
    case CONFIRMED = 'confirmed';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case REJECTED = 'rejected';
    case EXPIRED = 'expired';




    public static function unsentStatuses(): array
    {
        return [self::DRAFT];
    }


    public static function awaitingProviderStatuses(): array
    {
        return [self::PENDING];
    }


    public static function terminalRejectedStatuses(): array
    {
        return [self::REJECTED, self::EXPIRED];
    }


    public static function acceptedOrBeyondStatuses(): array
    {
        return [
            self::ACCEPTED,
            self::DEPOSIT_PAID,
            self::CONFIRMED,
            self::COMPLETED,
        ];
    }


}
