<?php

namespace Banjir\Http\Controllers;

use Illuminate\Http\Request;

use Banjir\Http\Controllers\Controller;

use Banjir\LatlongModel;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

class LatlongController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected $rule = array(
        'latitude' => 'required',
        'longitude' => 'required',
        'area' => 'required'
    );
    
    public function index()
    {
        //
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
        $validator = Validator::make($request->all(),$this->rule);
        // var_dump($validator->fails());
        if($validator->fails()){
            return redirect()
                ->route('wilayah.show',['id' => $request->id])
                ->withErrors($validator)
                ->withInput();
        }else{
            $data = New LatlongModel;
            $data->latitude = $request->latitude;
            $data->longitude = $request->longitude;
            $data->area_id = $request->id;
            $data->save();

            return redirect()
                ->route('wilayah.show',['id' => $request->id])
                ->with('alert-success','Insert record Sucsessed!');
        }
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
    public function destroy($id,$area)
    {
        $data = LatlongModel::where('id',$id)->first();
        if (empty($data)) {
            return redirect()
                ->route('wilayah.show',['id' => $id])
                ->withErrors('Data Not Found !');
        }else{
            $data->delete();
            return redirect()
                ->route('wilayah.show',['id' => $id,'area' => $area])
                ->with('alert-success','Delete record Sucsessed!');
        }
    }
    public function loadData(){
        $datasExcel = [];
        $file = "C:\Users\BM\Google Drive\Skripsi\Pemetaan Wilayah.xlsx"; 
        $reader = ReaderFactory::create(Type::XLSX );
        $reader->open($file);

        foreach ($reader->getSheetIterator() as $sheet) {
            $dataExcel = $this->readOrderSheet($sheet);
            array_push($datasExcel,$dataExcel);
        }   
        
        for ($i=0; $i < count($datasExcel) ; $i++) { 
            if($i == 0){
                $area_id = 3;
            }else if($i == 2){
                $area_id = 1;
            }else{
                $area_id = 2;
            }

            for ($a=0; $a < count($datasExcel[$i]) ; $a++) { 
                $data = New LatlongModel;
                $data->latitude = $datasExcel[$i][$a][0];
                $data->longitude = $datasExcel[$i][$a][1];
                $data->area_id = $area_id;
                $data->save();
            }
        }
        $reader->close();
        dd($datasExcel);
    }
    public function readOrderSheet($sheet){
        $data = [];
        foreach ($sheet->getRowIterator() as $idx => $row) {
         array_push($data,$row);
        }
        return $data;
    }
}
