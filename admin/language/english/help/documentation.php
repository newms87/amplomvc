<?php
$_['heading_title'] = "Betty 2.0 Documentation";

$_['sections'] = array();

//I. Flashsales
$_['sections']['flashsale'] = array();
$s = &$_['sections']['flashsale'];
	$s['title'] = "Flashsales";
		//A. Creating a new Flashsale
		$s['sub'][1]['title'] = "Creating a new Flashsale";
		$ss = &$s['sub'][1]['step'];
			$ss[1] = "Navigate to <a href='%@catalog/flashsale/insert%@'>Flashsales > New Flashsale</a>";
			$ss[2]['text'] = "You can choose to Autofill (see instructions below) the flashsale from a Designer/Brand or Manually enter the information.";
			$sss = &$ss[2]['step'];
				//Autofill
				$sss[0] = "%!IMPORTANT: Designers/Brands may have products in their inventory they do not wish to have in the flashsale. Be sure to double check the products that are automatically added!%!";
				$sss[1] = "The Autofill feature will automatically fill the the Name (which you can change), the page URL, the products (if specified), and the description.";
				$sss[2] = "To use the Autofill feature first select the Designer/Brand you would like to use.";
				$sss[3] = "Select whether you would like to autofill all of the Designer/Brand associated products or just the information";
				$sss[4] = "Specify the discount you would like to apply to the products in the %%Discount%% field (choose percentage or dollar amount)";
				$sss[5] = "Click on the 'Autofill' Button";
			//The fields
			$ss[3] = "%%Discount%%: Use this with the Autofill feature to automatically discount product prices. Also used to indicate (or approximate) the Flashsales discounted rate.";
			$ss[4] = "%%Flashsale Title%%: This title will be seen by the site customers. This can be the same as the Designer/Brand name ";
			$ss[5] = "%%Page URL%%: %!IF YOU NEED HELP WITH THIS, DO NOT MODIFY IT!!%!. Please use the 'generate url' button AFTER filling in the %%Flashsale Title%% or use the Autofill feature to let it make a URL for you.";
			$ss[6] = "%%Designers%%: Most flashsales will only have 1 Designer Associated. Do not add more than 1 unless there are more than 1 designers involved.";
			$ss[7] = "%%Teaser%%: This will display on the Flashsale Polaroids advertised around the site.";
			$ss[8] = "%%Description%%: This is the Deigner's / Brand's opportunity to personalize their product. Full HTML is allowed. This displays under the %%Flashsale Title%% on the flashsale page";
			$ss[9]['text'] = "%%Products%%: The Products associated to the flashsale.";
			$sss = &$ss[9]['step'];
				$sss[1] = "This is an Autocomplete field. Begin typing the name of the product you want to add and it will display a list of matching products.";
				$sss[2] = "Click on the product's name in the list to add it to the Flashsale.";
				$sss[3] = "You may modify the flashsale price for a product by clicking on the price. The price will automatically be calculated when using the Autofill feature with the %%Discount%% field.";
				$sss[4] = "These products are sortable by dragging and dropping them into the order you wish. You may also manually enter the number by clicking on the number to the right of the price.";
			$ss[10] = "%%Image%%: Displays on the Polaroids throughout the site for the flashsale and also on the flashsale page next to the %%Flashsale Title%%";
			$ss[11] = "%%Start Date / End Date%%: Click on these fields to bring up the calendar and select a date. Use the 'Now' button on the calendar to set the time to the current time. %!Be careful setting the hours/minutes! These will be the exact times the sale will start / end!%!";
			$ss[12] = "%%Product Sections%%: This is used to list the products in the flashsale by a certain attribute such as Men / Womens, or England / France / United States.  %!Each Product must have this attribute otherwise the product will not show up on the flashsale page!%!";
			$ss[13] = "%%Customer Group%%: Leave this as 'Default' unless you know what you are doing.";
			$ss[14] = "%%Status%%: If Disabled, the flashsale will be completely hidden to our customers.";
		//B. Viewing / Modifying current and expired Flashsales
		$s['sub'][2]['title'] = "Viewing / Modifying current and expired Flashsales";
		$ss = &$s['sub'][2]['step'];
			$ss[1] = "Navigate to <a href='%@catalog/flashsale%@'>Flashsales > Flashsales</a>";
			$ss[2] = "You can sort the flashsales by any of the table headings (eg. Title, End Date, Status, etc.)";
			$ss[3] = "To modify a flashsale click the 'Edit' Button to the right of the table. See \"Part A. Creating a New Flashsale\" for details about the flashsale form.";
			$ss[4] = "You can quickly modify a flashsale or multiple flashsales by checking the box to left of each flashsale you wish to modify and use the 'Batch Action' at the top right.";
		//C. Homepage Featured Flashsales
		$s['sub'][3]['title'] = "The Homepage Featured Flashsales (the polaroids)";
		$ss = &$s['sub'][3]['step'];
			$ss[1] = "Navigate to <a href='%@module/featured_flashsale%@'>Flashsales > Featured Flashsales</a>";
			$ss[2] = "You can specify Designer's that will show up on the polaroids if there are not enough active flashsales to cover the 3 polaroids. The ordering of the Designer's in the list will determine the order of when they will be shown.";
			$ss[3] = "The Module list below allows you to add the Featured Flashsales Block to any page Layout. The %%Style%% 'Large' should only be used with the 'Above Content' %%Position%%. This position must be added to the template for where you want it to be displayed.";
		//D. Flashsales Sidebar
		$s['sub'][4]['title'] = "The Flashsales Sidebar";
		$ss = &$s['sub'][4]['step'];
			$ss[1] = "Navigate to <a href='%@module/flashsale_sidebar%@'>Flashsales > Flashsale Sidebar</a>";
			$ss[2] = "The Flashsale Sidebar is the list of Flashsales that displays on most (or every page). Here you can change which pages it is displayed on and how many flashsales are shown at a time.";
			$ss[3] = "To change which pages either click the 'Remove' button to hide it or use the 'Add Module' button to add it to a specific Layout.";
			$ss[4] = "The %%Layout%% is the page the sidebar will be displayed on.";
			$ss[4] = "The %%Position%% will determine where the list is displayed on the page. ";
			
//II. Products
$_['sections']['product'] = array();
$s = &$_['sections']['product'];
	$s['title'] = "Products";
		//A. Creating a new Product
		$s['sub'][1]['title'] = "Creating a new Product";
		$ss = &$s['sub'][1]['step'];
			$ss[1] = "Navigate to <a href='%@catalog/product/insert%@'>Catalog > Products > Add</a>";
			//General Tab
			$ss[2]['text'] = "General Tab";
			$sss = &$ss[2]['step'];
				$sss[0] = "%%Product Name%%: This name will be seen by the customers.";
				$sss[1] = "%%Meta Tag Description%%: This is used for SEO purposes to make the product more visible in search engines.";
				$sss[2] = "%%Meta Tag Keywords%%: This is used for SEO purposes to make the product more visible in search engines.";
				$sss[3] = "%%Description%%: Seen on the product page under the description tab. Provides detailed information about the product.";
				$sss[4] = "%%Product Blurb%%: This is used to personalize the product. This is seen at the top of the product page. You may use Full HTML to add creativity to the product page.";
				$sss[5] = "%%Shipping / Return Policy%%: Is automatically filled with the default policy. You may customize it to match this individual items policy.";
				$sss[5] = "%%Product Tags%%: This is used to increase the visibility from our local search engine and allows products to be related to other products on the site.";
			//Data Tab
			$ss[3]['text'] = "Data Tab";
			$sss = &$ss[3]['step'];
				$sss[0] = "%%Model ID%%: A unique reference ID for this product.";
				$sss[1] = "%%Page URL%%: %!IF YOU NEED HELP WITH THIS, DO NOT MODIFY IT!!%!. Please use the 'generate url' button AFTER filling in the %%Product Name%%.";
				$sss[2] = "%%UPC%%: This is the Universal Product Code for this product (if it has one).";
				$sss[3] = "%%Price%%: The original price for this product. If this product has a sale price, please specify this using the Flashsales page if this product is part of a flashsale, or use the Special Tab or Discount Tab.";
				$sss[4] = "%%Cost%%: The cost for BettyConfidential to acquire this product.";
				$sss[6] = "%%Final Sale%%: Yes, if this product cannot be returned, otherwise choose No.";
				$sss[7] = "%%Quantity%%: The number of products of this type that is in stock.";
				$sss[8] = "%%Minimum Quantity%%: The minimum of this product a customer must order. Should be 1 for most products.";
				$sss[9] = "%%Subtract Stock%%: If this product's stock should be taken into account. If we have unlimited, choose No.";
				$sss[10] = "%%Requires Shipping%%: Choose No if we will not be sending anything to the customer via the Postal Service.";
				$sss[11] = "%%Image%%: This is the main image for this product. It will be seen by customers whereever this product is displayed. Use the Additional Images tab for images that appear under the main image on the product page.";
				$sss[12] = "%%Date Available / Date Expires%%: The date this product will be visible to customers on our site and the date this product will no longer be available for purchase (but may still be visible to customers)";
				$sss[13] = "%%Dimensions / Length Class /Weight / Weight Class%%: Used for calculating shipping if applicable.";
				$sss[14] = "%%Status%%: If the product is disabled, it will not be visible to customers on our site.";
				$sss[15] = "%%Sort Order%%: The order in which this product will appear relative to other products being displayed (May be overridden by flashsales or other modules that display products)";
			//Links Tab
			$ss[4]['text'] = "Links Tab";
			$sss = &$ss[4]['step'];
				$sss[0] = "%%Designer%%: The Designer this product is associated with. If this Designer is disabled or expired, this product will also be disabled/expired.";
				$sss[1] = "%%Categories%%: Select all that apply (eg: if the category is rings, select Accessories, Accessories > Jewelry, and Accessories > Jewelry > Rings)";
				$sss[2] = "%%Stores%%: Should always be associated to all stores unless there is a good reason not to.";
				$sss[3] = "%%Downloads%%: If there are any downloadable components for this product, select which should be accessible by our customers after they purchase this product.";
				$sss[4] = "%%Related Products%%: This is an autocomplete field. Type the name of the product to relate to this product. Click the name as it appears in the dropdown list.";
			//Attribute Tab
			$ss[5]['text'] = "Attribute Tab";
			$sss = &$ss[5]['step'];
				$sss['text'] =  "See \"II. Products > B. Attributes\" for more details. If you are not able to find the attribute you desire, you can create a new attribute group (eg. Country, Gender, etc.) at <a href='%@catalog/attribute_group%@'>Catalog > Attribute Group</a>, or create a new attribute value (eg. Men's, Women's, France, etc.) at <a href='%@catalog/attribute%@'>Catalog > Attributes</a>.";
				$sss[0] = "To add a new attribute, click the 'Add Attribute' button at the bottom.";
				$sss[1] = "%%Attribute%%: This is an autocomplete field. Type the name of the attribute (eg. Women's, France, Long Sleeve, etc.) and click the option from the dropdown list.";
				$sss[2] = "%%Text%%: This is the description text that gives further details for this attribute.";
			//Option Tab
			$ss[6]['text'] = "Option Tab";
			$sss = &$ss[6]['step'];
				$sss['text'] = "See \"II. Products > C. Product Options\" for more details. If you are not able to find the option you desire, youc an create a new Option (eg. Color, Size, etc.) at <a href='%@catalog/option%@'>Catalog > Options</a>. From here you can also add new Option Values under each option.";
				$sss[1] = "Add a new option by using the autocomplete field. Begin typing an option (eg. Color, Size, etc.). Click on the desired option when it appears.";
				$sss[2] = "%%Required%%: If the user must select an option in order to purchase this product, choose Yes.";
				$sss[3] = "%%Option Value%%: If you do not see the desired value, please reference \"step 1.\" or \"II. Products > C. Product Options\".";
				$sss[4] = "%%Subtract Stock%%: If we should keep track of the stock levels for this Option Value.";
				$sss[5] = "%%Price%%: The Price change for this Option Value when the customer has selected it. Note the '-' and '+' options.";
				$sss[6] = "%%Points%%: We currently do not offer points for product purchases.";
				$sss[7] = "%%Weight%%: How much weight this option will add for shipping purposes, relative to the %%Weight%% of our product (found under the Data tab). Note the '-' and '+' options.";
			//Discount Tab
			$ss[7]['text'] = "Discount Tab";
			$sss = &$ss[7]['step'];
				$sss[0] = "Use this to specify a discount applied at checkout (like a coupon) for a specific customer group. This discount will not be an advertised price.";
				$sss[1] = "%%Customer Group:%% The customer group this discount will automatically be applied for. See \"VIII. Sales > B. Customer Groups\" for more details.";
				$sss[2] = "%%Quantity%%: The quantity of prducts that will be sold at this discount. Leave blank for unlimited.";
				$sss[3] = "%%Priority%%: If there are multiple discounts, use this to set the priority level. Lower numbers will be seen first.";
				$sss[4] = "%%Price%%: The new Price for this product. Note that this will not subtract from the price. This is the new price.";
				$sss[5] = "%%Date Start / Date End%%: The date range this discount will be active.";
			//Special Tab
			$ss[8]['text'] = "Special Tab";
			$sss = &$ss[8]['step'];
				$sss[0] = "Use this to specify an ongoing special for this product. This price will be advertised wherever the product is seen.";
				$sss[1] = "%%Customer Group:%% The customer group this discount will automatically be applied for. See \"VIII. Sales > B. Customer Groups\" for more details.";
				$sss[2] = "%%Priority%%: If there are multiple specials, use this to set the priority level. Lower numbers will be seen first.";
				$sss[3] = "%%Price%%: The new Price for this product. Note that this will not subtract from the price. This is the new price.";
				$sss[4] = "%%Date Start / Date End%%: The date range this special will be active.";
			//Additional Images Tab
			$ss[9]['text'] = "Additional Images Tab";
			$sss = &$ss[9]['step'];
				$sss[0] = "These images will appear under the main %%Product Image%% (as set under the Data tab) on the product description page";
				$sss[1] = "%%Image:%% Use the 'Browse Files' button to upload and select images (be sure to double click the image to select it!). %!'Clear Image' will only replace the image with the default 'noimage.jpg'. This will not remove the image from being displayed! Use the 'Remove' Button to remove an image!%!";
				$sss[2] = "%%Sort Order%%: The order in which the images will be displayed under the main %%Product Image%%";
			//Reward Points Tab
			$ss[10]['text'] = "Reward Points Tab";
			$sss = &$ss[10]['step'];
				$sss[0] = "This is currently not in use by the BettyConfidential System";
			//Design Tab
			$ss[11]['text'] = "Design Tab";
			$sss = &$ss[11]['step'];
				$sss[0] = "Use this to specify the layout page for this product by store. %!Do not select anything if you are unsure what this does%!";
		//B. Product Attributes
		$s['sub'][2]['title'] = "Product Attributes";
		$ss = &$s['sub'][2]['step'];
			$ss[0]['text'] = 'Attribute Groups';
			$sss = &$ss[0]['step'];
				$sss[0] = "Attribute Groups are the overlaying category of attributes such as 'Country', 'Gender', 'Sleeve Type', etc.";
				$sss[1] = "Navigate to <a href='%@catalog/attribute_group%@'>Catalog > Attributes > Attribute Groups</a>.";
				$sss[2] = "If you do not see the %%Attribute Group%% that matches the one you desire, click the 'Add' button.";
				$sss[3] = "Enter the name of the new %%Attribute Group%% and click 'Save' at the top.";
				$sss[4] = "%!You are not done yet!%! You will first need to add %%Attributes%% to your %%Attribute Group%% before you can use it. See Part ii. for details on adding %%Attributes%%";
			$ss[1]['text'] = 'Attributes';
			$sss = &$ss[1]['step'];
				$sss[0] = "%%Attributes%% are assigned to %%Attribute Groups%%. These are the values that will be displayed to the customer and are searchable by our local search engine.";
				$sss[1] = "Navigate to <a href='%@catalog/attribute%@'>Catalog > Attributes > Attributes</a>.";
				$sss[2] = "If you do not see the %%Attribute%% that matches the one you desire, click the 'Add' button.";
				$sss[3] = "Enter the name of the new %%Attribute%% and Find the appropriate %%Attribute Group%% from the dropdown list. If you cannot find the right %%Attribute Group%% please see part i. for details on how to creat a new group.";
				$sss[4] = "Click the 'Save' Button at the top to save the new %%Attribute%%. You are now ready to use your attribute.";
		//C. Product Attributes
		$s['sub'][3]['title'] = "Product Options";
		$ss = &$s['sub'][3]['step'];
			$ss[0] = 'Options allow us to have multiple product options for the same product such as different Colors or Sizes. The customer will have to select an option (if specified) in order to purchase the product.';
			$ss[1] = "Navigate to <a href='%@catalog/option%@'>Catalog > Options</a>.";
			$ss[2] = "Search for the %%Option Category%% your specific option will fall under. Click the 'Edit' Button at the far right for that option category to add a new %%Option Value%%.";
			$ss[3] = "If you cannot find the %%Option Category%% you desire, click the 'Add' Option button.";
			$ss[4] = "%%Option Name%%: Specify the option category name that will be visible to our customers.";
			$ss[5] = "%%Type%%: This will determine how the %%Option Value%% will be selected by our customers (eg. dropdown menu, date / time picker via a Calendar, Checkboxes, etc.).";
			$ss[6] = "%%Sort Order%%: This will determine the ordering relative to other %%Option Categories%% that are associated to the same product.";
			$ss[7] = "Add as many %%Option Values%% as needed. %!These values will not all show up when the %%Option Category%% is assigned to a product%!. You may specify exactly which options will be availble per product. However, you may specify all the options if you want. So the more %%Option Values%% that are here the less likely you will need to come back to this screen to update new %%Option Values%%";
			
		
//III. Designers
$_['sections']['designer'] = array();
$s = &$_['sections']['designer'];
	$s['title'] = "Designers";
		//A. Creating a new Designer
		$s['sub'][1]['title'] = "Creating a new Designer";
		$ss = &$s['sub'][1]['step'];
			$ss[0] = "%!Important: Designer's are directly associated with Users. We use the contact information associated with a User to contact the designers. A User should always be setup and associated to a Designer after the Designer has been created. See \"VI. Users\" for more details on creating and associating a user.%!";
			$ss[1] = "Navigate to <a href='%@catalog/manufacturer%@'>Catalog > Designers</a> and click on the 'Add' button";
			//General Tab
			$ss[2]['text'] = "General Tab";
			$sss = &$ss[2]['step'];
				$sss[0] = "%%Designer Brand Name%%: This name will be seen by the customers. This is either the Designer's Name or the Brand name for the product line.";
				$sss[1] = "%%Vendor ID%%: There is no field for this as it will be generated automatically by the system. The Vendor ID can be viewed at <a href='%@catalog/manufacturer%@'>Catalog > Designers</a>";
				$sss[2] = "%%Page URL%%: %!IF YOU NEED HELP WITH THIS, DO NOT MODIFY IT!!%!. Please use the 'generate url' button AFTER filling in the %%Designer Brand Name%%.";
				$sss[3] = "%%Product Section%%: This will list all the products by the Designer with this %%Attribute%% by sections. %!Careful!%! Using this will only display products that have this %%Attribute%% associated! See \"II. Products > B. Product Attributes\" for details on how to add %%Attributes%%.";
				$sss[4] = "%%Description%%: Seen on the Designer's page next to the image. This is fully HTML capable allowing you to personalize the Designer page.";
				$sss[5] = "%%Stores%%: A Designer should always be associated to all of our stores, unless there is a reason not to.";
				$sss[6] = "%%Image%%: This will be displayed next to the Designer's Description on the Designer Page. Will also appear wherever the Designer is advertised on the site.";
				$sss[7] = "%%Expire On%%: Used with Bettycron (aka Scheduled Tasks). When this date is reached, the Designer will be set to Inactive as well as any products / flashsales associated with this Designer.";
				$sss[8] = "%%Status%%: If Inactive, this Designer will not be visible to our customers.";
				$sss[9] = "%%Sort Order%%: Use this to specify an ordering when multiple Designers are displayed together. Will be overridden in most pages by the Layout.";
			//Articles Tab
			$ss[3]['text'] = "Articles Tab";
			$sss = &$ss[3]['step'];
				$sss[0] = "These articles will be featured inline (mixed in) with the products on the Designer's page. They can be linked to anywhere on the internet.";
				$sss[1] = "Click on 'Add Article' to add a new article to be featured.";
				$sss[2] = "The %%Title%% for the article is only used for reference and will not be seen by our customers."; 
				$sss[3] = "The %%Description%% will be seen by our customers as this is the preview or teaser for the Article. You may use Full HTML to personalize the advertisement.";
				$sss[4] = "The %%URL%% must be the full URL path for the article (eg. http://www.bettyconfidential.com/article-name)";
		//B. Viewing / Modifying Designers
		$s['sub'][2]['title'] = "Viewing / Modifying Designers";
		$ss = &$s['sub'][2]['step'];
			$ss[0] = "Our Designer's will remain in our system whether or not they are expired / inactive. %!Do not delete Designer's unless you have a good reason!%!";
			$ss[1] = "Navigate to <a href='%@catalog/manufacturer%@'>Catalog > Designers</a>";
			$ss[2] = "From here you can sort the designers by any of the table headings that are sortable (eg. Designer Brand Name, Vendor ID, etc.)";
			$ss[3] = "To Modify a Designer, find the desired designer in the list and click on the 'Edit' button to the right of the table. See part A. for the Designer form details.";
				
//IV. Categories
$_['sections']['category'] = array();
$s = &$_['sections']['category'];
	$s['title'] = "Categories";
		//A. Creating a new Category
		$s['sub'][1]['title'] = "Creating a new Category";
		$ss = &$s['sub'][1]['step'];
			$ss[1] = "Navigate to <a href='%@catalog/category%@'>Catalog > Category</a> and click the 'Add' button";
			//General Tab
			$ss[2]['text'] = "General Tab";
			$sss = &$ss[2]['step'];
				$sss[0] = "%%Category Name%%: This name will be seen by our customers. The can be anything such as Jewels, Accessories, or Rings. To associate parent categories see section \"B. Data Tab\".";
				$sss[1] = "%%Meta Tag Description%%: This is used for SEO purposes to make the category more visible in search engines.";
				$sss[2] = "%%Meta Tag Keywords%%: This is used for SEO purposes to make the category more visible in search engines.";
				$sss[3] = "%%Description%%: Seen only on the category page. A Description of what kinds of products our customers will find in this category.";
			//Data Tab
			$ss[3]['text'] = "Data Tab";
			$sss = &$ss[3]['step'];
				$sss[0] = "%%Parent Category%%: This is how you associat a Parent to this category. If this is a top level category (eg. accessories, apparel, etc.) do not select anything.";
				$sss[1] = "%%Stores%%: A Category should always be associated to all of our stores, unless there is a reaosn not to.";
				$sss[2] = "%%Meta Tag Keywords%%: This is used for SEO purposes to make the category more visible in search engines.";
				$sss[3] = "%%Description%%: Seen only on the category page. A Description of what kinds of products our customers will find in this category.";
		//Design Tab
			$ss[4]['text'] = "Design Tab";
			$sss = &$ss[4]['step'];
			$sss[0] = "Use this to specify the Layout page for this category by store. %!Do not select anything if you are unsure what this does%!";
			
