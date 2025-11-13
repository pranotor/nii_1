<?php

namespace App\Http\Controllers;

use App\Customer;
use Illuminate\Http\Request;
use Response;
use DB;
use App\Http\Requests\StoreCustomer;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Csv as CsvReader;
use PhpOffice\PhpSpreadsheet\Writer\Csv as CsvWriter;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response::json(Customer::orderBy('id')->get());
    }

    public function blokir($level = '')
    {
        if($level == '')
            return Response::json(Customer::orderBy('id')->where('block','>=',1)->with('status')->get());
        else
            return Response::json(Customer::orderBy('id')->where('block','>',1)->with('status')->get());
    }

    public function custaktif()
    {
        return Response::json(Customer::orderBy('id')->where('block',0)->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

     public function blokirproc(Request $request)
     {
         $data = json_decode($request->getContent(), true);
            if(isset($data['formdata']['blokstat']))
                $status = $data['formdata']['blokstat'];
            else
                $status = 1;

            if(is_array($data['formdata']['cust_id'])) {
                foreach($data['formdata']['cust_id'] as $cust){
                    Customer::where('id',$cust)->update(['block'=>$status]);
                }
                $msg_status = 'OK';
            }
            else{
                if($status==2){
                    $q_due = Carbon::parse(DB::table('v_piutang')->where('status_piutang','<>','lunas')->where('cust_id',$data['formdata']['cust_id'])->min('payment_due'));
                    $now = Carbon::now();
                    $diff = $q_due->diffInDays($now);
                    if($diff <= 90){
                        $status=0;
                        $msg_status = 'OK';
                    }
                    else{
                        $msg_status = 'need approval';
                    }

                }
                Customer::where('id',$data['formdata']['cust_id'])->update(['block'=>$status]);


            }
            return Response::json(['message' => $msg_status]); // Status code here
     }
    public function store(StoreCustomer $request)
    {
        $data = json_decode($request->getContent(), true);
        DB::transaction(function () use ($data) {
            //$data_inp['kode'] = $data['formdata']['kode'];
            $data_inp['nama'] = $data['formdata']['nama'];
            $data_inp['nick'] = $data['formdata']['nick'];
            $data_inp['alamat'] = $data['formdata']['alamat'];
            $data_inp['kota'] = $data['formdata']['kota'];
            $data_inp['telepon'] = $data['formdata']['telepon'];
            $data_inp['fax'] = $data['formdata']['fax'];
            $data_inp['email'] = $data['formdata']['email'];
            $data_inp['npwp'] = $data['formdata']['npwp'];
            $data_inp['kredit_term'] = $data['formdata']['kredit_term'];
            $data_inp['kode_no_pajak'] = $data['formdata']['kode_no_pajak'];
            $data_inp['sales'] = $data['formdata']['sales'];
            $data_inp['market_id'] = $data['formdata']['market_id'];
            $data_inp['pic'] = $data['formdata']['pic'];
            $data_inp['hp_pic'] = $data['formdata']['hp_pic'];
            $data_inp['nama_keuangan'] = $data['formdata']['keuangan'];
            $data_inp['hp_keuangan'] = $data['formdata']['hp_keuangan'];
            $data_inp['nama_user'] = $data['formdata']['nama_user'];
            $data_inp['hp_user'] = $data['formdata']['hp_user'];
            $data_inp['alamat_kirim'] = $data['formdata']['alamat_kirim'];
            $data_inp['telp_kirim'] = $data['formdata']['telp_kirim'];
            //$data_inp['keterangan'] = $data['formdata']['keterangan'];
            //$data_inp['berikat'] = $data['formdata']['berikat'];
            $data_inp['no_rekening'] = $data['formdata']['no_rekening'];
            if($data['formdata']['isEdit']) {
                Customer::where('id',$data['formdata']['prevReferensi'])->update($data_inp);
            }
            else{
                Customer::create($data_inp);
            }



        }, 5);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Perkiraan  $perkiraan
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        return Response::json($customer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Perkiraan  $perkiraan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Perkiraan $perkiraan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Perkiraan  $perkiraan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        //
    }

    public function custxls(){
        $baris = 1;
        $dataH = DB::table('t_customer')->orderBy('nick','ASC')->get();
        //dd($dataH);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A'.$baris,'Nick');
        $sheet->setCellValue('B'.$baris,'Nama');
        $sheet->setCellValue('C'.$baris,'Alamat');
        $sheet->setCellValue('D'.$baris,'Kota');
        $sheet->setCellValue('E'.$baris,'Telepon');
        $sheet->setCellValue('G'.$baris,'Fax');
        $sheet->setCellValue('H'.$baris,'Kontak');
        $sheet->setCellValue('I'.$baris,'Email');
        $sheet->setCellValue('J'.$baris,'NPWP');
        $sheet->setCellValue('K'.$baris,'Termin');
        $baris++;
        foreach($dataH as $row){
            $sheet->setCellValue('A'.$baris,$row->nick);
            $sheet->setCellValue('B'.$baris,$row->nama);
            $sheet->setCellValue('C'.$baris,$row->alamat);
            $sheet->setCellValue('D'.$baris,$row->kota);
            $sheet->setCellValue('E'.$baris,$row->telepon);
            $sheet->setCellValue('G'.$baris,$row->fax);
            $sheet->setCellValue('H'.$baris,$row->kontak_person);
            $sheet->setCellValue('I'.$baris,$row->email);
            $sheet->setCellValue('J'.$baris,$row->npwp);
            $sheet->setCellValue('K'.$baris,$row->kredit_term);

            $baris++;
        }
        $baris++;

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="rekap_stock.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        header('Access-Control-Allow-Origin: http://'.env('SANCTUM_STATEFUL_DOMAINS'));
        header('Access-Control-Allow-Credentials: true');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }
}
