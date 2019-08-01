@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row mt-5">
        <div class="col-md-9">           
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th rowspan="2" style="vertical-align : middle;text-align:center;">No</th>
                        <!-- <th rowspan="2" style="vertical-align : middle;text-align:center;">Tanggal</th> -->
                        <th colspan="5" style="vertical-align : middle;text-align:center;">Parameter</th>
                        <th rowspan="2" style="vertical-align : middle;text-align:center;">Kemungkinan Aman</th>
                        <th rowspan="2" style="vertical-align : middle;text-align:center;">Kemungkinan Rawan</th>
                        <th rowspan="2" style="vertical-align : middle;text-align:center;">Hasil Prediksi</th>
                    </tr>
                    <tr>
                        <td>Hujan</td>
                        <td>Kemiringan</td>
                        <td>Jenis Tanah</td>
                        <td>Status Hujan</td>
                        <td>Status Kemiringan</td>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($vHulu) > 0){ $i=1;?>
                        <?php foreach ($vHulu as $key) {?>
                        <tr <?php if($key["status"]["text"] == "Rawan"){ echo "style='background-color: red;color: white;'"; } ?>>
                            <td><?php echo $i;?></td>
                            <!-- <td><?php echo $key["date"];?></td> -->
                            <td><?php echo $key["rainfall"];?></td>
                            <td><?php echo $key["slope"];?></td>
                            <td><?php echo $key["soil"];?></td>
                            <td><?php echo $key["status"]["RescaleRainfall"];?></td>
                            <td><?php echo $key["status"]["RescaleSlope"];?></td>
                            <td><?php echo round($key["status"]["pAman"],5);?></td>
                            <td><?php echo round($key["status"]["pRawan"],5);?></td>
                            <td><?php echo $key["status"]["text"];?></td>
                        </tr>
                        <?php $i++;} ?>
                    <?php }else{ ?>
                        <tr>
                            <td colspan="7">Data Tidak Tersedia</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div div class="col-md-3">
        <table class="table table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>Kemungkinan</th>
                    <th>Prior</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Rawan</td>
                    <td><?php echo count($vHulu[0]["status"]["Rawan"])/(count($vHulu[0]["status"]["Aman"])+count($vHulu[0]["status"]["Rawan"]))?></td>
                </tr>
                <tr>
                    <td>Aman</td>
                    <td><?php echo count($vHulu[0]["status"]["Aman"])/(count($vHulu[0]["status"]["Aman"])+count($vHulu[0]["status"]["Rawan"]))?></td>
                </tr>
            </tbody>
        </table>
        <hr>
        <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Parameter</th>
                        <th>Value</th>
                        <th>Probabilitas</th>
                        <th>Nilai Kemungkinan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($vHulu) > 0){ $i==0;?>
                        <tr>
                            <td rowspan="6"  style="vertical-align : middle;text-align:center;">Hujan</td>
                            <td>Rendah</td>
                            <td>Rawan</td>
                            <td><?php echo $vHulu[0]["status"]["paramRainfall"]["rendah"]["prob_rawan"] ?></td>
                        </tr>
                        <tr>
                            <td>Rendah</td>
                            <td>Aman</td>
                            <td><?php echo $vHulu[0]["status"]["paramRainfall"]["rendah"]["prob_aman"] ?></td>
                        </tr>
                        <tr>
                            <td>Sedang</td>
                            <td>Rawan</td>
                            <td><?php echo $vHulu[0]["status"]["paramRainfall"]["sedang"]["prob_rawan"] ?></td>
                        </tr>
                        <tr>
                            <td>Sedang</td>
                            <td>Aman</td>
                            <td><?php echo $vHulu[0]["status"]["paramRainfall"]["sedang"]["prob_aman"] ?></td>
                        </tr>
                        <tr>
                            <td>Tinggi</td>
                            <td>Rawan</td>
                            <td><?php echo $vHulu[0]["status"]["paramRainfall"]["tinggi"]["prob_rawan"] ?></td>
                        </tr>
                        <tr>
                            <td>Tinggi</td>
                            <td>Aman</td>
                            <td><?php echo $vHulu[0]["status"]["paramRainfall"]["tinggi"]["prob_aman"] ?></td>
                        </tr>
                        
                        <tr>
                            <td rowspan="6"  style="vertical-align : middle;text-align:center;">Kemiringan</td>
                            <td>Landai</td>
                            <td>Rawan</td>
                            <td><?php echo $vHulu[0]["status"]["paramSlope"]["landai"]["prob_rawan"] ?></td>
                        </tr>
                        <tr>
                            <td>Landai</td>
                            <td>Aman</td>
                            <td><?php echo $vHulu[0]["status"]["paramSlope"]["landai"]["prob_aman"] ?></td>
                        </tr>
                        <tr>
                            <td>Sedang</td>
                            <td>Rawan</td>
                            <td><?php echo $vHulu[0]["status"]["paramSlope"]["sedang"]["prob_rawan"] ?></td>
                        </tr>
                        <tr>
                            <td>Sedang</td>
                            <td>Aman</td>
                            <td><?php echo $vHulu[0]["status"]["paramSlope"]["sedang"]["prob_aman"] ?></td>
                        </tr>
                        <tr>
                            <td>Curam</td>
                            <td>Rawan</td>
                            <td><?php echo $vHulu[0]["status"]["paramSlope"]["curam"]["prob_rawan"] ?></td>
                        </tr>
                        <tr>
                            <td>Curam</td>
                            <td>Aman</td>
                            <td><?php echo $vHulu[0]["status"]["paramSlope"]["curam"]["prob_aman"] ?></td>
                        </tr>

                        <tr>
                            <td rowspan="4"  style="vertical-align : middle;text-align:center;">Jenis Tanah</td>
                            <td>AN</td>
                            <td>Rawan</td>
                            <td><?php echo $vHulu[0]["status"]["paramSoil"]["AN"]["prob_rawan"] ?></td>
                        </tr>
                        <tr>
                            <td>AN</td>
                            <td>Aman</td>
                            <td><?php echo $vHulu[0]["status"]["paramSoil"]["AN"]["prob_aman"] ?></td>
                        </tr>
                        <tr>
                            <td>AR</td>
                            <td>Rawan</td>
                            <td><?php echo $vHulu[0]["status"]["paramSoil"]["AR"]["prob_rawan"] ?></td>
                        </tr>
                        <tr>
                            <td>AR</td>
                            <td>Aman</td>
                            <td><?php echo $vHulu[0]["status"]["paramSoil"]["AR"]["prob_rawan"] ?></td>
                        </tr>
                    <?php }else{ ?>
                        <tr>
                            <td colspan="4">Data Tidak Tersedia</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection