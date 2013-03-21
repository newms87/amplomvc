<div id='featured_filter_sidebar' class="box sidebar_box">
  <div class="box-heading"><?= $category_title; ?></div>
  <div class="box-content">
    <div id='menu_categories'>
       <? function display_categories($categories, $menu_indent=10,$selected_cat=0, $level=0){
          $m = ($level*$menu_indent) . "px";
          $hidden = $level>0?'display:none':'';
          echo "<ul style='margin-left:$m;$hidden'>";
             foreach($categories as $cat){
                echo "<li>";
                echo "<a filter='category_id' value='$cat[category_id]' class='featured_menu_link ". ($cat['category_id']===$selected_cat?'active':'') . "'>$cat[name]</a>";
                if(isset($cat['children']) && count($cat['children']) > 0){
                  display_categories($cat['children'],$menu_indent, $selected_cat, $level+1);
                }
                echo "</li>";
             }
          echo "</ul>";
       }?>
       <? display_categories($categories, $menu_indent, $selected_cat);?>
    </div>
  </div>
  <div id='sort_by_sidebar'>
     <div class="box-heading"><?= $heading_title; ?></div>
     <div class="box-content">
        <ul>
           <? foreach ($menu_items as $key=>$item) { ?>
           <li>
              <a filter='sort_by' value='<?=$key;?>' class='featured_menu_link <?=$key==$selected?'active':'';?>'><?=$item;?></a>
           </li>
         <? }?>
        </ul>
     </div>
  </div>
</div>
