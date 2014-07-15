<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class V1 extends CI_Controller{
    
    public function __construct(){
        parent::__construct();
        $this->load->model('categories_model','cm');
        $this->load->model('places_model','pm');
    }
    
    public function index(){
        json_compress_output();
        echo "/*\nHTTP Host\t: ".$this->input->server('HTTP_HOST')."\n";
        echo "Remote Address\t: ".$this->input->server('REMOTE_ADDR')."\n";
        echo "Client\t\t: ".$this->agent->browser()."\n";
        echo "Platform\t: ".$this->agent->platform()."\n";
        echo "Generated\t: ".unix_to_human($this->session->userdata('last_activity'))."\n";
        echo "Lifetime\t: ".timespan($this->session->userdata('last_activity'))."\n*/\n\n\n";
    }
    
    public function retrieve_categories($since=0){
        sleep(10);
        json_compress_output();
        $categories=$this->cm->retrieve_categories($since);
        if($categories==NULL){
            $response=array('meta'=>array('error'=>FALSE),"count"=>0,"result"=>array());
        } else {
            $response=array();
            foreach($categories->result as $row){
                $parent = ($row->parent === NULL) ? $parent = NULL : $parent = (int)$row->parent;
                $data=array("id"=>(int)$row->id,"name"=>$row->name,"parent"=>$parent,"icon"=>$row->icon);
                array_push($response,$data);
                unset($data);
            }
            $response=array('meta'=>array('error'=>FALSE),"count"=>$categories->count,"result"=>$response);
        }
        encode_json($response);
    }
    
    public function nearby(){
        parse_str($_SERVER['QUERY_STRING'], $_GET);
        json_compress_output();
        $c=$this->input->get('cat');
        $m=($this->input->get('max')==FALSE) ? $m=10 : $m=(int)$this->input->get('max');
        $p=($this->input->get('page')==FALSE) ? $p=0 : $p=(int)$this->input->get('page');
        $r=($this->input->get('radius')==FALSE) ? $r=10 : $r=(int)$this->input->get('radius');
        $k=($this->input->get('keyword')==FALSE) ? $k=NULL : $k=$this->input->get('keyword');
        $f=($this->input->get('filter')==FALSE) ? $f='distance' : $f=$this->input->get('filter');
        $lat=$this->input->get('lat');
        $lng=$this->input->get('lng');
        if($c==FALSE){
            encode_json(array('meta'=>array('error'=>TRUE,'msg'=>'category must be provided')));
        } else {
            if(!ctype_digit($c)){
                encode_json(array('meta'=>array('error'=>TRUE,'msg'=>'category must be integer value')));
            } else {
                $c=(int)$c;
                if(($lat==FALSE) || ($lng==FALSE)){
                    encode_json(array('meta'=>array('error'=>TRUE,'msg'=>'latitude and longitude must be provided')));
                } else {
                    $nearby=$this->pm->get_nearby($c,$lat,$lng,$r,$m,$p,$f);
                    if($nearby==NULL){
                        $response=array('meta'=>array('error'=>FALSE),"count"=>0,"result"=>array());
                    } else {
                        $response=array();
                        foreach($nearby->result as $place){
                            $data=array('id'=>(int)$place->id,'name'=>$place->name,'address'=>$place->address,'lat'=>$place->lat,'lng'=>$place->lng,'distance'=>(double)$place->distance);
                            array_push($response,$data);
                            unset($data);
                        }
                        $response=array('meta'=>array('error'=>FALSE),"count"=>$nearby->count,"result"=>$response);
                    }
                    encode_json($response);
                }
            }
        }
    }
    
}
