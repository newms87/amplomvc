/*!
 * Dan Newman's Carousel
 *
 * Date: 06 / 06 / 2012
 *
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.opensource.org/licenses/gpl-2.0.php
 *
 * Depends on library: jQuery
 */

(function ($) {
    $.dn = $.dn || { };

    $.dn.carousel = {
        options: {
            start: 1, // where should the carousel start?
            display: 1, // how many blocks do you want display at 1 time?
            scroll: 1, // how many blocks do you want to move at 1 time?
            axis: 'x', // vertical or horizontal scroller? ( x || y ).
            controls: true, // show left and right navigation buttons.
            pager: false, // is there a page number navigation present?
            interval: false, // move to another block on intervals.
            intervaltime: 3000, // interval time in milliseconds.
            rewind: false, // If interval is true and rewind is true it will play in reverse if the last slide is reached.
            animation: true, // false is instant, true is animate.
            duration: 1000, // how fast must the animation move in ms?
            callback: null, // function that executes after every move.
            page_padding: 0, //padding for the page on left/right side or top/bottom depending on axis
            page_spacing: 0, //margin between each page on the right/bottom
            view_width: 'auto', //width of the viewport
            view_height: 'auto', //height of the viewport
            page_width: 'auto', //width of each page
            page_height: 'auto', //height of each page
        }
    };

    $.fn.dncarousel = function (options) {
        var options = $.extend({}, $.dn.carousel.options, options);
        this.each(function () {
            $(this).data('dnc', new Carousel($(this), options));
        });
        return this;
    };

    $.fn.dncarousel_update = function () {
        $(this).data('dnc').update();
    };
    $.fn.dncarousel_start = function () {
        $(this).data('dnc').start();
    };
    $.fn.dncarousel_stop = function () {
        $(this).data('dnc').stop();
    };
    $.fn.dncarousel_move = function (iNum) {
        $(this).data('dnc').move(iNum - 1, true);
    };
    $.fn.dncarousel_last = function () {
        $(this).data('dnc').last();
    };


    function Carousel(my_carousel, options) {
        var oSelf = this;
        var oCarouselBox = $("<div class='dncarousel_box'></div>");
        var oContent = my_carousel;
        var oPages = oContent.children();
        var oViewport = $("<div class='viewport'></div>");
        var oBtnNext = my_carousel.find('.next');
        var oBtnPrev = my_carousel.find('.prev');
        var oPager = $("<div class='dn_carousel_pager'></div>");
        var iPageSize, iSteps, iCurrent, oTimer, bPause, bForward = true, axis = options.axis == 'x';

        function initialize() {
            construct_layout();
            var extra_space = options.display * (options.page_spacing + options.page_padding);

            //set viewport width
            if (options.view_width != 'auto')
                oViewport.width(options.view_width + axis ? extra_space : 0);

            //set viewport height
            if (options.view_height != 'auto')
                oViewport.height(options.view_height + (axis ? 0 : extra_space));

            //set page width
            if (options.page_width == 'auto') {
                var set_width = axis ? (oViewport.width() / options.display) - (((options.display - 1) / options.display) * options.page_spacing) - options.page_padding : oViewport.width();
                oPages.width(set_width);
            }
            else {
                oPages.width(options.page_width);
            }

            //set page height
            if (options.page_height == 'auto') {
                var set_height = axis ? oViewport.height() : (oViewport.height() / options.display) - options.page_spacing;
                //oPages.height(set_height);
            }
            else {
                oPages.height(options.page_height);
            }

            //set page spacing
            if (axis) {
                oPages.css('margin-left', options.page_spacing);
                oPages.css('padding-left', options.page_padding);
            } else {
                oPages.css('overflow', 'hidden');
                oPages.css('margin-top', options.page_spacing / 2);
                oPages.css('padding-top', options.page_padding / 2);
            }

            iPageSize = axis ? oPages.outerWidth(true) : oPages.outerHeight(true);
            iSteps = (oPages.length - options.display) / options.scroll;
            iCurrent = 0;
            oContent.css(axis ? 'width' : 'height', (iPageSize * oPages.length) + options.page_spacing);
            setEvents();
            setButtons();
            return oSelf;
        };

        function construct_layout() {
            //construct layout
            my_carousel.before(oCarouselBox);

            oViewport.append(my_carousel);
            oCarouselBox.append(oViewport);

            //setup the buttons
            if (options.controls) {
                if (oBtnNext.length < 1) {
                    oBtnNext = $("<a href='#' class='buttons next'></a>");
                    oViewport.after(oBtnNext);
                }
                if (oBtnPrev.length < 1) {
                    oBtnPrev = $("<a href='#' class='buttons prev'></a>");
                    oViewport.before(oBtnPrev);
                }
            }

            if (axis) {
                oPages.css('float', 'left');
            }
            else {
            }

        }

        function setEvents() {
            if (options.controls && oBtnPrev.length > 0 && oBtnNext.length > 0) {
                oBtnPrev.click(function () {
                    oSelf.move(-1);
                    return false;
                });
                oBtnNext.click(function () {
                    oSelf.move(1);
                    return false;
                });
            }
            if (options.interval) {
                oViewport.hover(oSelf.stop, oSelf.start);
            }
            if (options.pager && oPager.length > 0) {
                $('a', oPager).click(setPager);
            }
        };
        function setButtons() {
            if (options.controls) {

                oBtnPrev.toggleClass('disable', !(iCurrent > 0));
                oBtnNext.toggleClass('disable', !(iCurrent + 1 <= iSteps));
            }
            if (options.pager) {
                var oNumbers = $('.pagenum', oPager);
                oNumbers.removeClass('active');
                $(oNumbers[iCurrent]).addClass('active');
            }
        };
        function setPager(oEvent) {
            if ($(this).hasClass('pagenum')) {
                oSelf.move(parseInt(this.rel), true);
            }
            return false;
        };
        function setTimer() {
            if (options.interval && !bPause) {
                clearTimeout(oTimer);
                oTimer = setTimeout(function () {
                    iCurrent = iCurrent == iSteps ? -1 : iCurrent;
                    //bForward = iCurrent +1 == iSteps ? false : (iCurrent == 0 ? true : bForward);
                    oSelf.move(1);//bForward ? 1 : -1);
                }, options.intervaltime);
            }
        };

        this.update = function () {
            oPages = oContent.children();
            iSteps = (oPages.length - options.display) / options.scroll;
            oContent.css(axis ? 'width' : 'height', (iPageSize * oPages.length) + options.page_spacing);
            setButtons();
        };
        this.current_page = $(oPages[0]);
        this.stop = function () {
            clearTimeout(oTimer);
            bPause = true;
        };
        this.start = function () {
            bPause = false;
            setTimer();
        };
        this.last = function () {
            this.move(iSteps, true);
        }
        this.move = function (iDirection, bPageNum) {
            iCurrent = bPageNum ? iDirection : iCurrent += iDirection;
            if (iCurrent > -1 && iCurrent <= iSteps) {
                var oPosition = {};
                oPosition[axis ? 'left' : 'top'] = -(iCurrent * (iPageSize * options.scroll));
                oContent.animate(oPosition, {
                    queue: false,
                    duration: options.animation ? options.duration : 0,
                    complete: function () {
                        if (typeof options.callback == 'function')
                            options.callback.call(this, $(oPages[iCurrent]), iCurrent);
                    }
                });
                oSelf.current_page = $(oPages[iCurrent]);
                setButtons();
                setTimer();
            }
        };
        return initialize();
    };
})(jQuery);

/***************************************** END ******************************************************************/