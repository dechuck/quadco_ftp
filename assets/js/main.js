(function($){
	var tfToolTip = function(element, options){
		var settings = $.extend({}, $.fn.tfToolTip.defaults, options);
		var element = $(element);
		var template = settings.template.replace('{title}', (settings.title == '') ? element.attr('title') : settings.title);
		var tooltip = $(template);
		
		element.removeAttr('title');
		
		element.live('mouseenter', function(){
			
			var Offset = element.offset();
			var Height = element.outerHeight();
			var Width = element.outerWidth();
			
			tooltip.hide();
			tooltip.appendTo('body');
			tooltip.css({left: Offset.left + Width / 2 - tooltip.outerWidth() / 2, top: Offset.top - tooltip.outerHeight() - 10});
			tooltip.css({display: 'block', opacity: 0});
			tooltip.stop().animate({opacity: 1, top: Offset.top - tooltip.outerHeight()}, 200);
		});
		
		element.live('mouseleave', function(){
			tooltip.stop().fadeOut(200, function(){
				tooltip.remove();	
			});
		});
	};
	
	$.fn.tfToolTip = function(options){
        return this.each(function(key, value){
            var element = $(this);
			
            if (element.data('tftooltip')) return element.data('tftooltip');

            var tftooltip = new tfToolTip(this, options);

            element.data('tftooltip', tftooltip);
        });
	};
	
	$.fn.tfToolTip.defaults = {
		title: '',
		template: '<div class="tf-tooltip">{title}</div>'
	};

	$(document).ready(function(){
		$('.tftooltip').tfToolTip();
	});
})(jQuery);

$(document).ready(function(){
	$(document).click(function(){
		$('.drop-content .drop-body').slideUp(200, function(){
			$(this).parents('.drop-content').parent().find('.drop-content, .drop-arrow').hide();
		});
	});
	
	$('.item').click(function(event){
		if(!$(this).find('.drop-content, .drop-arrow').is(':visible')){
			$(this).find('.drop-content, .drop-arrow').show().find('.drop-body').hide().slideDown(200);
		} else {
			$(this).find('.drop-content .drop-body').slideUp(100, function(){
				$(this).parents('.drop-content').parent().find('.drop-content, .drop-arrow').hide();	
			});	
		}
		
		$('.drop-content .drop-body').not($(this).find('.drop-content .drop-body')).slideUp(100, function(){
			$(this).parents('.drop-content').parent().find('.drop-content, .drop-arrow').hide();	
		});
		
		event.stopPropagation();	
	});
	
	$('.item .drop-content').click(function(event){event.stopPropagation();});
	
	
	$('#menu .item').mouseenter(function(){
		$(this).find('.light div').stop().animate({'background-color': $('.theme-colors-1').css('backgroundColor')}, 300);
	}).mouseleave(function(){
		$(this).find('.light div').stop().animate({'background-color': '#4d4d4d'}, 400);
	});
	
	$('.nav > ul > li').mouseenter(function(){
		$(this).stop().animate({'background-color': '#4c4c4c'}, 300);
	}).mouseleave(function(){
		$(this).stop().animate({'background-color': $('.theme-colors-2').css('backgroundColor')}, 400);
	});
	
	$('.nav-phone').mouseenter(function(){
		$(this).find('div').stop().animate({'background-color': '#4c4c4c'}, 300);
	}).mouseleave(function(){
		$(this).find('div').stop().animate({'background-color': $('.theme-colors-2').css('backgroundColor')}, 400);
	});
	
	$('#cart').mouseenter(function(){
		$(this).find('.cart-total').stop().animate({'background-color': '#4c4c4c'}, 300);
	}).mouseleave(function(){
		$(this).find('.cart-total').stop().animate({'background-color': $('.theme-colors-1').css('backgroundColor')}, 400);
	});
	
	$('.nav > ul > li').hover(function(event){
		var maxWidth = $(this).parents('.nav').outerWidth();
		var menuLeft = $(this).position().left;
		
		$(this).find('.sub-content').css({
			left: -menuLeft,
			width: maxWidth
		});
		
		var menuWidth = $(this).find('.sub-content, .sub-arrow').show().find('.sub-body').css({left: 0}).outerWidth() + 1;
	
		if(maxWidth <= menuWidth){
			$(this).find('.sub-body').css({left: 0});	
		} else {
			$(this).find('.sub-body').css({
				left: (menuLeft + menuWidth > maxWidth) ? maxWidth - menuWidth : menuLeft
			});	
		}
	}, function(){
		$(this).find('.sub-content, .sub-arrow').hide();	
	});
	
	$('.grid .struct').mouseenter(function(){
		$(this).stop().animate({'background-color': '#e5f0ff'}, 300);
		$('.frame', this).stop().animate({'border-color': '#cce1ff'}, 300);
	}).mouseleave(function(){
		$(this).stop().animate({'background-color': '#fafafa'}, 400);
		$('.frame', this).stop().animate({'border-color': '#ebebeb'}, 400);
	});
	
	$('footer .column h2').click(function(){
		var Parent = $(this).parent();
		
		Parent.find('ul').slideToggle(300, function(){
			Parent.toggleClass('active').find('ul').removeAttr('style');
		});
	});
	
	$('.button').mouseenter(function(){
		$(this).stop().animate({'background-color': $('.theme-colors-2').css('backgroundColor')}, 300);
	}).mouseleave(function(){
		if($(this).hasClass('button-alt')){
			$(this).stop().animate({'background-color': $('.theme-colors-1').css('backgroundColor')}, 300);		
		} else {
			$(this).stop().animate({'background-color': '#fff'}, 300);	
		}
	});
	
	$('.social a').hover(function(){
		$(this).stop().animate({'opacity': '0.5'}, 300);		
	}, function(){
		$(this).stop().animate({'opacity': '1'}, 300);		
	});
});