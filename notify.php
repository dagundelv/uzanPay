<?php
/**
 * 接收回调通知
 * 例如你将该文件放到网站根目录，那么在有赞云控制台的“消息推送配置”中填写推送网址为：http://你的网址/notify.php
 */
/*配置开始*/
$client_id = 'xxx';//应用的 client_id
$client_secret = 'xxx';//应用的 client_secret
$kdt_id = 'xxx';	//授权店铺id
/*配置结束*/

$json = file_get_contents('php://input');
$data = json_decode($json, true);
if(!is_array($data)) $data = $_POST;
if(empty($data)) $data = $_GET;
//判断消息是否合法，若合法则返回成功标识
$msg = $data['msg'];
$sign_string = $client_id."".$msg."".$client_secret;
$sign = md5($sign_string);
if($sign != $data['sign']){
    exit('sign error');
}else{
    if($data['id']){
        //查询订单
        $queryData['id'] = $data['id'];
        $queryData['client_id'] = $client_id;
        $queryData['client_secret'] = $client_secret;
        $queryData['kdt_id'] = $kdt_id;

        $result = curlPost("https://pay.dedemao.com/query.php",$queryData);
    }
	if($data['tid'] && $data['trade_status']=='TRADE_SUCCESS'){
		//支付成功，处理你的逻辑
		//订单号：$data['out_trade_no']
		//支付金额：$data['total_fee']
		//支付类型：$data['pay_type']
		//支付时间：$data['pay_at']
		//支付者信息：$data['buyer_info']
		//error_log('pay success',3,'1.txt');
		//error_log(print_r($data,true),3,'1.txt');

	}
    $result = array("code"=>0,"msg"=>"success") ;
    echo json_encode($result);exit();
}

function curlPost($url = '', $postData = '', $options = array())
{
    if (is_array($postData)) {
        $postData = http_build_query($postData);
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); //设置cURL允许执行的最长秒数
    if (!empty($options)) {
        curl_setopt_array($ch, $options);
    }
    //https请求 不验证证书和host
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}
