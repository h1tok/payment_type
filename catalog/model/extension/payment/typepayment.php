<?php
class ModelExtensionPaymentTypePayment extends Model {
	function getMethod($address, $total) {
		
		$this->language->load('extension/payment/typepayment');

		$typepayments = $this->config->get('payment_typepayment');

		$method_data = array();
		$quote_data = array();
		$sort_order = array();
		
		$cart_total = $this->cart->getTotal();
		$this->load->model('tool/image');

		foreach($typepayments as $i => $payment) {
				
			if(!$payment['status']) {
				continue;
			}
					
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$payment['geo_zone_id'] . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

			if ($payment['geo_zone_id'] == 0) {
				$status = true;
			} elseif ($query->num_rows) {
				$status = true;
			} else {
				$status = false;
			}
			
			$min_price = $payment['min_price'];
			
			if($status && ($total > $min_price)){
				if( isset($payment['image']) and $payment['image'] && file_exists(DIR_IMAGE .  $payment['image'])) {
					$thumb = $this->model_tool_image->resize($payment['image'], 40, 30);
				}else {
					$thumb = '';
				}

				$typepayments[$i]['thumb'] = $thumb;		

				$description = $payment['description'];
				$name = $payment['name'];
				
				$quote_data[$i] = array(
					'code' 			=>	'typepayment.and.' . $i,
					'title' 		=>	$name,
					'sort_order'    =>  isset($payment['sort_order']) ? $payment['sort_order'] : ($payment['sort_order']  + 100),
					'terms'         =>  $description,
					'description'	=>  $payment['description_full'],
					'img'			=>	$thumb,
				);
				
				if (isset($quote_data) and count($quote_data) > 0) {
					$sort_by = array();
					foreach ($quote_data as $key => $value) $sort_by[$key] = $value['sort_order'];
					array_multisort($sort_by, SORT_ASC, $quote_data);
				}
			}
		}
		
		$method_data = array(
			'code' 			=> 'typepayment',
			'title' 		=> $this->language->get('text_title'),
			'quote' 		=> $quote_data,
			'sort_order' 	=> 0,
		);

		return $method_data;
	}
}
?>