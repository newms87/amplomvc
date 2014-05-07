<?= call('common/header'); ?>
<?= area('left') . area('right'); ?>

<section id="home-page" class="home-video content">
	<? if ($this->area->hasBlocks('top')) { ?>
		<div class="row area-top">
			<div class="wrap">
				<?= area('top'); ?>
			</div>
		</div>
	<? } ?>

	<div class="home-main row">
		<div class="wrap">
			<div id="start-video">
				<video preload="none" loop autoplay poster="//fast.wistia.com/assets/images/blank.gif">
					<source src="<?= theme_url('video/home.mp4'); ?>" type="video/mp4">
					<div class="no-video"></div>
				</video>
			</div>

			<div class="intro">
				<h1><?= _l("THINK INSIDE THE BOX"); ?></h1>

				<h2><?= _l("First home delivery food service that cares about nutrition and flavor"); ?></h2>
				<a href="<?= $call_to_action; ?>" class="call-to-action button large"><?= _l("SUBSCRIBE NOW"); ?></a>
			</div>
		</div>
	</div>

	<div class="overview row">
		<div class="wrap">
			<div class="icon-list what-we-do clearfix">
				<h3><?= _l("What We Do"); ?></h3>

				<div class="icon planning col xs-12 sm-6 md-4">
					<div class="image">
						<img src="<?= theme_url('image/content/icons/planning.png'); ?>"/>
					</div>
					<h4 class="explain"><?= _l("We Plan Ahead"); ?></h4>
				</div>
				<div class="icon recipe col xs-12 sm-6 md-4">
					<div class="image">
						<img src="<?= theme_url('image/content/icons/recipe.png'); ?>"/>
					</div>
					<h4 class="explain"><?= _l("We Provide the Recipe"); ?></h4>
				</div>
				<div class="icon local-farms col xs-12 sm-6 md-4">
					<div class="image">
						<img src="<?= theme_url('image/content/icons/farmer.png'); ?>"/>
					</div>
					<h4 class="explain"><?= _l("We Shop From Local Farms"); ?></h4>
				</div>
				<div class="icon measure col xs-12 sm-6 md-4">
					<div class="image">
						<img src="<?= theme_url('image/content/icons/measure.png'); ?>"/>
					</div>
					<h4 class="explain"><?= _l("We Measure All of the Ingredients"); ?></h4>
				</div>
				<div class="icon deliver col xs-12 sm-6 md-4">
					<div class="image">
						<img src="<?= theme_url('image/content/icons/deliver.png'); ?>"/>
					</div>
					<h4 class="explain"><?= _l("We Deliver Right To Your Door"); ?></h4>
				</div>
				<div class="icon study col xs-12 sm-6 md-4">
					<div class="image">
						<img src="<?= theme_url('image/content/icons/study.png'); ?>"/>
					</div>
					<h4 class="explain"><?= _l("We Study Nutrition and Provide Nutrition Courses Free"); ?></h4>
				</div>
			</div>

			<div class="icon-list what-you-do clearfix">
				<h3><?= _l("What You Do"); ?></h3>

				<div class="icon cook col xs-12 sm-6">
					<div class="image">
						<img src="<?= theme_url('image/content/icons/cook.png'); ?>"/>
					</div>
					<h4 class="explain"><?= _l("You Cook"); ?></h4>
				</div>
				<div class="icon learn col xs-12 sm-6">
					<div class="image">
						<img src="<?= theme_url('image/content/icons/learn.png'); ?>"/>
					</div>
					<h4 class="explain"><?= _l("You Learn"); ?></h4>
				</div>
			</div>
		</div>
	</div>

	<div class="flexible-plans row">
		<div class="wrap">
			<div class="col xs-12 sm-6">
				<?= $this->block->render('widget/carousel', 'flex-plans'); ?>
			</div>
			<div class="col xs-12 sm-6">
				<div class="heading-list">
					<h2><?= _l("Flexible Plans"); ?></h2>
					<ul>
						<li><?= _l("Stop or place on hold at anytime at no cost"); ?></li>
						<li><?= _l("Starting at $7.99 per meal"); ?></li>
						<li><?= _l("Pair a glass of wine with each meal"); ?></li>
						<li><?= _l("Vegetarian meal options are also available"); ?></li>
						<li><?= _l("Get a fresh smoothie for each day of the week"); ?></li>
					</ul>
				</div>
			</div>
		</div>
	</div>

	<div class="ingredients row">
		<div class="wrap">
			<div class="col xs-12 sm-6">
				<?= $this->block->render('widget/carousel', 'ingredients'); ?>
			</div>
			<div class="col xs-12 sm-6">
				<div class="heading-list">
					<h2><?= _l("Fresh Ingredients"); ?></h2>
					<ul>
						<li><?= _l("Our vegetables and meats come directly from local Californian farms and artisanal purveyors"); ?></li>
						<li>
							<div><?= _l("Seasonal Ingredients"); ?></div>
							<div class="smaller"><?= _l("We tailor our recipes according to the seasons so that you are always eating the freshest produce with the maximum health benefits"); ?></div>
						</li>
						<li><?= _l("Fresher than the supermarket"); ?></li>
						<li><?= _l("Specialty ingredients difficult to find in most stores"); ?></li>
					</ul>
				</div>
			</div>
		</div>
	</div>

	<div class="nutrition row">
		<div class="wrap">
			<div class="col xs-12 sm-6">
				<?= $this->block->render('widget/carousel', 'nutrition'); ?>
			</div>
			<div class="col xs-12 sm-6">
				<div class="heading-list">
					<h2><?= _l("Nutrition Course"); ?></h2>
					<ul>
						<li><?= _l("Weekly e-mail with a simplified video and written nutrition course created by our nutritionist"); ?></li>
						<li><?= _l("Less than 10 minutes per lesson"); ?></li>
						<li><?= _l("Receive your weekly results with your corrected answers"); ?></li>
						<li><?= _l("Your next lessons will be customized according to your previous answers, your expectations and your needs"); ?></li>
						<li><?= _l("You will also receive all of the nutrition facts of your daily meals"); ?></li>
					</ul>
				</div>
			</div>
		</div>
	</div>

	<div class="deliveries row">
		<div class="wrap">
			<div class="col xs-12 sm-6">
				<div class="delivery-image">
					<img src="<?= theme_url('image/free-shipping.jpg'); ?>"/>
				</div>
			</div>
			<div class="col xs-12 sm-6">
				<div class="heading-list">
					<h2><?= _l("Free Deliveries (for members)"); ?></h2>
					<ul>
						<li><?= _l("Free delivery for all members"); ?></li>
						<li><?= _l("We carefully package ingredients each week in a refrigerated box"); ?></li>
						<li><?= _l("Guaranteed to arrive fresh"); ?></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	</div>

	<? if ($this->area->hasBlocks('bottom')) { ?>
		<div class="row area-bottom">
			<div class="wrap">
				<?= area('bottom'); ?>
			</div>
		</div>
	<? } ?>

</section>

<?= call('common/footer'); ?>
