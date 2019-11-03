/*
 * Version    : 3.2.6
 * Updated    : 2016-07-29 17:12:30 UTC+02:08
 * Developer  : Mark
 * Dependency : 
 * Lib        : jQuery 1.7+
 */



// notice this is a NON working plugin, please do NOT use it!
// this file is used as a code example noting more!

;(function(root, factory){
	
	if(typeof define === 'function' && define.amd){
		define(['jquery'], factory);
	}else{
		factory(root.jQuery);
	}
	
}(this, function($){		  
	
	//"use strict"; // jshint ;_;
	
	var pluginName = 'xxx';
	
	function Plugin(element, options){
		
		/**
		* Variables.
		**/	
		this.obj = $(element);		
		this.o   = $.extend({}, $.fn[pluginName].defaults, options);

		this.init();
	};

	Plugin.prototype = {
								
		/**	
		* INIT.
		**/	
		init: function(){
			
			var self   = this;
			
			/**
			* Global variables.
			**/	
			bd = $('body');

			/**
			* Check for CSS3 animation browser support.
			* http://stackoverflow.com/questions/7264899/detect-css-transitions-using-javascript-and-without-modernizr/13081497#13081497
			**/
			var supportsTransitions = (function() {
				var s = document.createElement('p').style,
					v = ['ms','O','Moz','Webkit'];
				if(s['transition'] == '') return true;
				while(v.length)
					if(v.pop() + 'Transition' in s)
						return true;
				return false;
			})();
			if(!supportsTransitions){
				bd.attr(d_pwfx, false);
			}
			
			/**	
			* Append/prepend the mask to the body. 
			**/
			if($('#'+c_pwmk).length < 1){
					
				var msk = '<div id="'+c_pwmk+'"></div>';
						
				if(self.o.appendMask != undefined && self.o.appendMask === false){
					bd.prepend(msk);	
				}else{
					bd.append(msk);	
				}				
			}
			
			/**	
			* All methods that need to run at init. 
			**/
			self._build();
			self._clickEvents();
			self._keyboardEvents();
			self._runOnStart();

		},		


		_build: function(){ 
		
			var self = this;
			
			/**
			* Create all stuff with a loop.
			**/	
			$.each(self.o.x, function(i, t){
				
				var sw  = $(t.trigger).attr(d_pwsw);
				var i   = parseInt(i + 1);
				
				/**
				* Use option or pre set value.
				**/	
				if(isNaN(sw)){
					var startWith = (t.startWith !== undefined && $.trim(t.startWith) != '' && t.startWith != '0') ? t.startWith : 1;	
				}else{
					var startWith = sw;	
				}

				/**
				* Set trigger datasets.
				**/				
				$(t.trigger).attr({
					'data-startwith' : startWith,
					'data-trigger'   : i
				})
				.addClass(c_pwdc);				
				
				/**
				* Build every single part.
				**/	
				$.each(t.steps, function(ii, s){				
					
					var ii = parseInt(ii + 1);
					
					/**
					* Setting the position.
					**/	
					self._setPosition(s, i, ii, t, true);

				});	
			});		
		},

		/**
		* SET POSITION.
		*
		* Step the position of each element.
		*
		* @param: s  | string  | .
		* @param: i  | integer | .
		* @param: ii | integer | .
		* @param: t  | object  | .
		* @param: b  | boolean | Prevent second build if called.
		**/
		_setPosition: function(s, i, ii, t, b){
			
			var self   = this;
			var stepId = '['+d_pwid+'="'+i+'"]['+d_pwst+'="'+ii+'"]';
			
			if(typeof t.stepDefaults == 'object'){
				var def = t.stepDefaults[0];
			}else{
				var def = 'undefined';
			}

			/**
			* If value is option is not present use stepdefault value(s) if these are present if not use default values.
			**/		
			
			var defWidth     = (def.width     !== undefined && $.trim(def.width)     != '') ? def.width     : 300;
			var defPosition  = (def.position  !== undefined && $.trim(def.position)  != '') ? def.position  : 'tl';
			var defY         = (def.offsetY   !== undefined && $.trim(def.offsetY)   != '') ? def.offsetY   : 0;
			var defX         = (def.offsetX   !== undefined && $.trim(def.offsetX)   != '') ? def.offsetX   : 0;
			var defHighlight = (def.highlight !== undefined && $.trim(def.highlight) != '') ? def.highlight : false;
				
		    var width     = (s.width     !== undefined && $.trim(s.width)     != '') ? s.width     : defWidth;
			var position  = (s.position  !== undefined && $.trim(s.position)  != '') ? s.position  : defPosition;
			var Y         = (s.offsetY   !== undefined && $.trim(s.offsetY)   != '') ? s.offsetY   : defY;
			var X         = (s.offsetX   !== undefined && $.trim(s.offsetX)   != '') ? s.offsetX   : defX;
			var highLight = (s.highlight !== undefined && $.trim(s.highlight) != '') ? s.highlight : defHighlight;
			 
			/**
			* Check for screen postions, they should 
			* not use an hook, so append it to the body instead.
			**/					
			if($.inArray(position, screenPos) == -1){
					
				var hook = $(s.hookTo);
					
				/**
				* Add a relitive class for the highlight function.
				**/	
				hook.addClass(c_pwhk);
					
			}else{

				/**
				* Attach to body(dummy).
				**/
				var hook = bd;								
			}


			
		},
		
		/**
		* UPDATE
		**/
		update: function(hook){ 
		
			var self = this; 
			
			/**
			* If not array make array.
			**/	
			if(!$.isArray(hook)){
				var hook = [hook];
			}			
	
			/**
			* Update every given step.
			**/	
			$.each(hook, function(i, h){

				var ti  = $(h).parent().attr(d_pwid);
				var si  = $(h).parent().attr(d_pwst);			
			
				var oti = (ti == 0) ? 0 : ti -1;
				var osi = (si == 0) ? 0 : si -1;
				
				var h   = self.o.tours[oti];				
				var s   = h.steps[osi];	

				/**
				* Setting the position.
				**/	
				self._setPosition(s, ti, si, h, false);	
			});
		
		},

	}

	/**
	* Default settings(dont change).
	* You can globally override these options
	* by using $.fn.pluginName.key = 'value';
	**/
	$.fn[pluginName].defaults = {
		
	};
	
	// Call directly from jQuery on 'body'
	$[pluginName] = function() {
		var $body = $(document.body);
		$body[pluginName].apply($body, arguments);
	}
			
}));