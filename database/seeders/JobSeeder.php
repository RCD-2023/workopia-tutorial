<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Load joblistings from the file
        $jobListings = include database_path("seeders/data/job_listings.php");
        // get test user id
        $testUserId = User::where('email', 'test@test.com')->value('id');
        //get all other user id-s from user model
        $userIds = User::where('email', "!=", "test@test.com")->pluck("id")->toArray();

        foreach ($jobListings as $index => &$listing) {
            //
            if ($index < 2) {
                //assign the first 2 listings to the test user
                $listing["user_id"] = $testUserId;
            } else {
                //Asign user_id to listing
                $listing["user_id"] = $userIds[array_rand($userIds)];
            }

            //Add timestamp
            $listing['created_at'] = now();
            $listing['updated_at'] = now();
        }
        //insert job listings
        DB::table('job_listings')->insert($jobListings);
        echo 'Jobs created!';
    }
}
