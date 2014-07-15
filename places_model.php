<?php

class Places_model extends CI_Model{
    
    public function get_nearby($cat,$lat,$lng,$radius,$max,$page,$filter){
        $max=$max;
        $cat=$cat;
        $page=$page;
        $radius=$radius;
        $start=($page==0) ? $start=0 : $start=(($page*$max)-$max);
        if($filter=='distance'){
            $order_by = 'distance';
        } else if($filter=='name') {
            $order_by = 'name';
        } else {
            $order_by = 'distance';
        }
        $query=$this->db->query("SELECT `id`,`name`,`address`,`lat`,`lng`, round(  6371 * acos( cos( radians({$lat}) ) * cos( radians( LAT ) ) * cos( radians( LNG ) - radians({$lng}) ) + sin( radians({$lat}) ) * sin( radians( LAT ) ) ) ,2) AS `distance` FROM `locations` WHERE ((`status` = '1') AND (`category`={$cat})) HAVING `distance` < {$radius} ORDER BY `{$order_by}` LIMIT {$start},{$max}");
        $count=$query->num_rows();
        if($count==0){
            return NULL;
        } else {
            $result=array("count"=>$count,"result"=>$query->result());
            return (object)$result;
        }
    }
    
}
