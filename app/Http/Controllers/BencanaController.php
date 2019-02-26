<?php

namespace Banjir\Http\Controllers;

use Illuminate\Http\Request;
use Input;
use app\Bencana;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;
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
        $datasExcel = [];
        // var_dump(Input::hasFile('import_files'));
        if(Input::hasFile('import_files')){
            $file = Input::file('import_files'); 
            $reader = ReaderFactory::create(Type::CSV);
            $reader->setFieldDelimiter(',');
            $reader->setEndOfLineCharacter("\n");
            // var_dump($file[0]->path());
            foreach ($file as $files) {
                $reader->open($files->path());
                foreach ($reader->getSheetIterator() as $sheet) {
                    $dataExcel = $this->readOrderSheet($sheet);
                    array_push($datasExcel,$dataExcel);
                }   
            }
            var_dump($datasExcel[1][21]);
            // $reader->close();
		}else{
            return redirect('bencana');
        }
    }

    public function readOrderSheet($sheet){
        $data = [];
        foreach ($sheet->getRowIterator() as $idx => $row) {
         array_push($data,$row);
        }
        return $data;
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