$_['sections']['content'] = array();
$s = &$_['sections']['content'];
	$s['title'] = "Content";
		//A. Betty's Daily Spotlight & The Product Search / Filter
		$s['sub'][1]['title'] = "Betty's Daily Spotlight & The Product Search / Filter";
		$ss = &$s['sub'][1]['step'];
			$ss[1] = "Navigate to <a href='%@module/featured%@'>Content > Featured Products</a>";
			$ss[2] = "Here you can specify which pages the Product Search / Filter is displayed on. You can also specify if the page will feature Betty's Daily Spotlight.";
			$ss[3] = "%%Filter Types%%: These are the filter categories displayed in the search bar when searching for products (via the popup window). To add a %%Filter Type%% simply type in the name and hit the enter key. You can drag and drop them to order them. %!The Filter Type must be implemented before it will work! Check with the webadmin to ensure functionality.%! The 'Default' category will be highlighted and active when initially searching.";
			$ss[4] = "%%Betty's Daily Spotlight%%: This is an autocomplete field. Type the product that is to be featured in the Spotlight. When the name appears in the dropdown, click on it to add it to the Spotlight.";
			$ss[5] = "In the list of modules below you can specify the %%Limit%% for the number of product displayed per page (the autoscroll feature will allow the entire list to be shown displayed as the user scrolls down).";
			$ss[6] = "%%Layout%% is the page to display it on, %%Position%% is where to display it, %%Filter Menu Position%% is where to put the category / filter menu, %%Display Style%% will always have a popup window, but you can optionally choose 'Context' to dislpay the Betty's Daily Spotlight inline with the content on that specified in the %%Layout%%.  %%Sort Order%% will determine in what order the content will appear relative to other active modules on the page."; 
		//B. Leaderboard / Advertised Promotions in Headers
		$s['sub'][2]['title'] = "Leaderboard / Advertised Promotions in Headers";
		$ss = &$s['sub'][2]['step'];
			$ss[1] = "Navigate to <a href='%@module/page_headers%@'>Content > Leaderboard</a>";
			$ss[2] = "Here you can advertise promotions such as 'Free Shipping' or any content that should be displayed in the page headers (above the content next to the logo)";
			$ss[3] = "%%Layout%%: This is the page(s) that the header should be promoted on. You may add as many Layouts as needed for each header. Duplicate Layouts between different headers will only render 1 header. May be useful if you are trying to use a temporary header, then fallback to the original header later without having to recreate it.";
			$ss[4] = "%%Advertisement%%: This is a full HTML capable field. You can customize the advertisement to match whatever the needs are.";
		//C. RSS Articles / Automatic Article Updates from BettyConfidential Feeds
		$s['sub'][3]['title'] = "RSS Articles / Automatic Article Updates from BettyConfidential Feeds";
		$ss = &$s['sub'][3]['step'];
			$ss[1] = "Navigate to <a href='%@module/rss_article%@'>Content > RSS Articles</a>";
			$ss[2] = "Here you can set which article feeds to update from and what content to display. These will display in the sidebar on every page (specified by the modules at the bottom)";
			$ss[3]['text'] = "%%RSS Settings%%: The # of Articles to grab, # of Articles to keep, and the Article Title Max Length.";
			$sss = &$ss[3]['step'];
				$sss[1] = "Specify the number of Articles to add to the list from the feed and the number of articles to be kept in the list after the update.";
				$sss[2] = "If there are 5 articles in the list and you grab 8 articles and limit the list to 10 articles, the result will be 8 new articles added to the top of the list (which will be displayed first), followed by 2 articles that were originally in the list (of the 5 that were there to start).";
				$sss[3] = "Finally set the Article Title Max length which will cut any article titles longer than this limit down to the specified limit by character (meaning words will be cut off half way through if that is where the limit is reached).";
			$ss[4] = "%%RSS Location%%: Must be the exact URL for the article RSS Feed. Should always be an .xml file. %!Important! You must save any changes you make in the %%RSS Settings%% before using the %%Update From RSS%% button!%!";
			$ss[5] = "%%Specify Article%%: Use this to add individual articles to the list. You must specify the title to be displayed and the exact URL (eg. http://www.bettyconfidential.com/article-name.xml)";
			$ss[6] = "The Module list lets you specify which pages will display the articles and how many articles to be displayed. The Sort Order will determine the ordering of the RSS Articles position relative to the other modules such as the Designer's List and search menu, etc.";
