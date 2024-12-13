<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DefaultUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach(range(1,5) as $i)
        {
            if (User::where('email', 'john.doe'.$i.'@helper.app')->count() == 0) {
                $user = User::create([
                    'name' => 'John DOE '.$i,
                    'email' => 'john.doe'.$i.'@helper.app',
                    'password' => bcrypt('Passw@rd'),
                    'email_verified_at' => now()
                ]);
                $user->creation_token = null;
                $user->save();
            }
        }
    }
}
