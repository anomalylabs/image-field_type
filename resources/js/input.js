// Initialize file pickers
$('.image-field_type').each(function () {

    var data = null;
    var wrapper = $(this);
    var field = wrapper.data('field');
    var modal = $('#' + field + '-modal');
    var image = wrapper.find('[data-provides="cropper"]');

    var options = {
        data: image.data('data'),
        modal: image.data('modal'),
        guides: image.data('guides'),
        movable: image.data('movable'),
        scalable: image.data('scalable'),
        zoomable: image.data('zoomable'),
        dragMode: image.data('drag-mode'),
        viewMode: image.data('view-mode'),
        rotatable: image.data('rotatable'),
        highlight: image.data('highlight'),
        aspectRatio: image.data('aspect-ratio'),
        autoCropArea: image.data('auto-crop-area'),
        minContainerHeight: image.data('min-container-height'),
        crop: function (e) {
            $('[name="' + field + '[data]"]').val(JSON.stringify(e));
        }
    };

    if (image.closest('.tab-content').length) {
        options.minContainerWidth = image.closest('.tab-content').width();
    }

    image.cropper(options);

    modal.on('click', '[data-file]', function (e) {

        e.preventDefault();

        modal.find('.modal-content').append('<div class="modal-loading"><div class="active loader"></div></div>');

        wrapper.find('.selected').load(APPLICATION_URL + '/streams/image-field_type/selected?uploaded=' + $(this).data('file'), function () {

            modal.modal('hide');

            image.next('.cropper-container').removeClass('hidden');
        });

        image
            .cropper('replace', APPLICATION_URL + '/streams/image-field_type/view/' + $(this).data('file'))
            .cropper('reset');

        $('[name="' + field + '[id]"]').val($(this).data('file'));
    });

    $(wrapper).on('click', '[data-dismiss="file"]', function (e) {

        e.preventDefault();

        $('[name="' + field + '[id]"]').val('');
        $('[name="' + field + '[data]"]').val('');

        wrapper.find('.selected').load(APPLICATION_URL + '/streams/image-field_type/selected', function () {

            modal.modal('hide');

            image.next('.cropper-container').addClass('hidden');
        });
    });
});
