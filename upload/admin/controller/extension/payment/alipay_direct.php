<?php
class ControllerExtensionPaymentAlipayDirect extends Controller {
	private $error = array();

	public function index() {
		// 加载语言数据
		$this->load->language('extension/payment/alipay_direct');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		// 处理提交的表单
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			//$this->load->model('setting/setting');

			$this->model_setting_setting->editSetting('alipay_direct', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			//$this->redirect(HTTPS_SERVER . 'index.php?route=extension/payment');
			//$this->redirect(HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token']);
			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL'));
		}

		// 存储模板需要的数据
		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');

		$data['entry_seller_email'] = $this->language->get('entry_seller_email');
		$data['entry_security_code'] = $this->language->get('entry_security_code');
		$data['entry_partner'] = $this->language->get('entry_partner');
		$data['entry_currency_code'] = $this->language->get('entry_currency_code');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$data['entry_trade_finished'] = $this->language->get('entry_trade_finished');
		$data['entry_total'] = $this->language->get('entry_total');
		$data['help_total'] = $this->language->get('help_total');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['tab_general'] = $this->language->get('tab_general');

 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

 		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		if (isset($this->error['secrity_code'])) {
			$data['error_secrity_code'] = $this->error['secrity_code'];
		} else {
			$data['error_secrity_code'] = '';
		}

		if (isset($this->error['currency_code'])) {
			$data['error_currency_code'] = $this->error['currency_code'];
		} else {
			$data['error_currency_code'] = '';
		}

		if (isset($this->error['partner'])) {
			$data['error_partner'] = $this->error['partner'];
		} else {
			$data['error_partner'] = '';
		}

		// 后台层级菜单（如：Home:Payment...）
		// 后台层级菜单（如：Home:Payment...）
		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_payment'),
			'href'      => $this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/payment/alipay_direct', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

		$data['action'] = HTTPS_SERVER . 'index.php?route=extension/payment/alipay_direct&token=' . $this->session->data['token'];

		$data['cancel'] = HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token'];

		// 设置表单的值
		if (isset($this->request->post['alipay_direct_total'])) {
			$data['alipay_direct_total'] = $this->request->post['alipay_direct_total'];
		} else {
			$data['alipay_direct_total'] = $this->config->get('alipay_direct_total');
		}

		if (isset($this->request->post['alipay_direct_seller_email'])) {
			$data['alipay_direct_seller_email'] = $this->request->post['alipay_direct_seller_email'];
		} else {
			$data['alipay_direct_seller_email'] = $this->config->get('alipay_direct_seller_email');
		}

		if (isset($this->request->post['alipay_direct_security_code'])) {
			$data['alipay_direct_security_code'] = $this->request->post['alipay_direct_security_code'];
		} else {
			$data['alipay_direct_security_code'] = $this->config->get('alipay_direct_security_code');
		}

		if (isset($this->request->post['alipay_direct_partner'])) {
			$data['alipay_direct_partner'] = $this->request->post['alipay_direct_partner'];
		} else {
			$data['alipay_direct_partner'] = $this->config->get('alipay_direct_partner');
		}

		if (isset($this->request->post['alipay_direct_currency_code'])) {
			$data['alipay_direct_currency_code'] = $this->request->post['alipay_direct_currency_code'];
		} else {
			$data['alipay_direct_currency_code'] = $this->config->get('alipay_direct_currency_code');
		}

		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$this->load->model('localisation/currency');
		$data['currencies'] = $this->model_localisation_currency->getCurrencies();

		// 保留
		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['alipay_direct_status'])) {
			$data['alipay_direct_status'] = $this->request->post['alipay_direct_status'];
		} else {
			$data['alipay_direct_status'] = $this->config->get('alipay_direct_status');
		}

		if (isset($this->request->post['alipay_direct_trade_finished'])) {
			$data['alipay_direct_trade_finished'] = $this->request->post['alipay_direct_trade_finished'];
		} else {
			$data['alipay_direct_trade_finished'] = $this->config->get('alipay_direct_trade_finished');
		}

		if (isset($this->request->post['alipay_direct_sort_order'])) {
			$data['alipay_direct_sort_order'] = $this->request->post['alipay_direct_sort_order'];
		} else {
			$data['alipay_direct_sort_order'] = $this->config->get('alipay_direct_sort_order');
		}

		$this->template = 'extension/payment/alipay_direct.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/alipay_direct.tpl', $data));


	}

	// 验证
	private function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/alipay_direct')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['alipay_direct_seller_email']) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if (!$this->request->post['alipay_direct_security_code']) {
			$this->error['secrity_code'] = $this->language->get('error_secrity_code');
		}

		if (!$this->request->post['alipay_direct_partner']) {
			$this->error['partner'] = $this->language->get('error_partner');
		}

		if (!$this->request->post['alipay_direct_currency_code']) {
			$this->error['currency_code'] = $this->language->get('error_currency_code');
		}

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
?>