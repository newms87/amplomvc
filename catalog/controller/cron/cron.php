<?php
class ControllerCronCron extends Controller {
   function index() {
      $tasks = $this->model_setting_setting->getSetting('cron_tasks');
      
		echo "Running Cron - " . $this->tool->format_datetime() . "<br><br>";
      foreach($tasks['tasks'] as $task){
         if($task['status'] != '1')continue;
         
         echo 'checking ' . $task['name'] . "<br>";
         if(isset($task['times'])){
            $run = false;
            foreach($task['times'] as $time){
               $s = date_create($time);
               $diff = date_diff(date_create(), $s);
               
               if($diff->y == 0 && $diff->m == 0 && $diff->d == 0 && $diff->h == 0 && $diff->i <= 30){
                  $run = true;
                  break;
               }
            }
            
            if(!$run){
               echo "Skipping...<br><br>";
               continue;
            }
         }
         
         echo "Executing $task[name]<br>";
         
         $this->getChild($task['action']);
         
         echo "<br><br>";
      }
      
      echo "Cron ran successfully!";
      
      exit;
   }
}