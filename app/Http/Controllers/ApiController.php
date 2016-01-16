<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class ApiController extends Controller {

	/**
	 * Display a foursquare api call json response
	 *
	 * @return Response
	 */
	public function get_foursquare()
	{
		return view('api.foursquare');
	}

	public function post_foursquare(Request $request)
	{	

		$area = $request['area'];
		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.foursquare.com/v2/venues/explore?near='.$area.'&oauth_token=1MZTZYIARGVDAGDQAHOVESDUR3P4OFZA2ABTIBESMJNNJM0T&v=20160106');

        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


        $contents = curl_exec ($ch);
        $result = json_decode($contents);

        dd($result,$ch);
      

        curl_close ($ch);
	}

	/**
	 * Display rome2rio api call response
	 *
	 * @return Response
	 */
	public function get_rome2rio(Request $request)
	{
		return view('api.rome2rio');
	}


	public function post_rome2rio(Request $request)
	{
		/**
         * $origin and $destination pwede ra e static
         * tang tanga lang ang parameter na $request
         */
        $origin = $request['origin'];
        $destination  = $request['destination'];
        /**
         * $url = API url
         * kani ray ilisi earl
         */
        $url = "http://free.rome2rio.com/api/1.2/json/Search?key=nKYL3BZS&oName=".$origin."&dName=".$destination;

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);

        $data = json_decode($data);

        dd($data,$ch);
        
        curl_close($ch);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function testAjax()
	{
		return response()->json('connected..',200);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
