<?php
namespace App\Http\Controllers;

use Illuminate\Support\Str;

use Illuminate\Http\Request;
use Exception;
use Redirect, Response, Session;
use ConsoleTVs\Profanity\Facades\Profanity;

use App\Pledges;

use Illuminate\Support\Facades\Mail;
use App\Mail\PledgeConfirmation;

class StripeController extends Controller
{

    public $customerID;

    public $tokenAmount = 1;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('payment');
    }

    /**
     * Pre-Auth Charge
     *
     * Grab and save customer payment details
     * but do NOT charge right away
     */
    public function preAuth (Request $request) {

        // Validate the form
        $validatedData = $request->validate([
            'pledge_level'          => 'required',
            'token_amount_custom'   => 'required|max:9999',
            'fname'                 => 'required_unless:remain_anon, 0',
            'lname'                 => 'required_unless:remain_anon, 0',
            'email'                 => 'required|email:rfc,dns,spoof',
            'message'               => 'max:100',
            'amount'                => 'min:1|max:999999'

        ]);

        // Get token amount
        $this->calculateTokenAmount($request);

        // Calculate pledge amount (in Stripe terms)
        $pledgeAmount = $this->calculatePledgeAmount($request->get('pledge_level'), $this->tokenAmount);

        //Check if existing customer. yes = update, no = create.
        $existingCustomer = Pledges::where('email', $request->get('email'))->first();

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        // Email doesn't exist
        if (!$existingCustomer) {

            //Create new Stripe customer
            $this->createCustomer($request);

            // Update their default payment source
            $source = \Stripe\Source::create([
                "type" => "card",
                "token" => $request->get('stripeToken'),
            ]);

            //Create a source so we can attach it to a customer
            $sourceCustomer = \Stripe\Customer::createSource(
                $this->customerID,
                [
                    'source' => $source
                ]
            );

            //Setup Intent (to begin to save their data and info)
            $intent = \Stripe\SetupIntent::create([
                'customer'              => $this->customerID,
                'payment_method'        => $source,
                'payment_method_types'  => ['card'],
                'confirm'               => true,
            ]);

            // Setup Payment Intent (for future charge)
            $paymentIntent = \Stripe\PaymentIntent::create([
                'customer'              => $this->customerID,
                'payment_method_types'  => ['card'],
                'payment_method'        => $source,
                'currency'              => 'usd',
                'amount'                => $pledgeAmount,
            ]);

            //Save pledge info to our DB
            $this->savePledge($request, $intent['id'], $paymentIntent['id'], $this->customerID);

        } else {

            // Update their default payment source
            $source = \Stripe\Source::create([
                "type" => "card",
                "token" => $request->get('stripeToken'),
            ]);

            //Create a source so we can attach it to a customer
            $sourceCustomer = \Stripe\Customer::createSource(
                $existingCustomer->stripe_customer_id,
                ['source' => $source]
            );

            //Setup Intent (to begin to save their data and info)
            $intent = \Stripe\SetupIntent::create([
                'customer'              => $existingCustomer->stripe_customer_id,
                'payment_method'        => $source,
                'payment_method_types'  => ['card'],
                'confirm'               => true,
            ]);

            // Setup Payment Intent (for future charge)
            $paymentIntent = \Stripe\PaymentIntent::create([
                'customer'              => $existingCustomer->stripe_customer_id,
                'payment_method_types'  => ['card'],
                'payment_method'        => $source,
                'currency'              => 'usd',
                'amount'                => $pledgeAmount,
            ]);

            //Save pledge info to our DB
            $this->savePledge($request, $intent['id'], $paymentIntent['id'], $existingCustomer->stripe_customer_id);
        }

        //Finally, display success message to UI
        Session::flash('message', (env('CONFIRMATION_MESSAGE')));
        Session::flash('class', 'success');

        return redirect('/');
    }

    /**
     * Create Customer
     *
     * Create new customer in Stripe
     */
    private function createCustomer (Request $request)
    {

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $displayName = $request->get('fname').' '.$request->get('lname');

        $customer = \Stripe\Customer::create([
            'description'   => (env('CONFIRMATION_EMAIL_HEADER')),
            'email'         => $request->get('email'),
            'name'          => $displayName
        ]);

        $this->customerID = $customer['id'];
    }

    /**
     * Save Pledge to DB
     */
    private function savePledge (Request $request, $intentID, $paymentIntentID, $customerID) {

        // Newsletter signup
        if ($request->get('nlSignup') == 1) {
            $nlSignup = true;
        } else {
            $nlSignup = false;
        }

        // Forum signup
        if ($request->get('forumSignup') == 1) {
            $forumSignup = true;
        } else {
            $forumSignup = false;
        }

        // Remain Anonymous
        if ($request->get('remain_anon') == 1) {
            $displayName = 'Anonymous';
        } else {
            $displayName = $request->get('fname').' '.$request->get('lname');
        }

        // Get token amount
        $this->calculateTokenAmount($request);

        // Calculate amount pledged
        $pledgeAmount = $this->calculatePledgeAmount($request->get('pledge_level'), $this->tokenAmount);

        // Send invites only if email address doesn't exist
        if (Pledges::where('email', '=', $request->get('email'))->count() < 1) {

            //Subscribe to newsletter
            if ($nlSignup) {
                SendyController::subscribeEmail($displayName, $request->get('email'));
            }

            //Invite to forum
            if ($forumSignup) {
                $this->discourseInvite($request->get('email'));
            }
        }

        // Save our pledge
        $pledge = new Pledges();
        $pledge->email                  = $request->get('email');
        $pledge->stripe_setup_intent_id = $intentID;
        $pledge->stripe_payment_intent  = $paymentIntentID;
        $pledge->stripe_customer_id     = $customerID;
        $pledge->pledge_amount          = $pledgeAmount;
        $pledge->pledge_level           = $request->get('pledge_level');
        $pledge->token_amount           = $this->tokenAmount;
        $pledge->display_name           = $displayName;
        $pledge->first_name             = $request->get('fname');
        $pledge->last_name              = $request->get('lname');
        $pledge->newsletter_join        = $nlSignup;
        $pledge->forum_join             = $forumSignup;
        $pledge->message                = Profanity::blocker($request->get('message'))->filter();
        $pledge->token                  = $request->get('stripeToken');
        $pledge->has_been_charged       = 0; //not yet charged
        $pledge->save();

        // Trigger confirmation email
        Mail::to($pledge->email)
            ->send(new PledgeConfirmation($pledge));
    }

    /**
     * Calculate Token Amount
     *
     * @param $request Request
     */
    private function calculateTokenAmount ($request) {

        // If the custom token amount is shown, use that value instead
        if ($request->get('token_amount_custom')) {
            if ($request->get('token_amount_custom') <= 0) {
                $tokenAmount = 1;
            } else if ($request->get('token_amount_custom') > 0) {
                $tokenAmount = $request->get('token_amount_custom');
            }
        }

        $this->tokenAmount = $tokenAmount;
    }

    /**
     * Calculate Total Amount
     *
     * @param $level string
     * @param $amount integer
     * @return integer
     */
    private function calculatePledgeAmount ($level, $amount) {

        $multiplier = 25;

        if ($level == 'customer') { $multiplier = 25; }
        if ($level == 'creator') { $multiplier = 50; }
        if ($level == 'developer') { $multiplier = 100; }

        return ($amount * $multiplier) * 100;
    }

    /**
     * Charge Stripe Card
     */
    public static function charge($customerID, $amount)
    {

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        \Stripe\Charge::create([
            'amount' => $amount,
            'currency' => 'usd',
            'customer' => $customerID
        ]);
    }

    /**
     * Get total pledged
     */
    public static function totalPledged ()
    {
        return Pledges::sum('pledge_amount');
    }

    /**
     * Discours Invite
     */
    private function discourseInvite ($email)
    {

        $client = new \GuzzleHttp\Client(['base_uri' => env('DISCOURSE_URL')]);

        $headers = [
            'Api-Key'       => env('DISCOURSE_API_KEY'),
            'Api-Username'  => env('DISCOURSE_API_USER'),
            'Accept'        => 'application/json',
        ];

        $response = $client->request('POST', '/invites', [
            'json'    => ['email' => $email],
            'headers' => $headers
        ]);

    }
}
