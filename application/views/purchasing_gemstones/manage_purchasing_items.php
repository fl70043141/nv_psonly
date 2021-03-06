<?php
	
	$result = array(
                        'id'=>"",
                        'supplier_id'=>"",
                        'vehicle_number'=>"",
                        'chasis_no'=>"",
                        'vehicle_model'=>"",
                        'insurance_company'=>"",
                        'reference'=> 'G-'.date('Ymd-Hi'),
                        'invoice_date'=>date('m/d/Y'),
                        'item_discount'=>0,
                        'currency_code'=>$this->session->userdata(SYSTEM_CODE)['default_currency'],
                        'item_quantity'=>1,
                        'item_quantity_2'=>1,
                        );   		
	
	 
	switch($action):
	case 'Add':
		$heading	= 'Add';
		$dis		= '';
		$view		= '';
		$o_dis		= ''; 
	break;
	
	case 'Edit':
		if(!empty($user_data[0])){$result= $user_data[0];} 
		$heading	= 'Edit';
		$dis		= '';
		$view		= '';
		$o_dis		= ''; 
	break;
	
	case 'Delete':
		if(!empty($user_data[0])){$result= $user_data[0];} 
		$heading	= 'Delete';
		$dis		= 'readonly';
		$view		= '';
		$o_dis		= ''; 
		$check_bx_dis		= 'disabled'; 
	break;
      
	case 'View':
		if(!empty($user_data[0])){$result= $user_data[0];} 
		$heading	= 'View';
		$view		= 'hidden';
		$dis        = 'readonly';
		$o_dis		= 'disabled'; 
	break;
endswitch;	 

//var_dump($result);
?> 
<!-- Main content -->
<style>
    
.pad1 {
  padding: 0px 10px 0 10px;
}
</style>

<?php // echo '<pre>'; print_r($facility_list); die;?>

<div class="row">
<div class="col-md-12">
    <br>   
    <div class="col-md-12">

    
