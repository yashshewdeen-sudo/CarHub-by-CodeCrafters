<?php
namespace Database\Seeders;

use App\Models\{User, Category, Tag, Listing};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Users
        User::create(['name'=>'Site Admin','email'=>'admin@carhub.local','password'=>Hash::make('Admin123'),'phone'=>'57000000','role'=>'Admin']);
        $aditya   = User::create(['name'=>'Aditya Ramdeworsing','email'=>'aditya@carhub.local','password'=>Hash::make('Admin123'),'phone'=>'57111111','role'=>'Seller']);
        $priyanka = User::create(['name'=>'Priyanka Teeluckdharee','email'=>'priyanka@carhub.local','password'=>Hash::make('Admin123'),'phone'=>'57222222','role'=>'Seller']);
        $yash     = User::create(['name'=>'Yash Shewdeen','email'=>'yash@carhub.local','password'=>Hash::make('Admin123'),'phone'=>'57333333','role'=>'Seller']);

        // Categories
        $sport = Category::create(['name'=>'Sports Car','slug'=>'sports-car']);
        $sedan = Category::create(['name'=>'Sedan','slug'=>'sedan']);
        $suv   = Category::create(['name'=>'SUV','slug'=>'suv']);
        $hatch = Category::create(['name'=>'Hatchback','slug'=>'hatchback']);

        // Tags
        $tagNames = ['Imported','Limited Edition','Manual','Automatic','Track-Ready','Family','Eco','Luxury'];
        $tags = collect($tagNames)->mapWithKeys(fn($n)=>[$n => Tag::create(['name'=>$n,'slug'=>Str::slug($n)])]);

        // Listings
        $bmw = Listing::create([
            'seller_id'=>$aditya->id,'category_id'=>$sport->id,
            'make'=>'BMW','model'=>'M4 CSL','year'=>2023,'mileage'=>5000,'price'=>8800000,
            'fuel_type'=>'Petrol','transmission'=>'Automatic','condition_status'=>'Used',
            'description'=>'Limited 50 Years of M edition. 543hp twin-turbo straight-six.','status'=>'Active',
        ]);
        $bmw->tags()->attach([$tags['Imported']->id, $tags['Limited Edition']->id, $tags['Track-Ready']->id, $tags['Luxury']->id]);

        $porsche = Listing::create([
            'seller_id'=>$priyanka->id,'category_id'=>$sport->id,
            'make'=>'Porsche','model'=>'911 Turbo S','year'=>2020,'mileage'=>35000,'price'=>27850000,
            'fuel_type'=>'Petrol','transmission'=>'Automatic','condition_status'=>'Used',
            'description'=>'650hp flat-six, AWD, PDK 8-speed.','status'=>'Active',
        ]);
        $porsche->tags()->attach([$tags['Imported']->id, $tags['Luxury']->id, $tags['Automatic']->id]);

        $yaris = Listing::create([
            'seller_id'=>$yash->id,'category_id'=>$hatch->id,
            'make'=>'Toyota','model'=>'GR Yaris GRMN','year'=>2022,'mileage'=>5000,'price'=>4650000,
            'fuel_type'=>'Petrol','transmission'=>'Manual','condition_status'=>'Used',
            'description'=>'Track-focused homologation special.','status'=>'Active',
        ]);
        $yaris->tags()->attach([$tags['Imported']->id, $tags['Limited Edition']->id, $tags['Manual']->id, $tags['Track-Ready']->id]);

        // A handful of pending listings to demonstrate pagination
        for ($i = 1; $i <= 15; $i++) {
            $l = Listing::create([
                'seller_id'        => $aditya->id,
                'category_id'      => $sedan->id,
                'make'             => 'Toyota',
                'model'             => "Corolla v$i",
                'year'             => 2015 + ($i % 8),
                'mileage'          => 20000 + $i * 1000,
                'price'            => 350000 + $i * 50000,
                'fuel_type'        => 'Petrol',
                'transmission'     => $i % 2 ? 'Manual' : 'Automatic',
                'condition_status' => 'Used',
                'description'      => "Demo listing #$i for pagination.",
                'status'           => 'Active',
            ]);
            $l->tags()->attach([$tags['Family']->id, $tags['Eco']->id]);
        }
    }
}
