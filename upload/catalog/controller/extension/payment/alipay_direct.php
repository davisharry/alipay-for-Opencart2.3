<?php
// Version By www.opencart.cn
/*
版权所有：www.opencart.cn
这不是一个免费的版本，购买者可以修改调试，严禁传播复制。
自己修改或者传播带来的一切后果自负。
*/
require_once("alipay_submit.class.php");
require_once("alipay_notify.class.php");

class ControllerExtensionPaymentAlipayDirect extends Controller {
 public function index() {
		// 为 alipay.tpl 准备数据
    $data['button_confirm'] = $this->language->get('button_confirm');
		$data['button_back'] = $this->language->get('button_back');

		// url

		$data['return'] = HTTPS_SERVER . 'index.php?route=checkout/success';
		
		if ($this->request->get['route'] != 'checkout/guest_step_3') {
			$data['cancel_return'] = HTTPS_SERVER . 'index.php?route=checkout/payment';
		} else {
			$data['cancel_return'] = HTTPS_SERVER . 'index.php?route=checkout/guest_step_2';
		}
		
		$encryption = new Encryption($this->config->get('config_encryption'));
		
		$data['custom'] = $encryption->encrypt($this->session->data['order_id']);
		
		if ($this->request->get['route'] != 'checkout/guest_step_3') {
			$data['back'] = HTTPS_SERVER . 'index.php?route=checkout/payment';
		} else {
			$data['back'] = HTTPS_SERVER . 'index.php?route=checkout/guest_step_2';
		}

		// 获取订单数据
		$this->load->model('checkout/order');
		$this->load->model('extension/payment/alipay_direct');

		$order_id = $this->session->data['order_id'];		
		$order_info = $this->model_checkout_order->getOrder($order_id);
	  $order_product_info = $this->model_extension_payment_alipay_direct->getOrderProduct($order_id);

		// 计算提交地址
		//商户信息
					//↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
					
					//合作身份者id，以2088开头的16位纯数字							
			
		  			$alipay_config['partner']		= trim($this->config->get('alipay_direct_partner'));				//合作伙伴ID					
					//安全检验码，以数字和字母组成的32位字符
			
					  $alipay_config['key']			= trim($this->config->get('alipay_direct_security_code'));	//安全检验码key					
					
					//↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
					
					
					//签名方式 不需修改
					$alipay_config['sign_type']    = strtoupper('MD5');
					
					//字符编码格式 目前支持 gbk 或 utf-8
					$alipay_config['input_charset']= strtolower('utf-8');
					
					//ca证书路径地址，用于curl中ssl校验
					//请保证cacert.pem文件在当前文件夹目录中
					$alipay_config['cacert']    = getcwd().'\\cacert.pem';
					
					//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
					$alipay_config['transport']    = 'http';
	//获取环境				
    		$currency_code = $this->config->get('alipay_direct_currency_code');				//人民币代号（CNY）
				$item_name = $this->config->get('config_store');
				$first_name = $order_info['payment_firstname'];	
    		
				$total = $order_info['total'];
				if($currency_code == ''){
					$currency_code = 'CNY';
				}
				$currency_value = $this->currency->getValue($currency_code);
				$amount = $total * $currency_value;
				$amount = number_format($amount,2,'.','');
				
							
		//支付类型
        $payment_type = "1";
        //必填，不能修改
        //服务器异步通知页面路径
        $notify_url     = HTTP_SERVER . 'callback/alipay_direct_notify_url.php';

        //需http://格式的完整路径，不能加?id=123这类自定义参数        //页面跳转同步通知页面路径
       //$return_url		= HTTPS_SERVER . 'index.php?route=checkout/success';
       $return_url = $this->url->link('checkout/success');                //成功后返回页面
       // $return_url = "http://192.168.1.6/test2.0/alipay/return_url.php";
        //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/        //卖家支付宝帐户
        $seller_email = $this->config->get('alipay_direct_seller_email');		// 商家邮箱
        //$seller_email = "scsunboy@sina.com";
        //必填        //商户订单号
        //$out_trade_no = date("Ymd").$order_id;
        $out_trade_no = $order_id;
        //$out_trade_no = "10";
        //商户网站订单系统中唯一订单号，必填        
        
        //商品名称
        //$subject = $_POST['WIDsubject'];
        $subject = $order_product_info['product_name'] ; 
                
        //必填        //付款金额
        //$total_fee = $_POST['WIDtotal_fee'];
        $total_fee = $amount;

        //必填        //订单描述        
        //$body = $_POST['WIDbody'];
        $body = '商品名称:' . $order_product_info['product_name'] . '-----X商品数量:' . $order_product_info['product_quantity']  ;
        //商品展示地址
        //$show_url = $_POST['WIDshow_url'];
         $show_url       = HTTPS_SERVER . 'index.php';
        //需以http://开头的完整路径，例如：http://www.商户网址.com/myorder.html        //防钓鱼时间戳
        $anti_phishing_key = "";
        //若要使用请调用类文件submit中的query_timestamp函数        //客户端的IP地址
        $exter_invoke_ip = "";
        //非局域网的外网IP地址，如：221.0.0.1
        
	$parameter = array(
		"service" => "create_direct_pay_by_user",
		"partner" => trim($alipay_config['partner']),
		"payment_type"	=> $payment_type,
		"notify_url"	=> $notify_url,
		"return_url"	=> $return_url,
		"seller_email"	=> $seller_email,
		"out_trade_no"	=> $out_trade_no,
		"subject"	=> $subject,
		"total_fee"	=> $total_fee,
		"body"	=> $body,
		"show_url"	=> $show_url,
		"anti_phishing_key"	=> $anti_phishing_key,
		"exter_invoke_ip"	=> $exter_invoke_ip,
		"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
   );

			
	//建立请求
		$alipaySubmit = new AlipaySubmit($alipay_config);
    $html_text = $alipaySubmit->buildRequestForm($parameter,"get");
	
		$action=$html_text;
		$data['action'] = $action;
		$this->id = 'payment';

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/payment/alipay_direct.tpl')) {

			return $this->load->view($this->config->get('config_template') . '/template/extension/payment/alipay_direct.tpl', $data);
		} else {
			return $this->load->view('/extension/payment/alipay_direct.tpl', $data);
		}	
		
		
	}

	
	// 支付返回后的处理,支付宝异步通知
	public function callback() {
	logResult("进入 public function callback()");
	
	//$oder_success = FALSE;      //sunboy 无用信息

		// 获取商家信息
    //商户信息
					//↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
		  			$alipay_config['partner']		= trim($this->config->get('alipay_direct_partner'));				//合作伙伴ID

					  $alipay_config['key']			= trim($this->config->get('alipay_direct_security_code'));	//安全检验码key
					
						$alipay_config['sign_type']    = strtoupper('MD5'); 	//签名方式 不需修改
										
						$alipay_config['input_charset']= strtolower('utf-8'); //字符编码格式 目前支持 gbk 或 utf-8
					 //ca证书路径地址，用于curl中ssl校验
					//请保证cacert.pem文件在当前文件夹目录中
					 $alipay_config['cacert']    = getcwd().'\\cacert.pem';					
					//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
					 $alipay_config['transport']    = 'http';

		//计算得出通知验证结果
		logResult("==开始认证====");
		$alipayNotify = new AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyNotify();
		
		logResult("==认证完成====".$verify_result);
		
		
		if($verify_result) {   //认证合格
			
			logResult("==认证合格====");

		 //获取支付宝的反馈参数
			$order_id   = $_POST['out_trade_no'];   //获取支付宝传递过来的订单号
			
			logResult("==支付宝反馈参数，外部 out_trade_no====".$order_id);

			$this->load->model('checkout/order');
			
			logResult("==认证合格==1111111111111==");
			
			// 获取订单ID
			$order_info = $this->model_checkout_order->getOrder($order_id);
			
			logResult("==认证合格==22222222222222==");
		
			// 存储订单至系统数据库
			if ($order_info) {
				
				logResult("==认证合格==3333333333333==");
				$order_status_id = $order_info["order_status_id"];

				$alipay_direct_order_status_id = $this->config->get('alipay_direct_order_status_id');
				$alipay_direct_wait_buyer_pay = $this->config->get('alipay_direct_wait_buyer_pay');
				$alipay_direct_wait_buyer_confirm = $this->config->get('alipay_direct_wait_buyer_confirm');
				$alipay_direct_trade_finished = $this->config->get('alipay_direct_trade_finished');
				$alipay_direct_wait_seller_send = $this->config->get('alipay_direct_wait_seller_send');
       
        logResult("==认证合格==44444444444==");
        
				
				// 避免处理已完成的订单,判断订单状态是否已经处理过。
				logResult('order_id=' . $order_id . ' order_status_id=' . $order_status_id);
				
				logResult("==认证合格==66666666666==");
				if ($order_status_id != $alipay_direct_trade_finished) {
					logResult("No finished.");						
					
					logResult("==认证合格==777777777777==");
					
					 if($_POST['trade_status'] == 'TRADE_SUCCESS') {    //交易成功结束
					

						logResult("==认证合格==88888888888==");
						$this->model_checkout_order->addOrderHistory($order_id, $alipay_direct_trade_finished);
						
						logResult("==认证合格==99999999==");

							echo "success";		//请不要修改或删除
						
						//调试用，写文本函数记录程序运行情况是否正常
						logResult('success - alipay_direct_trade_finished');						
				    }										
					else {
						logResult("==认证合格==000000==");
						echo "fail";
						logResult ("verify_failed");
					}
				}
			}
		}
	}
}