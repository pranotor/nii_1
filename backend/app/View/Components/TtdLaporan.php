<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Tandatangan;

class TtdLaporan extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $modName;
    public $jenis;
    //public $data;
    public $arr_ttd;
    public $tipe;

    public function __construct($modName, $jenis, $tipe='',$data='')
    {
        $this->modName = $modName;
        $this->jenis = $jenis;
        $this->data = $data;
        $this->tipe = $tipe;
        $query_ttd = Tandatangan::where('mod_name',$modName)
                    ->where('jenis',$jenis)->with(['pejabat' => function($query){
                        $query->orderBy('ordinal');
                    }
                    ])->get();
        if($query_ttd->count()){ //
            if($query_ttd->count() > 1){
                foreach($query_ttd as $q){
                    if(is_null($q->condition))
                        continue;
                    $test = "return $q->condition;";
                    //dd($test);
                    //$test = eval("return $q->condition;");
                    //dd($data['total']);
                    if(eval($test)){
                        $this->arr_ttd = $q->pejabat;
                        //$this->arr_ttd = $array_ttd[0]['pejabat'];
                    }

                }
            }
            else{
                $array_ttd = $query_ttd->toArray();
                $this->arr_ttd = $array_ttd[0]['pejabat'];
            }
        }
        else{
            $query_ttd = Tandatangan::where('mod_name','general')->with(['pejabat' => function($query){
                $query->orderBy('ordinal');
            }
            ])->first();
            //dd($query_ttd->toArray());
            $array_ttd = $query_ttd->toArray();
            $this->arr_ttd = $array_ttd['pejabat'];
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        if($this->jenis <> 1)
            return view('components.ttd-jurnal');
        else{
            if($this->tipe=='kuitansi')
                return view('components.ttd-kuitansi');
            else
                return view('components.ttd-dvud');
        }
        
    }
}
