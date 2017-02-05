$(document).on('ajaxComplete ready', function () {

    // Initialize file pickers
    $('[data-provides="anomaly.field_type.image"]:not([data-initialized])').each(function () {

        $(this).attr('data-initialized', '');

        var input = $(this);
        var field = input.data('field_name');
        var modal = $('#' + field + '-modal');
        var wrapper = input.closest('.form-group');
        var image = wrapper.find('[data-provides="cropper"]');

        console.log(image.data('data'));

        var options = {
            viewMode: 0,
            zoomable: false,
            autoCropArea: 1,
            data: image.data('data'),
            aspectRatio: image.data('aspect-ratio'),
            minContainerHeight: image.data('min-container-height'),
            build: function () {
                if (image.attr('src').length) {
                    image.closest('.cropper').removeClass('hidden');
                }
            },
            crop: function (e) {
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

        if (image.closest('.tab-content').length) {
            options.minContainerWidth = image.closest('.tab-content').width();
        }

        if (image.closest('.grid-body').length) {
            options.minContainerWidth = image.closest('.grid-body').width();
        }

        image.cropper(options);

        modal.on('click', '[data-file]', function (e) {

            e.preventDefault();

            modal.find('.modal-content').append('<div class="modal-loading"><div class="active loader"></div></div>');

            wrapper.find('.selected').load('/streams/image-field_type/selected?uploaded=' + $(this).data('file'), function () {
                modal.modal('hide');
            });

            image.closest('.cropper').removeClass('hidden');

            image
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
