<?php

namespace Banjir\Http\Controllers;

use Illuminate\Http\Request;
use Input;
use Yajra\Datatables\Datatables;
use Banjir\ParameterModel;
use Banjir\AreaModel;
use Banjir\ModelModel;
use Banjir\LocationModel;
use Banjir\ParameterBayesModel;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;
use Storage;
use Session;
use URL;

use Mapper;

class BencanaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $data = array(
            'model' => ModelModel::all(),
        );
        return view('importExcel',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id){
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        //
    }
    public function naiveBayesManual(Request $request){
        $parameterProbabilitas = array(
            'aman' => array(),
            'rawan' => array(),
            'paramSoil' => array(),
            'paramSlope' => array(),
            'paramRainfall' => array(),
        );
        
        $dataLocation = New LocationModel;
        $dataLocation->soil = $request->soil;
        $dataLocation->slope = $request->slope;
        $dataLocation->latitude = $request->latitude;
        $dataLocation->longitude = $request->longitude;
        $dataLocation->colum = $request->colum;
        $dataLocation->row = $request->row;
        $dataLocation->area_id = $request->area_id;
        $dataLocation->save();

        $parameter = array(
            'rainfall' =>  $request->rainFall,
            'soil' => $request->soil,
            'slope' => $request->slope,
            'location_id' => $dataLocation->id,
            'date'=> date('Y-m-d H:i:s'),
            'status' => '',
        );
        $parameter['status'] = $this->NaiveBayes($parameter,$parameterProbabilitas,116880,135917);

        $dataParamter = New ParameterBayesModel;
        $dataParamter->rainfall = round($parameter['rainfall'], 5);
        $dataParamter->soil = $parameter['soil'];
        $dataParamter->slope = $parameter['slope'];
        $dataParamter->status = $parameter['status']['text'];
        $dataParamter->location_id = $parameter['location_id'];
        $dataParamter->date = $parameter['date'];
        $dataParamter->save();

        return $parameter;
    }
    public function import(Request $request){
        $maxMin_id = explode(',',$request->model_naivebayes);

        // dd($maxMin_id);

        $sumValHulu = 0;
        $parameterProbabilitas = array(
            'aman' => array(),
            'rawan' => array(),
            'paramSoil' => array(),
            'paramSlope' => array(),
            'paramRainfall' => array(),
        );

        $datasExcel = [];
        $valueData = array(
            'vHulu' => array(),
            'akurasi' => 0,
        );
        $location = LocationModel::get();

        if(Input::hasFile('import_files')){
            $file = Input::file('import_files'); 
            $reader = ReaderFactory::create(Type::CSV);
            $reader->setFieldDelimiter(',');
            $reader->setEndOfLineCharacter("\n");

            foreach ($file as $files) {
                $reader->open($files->path());
                foreach ($reader->getSheetIterator() as $sheet) {
                    $dataExcel = $this->readOrderSheet($sheet);
                    array_push($datasExcel,$dataExcel);
                }   
            }
            $reader->close();
            
            for ($i=0; $i < count($file) ; $i++) { 
                $date = explode("_",$file[$i]->getClientOriginalName());
                $date_now = $date[1][0].$date[1][1].$date[1][2].$date[1][3].'-'.$date[1][4].$date[1][5].'-'.$date[1][6].$date[1][7].' '.$date[2][0].$date[2][1].':00:00';
                $a = 0;
                foreach ($location as $key) {
                    $keyNew = $key->colum - 1;

                    $parameter = array(
                        'rainfall' =>  $datasExcel[$i][$keyNew][$key->row],
                        'soil' => $key->soil,
                        'slope' => $key->slope,
                        'location_id' => $key->id,
                        // 'date'=> date('Y-m-d H:i:s'),
                        'date'=> $date_now,
                        'status' => '',
                    );
                    $parameter['status'] = $this->NaiveBayes($parameter,$parameterProbabilitas,$maxMin_id[0],$maxMin_id[1]);
                    
                    array_push($valueData['vHulu'],$parameter);

                    $parameterProbabilitas['aman'] = $parameter['status']['Aman'];
                    $parameterProbabilitas['rawan'] = $parameter['status']['Rawan'];
                    $parameterProbabilitas['paramSoil'] = $parameter['status']['paramSoil'];
                    $parameterProbabilitas['paramSlope'] = $parameter['status']['paramSlope'];
                    $parameterProbabilitas['paramRainfall'] = $parameter['status']['paramRainfall'];

                    $data = New ParameterBayesModel;
                    $data->rainfall = round($parameter['rainfall'], 5);
                    $data->soil = $parameter['soil'];
                    $data->slope = $parameter['slope'];
                    $data->status = $parameter['status']['text'];
                    $data->location_id = $parameter['location_id'];
                    $data->date = $parameter['date'];
                    $data->save();

                    $a++;
                }
            }
            // $total = 0;

            // $ExcelDataLatih = [];
            // $file = "D:\Naive Bayes per cordinat.xlsx"; 
            // $reader = ReaderFactory::create(Type::XLSX);
            // $reader->open($file);
            
            // foreach ($reader->getSheetIterator() as $sheet) {
            //     $dataExcel = $this->readOrderSheet($sheet);
            //     array_push($ExcelDataLatih,$dataExcel);
            // }   
            
            // $reader->close();

            // for ($i=0; $i < count($valueData['vHulu']) ; $i++) { 
            //     if(strtolower($valueData['vHulu'][$i]['status']['text']) == strtolower($ExcelDataLatih[1][$i+1][6])){
            //         $total++;
            //     }
            //     // echo strtolower($valueData['vHulu'][$i]['status']['text'])." = ";
            //     // echo strtolower($ExcelDataLatih[1][$i+1][6])."\n";
            // }
            
            // // var_dump($total);
            // $valueData['akurasi'] = $total/count($valueData['vHulu']);

            // dd($valueData);
            return view('naiveBayes',$valueData);
        }else{
            return redirect('bencana');
        }
    }
    public function importDataLatih(Request $request){
        $datasExcel = [];
        
        $parameterProbabilitas = array(
            'aman' => array(),
            'rawan' => array(),
            'paramSoil' => array(),
            'paramSlope' => array(),
            'paramRainfall' => array(),
        );

        $location = LocationModel::get();

        if(Input::hasFile('import_files')){
            $file = Input::file('import_files'); 
            $reader = ReaderFactory::create(Type::XLSX);
            foreach ($file as $files) {
                $reader->open($files->path());
                foreach ($reader->getSheetIterator() as $sheet) {
                    $dataExcel = $this->readOrderSheet($sheet);
                    array_push($datasExcel,$dataExcel);
                }   
            }
            $reader->close();
            
            $a = 0;
            for ($i=0; $i < count($dataExcel) ; $i++) { 
                if($i != 0){
                    if($a <= count($location)){
                        $data = New ParameterModel;
                        $data->rainfall = $dataExcel[$i][0];
                        $data->soil = $dataExcel[$i][1];
                        $data->slope = $dataExcel[$i][2];
                        $data->status = $dataExcel[$i][3];
                        $data->location_id = $location[$a]->id;
                        $data->date = date('Y-m-d H:i:s');
                        $data->save();
                        $a++;
                    }

                }
            }

            $parameter = array(
                'rainfall' =>  0,
                'soil' => 'AN',
                'slope' => 0,
                'location_id' => $location[$a]->id,
                'date'=> date('Y-m-d H:i:s'),
                'status' => '',
            );
            $max_id = ParameterModel::max('id');
            $min_id = ParameterModel::min('id');
            

            $parameter['status'] = $this->NaiveBayes($parameter,$parameterProbabilitas,$min_id,$max_id);

            $dataModel = New ModelModel;
            $dataModel->name = $max_id;
            $dataModel->max_id = $max_id;
            $dataModel->min_id = $min_id;
            $dataModel->date = date('Y-m-d H:i:s');
            $dataModel->praman = count($parameter["status"]["Rawan"])/(count($parameter["status"]["Aman"])+count($parameter["status"]["Rawan"]));
            $dataModel->prrawan = count($parameter["status"]["Aman"])/(count($parameter["status"]["Aman"])+count($parameter["status"]["Rawan"]));
            $dataModel->hrr = $parameter["status"]["paramRainfall"]["rendah"]["prob_rawan"];
            $dataModel->hra = $parameter["status"]["paramRainfall"]["rendah"]["prob_aman"];
            $dataModel->hsr = $parameter["status"]["paramRainfall"]["sedang"]["prob_rawan"];
            $dataModel->hsa = $parameter["status"]["paramRainfall"]["sedang"]["prob_aman"];
            $dataModel->htr = $parameter["status"]["paramRainfall"]["tinggi"]["prob_rawan"];
            $dataModel->hta = $parameter["status"]["paramRainfall"]["tinggi"]["prob_aman"];
            $dataModel->klr = $parameter["status"]["paramSlope"]["landai"]["prob_rawan"];
            $dataModel->kla = $parameter["status"]["paramSlope"]["landai"]["prob_aman"];
            $dataModel->ksr = $parameter["status"]["paramSlope"]["sedang"]["prob_rawan"];
            $dataModel->ksa = $parameter["status"]["paramSlope"]["sedang"]["prob_aman"];
            $dataModel->kcr = $parameter["status"]["paramSlope"]["curam"]["prob_rawan"];
            $dataModel->kca = $parameter["status"]["paramSlope"]["curam"]["prob_aman"];
            $dataModel->snr = $parameter["status"]["paramSoil"]["AN"]["prob_rawan"];
            $dataModel->sna = $parameter["status"]["paramSoil"]["AN"]["prob_aman"];
            $dataModel->srr = $parameter["status"]["paramSoil"]["AR"]["prob_rawan"];
            $dataModel->sra = $parameter["status"]["paramSoil"]["AR"]["prob_aman"];
            
            $dataModel->save();
            return redirect('bencana/config');
            // dd($data);
        }else{
            return redirect('bencana/config');
        }
    }
    public function NaiveBayes($dataUji,$parameterProbabilitas,$minId,$maxId){

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
            'AR' => array(
                'aman' => array(),
                'rawan' => array(),
                'prob_aman'=> 0,
                'prob_rawan'=> 0,
            ),
            'AN' => array(
                'aman' => array(),
                'rawan' => array(),
                'prob_aman'=> 0,
                'prob_rawan'=> 0,
            ),
        );

        if(empty($parameterProbabilitas['paramRainfall']) && empty($parameterProbabilitas['paramSlope']) && empty($parameterProbabilitas['paramSoil'])){
            $dataLatih = [];

            $data = ParameterModel::whereBetween('id',[$minId,$maxId])->get();
            $aman = ParameterModel::whereBetween('id',[$minId,$maxId])->where('status','aman')->get();
            $rawan = ParameterModel::whereBetween('id',[$minId,$maxId])->where('status','rawan')->get();
            // dd($rawan);

            foreach ($data as $key) {
                $rainfall = $this->rescale_rain($key->rainfall);
                
                $slope = $this->rescale_slope($key->slope);
    
                $params = array(
                    'v_rainfall' => $key->rainfall,
                    'rainfall' => $rainfall,
                    'v_slope' => $key->slope,
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
    
                if($params['soil'] == 'AR'){
                    if($params['status'] == 'AMAN'){
                        array_push($paramSoil['AR']['aman'],$params);
                    }else{
                        array_push($paramSoil['AR']['rawan'],$params);
                    }
                }else{
                    if($params['status'] == 'AMAN'){
                        array_push($paramSoil['AN']['aman'],$params);
                    }else{
                        array_push($paramSoil['AN']['rawan'],$params);
                    }
                }
                array_push($dataLatih,$params);

                $paramRainfall['rendah']['prob_aman'] = count($paramRainfall['rendah']['aman'])/count($aman);
                $paramRainfall['rendah']['prob_rawan'] = count($paramRainfall['rendah']['rawan'])/count($rawan);
                $paramRainfall['sedang']['prob_aman'] = count($paramRainfall['sedang']['aman'])/count($rawan);
                $paramRainfall['sedang']['prob_rawan'] = count($paramRainfall['sedang']['rawan'])/count($rawan);
                $paramRainfall['tinggi']['prob_aman'] = count($paramRainfall['tinggi']['aman'])/count($aman);
                $paramRainfall['tinggi']['prob_rawan'] = count($paramRainfall['tinggi']['rawan'])/count($rawan);

                $paramSlope['landai']['prob_aman'] = count($paramSlope['landai']['aman'])/count($aman);
                $paramSlope['landai']['prob_rawan'] = count($paramSlope['landai']['rawan'])/count($rawan);
                $paramSlope['sedang']['prob_aman'] = count($paramSlope['sedang']['aman'])/count($aman);
                $paramSlope['sedang']['prob_rawan'] = count($paramSlope['sedang']['rawan'])/count($rawan);
                $paramSlope['curam']['prob_aman'] = count($paramSlope['curam']['aman'])/count($aman);
                $paramSlope['curam']['prob_rawan'] = count($paramSlope['curam']['rawan'])/count($rawan);

                $paramSoil['AR']['prob_aman'] = count($paramSoil['AR']['aman'])/count($aman);
                $paramSoil['AR']['prob_rawan'] = count($paramSoil['AR']['rawan'])/count($rawan);
                $paramSoil['AN']['prob_aman'] = count($paramSoil['AN']['aman'])/count($aman);
                $paramSoil['AN']['prob_rawan'] = count($paramSoil['AN']['rawan'])/count($aman);

            }
        }else{
            $paramRainfall = $parameterProbabilitas['paramRainfall'];
            $paramSlope = $parameterProbabilitas['paramSlope'];
            $paramSoil = $parameterProbabilitas['paramSoil'];
            $aman = $parameterProbabilitas['aman'];
            $rawan =$parameterProbabilitas['rawan'];
        }

        $dataUji['rainfall'] = $this->rescale_rain($dataUji['rainfall']);
        $dataUji['slope'] = $this->rescale_slope($dataUji['slope']); 

        $rainRawan = $paramRainfall[strtolower($dataUji['rainfall'])]['prob_rawan'];
        $rainAman =  $paramRainfall[strtolower($dataUji['rainfall'])]['prob_aman'];
        $slopeRawan = $paramSlope[strtolower($dataUji['slope'])]['prob_rawan'];
        $slopeAman = $paramSlope[strtolower($dataUji['slope'])]['prob_aman'];
        $soilRawan = $paramSoil[$dataUji['soil']]['prob_rawan'];
        $soilAman = $paramSoil[$dataUji['soil']]['prob_aman'];
        
        // var_dump(count($paramRainfall['tinggi']['aman']));
        // var_dump($soilRawan);
        
        $pAman = ($rainAman * $slopeAman * $soilAman * (count($aman) / (count($aman) + count($rawan))));
        $pRawan = ($rainRawan * $slopeRawan * $soilRawan * (count($aman) / (count($aman) + count($rawan))));
        
        $max = max($pAman,$pRawan);

        // var_dump($pRawan);
        if($max == $pAman){
            $returnVal = array(
                'text' => 'Aman', 
                'value' => $pAman,
                'RescaleRainfall' =>$dataUji['rainfall'],
                'RescaleSlope' =>$dataUji['slope'],
                'RainRawan' => $rainRawan,
                'RawainAman'=> $rainAman,
                'SlopeRawan' => $slopeRawan,
                'SlopeAman' => $slopeAman,
                'SoilRawan' => $soilRawan,
                'SoilAman' => $soilAman,
                'pAman' => $pAman,
                'pRawan' => $pRawan, 
                'paramRainfall' =>  $paramRainfall,
                'paramSlope' =>  $paramSlope,
                'paramSoil' =>  $paramSoil,
                'Aman' => $aman,
                'Rawan' => $rawan,
            );
            return $returnVal;
        }else if($max == $pRawan){
            $returnVal = array(
                'text' => 'Rawan', 
                'value' => $pRawan,
                'RescaleRainfall' =>$dataUji['rainfall'],
                'RescaleSlope' =>$dataUji['slope'],
                'RainRawan' => $rainRawan,
                'RawainAman'=> $rainAman,
                'SlopeRawan' => $slopeRawan,
                'SlopeAman' => $slopeAman,
                'SoilRawan' => $soilRawan,
                'SoilAman' => $soilAman,
                'pAman' => $pAman,
                'pRawan' => $pRawan,
                'paramRainfall' =>  $paramRainfall,
                'paramSlope' =>  $paramSlope,
                'paramSoil' =>  $paramSoil,
                'Aman' => $aman,
                'Rawan' => $rawan,
            );
            return $returnVal;
        }else{
            return false;
        }
    }
    public function rescale_rain($param){
        if($param < 27.16666667){
            return 'RENDAH';
        }elseif ($param > 27.16666667 && $param <= 54.33333333) {
            return 'SEDANG';
        }else{
            return 'TINGGI';
        }
    }
    public function rescale_slope($param){
        if($param <= 0.244307993){
            return 'LANDAI';
        }elseif ($param > 0.244307993 && $param <= 0.446153997) {
            return 'SEDANG';
        }else{
            return 'CURAM';
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
        $area = AreaModel::all();
        
        $location = LocationModel::with(['paramsNaiveBayes' => function($q){$q->orderBy('id','DESC');}])->get();

        if(count($location[0]->paramsNaiveBayes) <= 0 ){
            $location = LocationModel::with(['params' => function($q){$q->orderBy('id','DESC');}])->get();
        }

        
        Mapper::map(-7.642431,110.457015,['zoom' =>12, 'marker' => false, 'eventBeforeLoad' => 'addEventListener(map);']);
        // dd($location[166]);
        foreach ($location as $key) {
            // var_dump($key);
            if(count($key->paramsNaiveBayes) || count($key->params)){
                if(count($key->paramsNaiveBayes) <= 0){
                    $date = $key->params[0]->date;
                    $status = $key->params[0]->status;
                    $slope =  $key->params[0]->slope;
                    $rainfall = $key->params[0]->rainfall;
                    $soil = $key->params[0]->soil;

                }else{
                    $date = $key->paramsNaiveBayes[0]->date;
                    $status = $key->paramsNaiveBayes[0]->status;
                    $slope =  $key->paramsNaiveBayes[0]->slope;
                    $rainfall = $key->paramsNaiveBayes[0]->rainfall;
                    $soil = $key->paramsNaiveBayes[0]->soil;
                }

                if($status == 'Aman'){
                    $icon = URL::to('/').'/save.png';
                }else{
                    $icon = URL::to('/').'/warning.png';
                }

                $html = '<div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <h5 class="text-left">
                                                '.$key->id.'
                                            </h5>
                                        </div>
                                        <div class="col-md-9">
                                            <p class="text-left">
                                                '.$date.'
                                            </p>
                                        </div>
                                    </div>
                                    <table class="table table-hover table-sm">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    Latitude
                                                </td>
                                                <td>
                                                    '.$key->latitude.'
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    Longitude
                                                </td>
                                                <td>
                                                    '.$key->longitude.'
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    Slope
                                                </td>
                                                <td>
                                                    '.$slope.'
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    Rain Fall
                                                </td>
                                                <td>
                                                    '.$rainfall.'
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    soil
                                                </td>
                                                <td>
                                                    '.$soil.'
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    Status
                                                </td>
                                                <td>
                                                    '.$status.'
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>';

                Mapper::informationWindow(
                    $key->latitude,
                    $key->longitude, 
                    $html, 
                    [
                        'open' => false, 
                        'maxWidth'=> 500,
                        'icon' =>  $icon, 
                        'markers' => 
                            [
                                'title' => 'Title',
                            ]
                    ]
                );
            }                
        }
        
        // dd($location[0]);
        return view('map');
    }
    public function dataLatih(){
        return view('config');
    }
    public function detailDatalatih($id){
        $data = ModelModel::where('id',$id)->first();
        if(!empty($data)){
            return view('detailDatalatih',$data);
        }else{
            $this->dataLatih();
        }
    }
    public function dataTablesLatih(){
        $datas = ParameterModel::all();
        // dd(Datatables::of($datas)->make(true));
        return Datatables::of($datas)->make(true);
    }
    public function dataTablesModel(){
        $datas = ModelModel::all();
        // dd(Datatables::of($datas)->make(true));
        return Datatables::of($datas)
        ->addColumn('detail_url', function ($datas) {
            return route('bencana.detaildatalatih', ['id' => $datas->id]);
        })
        ->make(true);
    }
    public function dataTableDetailDataLatih($id_model){
        $model = ModelModel::where('id',$id_model)->first();
        $datas = ParameterModel::whereBetween('id',[$model->min_id,$model->max_id]);
        // dd(Datatables::of($datas)->make(true));
        return Datatables::of($datas)->make(true);
    }
    public function loadData(){
        $datasExcel = [];
        // $file = "D:\LatLong.xlsx";
        $file = "D:\Naive Bayes per cordinat.xlsx"; 
        $reader = ReaderFactory::create(Type::XLSX );
        $reader->open($file);
            foreach ($reader->getSheetIterator() as $sheet) {
                $dataExcel = $this->readOrderSheet($sheet);
                array_push($datasExcel,$dataExcel);
            }   
        var_dump($datasExcel[1][1]);
        $reader->close();
        for ($i=0; $i < count($datasExcel[1]) ; $i++) { 
            if($i > 18000){
                $date_split = explode("_",$datasExcel[1][$i][0])[1];
                $date = $date_split[0].$date_split[1].$date_split[2].$date_split[3].'-'.$date_split[4].$date_split[5].'-'.$date_split[6].$date_split[7];
                var_dump($i);
                $data = New ParameterModel;
                $data->rainfall = $datasExcel[1][$i][1];
                $data->soil = $datasExcel[1][$i][3];
                $data->slope = $datasExcel[1][$i][2];
                $data->date = $date;
                $data->status = $datasExcel[1][$i][6];
                $data->location_id = $datasExcel[1][$i][7];
                $data->save();
            }else{
                echo "1 \n";
            }
        }
        // var_dump($dataExcel[0]);      
    }
    function load_data_new(){
       

        $datasExcel = [];
        $dataExportExcelFull = [];
        
        $fileName = 'Export Excel.xlsx';

        $parameHulu = array(
            array(190,234),
            array(190,235),
            array(190,236),

            array(191,234),
            array(191,235),
            array(191,236),
            
            array(192,234),
            array(192,235),
            array(192,236),
            array(192,237),
            
            array(193,234),
            array(193,235),
            array(193,236),
            array(193,237),
            
            array(194,234),
            array(194,235),
            array(194,236),
            array(194,237),
            
            array(195,234),
            array(195,235),
            array(195,236),
            array(195,237),
            array(195,238),
            
            array(196,234),
            array(196,235),
            array(196,236),
            array(196,237),
            array(196,238),
            
            array(197,234),
            array(197,235),
            array(197,236),
            array(197,237),
            array(197,238),
            array(197,239),
            
            array(198,234),
            array(198,235),
            array(198,236),
            array(198,237),
            array(198,238),
            array(198,239),
            
            array(199,234),
            array(199,235),
            array(199,236),
            array(199,237),
            array(199,238),
            array(199,239),
            array(199,240),
            
            array(200,234),
            array(200,235),
            array(200,236),
            array(200,237),
            array(200,238),
            array(200,239),
            array(200,240),
            
            array(201,234),
            array(201,235),
            array(201,236),
            array(201,237),
            array(201,238),
            array(201,239),
            array(201,240),
            
            array(202,234),
            array(202,235),
            array(202,236),
            array(202,237),
            array(202,238),
            array(202,239),
            array(202,240),
            
            array(203,234),
            array(203,235),
            array(203,236),
            array(203,237),
            array(203,238),
            array(203,239),
            array(203,240),
            array(203,241),
            
            array(204,234),
            array(204,235),
            array(204,236),
            array(204,237),
            array(204,238),
            array(204,239),
            array(204,240),
            array(204,241),
            
            array(205,233),
            array(205,234),
            array(205,235),
            array(205,236),
            array(205,237),
            array(205,238),
            array(205,239),
            array(205,240),
            array(205,241),
            
            array(206,234),
            array(206,235),
            array(206,236),
            array(206,237),
            array(206,238),
            array(206,239),
            array(206,240),
            array(206,241),
            
            array(207,234),
            array(207,235),
            array(207,236),
            array(207,237),
            array(207,238),
            array(207,239),
            array(207,240),
            array(207,241),
            
            array(208,234),
            array(208,235),
            array(208,236),
            array(208,237),
            array(208,238),
            array(208,239),
            array(208,240),
            array(208,241),
            array(208,242),
            
            array(209,234),
            array(209,235),
            array(209,236),
            array(209,237),
            array(209,238),
            array(209,239),
            array(209,240),
            array(209,241),
            array(209,242),
            
            array(210,235),
            array(210,236),
            array(210,237),
            array(210,238),
            array(210,239),
            array(210,240),
            array(210,241),
            array(210,242),
            
            array(211,235),
            array(211,236),
            array(211,237),
            array(211,238),
            array(211,239),
            array(211,240),
            array(211,241),
            array(211,242),
            
            array(212,235),
            array(212,236),
            array(212,237),
            array(212,238),
            array(212,239),
            array(212,240),
            array(212,241),
            array(212,242),
            
            array(213,236),
            array(213,237),
            array(213,238),
            array(213,239),
            array(213,240),
            array(213,241),
            
            array(214,236),
            array(214,237),
            array(214,238),
            array(214,239),
            array(214,240),
            array(214,241),
            
            array(215,237),
            array(215,238),
            array(215,239),
            array(215,240),
         );

        
        if(Input::hasFile('import_files')){
            $file = Input::file('import_files'); 
            $reader = ReaderFactory::create(Type::CSV);
            $reader->setFieldDelimiter(',');
            $reader->setEndOfLineCharacter("\n");
            $date = explode("_",$file[0]->getClientOriginalName());
            $date_now = $date[1][0].$date[1][1].$date[1][2].$date[1][3].'-'.$date[1][4].$date[1][5].'-'.$date[1][6].$date[1][7].' '.$date[2][0].$date[2][1].':00:00';
            // var_dump($date_now);

            foreach ($file as $files) {
                $reader->open($files->path());
                foreach ($reader->getSheetIterator() as $sheet) {
                    $dataExcel = $this->readOrderSheet($sheet);
                    array_push($datasExcel,$dataExcel);
                }   
            }
            $reader->close();
            
            $writer = WriterFactory::create(Type::XLSX); // for XLSX files
            $writer->setShouldUseInlineStrings(true); // default (and recommended) value
            $reader->setShouldFormatDates(true);
            $writer->openToBrowser($fileName); // stream data directly to the browser
            for ($i=0; $i < count($file) ; $i++) { 
                // array_push($dataExcelExport,$date_now);
                $dataExcelExport = [];
                foreach ($parameHulu as $key) {
                    $key_new = $key[0]-1;
                    // echo "Hulu ".$key[0].",".$key[1].": ".$datasExcel[$i][$key_new][$key[1]]."\n";
                    array_push($dataExcelExport,$datasExcel[$i][$key_new][$key[1]]);
                }
                array_push($dataExportExcelFull,$dataExcelExport);
                $writer->addRow($dataExcelExport); 
            }
            $writer->close();
            var_dump($dataExportExcelFull);
        }else{
            return redirect('bencana');
        }
    }

}
