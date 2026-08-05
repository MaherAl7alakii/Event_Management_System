<?php

namespace App\Services;

use App\Models\Package;
use App\Models\Service;
use App\Models\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PackageService
{

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {


            $provider = ServiceProvider::where(
                'user_id',
                auth()->id()
            )->firstOrFail();



            $services = Service::whereIn(
                'id',
                $data['service_ids']
            )
            ->where(
                'provider_id',
                auth()->id()
            )
            ->get();



            if ($services->count() != count($data['service_ids'])) {

                abort(403,'You can only add your services.');
            }



            $total = $services->sum('base_price');


            $discount = $data['discount'] ?? 0;


            $final = $total - ($total * $discount / 100);



            $imagePath = null;


            if(isset($data['image'])) {

                $imagePath = $data['image']->store(
                    'packages',
                    'public'
                );
            }



            $package = $provider->packages()->create([

                'image' => $imagePath,

                'name' => $data['name'],

                'description' => $data['description'] ?? null,

                'total_price' => $total,

                'discount' => $discount,

                'final_price' => $final,

                'status' => $data['status'] ?? 'active',

            ]);



            $package->services()->sync(
                $data['service_ids']
            );



            return $package->load('services');

        });
    }





    public function update(
        Package $package,
        array $data
    )
    {


        if(
            $package->service_provider_id 
            != auth()->user()->serviceProvider->id
        ){
            abort(403);
        }



        if(isset($data['image'])){


            if($package->image){

                Storage::disk('public')
                    ->delete($package->image);

            }


            $data['image'] = $data['image']->store(
                'packages',
                'public'
            );

        }




        if(isset($data['service_ids'])){


            $services = Service::whereIn(
                'id',
                $data['service_ids']
            )
            ->where(
                'provider_id',
                auth()->id()
            )
            ->get();



            if(
                $services->count() 
                != count($data['service_ids'])
            ){

                abort(403,'You can only add your services.');

            }



            $package->services()->sync(
                $data['service_ids']
            );


        }



       $discount = $data['discount'] ?? $package->discount;


$total = $package->services()
    ->sum('base_price');


$final = $total - ($total * $discount / 100);



$package->update(

    collect($data)
    ->except('service_ids')
    ->merge([
        'total_price' => $total,
        'discount' => $discount,
        'final_price' => $final,
    ])
    ->toArray()

);


        return $package->load('services');

    }







    public function delete(Package $package)
    {


        if(
            $package->service_provider_id 
            != auth()->user()->serviceProvider->id
        ){

            abort(403);

        }



        if($package->image){

            Storage::disk('public')
                ->delete($package->image);

        }



        return $package->delete();

    }






    public function index($providerId)
    {

        return Package::with([
            'services',
            'provider'
        ])
        ->where(
            'service_provider_id',
            $providerId
        )
        ->where(
            'status',
            'active'
        )
        ->get();

    }






    public function show(Package $package)
    {

        return $package->load([
            'services',
            'provider'
        ]);

    }

}