<!--    
        <div class="">
            <a href="<?php // echo base_url($this->router->fetch_class().'/add');?>" class="btn btn-app "><i class="fa fa-plus"></i>Create New</a>
            <a href="<?php // echo base_url($this->router->fetch_class());?>" class="btn btn-app "><i class="fa fa-search"></i>Search</a>

        </div>-->
    </div>
    
 <!--<br><hr>-->
    <section  class="content"> 
        <!--Flash Error Msg-->
        <?php  if($this->session->flashdata('error') != ''){ ?>
        <div class='alert alert-danger ' id="msg2">
        <a class="close" data-dismiss="alert" href="#">&times;</a>
        <i ></i>&nbsp;<?php echo $this->session->flashdata('error'); ?>
        <script>jQuery(document).ready(function(){jQuery('#msg2').delay(1500).slideUp(1000);});</script>
        </div>
        <?php } ?>

        <?php  if($this->session->flashdata('warn') != ''){ ?>
        <div class='alert alert-success ' id="msg2">
        <a class="close" data-dismiss="alert" href="#">&times;</a>
        <i ></i>&nbsp;<?php echo $this->session->flashdata('warn'); ?>
        <script>jQuery(document).ready(function(){jQuery('#msg2').delay(1500).slideUp(1000);});</script>
        </div>
        <?php } ?>  
        
        <div class="">
              <!-- general form elements -->
              <div class="box box-primary"> 
                <!-- /.box-header -->
                <!-- form start -->
              
            <?php echo form_open($this->router->fetch_class()."/validate", 'id="form_search" class="form-horizontal"')?>  
   
                    <div class="box-body">
                        
                        <div class="row header_form_sales"> 
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Supplier/Seller<span style="color: red">*</span></label>
                                    <div class="col-md-7">    
                                         <?php  echo form_dropdown('supplier_id',$supplier_list,set_value('supplier_id'),' class="form-control select2" data-live-search="true" id="supplier_id"');?>
                                         <!--<span class="help-block"><?php // echo form_error('customer_type_id');?>&nbsp;</span>-->
                                    </div> 
                                    <div class="col-md-1 pull-left">
                                        <a id="add_supp_btn" class="btn btn-sm btn-primary"><span class="fa fa-plus-circle"></span></a>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Reference<span style="color: red">*</span></label>
                                    <div class="col-md-9">    
                                         <?php  echo form_input('reference',set_value('reference',$result['reference']),' class="form-control" id="reference"');?>
                                         <!--<span class="help-block"><?php // echo form_error('reference');?>&nbsp;</span>-->
                                    </div> 
                                </div> 
                                
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Payments<span style="color: red">*</span></label>
                                    <div class="col-md-9">    
                                         <?php  echo form_dropdown('payment_term_id',$payment_term_list,set_value('payment_term_id'),' class="form-control select2" data-live-search="true" id="payment_term_id"');?>
                                         <!--<span class="help-block"><?php // echo form_error('customer_type_id');?>&nbsp;</span>-->
                                    </div> 
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Currency<span style="color: red">*</span></label>
                                    <div class="col-md-9">    
                                         <?php  echo form_dropdown('currency_code',$currency_list,set_value('currency_code',$result['currency_code']),' class="form-control select2" data-live-search="true" id="currency_code"');?>
                                         <!--<span class="help-block"><?php // echo form_error('customer_type_id');?>&nbsp;</span>-->
                                    </div> 
                                </div>
                            </div>
                            <div class="col-md-4">
                                
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Date <span style="color: red">*</span></label>
                                    <div class="col-md-9">    
                                         <?php  echo form_input('invoice_date',set_value('invoice_date',$result['invoice_date']),' class="form-control datepicker" readonly id="invoice_date"');?>
                                         <!--<span class="help-block"><?php // echo form_error('invoice_date');?>&nbsp;</span>-->
                                    </div> 
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Received_to<span style="color: red">*</span></label>
                                    <div class="col-md-9">    
                                         <?php  echo form_dropdown('location_id',$location_list,set_value('location_id'),' class="form-control select2" data-live-search="true" id="location_id"');?>
                                         <!--<span class="help-block"><?php // echo form_error('customer_type_id');?>&nbsp;</span>-->
                                    </div> 
                                </div>
                            </div> 
                        </div>
                        <div class="row"> 
                            <hr>
                            <div class="">
                                <div id='add_item_form' class="col-md-12  bg-light-blue-gradient">
                                     
                                    <div class="row col-md-12 col-md-offset-1">
                                        <div id="first_col_form" class="col-md-2">
                                            <div class="form-group pad1">
                                                <label for="item_category_id">Description</label>
                                                <?php echo form_dropdown('item_category_id',$item_category_list,set_value('item_category_id'),' class="form-control add_item_inpt select2" style="width:100%;" data-live-search="true" id="item_category_id"');?>
                                            </div>
                                        </div> 
                                        <div hidden id="first_col_form" class="col-md-2">
                                            <div class="form-group pad1">
                                                <label for="item_desc">Description</label>
                                                <?php echo form_input('item_desc',set_value('item_desc'),' class="form-control add_item_inpt " style="width:100%;" data-live-search="true" id="item_desc"');?>
                                            </div>
                                        </div> 
                                        
                                        <div id="first_col_form" class="col-md-2">
                                            <div class="form-group pad1">
                                                <label for="treatments">NH/H &nbsp;&nbsp;&nbsp;<span id="treatment_add_new" style="font-size: 18px;"class="fa fa-plus-circle add_new_btn"></span></label>
                                                <?php echo form_dropdown('treatments',$treatments_list,set_value('treatments'),' class="form-control add_item_inpt  " style="width:100%;" data-live-search="true" id="treatments"');?>
                                            </div>
                                        </div>
                                        <div id="uom_div">
                                            
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group pad1">
                                                <label for="item_unit_cost">price/crt</label>
                                                <input type="text" name="item_unit_cost" class="form-control add_item_inpt" id="item_unit_cost" placeholder="Unit Cost for item">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row col-md-12  col-md-offset-1"> 
                                        <div id="first_col_form" class="col-md-2">
                                            <div class="form-group pad1">
                                                <label for="shape">Shape &nbsp;&nbsp;&nbsp;<span id="shape_add_new" style="font-size: 18px;"class="fa fa-plus-circle add_new_btn"></span></label>
                                                <?php echo form_dropdown('shape',$shape_list,set_value('shape'),' class="form-control add_item_inpt select2 " style="width:100%;" data-live-search="true" id="shape"');?>
                                            </div>
                                        </div>
                                        <div id="first_col_form" class="col-md-2">
                                            <div class="form-group pad1">
                                                <label for="color">color &nbsp;&nbsp;&nbsp;<span id="color_add_new" style="font-size: 18px;"class="fa fa-plus-circle add_new_btn"></span></label>
                                                <?php echo form_dropdown('color',$color_list,set_value('color'),' class="form-control add_item_inpt  select2" style="width:100%;" data-live-search="true" id="color"');?>
                                            </div>
                                        </div>
                                        <div id="first_col_form" class="col-md-2">
                                            <div class="form-group pad1">
                                                <label for="origin">Origin &nbsp;&nbsp;&nbsp;<span id="origin_add_new" style="font-size: 18px;"class="fa fa-plus-circle add_new_btn"></span></label>
                                                <?php echo form_dropdown('origin',$origin_list,set_value('origin'),' class="form-control add_item_inpt select2" style="width:100%;" data-live-search="true" id="origin"');?>
                                            </div>
                                        </div>
                                        <div id="first_col_form" class="col-md-2">
                                            <div class="form-group pad1">
                                                <label for="certification">Cert. &nbsp;&nbsp;&nbsp;<span id="cert_add_new" style="font-size: 18px;"class="fa fa-plus-circle add_new_btn"></span></label>
                                                <?php echo form_dropdown('certification',$certification_list,set_value('certification'),' class="form-control add_item_inpt select2 " style="width:100%;" data-live-search="true" id="certification"');?>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group pad1 "><br>
                                                <a style="height: 40px; font-size: 20px;padding-top:5px; " id="add_item_btn" class="btn-default btn add_item_inpt pad1 center-block  "> <span class="fa  fa-plus-circle"></span> Add</a>
                                            </div>
                                        </div>
                                    </div>
