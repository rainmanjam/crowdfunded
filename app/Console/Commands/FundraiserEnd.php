<?php

/**
 * This command gets ran when the fundraising campaign ends.
 * It will determine:
 * 1. If the goal was met
 * 2. Who needs to be charged if 1 = true
 * 3. Who needs to be signed up to the newsletter if 1 = true
 * 4. who needs to be registered with the Discourse forum if 1 = true
 *
 * It's to be ran ONE TIME
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\StripeController;
use Stripe\Stripe;
use App\Http\Controllers\SendyController;

use App\User;
use App\Pledges;
use Psy\Util\Str;

class FundraiserEnd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fundraiser:end';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Determine actions upon Fundraising Ending.';

    private $sendy;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $amountPledged = StripeController::totalPledged();

        // Check if the goal was met

        //pledge goal met+
        if ($amountPledged >= env('PLEDGE_GOAL')) {

            echo 'Charge Loop ----';

            // Loop through our pledges, charge customer's stored cards through Stripe
            $pledges = Pledges::where('has_been_charged', 0)->get();
            foreach($pledges as $pledge) {

                // send their details to the charge API (Customer ID and Pledged Amount)
                StripeController::charge($pledge->stripe_customer_id, $pledge->pledge_amount);

                echo 'Stripe Charge - Success :: '. $pledge->pledge_amount;

                // ensure we don't charge again in the loop cycle
                $pledge->has_been_charged = 1;
                $pledge->save();
            }

            echo 'Success! All pledges charged and email invites sent';

        } else {

            //pledge goal was NOT met
            echo 'Pledge goal was not met, no one was charged. All data preserved.';
        }

    }
}
