<?php 
class ControllerNewsletterNewsletter extends Controller { 

   public function index(){
     
      $newsletter_id = isset($_GET['newsletter_id'])?$_GET['newsletter_id']:0;
      
      if(!$newsletter_id){
         $this->message->add('warning', $this->_('error_newsletter_preview'));
         $this->redirect($this->url->link('error/not_found'));
         return;
      }
      
      $html = $this->url->load($this->url->link_admin('mail/newsletter/preview', 'newsletter_id=' . $newsletter_id), true);
      
      $this->response->setOutput($html);
   }
}
