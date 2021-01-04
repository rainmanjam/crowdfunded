<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Redirect,Response;
use GuzzleHttp\Client;

use App\Pledges;

class MainController extends Controller
{

    private $pledgeGoal;
    private $recentPledgeLimit  = 25; // Show the last x recent pledges
    private $topPledgeLimit     = 25; // Show the last x top pledges
    private $pledgeAmountSum    = 0; // Total pledge amount so far
    private $pledgePercent      = 0; // Percent of pledge goal

    public function __construct()
    {

        //Set our pledge goal.
        $this->pledgeGoal = env('PLEDGE_GOAL');
    }

    /**
     * Display Home Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index ()
    {

        // Recent pledges

        $recentPledgesList = cache()->remember('recentPledgesList', 60, function(){
            return Pledges::orderBy('id', 'desc')->take($this->recentPledgeLimit)->get(); });

        // Recent top pledges
        $topPledgesList = cache()->remember('topPledgesList', 60, function(){
            return Pledges::take($this->topPledgeLimit)->orderBy('pledge_amount', 'desc')->get(); });

        // Calculate Pledge Goal
        $this->calculatePledgeGoal();

        $totalAmount = cache()->remember('totalAmount', 60, function(){
            return Pledges::sum('token_amount'); });

        return view('welcome')
            ->with('totalPledgeAmount', $this->pledgeAmountSum)
            ->with('pledgeGoal', $this->pledgeGoal)
            ->with('pledgePercent', $this->pledgePercent)
            ->with('recentPledges', $recentPledgesList)
            ->with('topPledges', $topPledgesList)
            ->with('totalAmount', $totalAmount);
    }

    /**
     * Calculate Pledge %
     *
     * Calculate the remaining % of the pledge
     * goal and feed to the progress bar
     */
    private function calculatePledgeGoal ()
    {

        $pledgeSum = cache()->remember('pledgeSum', 60, function(){
            return Pledges::with('pledge_amount')->sum('pledge_amount'); });

        // Set our pledge amount sum
        $this->pledgeAmountSum = round($pledgeSum, 2) / 100;

        if ( $this->pledgeGoal > 0 ) {
            $this->pledgePercent = round(($pledgeSum / 100) / ($this->pledgeGoal / 100), 2);
        } else {
            $this->pledgePercent = 0;
        }

    }
}
