<?php 
class ModelExtensionPaymentAlipayDirect extends Model {
  	public function getMethod($address, $total) {
		$this->load->language('extension/payment/alipay_direct');
		
		if ($this->config->get('alipay_direct_total') > 0 && $this->config->get('alipay_direct_total') > $total) {
			$status = false;
		} else {
			$status = true;
		}
		
		$method_data = array();
	
		if ($status) {  
      		$method_data = array( 
        		'code'       => 'alipay_direct',
        		'title'      => $this->language->get('text_title'),
        		'terms'      => '',
						'sort_order' => $this->config->get('alipay_direct_sort_order')
      		);
    	}
	
    	return $method_data;
  	}
  	
//sunboy add
	public function getOrderProduct($order_id) {
		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_product` WHERE order_id = '" . (int)$order_id . "'");
    //return $query->row;
		if ($order_query->num_rows) {			

			return array(
				'order_id'                => $order_query->row['order_id'],
				'product_id'                => $order_query->row['product_id'],
				'product_name'                => $order_query->row['name'],
				'product_model'               => $order_query->row['model'],
				'product_quantity'           => $order_query->row['quantity'],
				'product_total'                => $order_query->row['total']				
			);
		} else {
			$voucher_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_voucher` WHERE order_id = '" . (int)$order_id . "'");
		
			if ($voucher_query->num_rows) {			
				return array(
					'product_name'          =>$voucher_query->row['description'],
					'product_quantity'      => 1
				);
			} else {
				$recharge_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_recharge` WHERE order_id = '" . (int)$order_id . "'");
				return array(
					'product_name'          =>$recharge_query->row['description'],
					'product_quantity'      => 1
				);
			}
		}
		
	}
}