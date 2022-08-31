<?php
class ControllerExtensionPaymentTypePayment extends Controller {
	private $error = array(); 

	public function index() {   
		$this->load->language('extension/payment/typepayment');;

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting'); 

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('payment_typepayment', $this->request->post);		

			$this->session->data['success'] = $this->language->get('text_success');
			$data['success'] = $this->session->data['success'];
			$this->response->redirect($this->url->link('extension/payment/typepayment', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');		
        $data['text_edit'] = $this->language->get('text_edit');
		
		$data['entry_order_status'] = $this->language->get('entry_order_status');
		$data['text_browse'] = $this->language->get('text_browse');
		$data['text_clear'] = $this->language->get('text_clear');
		$data['text_image_manager'] = $this->language->get('text_image_manager');
	    $data['entry_file'] = $this->language->get('entry_file');
	    $data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
        $data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_description_full'] = $this->language->get('entry_description_full');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_min_price'] = $this->language->get('entry_min_price');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_status_mod'] = $this->language->get('entry_status_mod');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_add_module'] = $this->language->get('button_add_module');
		$data['button_remove'] = $this->language->get('button_remove');
		$data['help_total'] = $this->language->get('help_total');

		$data['tab_module'] = $this->language->get('tab_module');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		
  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_extension'),
			'href'      => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] , true)
   		);
		
   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/payment/typepayment', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
   		);
		
		$data['action'] = $this->url->link('extension/payment/typepayment', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], true);
		
		$data['modules'] = array();
		
		if (isset($this->request->post['payment_typepayment'])) {
			$data['modules'] = $this->request->post['payment_typepayment'];
		} elseif ($this->config->get('payment_typepayment')) {
			$data['modules'] = $this->config->get('payment_typepayment');
		}
		
		if (isset($this->request->post['payment_typepayment_status'])) {
			$data['payment_typepayment_status'] = $this->request->post['payment_typepayment_status'];
		} elseif ($this->config->get('payment_typepayment_status')) {
			$data['payment_typepayment_status'] = $this->config->get('payment_typepayment_status');
		}else{
			$data['payment_typepayment_status']=1;
		}

		$data['user_token'] = $this->session->data['user_token'];
		$this->load->model('tool/image');
		$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		$data['no_image'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		foreach ($data['modules'] as $key => $module) {
            if (isset($module['image']) and $module['image'] && file_exists(DIR_IMAGE .  $module['image'])) {
                $thumb = $this->model_tool_image->resize($module['image'], 100, 100);
            }else{
                $thumb = $this->model_tool_image->resize('no_image.png', 100, 100);
            }
            $data['modules'][$key]['thumb'] = $thumb;
        }
		
		$this->load->model('localisation/order_status');
		
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		
		$this->load->model('localisation/tax_class');
		$this->load->model('localisation/geo_zone');
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
				
		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();
		
		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/typepayment', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/typepayment')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
				
		foreach ($this->request->post['payment_typepayment'] as $value) {
			if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 32)) {
				$this->error['name'] = '';
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
?>