<!--                                    <table id="example1" class="table bg-gray-light table-bordered table-striped">
                                        <thead>
                                           <tr>
                                               <td><label for="item_code">Item Code</label></td>
                                               <td><label for="item_code">Item Description</label></td>
                                               <td><label for="item_code">Quantity</label></td>
                                               <td><label for="item_code">Unit Cost</label></td>
                                               <td><label for="item_code">Discount  </label></td>
                                               <td> </td>
                                           </tr>
                                           <tr>
                                               <td>
                                                    <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="item_code">Item Code</label>
                                                        <?php //  echo form_input('item_code',set_value('item_code'),' class="form-control add_item_inpt" data-live-search="true" id="item_code"');?>
                                                    </div>
                                                    </div>
                                               </td>
                                               <td>
                                                    <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="item_desc">Item Description</label>
                                                        <?php //  echo form_dropdown('item_desc',$item_list,set_value('item_desc'),' class="form-control add_item_inpt select2" style="width:100%;" data-live-search="true" id="item_desc"');?>
                                                    </div>
                                                    </div>
                                               </td>
                                               <td>
                                                    <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="item_quantity">Quantity <span id="unit_abbr">[Each]<span></label>
                                                        <input type="text" name="item_quantity" class="form-control add_item_inpt" id="item_quantity" placeholder="Enter Quamtity">
                                                    </div>
                                                    </div>
                                               </td>
                                               <td>
                                                    <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="item_unit_cost">Unit Cost</label>
                                                        <input type="text" name="item_unit_cost" class="form-control add_item_inpt" id="item_unit_cost" placeholder="Unit Cost for item">
                                                    </div>
                                                    </div>
                                               </td>
                                               <td>
                                                    <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="item_code">Discount</label>
                                                        <input type="text" name="item_discount" class="form-control add_item_inpt" id="item_discount" placeholder="Enter discount percent">
                                                    </div>
                                                    </div>
                                               </td>
                                               <td>
                                                    <div class="col-md-12">
                                                    <div class="form-group"><br>
                                                        <span id="add_item_btn" class="btn-default btn add_item_inpt">Add</span>
                                                    </div>
                                                    </div>
                                               </td>
                                           </tr>
                                       </thead>
                                    </table>-->
                                </div>
                                
                                <div class="box-body fl_scrollable_x_y"> 
                                    <table id="invoice_list_tbl" class="table table-bordered table-striped">
                                        <thead>
                                           <tr> 
                                               <th width="14%"  style="text-align: left;">Stone</th> 
                                               <th width="6%" style="text-align: center;">N/H</th> 
                                               <th width="10%" style="text-align: center;">Shape</th> 
                                               <th width="10%" style="text-align: center;">Color</th> 
                                               <th width="12%" style="text-align: center;">Cert</th> 
                                               <th width="8%" style="text-align: center;">Origin</th> 
                                               <th width="12%" style="text-align: center;">Carat Weight</th>   
                                               <th width="10%" style="text-align: right;">Total</th> 
                                               <th width="5%" style="text-align: center;">Action</th>
                                           </tr>
                                       </thead>
                                       <tbody>
                                           
                                       </tbody>
                                       <tfoot>
                                            <tr>
