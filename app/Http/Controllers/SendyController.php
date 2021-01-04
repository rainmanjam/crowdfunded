<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Redirect, Response, Session;
use SendyPHP\SendyPHP;

use App\Pledges;

class SendyController extends Controller
{

    public static function subscribeEmail ($name, $email) {

        //-------------------------- You need to set these --------------------------//
        $your_installation_url  = env('SENDY_URL'); //Your Sendy installation (without the trailing slash)
        $list                   = env('SENDY_LIST_ID'); //Can be retrieved from "View all lists" page
        $api_key                = env('SENDY_API_KEY'); //Can be retrieved from your Sendy's main settings

        //POST variables
        //$email = 'rainmanjam+9000@gmail.com';

        //Subscribe
        $postdata = http_build_query(
            array(
                'name'      => $name,
                'email'     => $email,
                'list'      => $list,
                'api_key'   => $api_key,
                'boolean'   => 'true'
            )
        );

        $opts = array('http' => array('method'  => 'POST', 'header'  => 'Content-type: application/x-www-form-urlencoded', 'content' => $postdata));
        $context  = stream_context_create($opts);
        $result = file_get_contents($your_installation_url.'/subscribe', false, $context);
    }
}