//VI. Users
$_['sections']['users'] = array();
$s = &$_['sections']['users'];
	$s['title'] = "Users";
		//A. User Groups
		$s['sub'][1]['title'] = "User Groups";
		$ss = &$s['sub'][1]['step'];
			$ss[1] = "User Groups determine the roles and privelages of user accounts. These roles can be Administrator, Designer, Etc.";
			$ss[2] = "Navigate to <a href='%@user/user_permission%@'>System > Users > User Groups</a>";
			$ss[3] = "From Here you find the user group to modify or create a new User Group by clicking the 'Add' Button at the top right.";
			$ss[4] = "%%User Group Name%%: this name is only used for reference in the backend system. Our users will never see this name.";
			$ss[5] = "%%Access Permissions%%: These are the pages that this User Group is allowed to access or view. They MAY NOT modify anything on these pages therefore the database will remain unchanged.";
			$ss[6] = "%%Modify Permissions%%: These are the pages / data that this User Group can view / modify. Only enable these for this user group if they should be allowed to make changes to the database concerning the specific tasks involved on the pages.";
			$ss[7] = "If you are unsure what content is accessible or modifiable on these pages you can view the page in question by going to the url: bettyconfidential.com/admin/index.php?route=<i>page/name</i>.  Simply replace <i>page/name</i> with the Permission name.";
		//B. Users
		$s['sub'][2]['title'] = "Users";
		$ss = &$s['sub'][2]['step'];
			$ss[1] = "Users are people who use the backend system where you are currently at. This includes both the administration system and the Designer Portal.";
			$ss[2] = "Navigate to <a href='%@user/user%@'>System > Users > Users</a>";
			$ss[3] = "From Here you may sort through the users using the table headers such as Username, Status, or Date Added. Find the user you wish to modify and click the 'Edit' Button at the right, or create a new user by clicking the 'Add' button at the top.";
			$ss[4] = "%%Username and Password%%: This is the login name / password that this user will use to access the system. This is a unique identifier for this user as well.";
			$ss[5] = "%%First Name, Last Name, and Email%%: This is the Basic information for the user. The contact email, name, phone and other information will be used from the information found under the %%Contact Tab%%";
			$ss[6] = "%%User Group%%: This will determine the Role and Access Permissions for this user. %!Be Careful Selecting which Group!%!, if this user account is for a Designer, choose the 'Designer' User Group.";
			$ss[7] = "%%Designers%%: Not all user account will be associated with Designers, however all user accounts for Designer's will have at least 1 Designer (and probably only 1) associated. If the 'Designer' %%User Group%% is selected, only the associated Designer information will be available to this user and they will only have access to the Designer Portal.";
			$ss[8] = "%%Status%%: If Disabled, this user will not be able to login to our system with this account.";
			
			