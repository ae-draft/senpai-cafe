<?php

error_reporting(0);

class Shop_Payment_System_Handler21 extends Shop_Payment_System_Handler
{
    private $_mid = 0; // код торговцы
    private $_pid = 0; // код продукта
    private $_sw = ''; // секретный код
    private $_acquiropay_currency = 1;

    public function execute()
    {
        parent::execute();
        $this->printNotification();
        return $this;
    }

    protected function _processOrder()
    {
        parent::_processOrder();
        $this->setXSLs();
        $this->send();
        return $this;
    }

    public function getSumWithCoeff()
    {
        return Shop_Controller::instance()->round(($this->_acquiropay_currency > 0 && $this->_shopOrder->shop_currency_id > 0 ? Shop_Controller::instance()->getCurrencyCoefficientInShopCurrency($this->_shopOrder->Shop_Currency, Core_Entity::factory('Shop_Currency', $this->_acquiropay_currency)) : 0) * $this->_shopOrder->getAmount());
    }

    public function paymentProcessing()
    {
        $this->ProcessResult();
        return TRUE;
    }

    public function ProcessResult()
    {
        $payment_id = $_POST['payment_id'];
        $merchant_id = $_POST['merchant_id'];
        $status = $_POST['status'];
        $cf = $_POST['cf'];
        $cf2 = $_POST['cf2'];
        $cf3 = $_POST['cf3'];
        $product_name = "Оплата заказа № $cf";
        $cardholder = $_POST['cardholder'];
        $amount = $_POST['amount'];
        $email = $_POST['email'];
        $datetime = $_POST['datetime'];
        $sw = $this->_sw;
        $str_md5 = md5($merchant_id . $payment_id . $status . $cf . $cf2 . $cf3 . $sw);
        $sign2 = $_POST['sign'];
        $tmp = print_r($_REQUEST, 1);
        if ($str_md5 == $_POST['sign']) {
            $this->_shopOrder->system_information = sprintf("Товар оплачен через
Acquiropay.\n\nИнформация:\n\nID магазина: %sНомер покупки (СКО):
%s\nНомер счета магазина: %s\nНазначение платежа: %s\nСумма платежа:
%s\nИмя пользователя: %s\nE-mail пользователя:
%s\nВремя выполнения платежа: %s\n", $pid, $payment_id, $merchant_id, $product_name, $amount, $cardholder, $email, $datetime);
            $this->_shopOrder->paid();
            $this->setXSLs();
            $this->send();
        }
    }

    public function getNotification()
    {
        $order_sum = $this->getSumWithCoeff();
        $oShop_Currency = Core_Entity::factory('Shop_Currency')->find($this->_acquiropay_currency);
        $oSite_Alias = $this->_shopOrder->Shop->Site->getCurrentAlias();
        $site_alias = !is_null($oSite_Alias) ? $oSite_Alias->name : '';
        $shop_path = $this->_shopOrder->Shop->Structure->getPath();
        $handler_url = 'http://' . $site_alias . $shop_path . 'cart/';
        $mid = $this->_mid;
        $pid = $this->_pid;
        $sw = $this->_sw;
        $cf = $this->_shopOrder->id;
        $token = md5($mid . $pid . $order_sum . $cf . $sw);
        if (true) {
            ?>
            <h1>Оплата через систему Acquiropay</h1>
            <p>К оплате <strong><?php echo $order_sum . " " . $oShop_Currency->name ?></strong></p>
            <form id="pay" action="https://secure.acquiropay.com" name="pay" method="post">
                <input type='hidden' name='product_id' value='<?php echo $pid ?>'/>
                <input type='hidden' name='product_name' value='<?php echo "Оплата заказа № $cf" ?>'/>
                <input type='hidden' name='token' value='<?php echo $token ?>'/>
                <input type='hidden' name='amount' value='<?php echo $order_sum ?>'/>
                <input type='hidden' name='cf' value='<?php echo $cf ?>'>
                <input type='hidden' name='cf2' value=''/>
                <input type='hidden' name='cf3' value=''/>
                <input type='hidden' name='first_name' value='<?php echo $this->_shopOrder->name ?>'/>
                <input type='hidden' name='last_name' value='<?php echo $this->_shopOrder->surname ?>'/>
                <input type='hidden' name='middle_name' value='<?php echo $this->_shopOrder->patronymic ?>'/>
                <input type='hidden' name='email' value='<?php echo $this->_shopOrder->email ?>'/>
                <input type='hidden' name='phone' value=''/>
                <input type='hidden' name='country' value=''/>
                <input type='hidden' name='city' value=''/>
                <input type='hidden' name='cb_url'
                       value='<?php echo $handler_url . "?orderId={$this->_shopOrder->invoice}&payment=result" ?>'/>
                <input type='hidden' name='ok_url'
                       value='<?php echo $handler_url . "?orderId={$this->_shopOrder->invoice}&payment=success" ?>'/>
                <input type='hidden' name='ko_url'
                       value='<?php echo $handler_url . "?orderId={$this->_shopOrder->invoice}&payment=fail" ?>'/>
                <input name="submit" value="Перейти к оплате" type="submit"/>
            </form>
        <?php }
    }

    public function getInvoice()
    {
        return $this->getNotification();
    }
}