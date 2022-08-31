<?php

class ControllerExtensionPaymentTypePayment extends Controller {
	public function index() {
		$this->load->language('extension/payment/typepayment');
		$data['text_instruction'] = $this->language->get('text_instruction');
		$data['text_loading'] = $this->language->get('text_loading');

		$data['button_confirm'] = $this->language->get('button_confirm');
		$data['continue'] = $this->url->link('checkout/success');
		$data['name'] = 'payment_typepayment';
		$payment_code = explode('.and.', $this->session->data['payment_method']['code']);
		
		foreach($this->config->get('payment_typepayment') as $i => $payment){
			if($i == $payment_code[1]){
				$data['description_full'] = $payment['description_full'];
			}
		}
		
		return $this->load->view('extension/payment/typepayment', $data);
	}

	public function confirm() {
		$json = array();
		
		$payment_code = explode('.and.', $this->session->data['payment_method']['code']);
		if ($payment_code[0] == 'typepayment') {
			$this->load->language('extension/payment/typepayment');
			$this->load->model('checkout/order');
			$comment  = $this->language->get('text_instruction') . "\n";
			foreach($this->config->get('payment_typepayment') as $i => $payment){
				if($i == $payment_code[1]){
					$data['description_full'] = $payment['description_full'];
					$data['order_status_id'] = $payment['order_status_id'];
				}
			}
			
			$comment .= $data['description_full'] . "\n";

			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $data['order_status_id'], $comment, true);
			
			$json['redirect'] = $this->url->link('checkout/success');
		}
		
		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(json_encode($json));	
	}
}