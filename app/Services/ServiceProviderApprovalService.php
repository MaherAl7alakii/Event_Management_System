<?php

namespace App\Services;

use App\Models\ServiceProvider;
use Illuminate\Support\Facades\DB;

class ServiceProviderApprovalService
{

   public function approve(ServiceProvider $provider)
{
    if ($provider->approval_status !== 'pending') {
        throw new \Exception(
            'Only pending providers can be approved'
        );
    }


    return DB::transaction(function () use ($provider) {

       $provider->update([
    'approval_status' => 'approved',
    'rejection_reason' => null,
    'verified_at' => now(),
]);

        return $provider;
    });
}



    public function reject(
    ServiceProvider $provider,
    string $reason
){

    if ($provider->approval_status !== 'pending') {
        throw new \Exception(
            'Only pending providers can be rejected'
        );
    }


    return DB::transaction(function () use ($provider,$reason) {

       $provider->update([
    'approval_status'=>'rejected',
    'rejection_reason'=>$reason,
    'verified_at'=>null,
]);


        return $provider;
    });
}
public function resubmit(ServiceProvider $provider)
{
    if ($provider->approval_status !== 'rejected') {
        throw new \Exception(
            'Only rejected providers can resubmit'
        );
    }


    $provider->update([
        'approval_status' => 'pending',
        'rejection_reason' => null,
        'verified_at' => null,
    ]);


    return $provider;
}
}