$(document).on('ajaxComplete ready', function () {

    // Initialize file pickers
    $('[data-provides="anomaly.field_type.image"]:not([data-initialized])').each(function () {

        $(this).attr('data-initialized', '');

        let input = $(this);
        let field = input.data('field_name');
        let modal = $('#' + field + '-modal');
        let wrapper = input.closest('.form-group');
        let image = wrapper.find('[data-provides="cropper"]');
        let toggle = wrapper.find('[data-toggle="cropper"]');

        let options = {
            viewMode: 0,
            autoCrop: true,
            zoomable: false,
            autoCropArea: 1,
            responsive: true,
            checkOrientation: false,
            data: image.data('data'),
            aspectRatio: image.data('aspect-ratio'),
            minContainerHeight: image.data('min-container-height'),
            crop: function (e) {

                /**
                 * This prevents trashy data from
                 * being parsed into the field value.
                 */
                if (!isFinite(e.x) || isNaN(e.x) || typeof e.x == 'undefined' || e.x == null) {
                    return;
                }

                $('[name="' + field + '[data]"]').val(JSON.stringify({
                    'x': e.x,
                    'y': e.y,
                    'width': e.width,
                    'height': e.height,
                    'rotate': e.rotate,
                    'scaleX': e.scaleX,
                    'scaleY': e.scaleY
                }));
            }
        };

        toggle.on('click', function () {

            image.closest('.cropper').removeClass('hidden');
            image.cropper(options);

            return false;
        });

        modal.on('click', '[data-file]', function (e) {

            e.preventDefault();

            modal.find('.modal-content').append('<div class="modal-loading"><div class="active loader"></div></div>');

            wrapper.find('.selected').load('/streams/image-field_type/selected?uploaded=' + $(this).data('file'), function () {
                modal.modal('hide');
            });

            image.closest('.cropper').removeClass('hidden');

            image
                .cropper(options)
                .cropper('replace', '/streams/image-field_type/view/' + $(this).data('file'))
                .cropper('reset');

            $('[name="' + field + '[id]"]').val($(this).data('file'));
        });

        $(wrapper).on('click', '[data-dismiss="file"]', function (e) {

            e.preventDefault();

            $('[name="' + field + '[id]"]').val('');
            $('[name="' + field + '[data]"]').val('');

            wrapper.find('.selected').load('/streams/image-field_type/selected', function () {

                modal.modal('hide');

                image.closest('.cropper').addClass('hidden');
            });
        });
    });
});
