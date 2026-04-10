
var Merlin = (function($){

    var t;

    // callbacks from form button clicks.
    var callbacks = {
        install_child: function(btn) {
            var installer = new ChildTheme();
            installer.init(btn);
        },
        activate_license: function(btn) {
            var license = new ActivateLicense();
            license.init(btn);
        },
        install_plugins: function(btn){
            var plugins = new PluginManager();
            plugins.init(btn);
        },
        install_content: function(btn){
            var content = new ContentManager();
            content.init(btn);
        }
    };

    function window_loaded(){
    	var
    	body 		= $('.merlin__body'),
    	body_loading 	= $('.merlin__body--loading'),
    	body_exiting 	= $('.merlin__body--exiting'),
    	drawer_trigger 	= $('#merlin__drawer-trigger'),
    	drawer_opening 	= 'merlin__drawer--opening';
    	drawer_opened 	= 'merlin__drawer--open';

    	setTimeout(function(){
	        body.addClass('loaded');
	    },100);

    	drawer_trigger.on('click', function(){
        	body.toggleClass( drawer_opened );
        });

    	$('.merlin__button--proceed:not(.merlin__button--closer)').on( 'click', function (e) {
		    e.preventDefault();
		    var goTo = this.getAttribute("href");

		    body.addClass('exiting');

		    setTimeout(function(){
		        window.location = goTo;
		    },400);
		} );

        $(".merlin__button--closer").on('click', function(e){

        	body.removeClass( drawer_opened );

            e.preventDefault();
		    var goTo = this.getAttribute("href");

		    setTimeout(function(){
		        body.addClass('exiting');
		    },600);

		    setTimeout(function(){
		        window.location = goTo;
		    },1100);
        });

        $(".button-next").on( "click", function(e) {
            e.preventDefault();
            var loading_button = merlin_loading_button(this);
            if ( ! loading_button ) {
                return false;
            }
            var data_callback = $(this).data("callback");
            if( data_callback && typeof callbacks[data_callback] !== "undefined"){
                // We have to process a callback before continue with form submission.
                callbacks[data_callback](this);
                return false;
            } else {
                return true;
            }
        });

		$( document ).on( 'change', '.js-merlin-demo-import-select', function() {
			var selectedIndex  = $( this ).val();

			$( '.js-merlin-select-spinner' ).show();

			$.post( merlin_params.ajaxurl, {
				action: 'merlin_update_selected_import_data_info',
				wpnonce: merlin_params.wpnonce,
				selected_index: selectedIndex,
			}, function( response ) {
				if ( response.success ) {
					$( '.js-merlin-drawer-import-content' ).html( response.data );
				}
				else {
					alert( merlin_params.texts.something_went_wrong );
				}

				$( '.js-merlin-select-spinner' ).hide();
			} )
				.fail( function() {
					$( '.js-merlin-select-spinner' ).hide();
					alert( merlin_params.texts.something_went_wrong )
				} );
		} );

		// Cmsmasters Activate License
		$( 'input[name=cmsmasters_merlin_license__source_code]' ).on( 'change', function() {
			const value = $( this ).val();

			$( '.cmsmasters-merlin-license__code' ).slideUp( 'fast' );
			$( `.cmsmasters-merlin-license__code.cmsmasters-merlin-license--${value}` ).slideDown( 'fast' );
		} );
    }

    function ChildTheme() {
    	var body 				= $('.merlin__body');
        var complete, notice 	= $("#child-theme-text");

        function ajax_callback(r) {

            if (typeof r.done !== "undefined") {
            	setTimeout(function(){
			        notice.addClass("lead");
			    },0);
			    setTimeout(function(){
			        notice.addClass("success");
			        notice.html(r.message);
			    },600);


                complete();
            } else {
                notice.addClass("lead error");
                notice.html(r.error);
            }
        }

        function do_ajax() {
            jQuery.post(merlin_params.ajaxurl, {
                action: "merlin_child_theme",
                wpnonce: merlin_params.wpnonce,
            }, ajax_callback).fail(ajax_callback);
        }

        return {
            init: function(btn) {
                complete = function() {

                	setTimeout(function(){
				$(".merlin__body").addClass('js--finished');
			},1500);

                	body.removeClass( drawer_opened );

                	setTimeout(function(){
				$('.merlin__body').addClass('exiting');
			},3500);

                    	setTimeout(function(){
				window.location.href=btn.href;
			},4000);

                };
                do_ajax();
            }
        }
    }

	// Cmsmasters Activate License
	function ActivateLicense() {
		const body = $( '.merlin__body' ),
			wrapper = $( '.cmsmasters-merlin-license' ),
			notice = $( '.cmsmasters-merlin-license__notice' );

		let complete;

		function ajax_callback( response ) {
			if ( typeof response.success !== 'undefined' && response.success ) {
				setTimeout( function() {
					notice.addClass( 'lead' );
				}, 0 );

				setTimeout( function() {
					notice.addClass( 'success' );
					notice.html( response.message );
				}, 600 );

				complete();
			} else {
				$( '.js-merlin-license-activate-button' ).removeClass( 'merlin__button--loading' ).data( 'done-loading', 'no' );

				wrapper.addClass( 'has-error' );

				if ( typeof response.error_field !== 'undefined' ) {
					wrapper.attr( 'data-error-field', response.error_field );
				}
				
				notice.addClass( 'lead error' ).html( response.message );
			}
		}

		function do_ajax() {
			jQuery.post( merlin_params.ajaxurl, {
				action: 'merlin_activate_license',
				wpnonce: merlin_params.wpnonce,
				user_name: $( 'input[name=cmsmasters_merlin_license__user_name]' ).val(),
				user_email: $( 'input[name=cmsmasters_merlin_license__user_email]' ).val(),
				source_code: $( 'input[name=cmsmasters_merlin_license__source_code]:checked' ).val(),
				purchase_code: $( 'input[name=cmsmasters_merlin_license__purchase_code]' ).val(),
				envato_elements_token: $( 'input[name=cmsmasters_merlin_license__envato_elements_token]' ).val(),
			}, ajax_callback ).fail( ajax_callback );
		}

		return {
			init: function( btn ) {
				wrapper.removeClass( 'has-error' ).removeAttr( 'data-error-field' );
				notice.removeClass('lead error');

				complete = function() {
					setTimeout( function() {
						$( '.merlin__body' ).addClass( 'js--finished' );
					}, 1500 );

					body.removeClass( drawer_opened );

					setTimeout( function() {
						$( '.merlin__body' ).addClass( 'exiting' );
					}, 3500);

					setTimeout( function() {
						window.location.href = btn.href;
					}, 4000 );
				};

				do_ajax();
			}
		}
	}

	function PluginManager(){

    	var body = $('.merlin__body');
        var complete;
        var items_completed 	= 0;
        var current_item 		= "";
        var $current_node;
        var current_item_hash 	= "";

        function ajax_callback(response){
            var currentSpan = $current_node.find("label");
            if(typeof response === "object" && typeof response.message !== "undefined"){
                currentSpan.removeClass( 'installing success error' ).addClass(response.message.toLowerCase());

                // The plugin is done (installed, updated and activated).
                if(typeof response.done != "undefined" && response.done){
                    find_next();
                }else if(typeof response.url != "undefined"){
                    // we have an ajax url action to perform.
                    if(response.hash == current_item_hash){
                        currentSpan.removeClass( 'installing success' ).addClass("error");
                        find_next();
                    }else {
                        current_item_hash = response.hash;
                        jQuery.post(response.url, response, ajax_callback).fail(ajax_callback);
                    }
                }else{
                    // error processing this plugin
                    find_next();
                }
            }else{
                // The TGMPA returns a whole page as response, so check, if this plugin is done.
                process_current();
            }
        }

        function process_current(){
            if(current_item){
                var $check = $current_node.find("input:checkbox");
                if($check.is(":checked")) {
                    jQuery.post(merlin_params.ajaxurl, {
                        action: "merlin_plugins",
                        wpnonce: merlin_params.wpnonce,
                        slug: current_item,
                    }, ajax_callback).fail(ajax_callback);
                }else{
                    $current_node.addClass("skipping");
                    setTimeout(find_next,300);
                }
            }
        }

        function find_next(){
            if($current_node){
                if(!$current_node.data("done_item")){
                    items_completed++;
                    $current_node.data("done_item",1);
                }
                $current_node.find(".spinner").css("visibility","hidden");
            }
            var $li = $(".merlin__drawer--install-plugins li");
            $li.each(function(){
                var $item = $(this);

                if ( $item.data("done_item") ) {
                    return true;
                }

                current_item = $item.data("slug");
                $current_node = $item;
                process_current();
                return false;
            });
            if(items_completed >= $li.length){
                // finished all plugins!
                complete();
            }
        }

        return {
            init: function(btn){
                $(".merlin__drawer--install-plugins").addClass("installing");
                $(".merlin__drawer--install-plugins").find("input").prop("disabled", true);
                complete = function(){

                	setTimeout(function(){
				        $(".merlin__body").addClass('js--finished');
				    },1000);

                	body.removeClass( drawer_opened );

                	setTimeout(function(){
				        $('.merlin__body').addClass('exiting');
				    },3000);

                    setTimeout(function(){
				        window.location.href=btn.href;
				    },3500);

                };
                find_next();
            }
        }
    }
    function ContentManager(){

    	var body 				= $('.merlin__body');
        var complete;
        var items_completed 	= 0;
        var current_item 		= "";
        var $current_node;
        var current_item_hash 	= "";
        var current_content_import_items = 1;
        var total_content_import_items = 0;
        var progress_bar_interval;

        function ajax_callback(response) {
            var currentSpan = $current_node.find("label");
            if(typeof response == "object" && typeof response.message !== "undefined"){
                currentSpan.addClass(response.message.toLowerCase());

                if( typeof response.num_of_imported_posts !== "undefined" && 0 < total_content_import_items ) {
                    current_content_import_items = 'all' === response.num_of_imported_posts ? total_content_import_items : response.num_of_imported_posts;
                    update_progress_bar();
                }

                if(typeof response.url !== "undefined"){
                    // we have an ajax url action to perform.
                    if(response.hash === current_item_hash){
                        currentSpan.addClass("status--failed");
                        find_next();
                    }else {
                        current_item_hash = response.hash;

                        // Fix the undefined selected_index issue on new AJAX calls.
                        if ( typeof response.selected_index === "undefined" ) {
                            response.selected_index = $( '.js-merlin-demo-import-select' ).val() || 0;
                        }

                        jQuery.post(response.url, response, ajax_callback).fail(ajax_callback); // recuurrssionnnnn
                    }
                }else if(typeof response.done !== "undefined"){
                    // finished processing this plugin, move onto next
                    find_next();
                }else{
                    // error processing this plugin
                    find_next();
                }
            }else{
                console.log(response);
                // error - try again with next plugin
                currentSpan.addClass("status--error");
                find_next();
            }
        }

        function process_current(){
            if(current_item){
                var $check = $current_node.find("input:checkbox");
                if($check.is(":checked")) {
                    jQuery.post(merlin_params.ajaxurl, {
                        action: "merlin_content",
                        wpnonce: merlin_params.wpnonce,
                        content: current_item,
                        selected_index: $( '.js-merlin-demo-import-select' ).val() || 0
                    }, ajax_callback).fail(ajax_callback);
                }else{
                    $current_node.addClass("skipping");
                    setTimeout(find_next,300);
                }
            }
        }

        function find_next(){
            var do_next = false;
            if($current_node){
                if(!$current_node.data("done_item")){
                    items_completed++;
                    $current_node.data("done_item",1);
                }
                $current_node.find(".spinner").css("visibility","hidden");
            }
            var $items = $(".merlin__drawer--import-content__list-item");
            var $enabled_items = $(".merlin__drawer--import-content__list-item input:checked");
            $items.each(function(){
                if (current_item == "" || do_next) {
                    current_item = $(this).data("content");
                    $current_node = $(this);
                    process_current();
                    do_next = false;
                } else if ($(this).data("content") == current_item) {
                    do_next = true;
                }
            });
            if(items_completed >= $items.length){
                complete();
            }
        }

        function init_content_import_progress_bar() {
            if( ! $(".merlin__drawer--import-content__list-item .checkbox-content").is( ':checked' ) ) {
                return false;
            }

            jQuery.post(merlin_params.ajaxurl, {
                action: "merlin_get_total_content_import_items",
                wpnonce: merlin_params.wpnonce,
                selected_index: $( '.js-merlin-demo-import-select' ).val() || 0
            }, function( response ) {
                total_content_import_items = response.data;

                if ( 0 < total_content_import_items ) {
                    update_progress_bar();

                    // Change the value of the progress bar constantly for a small amount (0,2% per sec), to improve UX.
                    progress_bar_interval = setInterval( function() {
                        current_content_import_items = current_content_import_items + total_content_import_items/500;
                        update_progress_bar();
                    }, 1000 );
                }
            } );
        }

        function valBetween(v, min, max) {
            return (Math.min(max, Math.max(min, v)));
        }

        function update_progress_bar() {
			const percentage_old_number = parseInt( $('.js-merlin-progress-bar-percentage').html() );
			let percentage_new_number = (current_content_import_items/total_content_import_items) * 100;

			if ( percentage_old_number > percentage_new_number ) {
				percentage_new_number = percentage_old_number
			}

            $('.js-merlin-progress-bar').css( 'width', percentage_new_number + '%' );

            var $percentage = valBetween( percentage_new_number, 0, 99 );

            $('.js-merlin-progress-bar-percentage').html( Math.round( $percentage ) + '%' );

            if ( 1 === current_content_import_items/total_content_import_items ) {
                clearInterval( progress_bar_interval );
            }
        }

        return {
            init: function(btn){
                $(".merlin__drawer--import-content").addClass("installing");
                $(".merlin__drawer--import-content").find("input").prop("disabled", true);
                complete = function() {
					$.post(merlin_params.ajaxurl, {
						action: "merlin_import_finished",
						wpnonce: merlin_params.wpnonce,
						selected_index: $( '.js-merlin-demo-import-select' ).val() || 0
					});

					setTimeout(function(){
						$('.js-merlin-progress-bar-percentage').html( '100%' );
					},100);

                	setTimeout(function(){
				       body.removeClass( drawer_opened );
				    },500);

                	setTimeout(function(){
				        $(".merlin__body").addClass('js--finished');
				    },1500);

                	setTimeout(function(){
				        $('.merlin__body').addClass('exiting');
				    },3400);

                    setTimeout(function(){
				        window.location.href=btn.href;
				    },4000);
                };

                init_content_import_progress_bar();

                find_next();
            }
        }
    }

    function merlin_loading_button( btn ){

        var $button = jQuery(btn);

        if ( $button.data( "done-loading" ) == "yes" ) {
        	return false;
        }

        var completed = false;

        var _modifier = $button.is("input") || $button.is("button") ? "val" : "text";

        $button.data("done-loading","yes");

        $button.addClass("merlin__button--loading");

        return {
            done: function(){
                completed = true;
                $button.attr("disabled",false);
            }
        }

    }

    return {
        init: function(){
            t = this;
            $(window_loaded);
        },
        callback: function(func){
            console.log(func);
            console.log(this);
        }
    }

})(jQuery);

Merlin.init();
