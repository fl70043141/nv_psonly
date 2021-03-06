<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class WebsiteSync_model extends CI_Model
{   
    public function __construct()
    {
        parent::__construct(); 
        $this->oc_website = $this->load->database('oc_website', TRUE);   
    }
            
    
    function get_report_data($idsString=''){
        $query = "select id,report_no from ".LAB_REPORT." WHERE sync_required = 1 ";
         
        if($idsString!=''){ 
            $query .= "AND id IN($idsString)" ;
        }
        $result = $this->db->query($query)->result_array();
//        echo $this->db->last_query(); die;
        return $result;
    }
    
    function get_report_sync($report_id){
        $this->db->select('id');
        $this->db->from(LAB_REPORT_SYNC);
        $this->db->where('report_id',$report_id);
        $this->db->where('remote_sync_status',0);
        
//        echo $this->db->get()->num_rows();die;
        return $this->db->get()->num_rows();;
    }
    
      public function add_local_sync($data){  
                $this->db->trans_start();
		$this->db->insert(LAB_REPORT_SYNC, $data); 
                
                $this->db->where('id', $data['report_id']);
//                $this->db->where('deleted',0);
		$this->db->update(LAB_REPORT, array('sync_required'=>0));
                $status=$this->db->trans_complete();

		return $status;
	}
    
      public function update_lab_report_statOnly($id){  
                $this->db->trans_start();
                
                $this->db->where('id', $id);
//                $this->db->where('deleted',0);
		$this->db->update(LAB_REPORT, array('sync_required'=>0));
                $status=$this->db->trans_complete();

		return $status;
	}
    
     public function edit_lab_report_syncStat($id,$data){
//            echo '<pre>'; print_r($data); die;
		$this->db->trans_start();
                
		$this->db->where('id', $id);
                $this->db->where('deleted',0);
		$this->db->update(LAB_REPORT, $data);
                        
		$status=$this->db->trans_complete();
		return $status;
	}
    
     public function edit_lab_report_syncTable($id,$data){
//            echo '<pre>'; print_r($data); die;
		$this->db->trans_start();
                
		$this->db->where('id', $id); 
		$this->db->update(LAB_REPORT_SYNC, $data);
                        
		$status=$this->db->trans_complete();
		return $status;
	}
    
    
    function get_report_local_syncData($rep_ids=array()){
        
        $query = "select lrs.id as lrs_id,lrs.remote_sync_status, lr.*,
                (select dropdown_value from dropdown_list where id=lr.object) as object_val, 
                (select dropdown_value from dropdown_list where id=lr.identification) as identification_val, 
                (select dropdown_value from dropdown_list where id=lr.variety) as variety_val, 
                (select dropdown_value from dropdown_list where id=lr.cut) as cut_val, 
                (select dropdown_value from dropdown_list where id=lr.shape) as shape_val, 
                (select dropdown_value from dropdown_list where id=lr.color_distribution) as color_distribution_val, 
                (select dropdown_value from dropdown_list where id=lr.refractive_index) as refractive_index_val, 
                (select dropdown_value from dropdown_list where id=lr.specific_gravity) as specific_gravity_val
                  from ".LAB_REPORT_SYNC." lrs
                  LEFT JOIN ".LAB_REPORT." lr ON lr.id = lrs.report_id
                  WHERE remote_sync_status = 0 ";
        $query_part = '';
         if(!empty($rep_ids)){
            $query_part = ' AND (';
            $i=1;
            foreach ($rep_ids as $rep_id){
                $query_part .= " lr.id = '$rep_id' ";
                $query_part .= (count($rep_ids)==$i)?'':' OR ';
                $i++;
            }
            $query_part .= ')';
        }
        $query .=$query_part; 
        
        $result = $this->db->query($query)->result_array();
//        echo $this->db->last_query(); die;
        return $result;
    }
    
    public function add_item_data_website($item_data){  
        
//            echo '<pre>';            print_r($item_data); die;
       
                $this->oc_website->trans_start(); 
                    
//                    echo '<pre>';            print_r($item_data); die;
                    $this->oc_website->insert('oc_product', $item_data['product']); 
                    $this->oc_website->insert('oc_product_description', $item_data['product_desc']); 
                    
                    if(!empty($item_data['product_images']))  
                        $this->oc_website->insert_batch('oc_product_image', $item_data['product_images']); 
                    
                    $this->oc_website->insert('oc_product_to_category', $item_data['product_category']); 
                    $this->oc_website->insert('oc_product_to_layout', $item_data['product_layout']);  
                    $this->oc_website->insert('oc_product_to_store', $item_data['product_to_store']); 
                
                $status=$this->oc_website->trans_complete();

		return $status;
	}
        
    public function add_category_data_website($item_data){  
        
//            echo '<pre>';            print_r($item_data); die;
       
                $this->oc_website->trans_start(); 
                    
//                    echo '<pre>';            print_r($item_data); die;
                    $this->oc_website->insert('oc_category', $item_data['category']); 
                    $this->oc_website->insert('oc_category_description', $item_data['category_desc']); 
                    $this->oc_website->insert('oc_category_path', $item_data['category_path']); 
                    $this->oc_website->insert('oc_category_to_layout', $item_data['category_to_layout']);  
                    $this->oc_website->insert('oc_category_to_store', $item_data['category_to_store']); 
                
                $status=$this->oc_website->trans_complete();

		return $status;
	}
    public function test_db2(){  
               $this->oc_website->select('*');
               $this->oc_website->from('oc_product');
               $res = $this->oc_website->get()->result_array();
            echo '<pre>';            print_r($res); die;

		return $res;
	}
    
    
    
    
    
    
    
    
    
    
    
    
    /**
     * FL NOTE --> WE MUST SET THIS REMOTE URL CORRECTLY
     * IF IT IS IN SAME LOCAL SERVER --> $url = "http://localhost/SYSTEM_NAME/PATH/TO/FILE/a.php";
     * IF IT IS IN REMOTE SERVER --> $url = "http://BGL.com/interface/punch/a.php";
     */    
    
    private $url = "http://localhost/nveloop_pos_sync/POS_sync/";
//     private $url = "http://berberyngemlab.com/bgl_reports/test_report_sync/BglSync/";
//   private $url = "http://berberyngemlab.com/bgl_reports/report_sync/BglSync/";
  
    public function postToRemoteServer($post_sub_array=array('fahry'=>1991))
    {
        $this->curl->create($this->url);
        $json_data = json_encode($post_sub_array); 
        $encrypted_post_data = mc_encrypt($json_data, ENCRYPTION_KEY);
        //data serialize
        $post_data = array('post_data' => serialize($encrypted_post_data));
        //Post - If you do not use post, it will just run a GET request 
        $this->curl->post($post_data);    
        //Execute - returns responce
        $result = $this->curl->execute();  
//        echo '<img src="data:image/gif;base64,'.$result.'">'; die;
        return $result; 
    }
    
    
      public function add_oc_sync_log($data){  
                $this->db->trans_start();
		$this->db->insert_batch(ITEM_SYNCED_LOG, $data); 
                
                foreach ($data as $itm_stat){
                    $this->db->where('id', $itm_stat['item_id']); 
                    $this->db->update(ITEMS, array('synced'=>1));
                }
                $status=$this->db->trans_complete();

		return $status;
	}
}

?>