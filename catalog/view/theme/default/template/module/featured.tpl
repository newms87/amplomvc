<? if($display == 'context'){ ?>
   <?= $spotlight;?>
   <?=$ending_soon;?>
<? }?>
<div class="box featured_box">
  <div id='featured_filter_results' style='display:none'></div>
  <form id='featured_filter_form' style='display:none'>
        <input type='hidden' id='sort_by' name='sort_by' value='<?=$sort_by;?>' />
        <input type='hidden' id='category_id' name='category_id' value='<?=$category_id;?>' />
        <input type='hidden' id='featured_page' name='page' value='<?=$page;?>' />
  </form>
</div>

<script type='text/javascript'>// <!--
   function toggleShowSearch(event,args){
      if(event && event.preventDefault){
         event.preventDefault();
         event.returnValue = false;
         event.stopImmediatePropagation();
      }
      args = args || event.data;
      
      var result = args?args.result:$('#featured_filter_results');
      var show = args ? args.show || false : false;
      
      if(result.is(':animated'))return;
      
      if(typeof result.data('width') == 'undefined' || result.data('width') === null)
          result.data('width',result.width());
      if(show){
         page = $('#featured_page').val();
         $('#sort_by_sidebar').show();
         result_content = $("<div id='result_content' style='display:none; width:100%;height:100%;' ></div>");
         
         if(show ===  result.data('showing')){
            result.find('.load_more_products span').hide();
            result.find('.load_more_products img').fadeIn(300);
            if(page <= 1)
               result.html($("<div id='grabbing_results'>Grabbing Results</div>").fadeIn(300));
         }
         else{
            result.css({width:0}).show().animate({width:result.data('width')}, {duration: 500, complete: function(){result.data('showing',true);}});
            //the grayed out background
            var close = $("<div id='result_close' style='width: 100%; height:100%; background: rgba(0,0,0,.2);z-index:5000;position:absolute;top:0;left:0;'></div>");
            close.click({result:result}, toggleShowSearch);
            $('body').append(close);
            $('#featured_filter_sidebar').addClass('popup_sidebar');
         }
         
         close = $("<div class='close_featured_results'>Close</div>").click(toggleShowSearch);
         $.post(args.url, args.form.serialize(),function(data){
            if(page > 1){
               result.find('.box-product').append($(data).fadeIn(500));
               if(page < parseInt(result.find('.load_more_products').attr('pages'))){
                  result.find('.load_more_products span').fadeIn(300);
                  result.find('.box-content').scroll(check_scroll_bottom);
               }
               result.find('.load_more_products img').hide();
            }
            else{
               result.html(result_content.html(data).fadeIn(500))
               result.append(close);
               if($('.load_more_products').length)
                  result.find('.box-content').scroll(check_scroll_bottom);
            }
         });
      }
      else{
         $('#sort_by_sidebar').hide();
         result.animate({width:0},{duration:500, complete: function(){result.data('showing',false);}}).hide();
         $('#result_close').remove();
         result.html('');
         $('#featured_filter_sidebar').removeClass('popup_sidebar');
      }
   }
   
   function show_search(event){
      toggleShowSearch(event,{show:true,url:'<?= html_entity_decode($filter_url);?>', result:$('#featured_filter_results'),form:$("#featured_filter_form")});
   }
   
   function check_scroll_bottom(event){
      trigger_offset = 20;
      scrollmax = $('#featured_filter_results .box-product').height() - $('#featured_filter_results .box-content').height();
      if((scrollmax - $(this).scrollTop()) < trigger_offset){
         $(this).unbind('scroll');
         $('#featured_page').val(parseInt($('#featured_page').val())+1);
         show_search(event);
      }
   }
   
   $(document).ready(function(){
      //Search Buttons
      $('#menu_categories li').hover(function(event){
         $(this).children('ul').each(function(i,e){
            animate = !$(e).is(':visible');
            $(e).show();
            if(animate){
               height = $(e).height();
               $(e).height(0);
               $(e).animate({height:height},{duration:100, complete:function(){$(this).css('height','auto');}});
            }
         });
      },function(event){if($(this).find('.active').length == 0)$(this).children('ul').hide();});
      
      $('.featured_menu_link').click(function(event){
         $('#' + $(this).attr('filter')).val($(this).attr('value'));
         $('#featured_page').val(1);
         $(this).closest('div').find('.active').removeClass('active');
         $(this).addClass('active');
         
         if($(this).attr('filter') == 'category_id'){
            $('.featured_menu_link + ul').hide();
            $(this).parents('ul').show();
            $(this).siblings('ul').each(function(i,e){
               animate = !$(e).is(':visible');
               $(e).show();
               if(animate){
                  height = $(e).height();
                  $(e).height(0);
                  $(e).animate({height:height},{duration:300, complete:function(){$(this).css('height','auto');}});
               }
            });
         }
         show_search(event);
      });
   
      $('#featured_filter_results .box-content').live('mousewheel',function(e, d){
         scroll_max = this.scrollHeight - $(this).height();
         if((this.scrollTop >= scroll_max && d < 0) || (this.scrollTop <= 0 && d > 0) || scroll_max < 1) {e.preventDefault();}
      });
   });
// -->
</script>
