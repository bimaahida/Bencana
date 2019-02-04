<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Input;
use app\Bencana;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use Mapper;

class BencanaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('importExcel');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function import()
    {
        if(Input::hasFile('import_file')){
			$path = Input::file('import_file')->getRealPath();
			$data = Excel::load($path, function($reader) {})->get();
			if(!empty($data) && $data->count()){
                var_dump($data[9][0]);
				// foreach ($data as $key => $value) {
				// 	$insert[] = ['title' => $value->title, 'description' => $value->description];
				// }
				// if(!empty($insert)){
				// 	DB::table('items')->insert($insert);
				// 	dd('Insert Record successfully.');
				// }
            }
		}
    }
    public function maps()
    {
        
        Mapper::map(53.381128999999990000, -1.470085000000040000);
        Mapper::polygon(
            [
                [
                    'latitude' => 53.381128999999990000, 
                    'longitude' => -1.470085000000040000
                ], 
                [
                    'latitude' => 52.381128999999990000, 
                    'longitude' => 0.490085000000040000,
                ],
                [
                    'latitude' => 52.481128999999990000, 
                    'longitude' => 0.490085000000040000,
                ],
            ],
            [
                'strokeColor' => '#000000',
                'strokeOpacity' => 0.1,
                'strokeWeight' => 2,
                'fillColor' => '#FFFFFF'
            ]
        );
        return view('map');
    }
}
