<?php
class ControllerMailOrder extends Controller {
      
   public function index($order_info){
      $order_id = $order_info['order_id'];
      
      // Send out order confirmation mail
      $language = new Language($order_info['language_directory'], $this->plugin_handler);
      $language->load($order_info['language_filename']);
      $language->load('mail/order');
    
      $subject = $language->format('text_subject', html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'), $order_id);
      
      $language->format('text_greeting', html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
      
      $this->data += $language->data;
      
      //order information
      $this->data['order_id'] = $order_info['order_id'];
      $this->data['order_status'] = $order_info['order_status'];
      $this->data['comment'] = $order_info['comment'];
      $this->data['notify_comment'] = $order_info['notify_comment'];
      
      //store information
      $this->data['logo'] = $this->image->get($this->config->get('config_logo'));     
      $this->data['store_name'] = $order_info['store_name'];
      $this->data['store_url'] = $order_info['store_url'];
      $this->data['title'] = $order_info['store_name'];
      $this->data['link'] = $order_info['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_id;
      
      //customer information
      $this->data['customer_id'] = $order_info['customer_id'];
      $this->data['date_added'] = $this->tool->format_date($order_info['date_added'], $language->getInfo('date_format_short'));       
      $this->data['payment_method'] = $order_info['payment_method'];
      $this->data['shipping_method'] = $order_info['shipping_method'];
      $this->data['email'] = $order_info['email'];
      $this->data['telephone'] = $order_info['telephone'];
      $this->data['ip'] = $order_info['ip'];
      
      //shipping address
      if ($order_info['shipping_address_format']) {
         $format = $order_info['shipping_address_format'];
      } else {
         $format = $this->config->get('config_address_format');
      }
      
      $insertables = array(
         'firstname' => $order_info['shipping_firstname'],
         'lastname'  => $order_info['shipping_lastname'],
         'company'   => $order_info['shipping_company'],
         'address_1' => $order_info['shipping_address_1'],
         'address_2' => $order_info['shipping_address_2'],
         'city'      => $order_info['shipping_city'],
         'postcode'  => $order_info['shipping_postcode'],
         'zone'      => $order_info['shipping_zone'],
         'zone_code' => $order_info['shipping_zone_code'],
         'country'   => $order_info['shipping_country'],
      );
   
      $this->data['shipping_address'] = $this->tool->insertables($insertables, $format, '{', '}');
      
      
      //payment address
      if ($order_info['payment_address_format']) {
         $format = $order_info['payment_address_format'];
      } else {
         $format = $this->config->get('config_address_format');
      }
      
      $insertables = array(
         'firstname' => $order_info['payment_firstname'],
         'lastname'  => $order_info['payment_lastname'],
         'company'   => $order_info['payment_company'],
         'address_1' => $order_info['payment_address_1'],
         'address_2' => $order_info['payment_address_2'],
         'city'      => $order_info['payment_city'],
         'postcode'  => $order_info['payment_postcode'],
         'zone'      => $order_info['payment_zone'],
         'zone_code' => $order_info['payment_zone_code'],
         'country'   => $order_info['payment_country'],
      );
   
      $this->data['payment_address'] = $this->tool->insertables($insertables, $format, '{', '}');
      
      
      // Vouchers
      foreach ($order_info['order_vouchers'] as &$voucher) {
         $voucher['amount'] = $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']);
      }unset($voucher);
      
      $this->data['order_vouchers'] = $order_info['order_vouchers'];
      
      
      //Products
      foreach ($order_info['order_products'] as &$product) {
         $product['price'] = $this->currency->format($product['price'], $order_info['currency_code'], $order_info['currency_value']);
         $product['cost'] = $this->currency->format($product['cost'], $order_info['currency_code'], $order_info['currency_value']);
         $product['total'] = $this->currency->format($product['total'], $order_info['currency_code'], $order_info['currency_value']);
         
         foreach ($product['option'] as &$option) {
            if(strlen($option['value']) > 22){
               $option['value'] = substr($option['value'], 0, 20) . '..';
            }
         }unset($option);
      }unset($product);
      
      $this->data['order_products'] = $order_info['order_products'];
      
      
      //Totals
      foreach ($order_info['order_totals'] as &$total) {
         $total['text'] = html_entity_decode($total['text'], ENT_NOQUOTES, 'UTF-8');
      }unset($total);
      
      $this->data['order_totals'] = $order_info['order_totals'];
      
      //Urls
      $this->data['order_info_url'] = $order_info['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_id;
      
      $this->data['downloads_url'] = $order_info['order_downloads'] ? $order_info['store_url'] . 'index.php?route=account/download' : '';
      
      //Generate HTML email
      $this->template->load('mail/order_html');
      $this->data['shipping_address_html'] = nl2br(htmlentities($this->data['shipping_address']));
      $this->data['payment_address_html'] = nl2br(htmlentities($this->data['payment_address']));
      
      $html = $this->render();
      
      //Generate Text email
      $this->template->load('mail/order_text');
      
      $text = $this->render();
      
      $this->mail->init();
         
      $this->mail->setTo($order_info['email']);
      $this->mail->setCopyTo($this->config->get('config_email'));
      $this->mail->setFrom($this->config->get('config_email'));
      $this->mail->setSender($order_info['store_name']);
      $this->mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
      $this->mail->setHtml($html);
      $this->mail->setText(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
      
      $this->mail->send();

      // Admin Alert Mail
      if ($this->config->get('config_alert_mail')) {
         $this->template->load('mail/order_text_admin');
         
         $text = $this->render();
         
         $subject = $language->format('text_subject', $this->config->get('config_name'), $order_id);
      
         $this->mail->init();
         
         $this->mail->setTo($this->config->get('config_email'));
         $this->mail->setFrom($this->config->get('config_email'));
         $this->mail->setSender($order_info['store_name']);
         $this->mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
         $this->mail->setText(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
         $this->mail->send();
         
         //Send additional alert emails
         $this->mail->setTo($this->config->get('config_alert_emails'));
         $this->mail->send();
      }
   }
}