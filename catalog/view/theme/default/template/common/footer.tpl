<div style='clear:both'></div>
</div> <!-- /content -->

<div id="footer">
	<div id="links_footer" class="links">
		<?= $this->builder->build_links($links_footer);?>
	</div>
	<? if(!empty($social_networks)){ ?>
		<div id="footer_social_networks">
		<?= $social_networks;?>
		</div>
	<? }?>
</div>

</div><!-- /container_content -->
</div><!-- /container -->
</body></html>