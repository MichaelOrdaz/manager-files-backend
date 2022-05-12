<?php

namespace Database\Seeders;

use App\Helpers\Dixa;
use App\Models\Action;
use Illuminate\Database\Seeder;

class ActionHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $actions = Dixa::HISTORY_ACTIONS;

        foreach ($actions as $action) {
            Action::create([
                'name' => $action
            ]);
        }
    }
}
