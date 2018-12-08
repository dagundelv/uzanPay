<?php
/**
 * 发起支付
 */
error_reporting(1);
/*配置开始*/
$client_id = 'xxx';        //应用的 client_id
$client_secret = 'xxx';    //应用的 client_secret
$kdt_id = 'xxx';	        //授权店铺id
$pay_subject = '支付测试';      //订单标题
$return_url = 'http://www.xxx.com/notify.php';       //回调地址，请先下载notify.php
$total_fee = 0.01;        //支付金额
$out_trade_no = uniqid();       //订单号
$pay_tip = '';       //可省略。显示在支付二维码下方的提示信息。
/*配置结束*/
$uzan = new UzanService($client_id,$client_secret,$kdt_id,$pay_subject,$return_url,$total_fee,$out_trade_no);
$uzan->setPayTip($pay_tip);
echo $uzan->doPay();exit();
class UzanService
{
    protected $clientId;
    protected $clientSecret;
    protected $kdtId;
    protected $paySubject;
    protected $returnUrl;
    protected $totalFee;
    protected $outTradeNo;
    protected $payTip;
    protected $isMobile;
    protected $uzanUser;
    protected $uzanPass;
    protected $ver;
    protected $charset;

    public function __construct($clientId,$clientSecret,$kdtId,$paySubject,$returnUrl,$totalFee,$outTradeNo)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->kdtId = $kdtId;
        $this->paySubject = $paySubject;
        $this->returnUrl = $returnUrl;
        $this->totalFee = $totalFee;
        $this->outTradeNo = $outTradeNo;
        $this->charset = 'utf-8';
    }

    public function setPayTip($payTip)
    {
        $this->payTip = $payTip;
    }

    public function setIsMobile($isMobile)
    {
        $this->isMobile = $isMobile;
    }

    public function setUzanUser($uzanUser)
    {
        $this->uzanUser = $uzanUser;
    }

    public function setUzanPass($uzanPass)
    {
        $this->uzanPass= $uzanPass;
    }

    public function setVer($ver)
    {
        $this->ver= $ver;
    }

    /**
     * 发起订单
     * @return array
     */
    public function doPay()
    {
        $requestConfigs = array(
            'client_id'=>$this->clientId,
            'client_secret'=>$this->clientSecret,
            'kdt_id'=>$this->kdtId,
            'subject' => $this->paySubject,
            'return_url'=>$this->returnUrl,
            'total_fee'=>$this->totalFee,
            'out_trade_no'=>$this->outTradeNo,
            'pay_tip'=>$this->payTip,
            'is_mobile'=>$this->isMobile,
            'uzan_user'=>$this->uzanUser,
            'uzan_pass'=>$this->uzanPass,
            'charset'=>$this->charset,
            'ver'=>$this->ver,
        );
        return $this->buildRequestForm($requestConfigs);
    }

    /**
     * 建立请求，以表单HTML形式构造（默认）
     * @param $para_temp 请求参数数组
     * @return 提交表单HTML文本
     */
    function buildRequestForm($para_temp) {
        //待请求参数数组
        $para = $para_temp;
        $sHtml = "<form id='uzansubmit' name='uzansubmit' action='https://pay.dedemao.com/api.php' method='post'>";
        while (list ($key, $val) = each ($para)) {
            $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
        }
        //submit按钮控件请不要含有name属性
        $sHtml = $sHtml."<input type='submit'  value='提交' style='display:none;'></form>";

        $sHtml = $sHtml."<script>document.forms['uzansubmit'].submit();</script>";

        return $sHtml;
    }
}