<!--                                                <th colspan="5"></th>
                                                <th  style="text-align: right;">Sub Total</th>
                                                <th  style="text-align: right;"><input hidden value="0" name="invoice_total" id="invoice_total"><span id="inv_total">0</span></th>
                                                <th  style="text-align: right;"></th>
                                            </tr>-->
                                            
                                            <tr>
                                                <th colspan="7"></th>
                                                <th  style="text-align: right;">Total</th>
                                                <th  style="text-align: right;"><input hidden value="0" name="invoice_total" id="invoice_total"><span id="inv_total">0</span></th>
                                            </tr> 
                                       </tfoot>
                                        </table>
                                </div>
                                <div id="search_result_1"></div>
                            </div>    
                        </div>
                        <div class="row" id="footer_sales_form">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="memo" class="col-sm-4 control-label">Memo</label>

                                    <div class="col-sm-8">
                                        <textarea class="form-control" name="memo"></textarea>
                                    </div>
                                  </div>
                            </div>
                            <div class="col-sm-8">
                                <button id="place_invoice" class="btn btn-app pull-right  primary"><i class="fa fa-check"></i>Place Invoice</button>
                
                            </div>
                        </div>
                    </div>
                
              </div>
    </section>      
                            
                            <?php echo form_hidden('id', $result['id']); ?>
                            <?php echo form_hidden('action',$action); ?>
                            <?php echo form_close(); ?>               
                                
                         
                            
    </div>
        <div class="col-md-12">
            <div class="box">
               <!-- /.box-header -->
               <!-- /.box-body -->
             </div>

        </div>
        
<?php $this->load->view('Purchasing_gemstones//purch_items_modals/add_supplier_model'); ?>
<?php $this->load->view('Purchasing_gemstones//purch_items_modals/add_dropdown_modal'); ?>
</div>
    
<script>
    
$(document).keypress(function(e) {
//    fl_alert('info',e.keyCode)
        if(e.keyCode == 13) {//13 for enter
            if ($(".add_item_inpt").is(":focus")) {
                    $('#add_item_btn').trigger('click');
//                fl_alert('info',)
              }
            $('#item_code').focus();
            return false;

        }
        if(e.keyCode == 10) {//submit for  ctr+ enter
            $('#place_invoice').trigger('click');
        }
    });
