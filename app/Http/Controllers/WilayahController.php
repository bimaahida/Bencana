<?php

namespace Banjir\Http\Controllers;

use Illuminate\Http\Request;
use Banjir\Http\Controllers\Controller;
use Banjir\AreaModel;
use Banjir\LatlongModel;
use Yajra\Datatables\Datatables;
use Banjir\Exceptions\Handler;
use Validator;
use Mapper;

class WilayahController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected $rule = array(
        'name' => 'required',
    );
    public function index()
    {
        // $limit = 5;
        // $data = AreaModel::paginate($limit);
        // $no = $limit * ($data->currentPage() - 1);
        // return view('wilayah.list',compact('data','no'));
        return view('wilayah.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = array(
            'title' => "Add Area",
            'action' => "WilayahController@store",
            'button' => "Save",
            'metod' => "post"
        );
            
        return view('wilayah.create',$data);
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
                ->route('wilayah.create')
                ->withErrors($validator)
                ->withInput();
        }else{
            $data = New AreaModel;
            $data->name = $request->name;
            $data->save();

            return redirect()
                ->route('wilayah.index')
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
        $data = AreaModel::with('LatLongs')->where('id',$id)->get();
        $params = array();

        if(empty($data)){
            return redirect()
                ->route('wilayah.index')
                ->withErrors('Data Not Found !');
        }else{
            foreach ($data as $key) {
                foreach($key->LatLongs as $lat){
                    $a = array(
                        'latitude' => $lat->latitude,
                        'longitude' => $lat->longitude,
                    );
                    array_push($params,$a);
                }
            }
            Mapper::map(53.381128999999990000, -1.470085000000040000);
            Mapper::polygon(
                $params,
                [
                    'strokeColor' => '#000000',
                    'strokeOpacity' => 0.1,
                    'strokeWeight' => 2,
                    'fillColor' => '#FFFFFF'
                ]
            );
            return view('wilayah.detail',$data[0]);   
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $find = AreaModel::find($id);
        if (empty($find)) {
            return redirect()
                ->route('wilayah.index')
                ->withErrors('Data Not Found !');
        }else{
            $param = array(
                'id' => $find->id,
            );
            $data = array(
                'data' => $find,
                'title' => "Edit Area",
                'action' => "WilayahController@update",
                'param' =>  $param,
                'button' => "Update",
                'metod' => "post"
            );
            return view('wilayah.create',$data);
        }
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
        $validator = Validator::make($request->all(),$this->rule);
        if($validator->fails()){
            return redirect()
                ->route('wilayah.edit',['id'=>$id])
                ->withErrors($validator)
                ->withInput();
        }else{
            $data = AreaModel::find($id);
            $data->name = $request->name;
            $data->update();
            return redirect()
                ->route('wilayah.index')
                ->with('alert-success','Update record Sucsessed!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = AreaModel::where('id',$id)->first();
        if (empty($data)) {
            return redirect()
                ->route('wilayah.index')
                ->withErrors('Data Not Found !');
        }else{
            $data->delete();
            return redirect()
                ->route('wilayah.index')
                ->with('alert-success','Delete record Sucsessed!');
        }
    }
    public function dataTables(){
        $datas = AreaModel::all();
        return Datatables::of($datas)
        ->addColumn('edit_url', function ($datas) {
            return route('wilayah.edit', $datas->id);
        })
        ->addColumn('delete_url', function ($datas) {
            return route('wilayah.destroy', $datas->id);
        })
        ->addColumn('detail_url', function ($datas) {
            return route('wilayah.show', $datas->id);
        })
        ->make(true);
    }
    public function dataTablesDetail($area){
        $datas = LatlongModel::where('area_id',$area)->get();
        return Datatables::of($datas)
        ->addColumn('delete_url', function ($datas) {
            return route('position.destroy', ['id' => $datas->id,'area' => $datas->area_id]);
        })
        ->make(true);
    }

}
