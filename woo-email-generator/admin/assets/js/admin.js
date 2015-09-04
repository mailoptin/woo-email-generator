(function ( $ ) {
	"use strict";

	$(function () {

		/* Handle Drag'n'Drop */
		$( "#wooemail-products .wooemail-product-item" ).draggable({
			appendTo: "body",
			helper: "clone"
		});
		$( "#wooemail-product-container" ).droppable({
			activeClass: "ui-state-default",
			hoverClass: "ui-state-hover",
			accept: ":not(.ui-sortable-helper)",
			drop: function( event, ui ) {
				$( this ).find( ".template-placeholder" ).remove();
				$( "<div class='wooemail-item-placeholder'></div>" ).html( ui.draggable.html() ).appendTo( this );
			}
		}).sortable({
			items: "div.wooemail-item-placeholder:not(.template-placeholder)",
			sort: function() {
				// gets added unintentionally by droppable interacting with sortable
				// using connectWithSortable fixes this, but doesn't allow you to customize active/hoverClass options
				$( this ).removeClass( "ui-state-default" );
			},
			out: function (event, ui) {
				var self = ui;
				ui.helper.off('mouseup').on('mouseup', function () {
					$(this).remove();
					self.draggable.remove();
				});
			}
		});

		/* END DRAG'N'DROP*/

		/* AJAX Search Products */
		$('#wooemail-form-search').on('submit', function () {
			var search=$('#wooemail-search').val();
			$('#wooemail-btn-search').attr('disabled', true);
			$('#wooemail-btn-search').val('Please wait..');

			$('#wooemail-products').empty().append('<h3>Processing your request..</h3>');

			$.ajax({
				data: {
					search:search,
					action: 'wooemail_ajax_search',
					nonce: wooEmail.nonce_search
				},
				type: 'post',
				dataType: 'html',
				url:wooEmail.ajaxURL,
				success: function(response) {
					console.log(response);
					$('#wooemail-products').empty().append(response);
					$( "#wooemail-products .wooemail-product-item" ).draggable({
						appendTo: "body",
						helper: "clone"
					});

					$('#wooemail-btn-search').attr('disabled', false);
					$('#wooemail-btn-search').val('Search');
				},
				error   : function( xhr, err ) {
					// Log errors if AJAX call is failed
					//	console.log(xhr);
					//	console.log(err);
				}
			});

			return false;
		});
		/* END AJAX Search Products */

		/* Generate HTML */
		$('#btn-generate-html').on('click', function () {

			var productContainer=$('#wooemail-product-container');
			var popupContainer=$('#popup-generated-html');
			var productList=[];
			var optionsVAT=$('#wooemail-options-vat').is(':checked')?1:0;
			var optionsBulkPrices=$('#wooemail-options-bulk').is(':checked')?1:0;

			//Get products IDs
			productContainer.children('.wooemail-item-placeholder').each(function(){
				productList.push($('.product-info',this).data('product-id'));
			});

			if(productList.length < 1){
				alert('Please, choose products for HTML-template');
				return false;
			}

			$('#btn-generate-html').attr('disabled', true);
			$('#btn-generate-html').val('Please wait..');

			$.ajax({
				data: {
					products:productList,
					optionsVAT:optionsVAT,
					optionsBulkPrices:optionsBulkPrices,
					action: 'wooemail_ajax_generate_html',
					nonce: wooEmail.nonce_generate_html
				},
				type: 'post',
				dataType: 'html',
				url:wooEmail.ajaxURL,
				success: function(response) {
					$('.generated-html',popupContainer).val(response);
					tb_show('HTML-Email Template', '#TB_inline?height=480&width=620&inlineId=popup-generated-html', false);
					$("#btn-generate-html").attr("disabled", false);
					$("#btn-generate-html").val('Generate HTML');
				},
				error   : function( xhr, err ) {
					// Log errors if AJAX call is failed
					//	console.log(xhr);
					//	console.log(err);
				}
			});

			return false;
		});
		/* END Generate HTML */

		/* Handle Preview Generated HTML */
		$('#wooemail-btn-preview-html').on('click', function () {
			var w = window.open(null, 'Preview HTML', 'width=700,height=500,resizeable,scrollbars');
			w.document.write($('#TB_ajaxContent textarea.generated-html').val());
			w.document.close(); // needed for chrome and safari
		});


	});

}(jQuery));