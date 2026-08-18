<?php

namespace Database\Seeders;

use App\Http\Resources\WorkingHourResource;
use App\Models\Governorate;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Psy\Readline\Hoa\Event;


class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {


       $this->call([
          GovernorateSeeder::class,
   CitySeeder::class,
     CategorySeeder::class,
    //RolesAndPermissionsSeeder::class,

    UserSeeder::class,
StripeAccountSeeder::class,
    ServiceProviderCategorySeeder::class,
    PortfolioSeeder::class,
   // ServiceProviderDocumentSeeder::class,
    WorkingHoursSeeder::class,
    TimeOffSeeder::class,


 EventTypeSeeder::class,
    EventSeeder::class,

        EventSeeder::class,

    ServiceSeeder::class,
    ServiceImageSeeder::class,
    FeatureSeeder::class,
    ServiceLinkSeeder::class,

    ServiceOfferSeeder::class,

    PackageSeeder::class,
   // PackageServiceSeeder::class,

   ServiceProviderGallerySeeder::class,

   // ServiceSeeder::class,

    BookingSeeder::class,

    PaymentSeeder::class,

    PaymentTransferSeeder::class,

    ProviderPayoutSeeder::class,

    BookingComplaintSeeder::class,
    

    BookingPriceProposalSeeder::class,
ServiceProviderGallerySeeder::class,
    RefundSeeder::class,
    SearchHistorySeeder::class,
    ReviewSeeder::class,
    FavoriteSeeder::class,
    ConversationSeeder::class,
    NotificationSeeder::class,
       MessageSeeder::class,
           EventTypeCategoryBudgetSeeder::class,
]);
    }
}
