<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\BookingPriceProposal;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BookingPriceProposalPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BookingPriceProposal $bookingPriceProposal): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BookingPriceProposal $bookingPriceProposal): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BookingPriceProposal $bookingPriceProposal): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, BookingPriceProposal $bookingPriceProposal): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, BookingPriceProposal $bookingPriceProposal): bool
    {
        return false;
    }

    public function propose(User $user, Booking $booking): bool
    {
        return $user->id === $booking->provider_id;
    }

    public function viewHistory(User $user, Booking $booking): bool
    {
        return in_array($user->id, [$booking->provider_id, $booking->customer_id], true);
    }

    public function respond(User $user, BookingPriceProposal $proposal): bool
    {
        return $user->id === $proposal->booking->customer_id
            && $proposal->status->value === 'pending';
    }
}
