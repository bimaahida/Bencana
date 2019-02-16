<?php

namespace Banjir\Http\Controllers;

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
        if(Input::hasFile('import_files')){
            $data_cel = [];
            $input = Input::file('import_files'); 
            foreach ($input as $key) {
                $path = $key->getRealPath();
                //get value dari row ke 2
                // Excel::selectSheets('sheet1')->load();
                $data = Excel::load($path, function($reader) {})->get()->toArray();
                    var_dump($data[6]);
                //get value row ke 1
                // $headerRow = $data->first()->keys()->toArray();
                // if(!empty($data) && $data->count()){
                //     array_push($data_cel,$data);
                // 	// foreach ($data as $key => $value) {
                // 	// 	$insert[] = ['title' => $value->title, 'description' => $value->description];
                // 	// }
                // 	// if(!empty($insert)){
                // 	// 	DB::table('items')->insert($insert);
                // 	// 	dd('Insert Record successfully.');
                // 	// }
                // }
            }
            // var_dump($data_cel);
			
		}else{
            return redirect('bencana');
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
