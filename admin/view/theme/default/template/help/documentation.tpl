<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<?= $this->builder->display_messages($messages); ?>
<div class="box">
	<div class="heading">
		<h1><img src="<?= HTTP_THEME_IMAGE . 'module.png'; ?>" alt="" /> <?= $head_title; ?></h1>
	</div>
	<div id='help_docs' class="content">
		<? function display_levels($sub, $level){
			$list_types = array('A','i','a');
			$t = $list_types[$level%3];
			echo "<ol type='$t' class='level-$level'>";
			foreach($sub as $item){
					if(!is_array($item))
						echo "<li>$item</li>";
					else{
						foreach($item as $type=>$data){
								switch($type){
									case'title':
											echo "<li>$data</li>";
											break;
									case 'text':
											echo "<li>$data</li>";
											break;
									case 'step':
											is_array($data)?display_levels($data,$level+1):"<li>$data</li>";
											break;
									default:
											break;
								}
						}
					}
			}
			echo"</ol>";
		}
		
		echo "<ol type='I' class='top'>";
		foreach($sections as $s){
			echo "<li>$s[title]";
			display_levels($s['sub'],0);
			echo "</li>";
		}
		echo "</ol>";
		?>
	</div>
</div>

<script type="text/javascript">//<!--
$('ol li').click(toggle_item).each(
	function(i,e){
			if($(e).next('ol').length || $(e).children('ol').length)
				$(e).addClass('expandable');
	});

$('ol li a').click(function(){window.open($(this).attr('href'));});

function toggle_item(event){
	if($(this).hasClass('expandable')){
			$(this).children('ol').slideToggle();
			$(this).next('ol').slideToggle();
			if($(this).hasClass('open'))
				$(this).removeClass('open');
			else
				$(this).addClass('open');
	}
	
	if(event.stopPropagation)
			event.stopPropagation();
	window.event.cancelBubble = true;
	return false;
}
//--></script>
<?= $footer; ?>