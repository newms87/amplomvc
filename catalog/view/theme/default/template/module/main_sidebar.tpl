<div class="box">
  <div class="box-main_sidebar">
  	<?= $heading_title; ?>
    <ul>
    	<? foreach($designers as $designer){
    		echo "<li><a href='$designer[href]'>$designer[name]</a></li>";
    	}
		?>
    </ul>
  </div>
</div>
