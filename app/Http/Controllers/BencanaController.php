<?php

namespace Banjir\Http\Controllers;

use Illuminate\Http\Request;
use Input;
use Banjir\ParameterModel;
use Banjir\LatlongModel;
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
        $valueData = [];
        $dataParameter = array(
            array(28,175),
            array(29,174),
            array(29,175),
            array(29,176),
            array(30,173),
            array(30,174),
            array(30,175),
            array(30,176),
            array(30,177),
            array(31,174),
            array(31,175),
            array(31,176),
            array(32,175),
         );

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
            $reader->close();
            for ($i=0; $i < count($file) ; $i++) { 
                $sumValue = 0;
                foreach ($dataParameter as $value) {
                    $sumValue += $datasExcel[$i][$value[0]][$value[1]];
                }
                array_push($valueData,$sumValue/count($dataParameter));
            }
            dd($valueData);
        }else{
            return redirect('bencana');
        }
    }

    public function NaiveBayes($dataUji){
        $dataLatih = [];

        $paramRainfall = array(
            'rendah' => array(
                'aman' => array(),
                'rawan' => array(),
                'prob_aman'=> 0,
                'prob_rawan'=> 0,
            ),
            'sedang' => array(
                'aman' => array(),
                'rawan' => array(),
                'prob_aman'=> 0,
                'prob_rawan'=> 0,
            ),
            'tinggi' => array(
                'aman' => array(),
                'rawan' => array(),
                'prob_aman'=> 0,
                'prob_rawan'=> 0,
            ),
        );
        $paramSlope = array(
            'landai' => array(
                'aman' => array(),
                'rawan' => array(),
                'prob_aman'=> 0,
                'prob_rawan'=> 0,
            ),
            'sedang' => array(
                'aman' => array(),
                'rawan' => array(),
                'prob_aman'=> 0,
                'prob_rawan'=> 0,
            ),
            'curam' => array(
                'aman' => array(),
                'rawan' => array(),
                'prob_aman'=> 0,
                'prob_rawan'=> 0,
            ),
        );
        $paramSoil = array(
            'soil' => array(
                'aman' => array(),
                'rawan' => array(),
                'prob_aman'=> 0,
                'prob_rawan'=> 0,
            ),
            'non_soil' => array(
                'aman' => array(),
                'rawan' => array(),
                'prob_aman'=> 0,
                'prob_rawan'=> 0,
            ),
        );

        $data = ParameterModel::where('area_id',$dataUji['area_id'])->get();
        $aman = ParameterModel::where('area_id',$dataUji['area_id'])->where('status','aman')->get();
        $rawan = ParameterModel::where('area_id',$dataUji['area_id'])->where('status','rawan')->get();

        foreach ($data as $key) {
            $rainfall = $this->rescale_rain($key->rainfall);

            $slope = $this->rescale_slope($key->slope);

            $params = array(
                'rainfall' => $rainfall,
                'slope' => $slope,
                'soil' => $key->soil,
                'area_id' => $key->area_id,
                'status' => $key->status,
                'date' => $key->date
            );

            if($params['rainfall'] == 'RENDAH'){
                if($params['status'] == 'AMAN'){
                    array_push($paramRainfall['rendah']['aman'],$params);
                }else{
                    array_push($paramRainfall['rendah']['rawan'],$params);
                }
            }elseif ($params['rainfall'] == 'SEDANG') {
                if($params['status'] == 'AMAN'){
                    array_push($paramRainfall['sedang']['aman'],$params);
                }else{
                    array_push($paramRainfall['sedang']['rawan'],$params);
                }
            }else{
                if($params['status'] == 'AMAN'){
                    array_push($paramRainfall['tinggi']['aman'],$params);
                }else{
                    array_push($paramRainfall['tinggi']['rawan'],$params);
                }
            }

            if($params['slope'] == 'LANDAI'){
                if($params['status'] == 'AMAN'){
                    array_push($paramSlope['landai']['aman'],$params);
                }else{
                    array_push($paramSlope['landai']['rawan'],$params);
                }
            }elseif ($params['slope'] == 'SEDANG') {
                if($params['status'] == 'AMAN'){
                    array_push($paramSlope['sedang']['aman'],$params);
                }else{
                    array_push($paramSlope['sedang']['rawan'],$params);
                }
            }else{
                if($params['status'] == 'AMAN'){
                    array_push($paramSlope['curam']['aman'],$params);
                }else{
                    array_push($paramSlope['curam']['rawan'],$params);
                }
            }

            if($params['soil'] == 'SOIL'){
                if($params['status'] == 'AMAN'){
                    array_push($paramSoil['soil']['aman'],$params);
                }else{
                    array_push($paramSoil['soil']['rawan'],$params);
                }
            }else{
                if($params['status'] == 'AMAN'){
                    array_push($paramSoil['non_soil']['aman'],$params);
                }else{
                    array_push($paramSoil['non_soil']['rawan'],$params);
                }
            }
            array_push($dataLatih,$params);
        }

        $dataUji['rainfall'] = $this->rescale_rain($dataUji['rainfall']);
        $dataUji['slope'] = $this->rescale_slope($dataUji['slope']); 

        $paramRainfall['rendah']['prob_aman'] = count($paramRainfall['rendah']['aman'])/count($aman);
        $paramRainfall['rendah']['prob_rawan'] = count($paramRainfall['rendah']['rawan'])/count($rawan);
        $paramRainfall['sedang']['prob_aman'] = count($paramRainfall['sedang']['aman'])/count($aman);
        $paramRainfall['sedang']['prob_rawan'] = count($paramRainfall['sedang']['rawan'])/count($rawan);
        $paramRainfall['tinggi']['prob_aman'] = count($paramRainfall['tinggi']['aman'])/count($aman);
        $paramRainfall['tinggi']['prob_rawan'] = count($paramRainfall['tinggi']['rawan'])/count($rawan);

        $paramSlope['landai']['prob_aman'] = count($paramSlope['landai']['aman'])/count($aman);
        $paramSlope['landai']['prob_rawan'] = count($paramSlope['landai']['rawan'])/count($rawan);
        $paramSlope['sedang']['prob_aman'] = count($paramSlope['sedang']['aman'])/count($aman);
        $paramSlope['sedang']['prob_rawan'] = count($paramSlope['sedang']['rawan'])/count($rawan);
        $paramSlope['curam']['prob_aman'] = count($paramSlope['curam']['aman'])/count($aman);
        $paramSlope['curam']['prob_rawan'] = count($paramSlope['curam']['rawan'])/count($rawan);


        $paramSoil['soil']['prob_aman'] = count($paramSoil['soil']['aman'])/count($aman);
        $paramSoil['soil']['prob_rawan'] = count($paramSoil['soil']['rawan'])/count($rawan);
        $paramSoil['non_soil']['prob_aman'] = count($paramSoil['non_soil']['aman'])/count($aman);
        $paramSoil['non_soil']['prob_rawan'] = count($paramSoil['non_soil']['rawan'])/count($rawan);

        $rainRawan = $paramRainfall[strtolower($dataUji['rainfall'])]['prob_rawan'];
        $rainAman =  $paramRainfall[strtolower($dataUji['rainfall'])]['prob_aman'];
        $slopeRawan = $paramSlope[strtolower($dataUji['slope'])]['prob_rawan'];
        $slopeAman = $paramSlope[strtolower($dataUji['slope'])]['prob_aman'];
        if(strtolower($dataUji['soil']) == 'soil'){
            $soiltype = strtolower($dataUji['soil']);
        }else{
            $soiltype = 'non_soil';
        }
        $soilRawan = $paramSoil[$soiltype]['prob_rawan'];
        $soilAman = $paramSoil[$soiltype]['prob_aman'];

        $pAman = ($rainAman * $slopeAman * $soilAman)/count($aman);
        $pRawan = ($rainRawan * $slopeRawan * $soilRawan)/count($rawan);

        if($pAman > $pRawan){
            echo "Status Aman \n";
            echo "Nilai : ".$pAman;
        }else{
            echo "Status Rawan \n";
            echo "Nilai : ".$pRawan;
        }
        // dd($dataUji);
        
    }
    public function rescale_rain($param){
        if($param < 5){
            return 'RENDAH';
        }elseif ($param >= 5 && $param < 10) {
            return 'SEDANG';
        }else{
            return 'TINGGI';
        }
    }
    public function rescale_slope($param){
        if($param < 5){
            return 'CURAM';
        }elseif ($param >= 5 && $param < 10) {
            return 'SEDANG';
        }else{
            return 'LANDAI';
        }
    }
    public function readOrderSheet($sheet){
        $data = [];
        foreach ($sheet->getRowIterator() as $idx => $row) {
         array_push($data,$row);
        }
        return $data;
    }
    public function maps(){
        $data = LatlongModel::with('area')->get();
        $params = array(
            array(),
            array(),
            array(),
        );
        $color = array(
            array(
                'strokeColor' => '#0000FF',
                'strokeOpacity' => 0.8,
                'strokeWeight' => 2,
                'fillColor' => '#0000FF',
                'fillOpacity' => 0.4
            ),
            array(
                'strokeColor' => '#f1c40f',
                'strokeOpacity' => 0.8,
                'strokeWeight' => 2,
                'fillColor' => '#f1c40f',
                'fillOpacity' => 0.4
            ),
            array(
                'strokeColor' => '#8e44ad',
                'strokeOpacity' => 0.8,
                'strokeWeight' => 2,
                'fillColor' => '#8e44ad',
                'fillOpacity' => 0.4
            ),
        );
        foreach ($data as $key) {
            $a = array(
                'latitude' => $key->latitude,
                'longitude' => $key->longitude,
            );
            if($key->area_id == 1){
                array_push($params[0],$a);
            }else if($key->area_id == 2){
                array_push($params[1],$a);
            }else{
                array_push($params[2],$a);
            }
        }
        Mapper::map(-7.642431,110.47158);
        
        for ($i=0; $i < 3; $i++) { 
            Mapper::polygon(
                $params[$i],
                $color[$i]
            );   
        }
        return view('map');
    }

    public function loadData(){
        $dataTest = array(
            'rainfall' => 5.00,
            'soil' => 'SOIL',
            'slope' => 23.7,
            'area_id' => 1,
        );
        $this->NaiveBayes($dataTest);
        // $datasExcel = [];
        // $file = "C:\Users\BM\Google Drive\Skripsi\Percobaan NaiveBayes.xlsx"; 
        // $reader = ReaderFactory::create(Type::XLSX );
        // // $reader->setFieldDelimiter(',');
        // // $reader->setEndOfLineCharacter("\n");
        // $reader->open($file);
        //     foreach ($reader->getSheetIterator() as $sheet) {
        //         $dataExcel = $this->readOrderSheet($sheet);
        //         array_push($datasExcel,$dataExcel);
        //     }   
        // // var_dump($datasExcel[0]);
        // $reader->close();
        // for ($i=0; $i < count($datasExcel[0]) ; $i++) { 
        //     if($i > 0){
        //         $sungai = $datasExcel[0][$i][0]." ".$datasExcel[0][$i][1];
        //         if($datasExcel[0][$i][0] == "Gendol"){
        //             if ($sungai == "Gendol Tengah") {
        //                 $areaid = 1;
        //             }else if($sungai == "Gendol Hulu"){
        //                 $areaid = 2;
        //             }else{
        //                 $areaid = 3;
        //             }

        //             $data = New ParameterModel;
        //             $data->rainfall = $datasExcel[0][$i][2];
        //             $data->soil = $datasExcel[0][$i][8];
        //             $data->slope = $datasExcel[0][$i][5];
        //             $data->status = $datasExcel[0][$i][9];
        //             $data->area_id = $areaid;
        //             $data->save();
        //             echo "Save success \n";
        //         }else{
        //             echo "gagal\n";
        //         }
        //     }else{
        //         echo "1 \n";
        //     }
        // }
        // var_dump($dataExcel[0]);      
    }

}
