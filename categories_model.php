<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Categories_model extends CI_Model{
    
    public function retrieve_categories($since){
        $query=$this->db->select('*')->where('id >',$since)->get('categories');
        $count=$query->num_rows();
        if($count==0){
            return null;
        } else {
            $result=array("count"=>$count,"result"=>$query->result());
            return (object)$result;
        }
    }
    
}
