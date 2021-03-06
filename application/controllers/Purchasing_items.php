<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchasing_items extends CI_Controller {

	
        function __construct() {
            parent::__construct();
            $this->load->model('Purchasing_items_model'); 
        }

        public function index(){
            $this->view_search();
	}
        
        function view_search($datas=''){
//            $this->add();
            $data['search_list'] = $this->Purchasing_items_model->search_result();
            $data['main_content']='purchasing_invoices/search_purchasing_invoices'; 
            $data['supplier_list'] = get_dropdown_data(SUPPLIERS,'supplier_name','id','Suppliers');
            $this->load->view('includes/template',$data);
	}
        
	function add(){ 
            $data  			= $this->load_data(); 
            $data['action']		= 'Add';
            $data['main_content']='purchasing_items/manage_purchasing_items';    
            $this->load->view('includes/template',$data);
	}
	
	function edit($id){ 
            $data  			= $this->load_data($id); 
            $data['action']		= 'Edit';
            $data['main_content']='vehicle_rates/manage_purchasing_items'; 
            $this->load->view('includes/template',$data);
	}
	
	function delete($id){ 
            $data  			= $this->load_data($id);
            $data['action']		= 'Delete';
            $data['main_content']='purchasing_items/view_purchasing_items'; 
            $data['inv_data'] = $this->get_invoice_info($id);
            
            $this->load->view('includes/template',$data); 
	}
	
	function view($id){ 
            $data  			= $this->load_data($id);
            $data['action']		= 'View';
            $data['main_content']='purchasing_items/view_purchasing_items'; 
            $data['inv_data'] = $this->get_invoice_info($id);
//            echo '<pre>';            print_r($data); die;
            $this->load->view('includes/template',$data);
	}
	
        
	function validate(){   
//            echo '<pre>';            print_r($this->input->post()); die;
            $this->form_val_setrules(); 
            if($this->form_validation->run() == False){
                switch($this->input->post('action')){
                    case 'Add':
                            $this->session->set_flashdata('error','Not Saved! Please Recheck the form'); 
                            $this->add();
                            break;
                    case 'Edit':
                            $this->session->set_flashdata('error','Not Saved! Please Recheck the form');
                            $this->edit($this->input->post('id'));
                            break;
                    case 'Delete':
                            $this->delete($this->input->post('id'));
                            break;
                } 
            }
            else{
                switch($this->input->post('action')){
                    case 'Add':
                            $this->create();
                    break;
                    case 'Edit':
                        $this->update();
                    break;
                    case 'Delete':
                        $this->remove();
                    break;
                    case 'View':
                        $this->view();
                    break;
                }	
            }
	}
        
	function form_val_setrules(){
            $this->form_validation->set_error_delimiters('<p style="color:rgb(255, 115, 115);" class="help-block"><i class="glyphicon glyphicon-exclamation-sign"></i> ','</p>');

            $this->form_validation->set_rules('invoice_date','Invoice Date','required');
            $this->form_validation->set_rules('reference','Reference','required'); 
      }	
      function check_unique_vehicle(){
          $res = array();
          if($this->input->post('id')!=''){
                $res =  get_dropdown_data(VEHICLE_RATES,'id','id','','vehicle_id = "'.$this->input->post('vehicle_id').'" and id!= "'.$this->input->post('id').'" ');  
          } else {
                 $res =  get_dropdown_data(VEHICLE_RATES,'id','id','','vehicle_id = "'.$this->input->post('vehicle_id').'" ');;    
          } 
                if(count($res)==0){
                    return true;
                }else{
                    $this->form_validation->set_message('check_unique_vehicle','Active Vehicle Rates alrady exists for this vehicle.');
                    return false;
                } 
        }
        function new_items_insertion($item){
            
            $cur_det = $this->Purchasing_items_model->get_currency_for_code($this->input->post('currency_code'));
//                    echo '<pre>';            print_r($this->input->post()); die;
//                    echo '<pre>';            print_r($cur_det); die;
                    
            if(!empty($item)){ 
                    
                    $item_id = get_autoincrement_no(ITEMS); 
                    $item_code = ($item['item_code']=='')?gen_id('1', ITEMS, 'id',4):$item['item_code'];
                    $inputs['status'] = (isset($inputs['status']))?1:0;
                    $inputs['sales_excluded'] = (isset($inputs['sales_excluded']))?1:0;
                    $inputs['purchases_excluded'] = (isset($inputs['purchases_excluded']))?1:0;
                    $data['item']           =   array(
                                                    'id' => $item_id,
                                                    'item_code' => $item_code,
                                                    'item_name' => $item['item_desc'],
                                                    'description' => $item['description'],
                                                    'item_uom_id' => $item['item_quantity_uom_id'],
                                                    'item_uom_id_2' => $item['item_quantity_uom_id_2'],
                                                    'item_category_id' => $item['cat_id'],
                                                    'item_type_id' => 1, // 1 => for purchased item  
                                                    'sales_excluded' => 0,
                                                    'purchases_excluded' => 0,
                                                    'purch_inv_ref' => 1, //item created when invoicing for purchase
                                                    'synced' => 0, 
                                                    'status' => 1, 
                                                    'added_on' => date('Y-m-d'),
                                                    'added_by' => $this->session->userdata(SYSTEM_CODE)['ID'],
                                                ); 
                    $data['purchase_price'] =   array(
                                                    'item_id' => $item_id,
                                                    'item_price_type' => 1, //2 purchasing price
                                                    'price_amount' =>$item['item_unit_cost'],
                                                    'currency_code' =>$cur_det['code'],
                                                    'currency_value' =>$cur_det['value'], 
                                                    'supplier_id' =>$this->input->post('supplier_id'),
                                                    'supplier_unit' =>'cts', // string value can be change at item mng purchase price
                                                    'supplier_unit_conversation' =>1,// can be change at item mng purchase price
                                                    'note' =>'',//Genrated on Purchasing Invoice
                                                    'status' =>1,
                                                ); 
                    
                    $data['standard_price'] = array(
                                                    'item_id' => $item_id,
                                                    'item_price_type' => 3, //3 Standard price
                                                    'price_amount' =>$item['item_unit_cost'],
                                                    'currency_code' =>$cur_det['code'],
                                                    'currency_value' =>$cur_det['value'], 
                                                    'status' =>1,
                                                    );
                    
//                    echo '<pre>';            print_r($data); die;
                    $ins_res = $this->Purchasing_items_model->add_new_invoice_item($data);
            }
            
            return $ins_res;
        }
	function create(){  
            $inputs = $this->input->post();
            $invoice_id = get_autoincrement_no(SUPPLIER_INVOICE);
            $invoice_no = gen_id(SUP_INVOICE_NO_PREFIX, SUPPLIER_INVOICE, 'id');
            
//            echo '<pre>';            print_r($this->input->post()); die; 
            $cur_det = $this->Purchasing_items_model->get_currency_for_code($inputs['currency_code']);
            if(isset($inputs['status'])){
                $inputs['status'] = 1;
            } else{
                $inputs['status'] = 0;
            }
            $data['inv_tbl'] = array(
                                    'id' => $invoice_id,
                                    'supplier_invoice_no' => $invoice_no,
                                    'supplier_id' => $inputs['supplier_id'], 
                                    'reference' => $inputs['reference'],  
                                    'comments' => $inputs['memo'], 
                                    'invoice_date' => strtotime($inputs['invoice_date']), 
                                    'invoiced' => 1,   
                                    'payment_term_id' => $inputs['payment_term_id'], 
                                    'currency_code' => $inputs['currency_code'], 
                                    'currency_value' => $cur_det['value'], 
                                    'location_id' => $inputs['location_id'],
                                    'status' => 1,
                                    'added_on' => date('Y-m-d'),
                                    'added_by' => $this->session->userdata(SYSTEM_CODE)['ID'],
                                );
            $data['inv_desc'] = array(); 
            $data['item_stock_transection'] = array(); //stock transection purchasing
            
            $total = 0;
            foreach ($inputs['inv_items'] as $inv_item){
                $total += $inv_item['item_quantity']*$inv_item['item_unit_cost'];
                
                $item_add_res = $this->new_items_insertion($inv_item);
                
                $data['inv_desc'][] = array(
                                            'supplier_invoice_id' => $invoice_id,
                                            'item_id' => $item_add_res[1],
                                            'supplier_item_desc' => $inv_item['item_desc'],
                                            'purchasing_unit' => $inv_item['item_quantity'],
                                            'purchasing_unit_uom_id' => $inv_item['item_quantity_uom_id'],
                                            'secondary_unit' => $inv_item['item_quantity_2'],
                                            'secondary_unit_uom_id' => $inv_item['item_quantity_uom_id_2'],
                                            'purchasing_unit_price' => $inv_item['item_unit_cost'],
                                            'location_id' => $inputs['location_id'],
                                            'status' => 1,
                                            'deleted' => 0,
                                        );
                
                $data['item_stock_transection'][] = array(
                                                            'transection_type'=>1, //1 for purchsing transection
                                                            'trans_ref'=>$invoice_id, 
                                                            'item_id'=>$item_add_res[1], 
                                                            'units'=>$inv_item['item_quantity'], 
                                                            'uom_id'=>$inv_item['item_quantity_uom_id'], 
                                                            'units_2'=>$inv_item['item_quantity_2'], 
                                                            'uom_id_2'=>$inv_item['item_quantity_uom_id_2'], 
                                                            'location_id'=>$inputs['location_id'], 
                                                            'status'=>1, 
                                                            'added_on' => date('Y-m-d'),
                                                            'added_by' => $this->session->userdata(SYSTEM_CODE)['ID'],
                                                            );
                
                if($inv_item['item_quantity_uom_id_2']!=0)
                    $item_stock_data = $this->stock_status_check($item_add_res[1],$inputs['location_id'],$inv_item['item_quantity_uom_id'],$inv_item['item_quantity'],$inv_item['item_quantity_uom_id_2'],$inv_item['item_quantity_2']);
                else
                    $item_stock_data = $this->stock_status_check($item_add_res[1],$inputs['location_id'],$inv_item['item_quantity_uom_id'],$inv_item['item_quantity']);
                
                if(!empty($item_stock_data)){
                    $data['item_stock'][] = $item_stock_data;
                }
                
            }
            
            //payments
            $this->load->model('Payments_model');
            $payment_terms = $this->Payments_model->get_payment_term($inputs['payment_term_id']);
            if($payment_terms['payment_done']==1){ //cash payment for invoice 
                $trans_id = get_autoincrement_no(TRANSECTION);
                $data['payment_transection'] = array(
                                                'id' =>$trans_id,
                                                'transection_type_id' =>3, //1 for Supp payments
                                                'reference' =>'', 
                                                'person_type' =>20, //10 for Supplier 
                                                'person_id' =>$inputs['supplier_id'],
                                                'transection_amount' =>$total,
                                                'currency_code' => $inputs['currency_code'], 
                                                'currency_value' => $cur_det['value'], 
                                                'trans_date' => strtotime($inputs['invoice_date']),
                                                'trans_memo' => 'Supplier Cash Invoice',
                                                'status' => 1,
                                            );
                
                $data['payment_transection_ref'] = array(
                                                'transection_id' =>$trans_id,
                                                'reference_id' =>$invoice_id,
                                                'trans_reference' =>$invoice_no,
                                                'transection_ref_amount' =>$total, 
                                                'person_type' =>20, //10 for supplier 
                                                'status' =>1, 
                                            );
                
                $data['inv_tbl']['payment_settled']=1;
                
            }
            
            $data['gl_trans'] = array(array(
                                                'person_type' => 20,
                                                'person_id' => $inputs['supplier_id'],
                                                'trans_ref' => $invoice_id,
                                                'trans_date' =>strtotime($inputs['invoice_date']),
                                                'account' => 5, //5 inventory GL
                                                'account_code' => 1510, //5 inventory GL
                                                'memo' => '',
                                                'amount' => +($total),
                                                'currency_code' => $inputs['currency_code'], 
                                                'currency_value' => $cur_det['value'], 
                                                'fiscal_year'=> $this->session->userdata(SYSTEM_CODE)['active_fiscal_year_id'],
                                                'status' => 1,
                                        ),array(
                                                'person_type' => 20,
                                                'person_id' => $inputs['supplier_id'],
                                                'trans_ref' => $invoice_id,
                                                'trans_date' => strtotime($inputs['invoice_date']),
                                                'account' => 14, //14 AC Payable GL
                                                'account_code' => 2100, //inventory GL
                                                'memo' => '',
                                                'amount' => (-$total),
                                                'currency_code' => $inputs['currency_code'], 
                                                'currency_value' => $cur_det['value'], 
                                                'fiscal_year'=> $this->session->userdata(SYSTEM_CODE)['active_fiscal_year_id'],
                                                'status' => 1,
                                        )
                                    );
            if($inputs['payment_term_id']==1){
                $data['gl_trans'][] = array(
                                                'person_type' => 20,
                                                'person_id' => $inputs['supplier_id'],
                                                'trans_ref' => $invoice_id,
                                                'trans_date' => strtotime($inputs['invoice_date']),
                                                'account' => 14, //14 AC Payable GL
                                                'account_code' => 2100, 
                                                'memo' => '',
                                                'amount' => ($total),
                                                'currency_code' => $inputs['currency_code'], 
                                                'currency_value' => $cur_det['value'], 
                                                'fiscal_year'=> $this->session->userdata(SYSTEM_CODE)['active_fiscal_year_id'],
                                                'status' => 1,
                                        );
                $data['gl_trans'][] = array(
                                                'person_type' => 20,
                                                'person_id' => $inputs['supplier_id'],
                                                'trans_ref' => $invoice_id,
                                                'trans_date' => strtotime($inputs['invoice_date']),
                                                'account' => 1, //2 petty cash
                                                'account_code' => 1060,
                                                'memo' => '',
                                                'amount' => (-$total),
                                                'currency_code' => $inputs['currency_code'], 
                                                'currency_value' => $cur_det['value'], 
                                                'fiscal_year'=> $this->session->userdata(SYSTEM_CODE)['active_fiscal_year_id'],
                                                'status' => 1,
                                        );
            }
//            echo '<pre>';            print_r($data); die;
                    
		$add_stat = $this->Purchasing_items_model->add_db($data);
                
		if($add_stat[0]){ 
                    //update log data
                    $new_data = $this->Purchasing_items_model->get_single_row($add_stat[1]);
                    add_system_log(SUPPLIER_INVOICE, $this->router->fetch_class(), __FUNCTION__, '', $new_data);
                    $this->session->set_flashdata('warn',RECORD_ADD);
                    redirect(base_url($this->router->fetch_class().'/view/'.$invoice_id)); 
                }else{
                    $this->session->set_flashdata('warn',ERROR);
                    redirect(base_url($this->router->fetch_class()));
                } 
	}
        
        function stock_status_check($item_id,$loc_id,$uom,$units=0,$uom_2='',$units_2=0,$calc='-'){ //updatiuon for item_stock table
            $this->load->model('Item_stock_model');
            $stock_det = $this->Item_stock_model->get_single_row('',"location_id = '$loc_id' and item_id = '$item_id'");
            $available_units= $available_units_2 = 0;
            $update_arr = array();
            if(empty($stock_det)){
                $insert_data = array(
                                        'location_id'=>$loc_id,
                                        'item_id'=>$item_id,
                                        'uom_id'=>$uom,
                                        'units_available'=>0,
                                        'units_on_order'=>0,
                                        'units_on_demand'=>0,
                                        );
                if($uom_2!=''){
                    $insert_data['units_available_2'] = 0;
                    $insert_data['uom_id_2'] = $uom_2;
                }
                $this->Item_stock_model->add_db($insert_data);
                $available_units = $units;
                $available_units_2 = $units_2;
            }else{
                if($calc=='+'){
                    $available_units = $stock_det['units_available'] + $units;
                    $available_units_2 = $stock_det['units_available_2'] + $units_2;
                }else{
                    
                    $available_units = $stock_det['units_available'] - $units;
                    $available_units_2 = $stock_det['units_available_2'] - $units_2;
                }
            }
                $update_arr = array('location_id'=>$loc_id,'item_id'=>$item_id,'new_units_available'=>$available_units,'new_units_available_2'=>$available_units_2);
                
            return $update_arr;
        }
        
        
	function update(){ 
            $inputs = $this->input->post();
            $agent_id = $inputs['id']; 
            if(isset($inputs['status'])){
                $inputs['status'] = 1;
            } else{
                $inputs['status'] = 0;
            }
            $data = array(
                            'vehicle_id' => $inputs['vehicle_id'], 
                            'description' => $inputs['description'], 
                            'rate_amount' => $inputs['rate_amount'], 
                            'owner_rate_plan' => $inputs['owner_rate_plan'], 
                            'owner_rate' => $inputs['owner_rate'], 
                            'km_limit_day' => $inputs['km_limit_day'], 
                            'extra_km_rate' => $inputs['extra_km_rate'], 
                            'extra_time_rate' => $inputs['extra_time_rate'], 
                            'status' => $inputs['status'],
                            'updated_on' => date('Y-m-d'),
                            'updated_by' => $this->session->userdata(SYSTEM_CODE)['ID'],
                        );
            
//            echo '<pre>'; print_r($data); die;
            //old data for log update
            $existing_data = $this->Purchasing_items_model->get_single_row($inputs['id']);

            $edit_stat = $this->Purchasing_items_model->edit_db($inputs['id'],$data);
            
            if($edit_stat){
                //update log data
                $new_data = $this->Purchasing_items_model->get_single_row($inputs['id']);
                add_system_log(VEHICLE_RATES, $this->router->fetch_class(), __FUNCTION__, $new_data, $existing_data);
                $this->session->set_flashdata('warn',RECORD_UPDATE);
                    
                redirect(base_url($this->router->fetch_class().'/edit/'.$inputs['id']));
            }else{
                $this->session->set_flashdata('warn',ERROR);
                redirect(base_url($this->router->fetch_class()));
            } 
	}	
        
        function remove(){
            $inputs = $this->input->post(); 
            $existing_data = $this->get_invoice_info($inputs['id']);  
            //check the payments before delete reservation
            $this->load->model('Payments_model');
            
            $cur_det = get_currency_for_code($existing_data['invoice_dets']['currency_code']);
            $trans_data = $this->Payments_model->get_transections(20,$inputs['id']); //20 for supp in transection
            if(!empty($trans_data)){
                $this->session->set_flashdata('error','You need to remove the Payments transections before delete Invoice!');
                redirect(base_url($this->router->fetch_class().'/delete/'.$inputs['id']));
                return false;
            }
            $data['pi_tbl'] = array(
                                        'deleted' => 1,
                                        'deleted_on' => date('Y-m-d'),
                                        'deleted_by' => $this->session->userdata(SYSTEM_CODE)['ID']
                                     ); 
            
                //GL Entries
            $data['gl_trans'] = array(array(
                                                'person_type' => 20,
                                                'person_id' => $existing_data['invoice_dets']['supplier_id'],
                                                'trans_ref' => $existing_data['invoice_dets']['id'],
                                                'trans_date' => strtotime("now"),
                                                'account' => 5, //5 inventory GL
                                                'account_code' => 1510, //5 inventory GL
                                                'memo' => 'SI Deletion',
                                                'amount' => (-$existing_data['invoice_total']),
                                                'currency_code' => $cur_det['code'],
                                                'currency_code' => $cur_det['value'],
                                                'fiscal_year'=> $this->session->userdata(SYSTEM_CODE)['active_fiscal_year_id'],
                                                'status' => 1,
                                        ),array(
                                                'person_type' => 20,
                                                'person_id' => $existing_data['invoice_dets']['supplier_id'],
                                                'trans_ref' => $existing_data['invoice_dets']['id'],
                                                'trans_date' => strtotime("now"),
                                                'account' => 14, //14 AC Payable GL
                                                'account_code' => 2100, //inventory GL
                                                'memo' => 'SI Deletion',
                                                'amount' => ($existing_data['invoice_total']),
                                                'currency_code' => $cur_det['code'],
                                                'currency_code' => $cur_det['value'],
                                                'fiscal_year'=> $this->session->userdata(SYSTEM_CODE)['active_fiscal_year_id'],
                                                'status' => 1,
                                        )
                                    );
                
            $delete_stat = $this->Purchasing_items_model->delete_db($inputs['id'],$data);
                    
            if($delete_stat){
                //update log data
                add_system_log(SUPPLIER_INVOICE, $this->router->fetch_class(), __FUNCTION__,$existing_data, '');
                $this->session->set_flashdata('warn',RECORD_DELETE);
                redirect(base_url($this->router->fetch_class()));
            }else{
                $this->session->set_flashdata('warn',ERROR);
                redirect(base_url($this->router->fetch_class()));
            }  
	}
	
	
	function remove2(){
            $id  = $this->input->post('id'); 
            
            $existing_data = $this->Purchasing_items_model->get_single_row($inputs['id']);
            if($this->Purchasing_items_model->delete2_db($id)){
                //update log data
                add_system_log(HOTELS, $this->router->fetch_class(), __FUNCTION__, '', $existing_data);
                
                $this->session->set_flashdata('warn',RECORD_DELETE);
                redirect(base_url('company'));

            }else{
                $this->session->set_flashdata('warn',ERROR);
                redirect(base_url('company'));
            }  
	}
        
        function load_data($id=''){
            if($id!=''){
                $data['user_data'] = $this->Purchasing_items_model->get_single_row($id); 
                if(empty($data['user_data'])){
                    $this->session->set_flashdata('error','INVALID! Please use the System Navigation');
                    redirect(base_url($this->router->fetch_class()));
                }
            }
            
            
            $data['country_list'] = get_dropdown_data(COUNTRY_LIST,'country_name','country_code','');
            $data['dropdown_name_list'] = get_dropdown_data(DROPDOWN_LIST_NAMES,'dropdown_list_name','id','');
            $data['supplier_list'] = get_dropdown_data(SUPPLIERS, 'supplier_name', 'id','');
            $data['payment_term_list'] = get_dropdown_data(PAYMENT_TERMS, 'payment_term_name', 'id');
            $data['location_list'] = get_dropdown_data(INV_LOCATION,'location_code','id',''); //14 for sales type
            $data['item_list'] = get_dropdown_data(ITEMS,array('item_name',"CONCAT(item_name,'-',item_code) as item_name"),'item_code','','item_type_id = 1'); 
            $data['item_category_list'] = get_dropdown_data(ITEM_CAT,'category_name','id',''); 
            $data['currency_list'] = get_dropdown_data(CURRENCY,'code','code','Currency');

            return $data;
	}	
        
        function search(){ 
            $input = $this->input->post();
            $search_data=array( 
                                'supp_invoice_no' => $this->input->post('supp_invoice_no'),
                                'supplier_id' => $input['supplier_id'],  
//                                    'category' => $this->input->post('category'), 
                                ); 
            $invoices['search_list'] = $this->Purchasing_items_model->search_result($search_data);
            
//		$data_view['search_list'] = $this->Purchasing_items_model->search_result();
            $this->load->view('purchasing_items/search_purchasing_invoices_result',$invoices);
	}
        
        function get_single_item(){
            $inputs = $this->input->post(); 
            $data = $this->Purchasing_items_model->get_single_item($inputs['item_code'],$inputs['supplier_id']); 
            echo json_encode($data);
        }
        function test(){
//            echo '<pre>';            print_r($this->router->class); die;
            
//            $this->load->view('invoices/sales_invoices');
            $data = $this->Purchasing_items_model->get_single_item(1002,15);
            echo '<pre>' ; print_r($data);die;
//            log_message('error', 'Some variable did not contain a value.');
        }
        function sales_invoice_print($inv_id){
//            echo '<pre>';            print_r($this->get_invoice_info(2)); die;
            $inv_data = $this->get_invoice_info($inv_id);
            $inv_dets = $inv_data['invoice_dets'];
            $inv_desc = $inv_data['invoice_desc'];
            $inv_trans = $inv_data['inv_transection'];
            $this->load->library('Pdf');
            
            // create new PDF document
            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->fl_header='header_am';//invice bg
            
            // set document information
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Fahry Lafir');
            $pdf->SetTitle('PDF AM Invoice');
            $pdf->SetSubject('AM Invoice');
            $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
            
            // set default header data
            $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

            // set header and footer fonts
            $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

            // set default monospaced font
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

            // set margins
            $pdf->SetMargins(PDF_MARGIN_LEFT, 50, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

            // set auto page breaks
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

            // set image scale factor
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
                    
            // set font
            $pdf->SetFont('times', '', 11);
        
        
            $pdf->AddPage();   
            
            
            
            $pdf->SetTextColor(32,32,32);     
            
            $html = '<table>
                        <tr>
                            <td>Invoiced Date: '.date('m/d/Y',$inv_dets['invoice_date']).'</td>
                            <td align="right">Invoiced by: '.$inv_dets['sales_person'].'</td>
                        </tr>
                        <tr> 
                            <td colspan="2" align="center"><h1>INVOICE</h1></td>
                        </tr> 
                        <tr>
                            <td>Insurance Company: '.((isset($inv_dets['insurance_company']) && $inv_dets['insurance_company']!='')?$inv_dets['insurance_company']:'-').'</td>
                            <td align="right">Vehicle No: '.((isset($inv_dets['vehicle_number']) && $inv_dets['vehicle_number']!='')?$inv_dets['vehicle_number']:'-').'</td>
                        </tr>
                        <tr>
                            <td>Customer: '.$inv_dets['customer_name'].'</td>
                            <td align="right">Make/Model: '.((isset($inv_dets['vehicle_model']) && $inv_dets['vehicle_model']!='')?$inv_dets['vehicle_model']:'-').'</td>
                        </tr>
                        <tr>
                            <td>Customer Contact Number: '.$inv_dets['phone'].'</td>
                            <td align="right">Chasis No: '.((isset($inv_dets['chasis_no']) && $inv_dets['chasis_no']!='')?$inv_dets['chasis_no']:'-').'</td>
                        </tr>
                        <tr><td  colspan="5"><br></td></tr>
                    </table> 
                ';
           
//            echo '<pre>';            print_r($inv_data); die;
            foreach ($inv_desc as $inv_itms){ 
                     $html .= '<table id="example1" class="table-line" border="0">
                                <thead>
                                    <tr class="colored_bg" style="background-color:#E0E0E0;">
                                         <th colspan="5">'.$inv_data['item_cats'][$inv_itms[0]['item_category']].'</th> 
                                     </tr>
                                    <tr style="">
                                         <th  width="10%"><u><b>Qty</b></u></th> 
                                         <th width="40%" style="text-align: left;"><u><b>Description</b></u></th>  
                                         <th width="15%" style="text-align: right;"><u><b>Rate</b></u></th> 
                                         <th width="15%" style="text-align: right;"><u><b>Discount</b></u></th> 
                                         <th width="19%" style="text-align: right;"><u><b>Total</b></u></th> 
                                     </tr>
                                </thead>
                            <tbody>';
                     
                     foreach ($inv_itms as $inv_itm){
                         $html .= '<tr>
                                        <td width="10%">'.$inv_itm['quantity'].'</td> 
                                        <td width="40%" style="text-align: left;">'.$inv_itm['item_description'].'</td>  
                                        <td width="15%" style="text-align: right;">'. number_format($inv_itm['unit_price'],2).'</td> 
                                        <td width="15%" style="text-align: right;">'. number_format($inv_itm['discount_persent'],2).'</td> 
                                        <td width="19%" style="text-align: right;">'. number_format($inv_itm['sub_total'],2).'</td> 
                                    </tr> ';
                     }
                     $html .= '
                                <tr><td  colspan="5"></td></tr></tbody></table>'; 
            }
            $html .= '
                    
                    <table id="example1" class="table-line" border="0">
                        
                       <tbody>

                                <tr class="td_ht">
                                    <td style="text-align: right;" colspan="4"><b> Total</b></td> 
                                    <td  width="19%"  style="text-align: right;"><b>'. number_format($inv_data['invoice_desc_total'],2).'</b></td> 
                                </tr>'; 
                        foreach ($inv_trans as $inv_tran){
                            $html .= '<tr>
                                            <td  style="text-align: right;" colspan="4">'.$inv_tran['name'].'</td> 
                                            <td  width="19%"  style="text-align: right;">'. number_format($inv_tran['transection_amount'],2).'</td> 
                                        </tr> ';

                        }
                        $html .= '<tr>
                                    <td  style="text-align: right;" colspan="4"><b>Balance</b></td> 
                                    <td width="19%"  style="text-align: right;"><b>'. number_format($inv_data['invoice_total'],2).'</b></td> 
                                </tr> 
                        </tbody>
                    </table>
                                                               
                ';
             $html .= '
            <style>
            .colored_bg{
                background-color:#E0E0E0;
            }
            .table-line th, .table-line td {
                padding-bottom: 2px;
                border-bottom: 1px solid #ddd;
                text-align:center; 
            }
            .text-right,.table-line.text-right{
                text-align:right;
            }
            .table-line tr{
                line-height: 30px;
            }
            </style>
                    ';
            $pdf->writeHTML($html);
            
            $pdf->SetFont('times', '', 12.5, '', false);
            $pdf->SetTextColor(255,125,125);           
            $pdf->Text(160,20,$inv_dets['invoice_no']);
            // force print dialog
            $js = 'this.print();';
//            $js = 'print(true);';
            // set javascript
            $pdf->IncludeJS($js);
            $pdf->Output('example_003.pdf', 'I');
                
        }
        
        function get_invoice_info($inv_id){
            if($inv_id!=''){
                 $data['invoice_dets'] = $this->Purchasing_items_model->get_single_row($inv_id);
                if(empty($data['invoice_dets'])){
                    $this->session->set_flashdata('error','INVALID! Please use the System Navigation');
                    redirect(base_url($this->router->fetch_class()));
                }
            }
           
            $data['invoice_desc'] = array();
            $invoice_desc = $this->Purchasing_items_model->get_invc_desc($inv_id);
            
            $data['invoice_desc_list'] = $invoice_desc;
            
            $data['item_cats'] = get_dropdown_data(ITEM_CAT, 'category_name','id');
            $item_cats = get_dropdown_data(ITEM_CAT, 'category_name','id');
            
            $data['invoice_desc_total']= 0;
            foreach ($item_cats as $cat_key=>$cay_name){ 
                foreach ($invoice_desc as $invoice_itm){
                    if($invoice_itm['item_category']==$cat_key){
                        $data['invoice_desc'][$cat_key][]=$invoice_itm;
                        $data['invoice_desc_total'] +=  $invoice_itm['sub_total'];
                    }
                }
            }
            $data['invoice_total'] = $data['invoice_desc_total'];
            $data['transection_total']=0;
            $this->load->model('Payments_model');
            $data['inv_transection'] = $this->Payments_model->get_transections(20,$inv_id); //20 for Supplier 
            
            foreach ($data['inv_transection'] as $trans){
                switch ($trans['calculation']){
                    case 1: //  addition from invoive
                            $data['transection_total'] += $trans['transection_ref_amount'];
                            $data['invoice_total'] += $trans['transection_ref_amount'];
                            break;
                    case 2: //substitute from invoiice
                            $data['transection_total'] -= $trans['transection_ref_amount'];
                            $data['invoice_total'] -= $trans['transection_ref_amount'];
                            break;
                    case 4: //settlement cust
                            $data['transection_total'] += $trans['transection_ref_amount'];
                            $data['invoice_total'] += $trans['transection_ref_amount'];
                            break;
                    default:
                            break;
                } 
            }
//            echo '<pre>';            print_r($data); die;
            return $data;
        }
        
        function fl_ajax(){
            
//            echo '<pre>';            print_r($this->input->post()); die;
            $func = $this->input->post('function_name');
            $param = $this->input->post();
            
            if(method_exists($this, $func)){ 
                (!empty($param))?$this->$func($param):$this->$func();
            }else{
                return false;
            }
        }
        function get_single_category(){
            $inputs = $this->input ->post();
//            echo '<pre>';            print_r($inputs); die;
            $data = $this->Purchasing_items_model->get_single_category($inputs['item_category_id']); 
            echo json_encode($data);
        }
}
