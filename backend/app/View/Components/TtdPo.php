<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Tandatangan;

class TtdPo extends Component
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
                    ->where('jenis',$jenis)->where('tipe',$tipe)->with(['pejabat' => function($query){
                        $query->whereDate('created_at','<=',$this->data['tanggal'])->orderBy('created_at','DESC')->orderBy('ordinal');
                    }
                    ])->get();
        //dd($query_ttd);
        if($query_ttd->count()){ //
            if($query_ttd->count() > 1){
                //dd('1');
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
                //dd('2');
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
        return view('components.ttd-po');
    }
}
