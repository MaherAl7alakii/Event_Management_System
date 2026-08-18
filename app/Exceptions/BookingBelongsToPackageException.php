<?php

namespace App\Exceptions;

use Exception;

/**
 * يُرمى عند محاولة إلغاء حجز واحد مرتبط بحزمة (package_id != null)
 * عبر المسار العادي BookingCancellationService::cancel(). الإلغاء
 * لحجز كهذا يجب أن يمر حصراً عبر cancelPackage() على مستوى الحزمة
 * بالكامل — راجع BookingCancellationService.
 */
class BookingBelongsToPackageException extends Exception
{
    public function __construct(
        public readonly int $bookingId,
        public readonly int $packageId,
    ) {
        parent::__construct(
            "Booking #{$bookingId} belongs to package #{$packageId} and cannot be cancelled individually. "
            . 'Cancel the entire package instead.'
        );
    }
}
