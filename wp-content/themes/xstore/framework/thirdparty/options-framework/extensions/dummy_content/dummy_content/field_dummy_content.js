/* global confirm, redux, redux_change */

jQuery(document).ready(function($) {

	/****************************************************/
    /* Import XML data */
    /****************************************************/

	var importSection = $('.etheme-import-section'),
		loading = false,
		additionalSection = importSection.find('.import-additional-pages'),
		pagePreview = additionalSection.find('img').first(),
		pagesSelect = additionalSection.find('select'),
		pagesPreviewBtn = additionalSection.find('.preview-page-button'),
		importPageBtn = additionalSection.find('.et-button');

	pagesSelect.change(function() {
		var url = $(this).data('url'),
			version = $(this).find(":selected").val(),
			previewUrl = $(this).find(":selected").data('preview');

		pagePreview.attr('src', url + version + '/screenshot.jpg');
		importPageBtn.data('version', version);
		pagesPreviewBtn.attr('href', previewUrl);
	}).trigger('select');

	importSection.on('click', '.button-import-version', function(e) {
		e.preventDefault();

		var version = $(this).data('version');

		importVersion(version);
	});

	var importVersion = function(version) {
		if( loading ) return false;

		if(!confirm('Are you sure you want to install demo data? (It will change all your theme configuration, menu etc.)')) {
			return false;
		}

		$('html, body').animate({scrollTop:100}, 600);

		loading = true;
		importSection.addClass('import-process');

		importSection.find('.import-results').remove();

		var data = {
			action:'etheme_import_ajax',
			version: version,
			pageid: 0
		};

		$.ajax({
			method: "POST",
			url: ajaxurl,
			data: data,
			success: function(data){
				importSection.prepend('<div class="import-results etheme-options-success">' + data + '</div>');
				if( version == 'default' ) {
					importSection.removeClass('no-default-imported');
				}
				importSection.find('.version-preview-' + version).removeClass('not-imported').addClass('version-imported just-imported').find('.et-button').remove();
				setTimeout(function() {
					window.location.reload();
				}, 2000);
			},
			complete: function(){
				importSection.removeClass('import-process');
				loading = false;
			}
		});
	};

});