$(document).ready(function(){
    $('#item_category_id').focus(); 
    $('.select2').on("select2:close", function () { $(this).focus(); });
     
    get_item_dets($('#item_category_id').val());
    $("#item_category_id").on("change",function(){ 
            get_item_dets(this.id);
    });
    
    $("#place_invoice").click(function(){
            if($('input[name^="inv_items"]').length<=0){
                fl_alert('info',"Atleast one item need to create an invoice!")
                return false;
            }
            if(!confirm("Click ok confirm your invoice submission.")){
                return false;
            }
    });
     $("#supplier_id").change(function(){ 
	 $('#item_code').trigger('keyup');
    });
    
    $("#add_item_btn").click(function(){
        if($('#item_desc').val()==null || $('#item_desc').val()==""){
            fl_alert('info','Item Name Required! Please enter Item Name.');
            $('#item_desc').focus();
            return false;
        }
         $.ajax({
			url: "<?php echo site_url('Purchasing_gemstones/fl_ajax');?>",
			type: 'post',
			data : {function_name:'get_single_category', item_category_id:$('#item_category_id').val()},
			success: function(result){
                                var res2 = JSON.parse(result);
//                                $("#search_result_1").html(result);
                                
                                if(res2.id==null){
                                    fl_alert('info','Item category invalid! Please recheck before add.');
                                    return false;
                                }
                                res2.item_name = $('#item_desc').val();
                                var rowCount = $('#invoice_list_tbl tr').length;
                                var counter = rowCount+1;
                                var qtyXprice = parseFloat($('#item_unit_cost').val()) * parseFloat($('#item_quantity').val());
//                                var item_total = qtyXprice - (parseFloat($('#item_discount').val())* 0.01 * qtyXprice);
                                var item_total = qtyXprice;
                                
                                
                                var row_str = '<tr style="pad1ding:10px" id="tr_'+rowCount+'">'+ 
                                                        '<td><input hidden name="inv_items['+rowCount+'][item_desc]" value="'+res2.item_name+'"><input hidden name="inv_items['+rowCount+'][cat_id]" value="'+res2.id+'">'+res2.item_name+'</td>'+
                                                        '<td align="center"><input hidden name="inv_items['+rowCount+'][item_treatments]" value="'+$('#treatments').val()+'">'+$("#treatments option:selected" ).text()+'</td>'+
                                                        '<td align="center"><input hidden name="inv_items['+rowCount+'][shape]" value="'+$('#shape').val()+'">'+$("#shape option:selected" ).text()+'</td>'+
                                                        '<td align="center"><input hidden name="inv_items['+rowCount+'][color]" value="'+$('#color').val()+'">'+$("#color option:selected" ).text()+'</td>'+
                                                        '<td align="center"><input hidden name="inv_items['+rowCount+'][certification]" value="'+$('#certification').val()+'">'+$("#certification option:selected" ).text()+'</td>'+
                                                        '<td align="center"><input hidden name="inv_items['+rowCount+'][origin]" value="'+$('#origin').val()+'">'+$("#origin option:selected" ).text()+'</td>'+
                                                        '<td align="center"><input hidden name="inv_items['+rowCount+'][item_quantity]" value="'+$('#item_quantity').val()+'"><input hidden name="inv_items['+rowCount+'][item_quantity_2]" value="'+(($('#item_quantity_2').val()==null)?0:$('#item_quantity_2').val())+'">'+
                                                        '<input hidden name="inv_items['+rowCount+'][item_quantity_uom_id]" value="'+res2.item_uom_id+'"><input hidden name="inv_items['+rowCount+'][item_quantity_uom_id_2]" value="'+res2.item_uom_id_2+'">'+
                                                                                                                                                                                                                                                                                $('#item_quantity').val()+' '+res2.unit_abbreviation;
                                if(res2.unit_abbreviation_2!=null && res2.unit_abbreviation_2!=0){
                                    row_str = row_str + ' | ' + $('#item_quantity_2').val()+' '+res2.unit_abbreviation_2;
                                }                                                                                                                                                                                                                                                                        
                                row_str = row_str + '</td> <td align="right"><input hidden name="inv_items['+rowCount+'][item_unit_cost]" value="'+$('#item_unit_cost').val()+'">'+parseFloat($('#item_unit_cost').val()).toFixed(2)+'</td>'+ 
                                                        '<td align="right"><input class="item_tots" hidden name="inv_items['+rowCount+'][item_total]" value="'+item_total+'">'+item_total.toFixed(2)+'</td>'+
                                                        '<td width="5%"><button id="del_btn" type="button" class="del_btn_inv_row btn btn-danger"><i class="fa fa-trash"></i></button></td>'+
                                                    '</tr>';
                                var newRow = $(row_str);
                                jQuery('table#invoice_list_tbl ').append(newRow);
                                var inv_total = parseFloat($('#invoice_total').val()) + item_total;
                                $('#invoice_total').val(inv_total.toFixed(2));
                                $('#inv_total').text(inv_total.toFixed(2));
                                $('#item_desc').focus();
                                //delete row
                                $('.del_btn_inv_row').click(function(){
//                                    if(!confirm("click ok Confirm remove this item.")){
//                                        return false;
//                                    }
                                    var tot_amt = 0;
                                    $(this).closest('tr').remove(); 
                                    $('input[class^="item_tots"]').each(function() {
//                                        console.log(this);
                                        tot_amt = tot_amt + parseFloat($(this).val());
                                    });
                                    $('#invoice_total').val(tot_amt.toFixed(2));
                                    $('#inv_total').text(tot_amt.toFixed(2)); 
                                });
                        }
		});

        
        
    });
});

	function get_item_dets(id1=''){ //id1 for input element id [cat_id]
            $.ajax({
			url: "<?php echo site_url('Purchasing_gemstones/fl_ajax');?>",
			type: 'post',
			data : {function_name:'get_single_category', item_category_id:$('#item_category_id').val()},
			success: function(result){
                            
//                            $("#search_result_1").html(result);
//                            return false;
                            var res1 = JSON.parse(result);
                            
//                             $('#first_col_form').removeClass('col-md-offset-1');
                            var div_str = '<div class="col-md-2">'+
                                                    '<div class="form-group pad1">'+
                                                        '<label for="item_quantity"> <span id="unit_abbr">[Each]<span></label>'+
                                                        '<input type="text" name="item_quantity" class="form-control add_item_inpt" id="item_quantity" placeholder="Enter Quantity">'+
                                                    '</div>'+
                                                '</div>';
                            if(res1.item_uom_id_2!=0){
                                    div_str = div_str + '<div class="col-md-2">'+
                                                            '<div class="form-group pad1">'+
                                                                '<label for="item_quantity_2"> <span id="unit_abbr_2">[Each]<span></label>'+
                                                                '<input type="text" name="item_quantity_2" class="form-control add_item_inpt" value="1" id="item_quantity_2" placeholder="Enter Quantity">'+
                                                            '</div>'+
                                                        '</div>';
                                    
                            }else{
//                                $('#first_col_form').addClass('col-md-offset-1')
                            }
                            $('#uom_div').html(div_str);
                            
                            if(typeof(res1.id) != "undefined" && res1.id !== null) { 
                                if(id1!='item_desc'){$('#item_desc').val(res1.item_code).trigger('change');}
                                if(id1!='item_code'){ $('#item_code').val(res1.item_code);}
                                (res1.price_amount==null)? $('#item_unit_cost').val(0):$('#item_unit_cost').val(res1.price_amount);
                                $('#unit_abbr').text('Weight '+res1.unit_abbreviation);
                                $('#unit_abbr_2').text(res1.unit_abbreviation_2);
//                                $('#item_discount').val(0);
                                $('#item_quantity').val(1);
                                $('#item_desc').val(res1.category_name);

                                $("#result_search").html(result);
                            }
                        }
		});
	}
</script>
 