(function (window, document) {

    let fields = Array.prototype.slice.call(
        document.querySelectorAll('[data-provides="anomaly.field_type.image"]')
    );

    fields.forEach(function (field) {

        let container = field.parentElement.querySelector('.cropper__container');
        let input = field.parentElement.querySelector('input[type="hidden"]');
        let image = field.parentElement.querySelector('[data-provides="cropper"]');
        let toggle = field.parentElement.querySelector('[data-toggle="cropper"]');
        let modal = field.parentElement.querySelector('.modal');

        let value = JSON.parse(image.getAttribute('data-data'));

        let options = {
            left: value.x,
            top : value.y,
            width : value.width,
            height : value.height,
            aspectRatio: Number(image.getAttribute('data-aspect-ratio')),
            onCropEnd: function (data) {

                /**
                 * This prevents trashy data from
                 * being parsed into the field value.
                 */
                // if (!isFinite(data.x) || isNaN(data.x) || typeof data.x == 'undefined' || data.x == null) {
                //     return;
                // }

                input.value = JSON.stringify({
                    'x': data.x,
                    'y': data.y,
                    'width': data.width,
                    'height': data.height,
                });
            }
        };

        new Croppr(image, options);

        toggle.addEventListener('click', function (event) {

            event.preventDefault();

            container.classList.remove('hidden');

            return false;
        });

        modal.on('click', '[data-file]', function (e) {

            e.preventDefault();

            let $button = $(this);

            $modal.find('.modal-content').append('<div class="modal-loading"><div class="active loader"></div></div>');

            $wrapper.find('.selected').load('/streams/image-field_type/selected?uploaded=' + $button.data('file'), function () {
                $modal.modal('hide');
            });

            $image.closest('.cropper').removeClass('hidden');

            $image
                .cropper(options)
                .cropper('replace', '/streams/image-field_type/view/' + $button.data('file'))
                .cropper('reset');

            $('[name="' + $input.data('field_name') + '[id]"]').val($button.data('file'));
        });

        $wrapper.on('click', '[data-dismiss="file"]', function (e) {

            e.preventDefault();

            $('[name="' + $input.data('field_name') + '[id]"]').val('');
            $('[name="' + $input.data('field_name') + '[data]"]').val('');

            $wrapper.find('.selected').load('/streams/image-field_type/selected', function () {

                $modal.modal('hide');

                $image
                    .closest('.cropper')
                    .addClass('hidden');
            });
        });

    });

    // Initialize file pickers
    $(':not([data-initialized])').each(function () {

        // let $wrapper = $input.closest('.form-group');
        // let $image = $wrapper.find('[data-provides="cropper"]');
        // let $toggle = $wrapper.find('[data-toggle="cropper"]');
        // let $modal = $('#' + $input.data('field_name') + '-modal');

        // let options = {
        //     viewMode: 2,
        //     autoCrop: true,
        //     zoomable: false,
        //     autoCropArea: 1,
        //     responsive: true,
        //     checkOrientation: false,
        //     data: $image.data('data'),
        //     aspectRatio: $image.data('aspect-ratio'),
        //     crop: function (e) {
        //
        //         /**
        //          * This prevents trashy data from
        //          * being parsed into the field value.
        //          */
        //         if (!isFinite(e.x) || isNaN(e.x) || typeof e.x == 'undefined' || e.x == null) {
        //             return;
        //         }
        //
        //         $('[name="' + $input.data('field_name') + '[data]"]').val(JSON.stringify({
        //             'x': e.x,
        //             'y': e.y,
        //             'width': e.width,
        //             'height': e.height,
        //             'rotate': e.rotate,
        //             'scaleX': e.scaleX,
        //             'scaleY': e.scaleY
        //         }));
        //     }
        // };
        //
        // $toggle.on('click', function () {
        //
        //     $image
        //         .closest('.cropper')
        //         .removeClass('hidden');
        //
        //     $image.cropper(options);
        //
        //     return false;
        // });
        //
        // $modal.on('click', '[data-file]', function (e) {
        //
        //     e.preventDefault();
        //
        //     let $button = $(this);
        //
        //     $modal.find('.modal-content').append('<div class="modal-loading"><div class="active loader"></div></div>');
        //
        //     $wrapper.find('.selected').load('/streams/image-field_type/selected?uploaded=' + $button.data('file'), function () {
        //         $modal.modal('hide');
        //     });
        //
        //     $image.closest('.cropper').removeClass('hidden');
        //
        //     $image
        //         .cropper(options)
        //         .cropper('replace', '/streams/image-field_type/view/' + $button.data('file'))
        //         .cropper('reset');
        //
        //     $('[name="' + $input.data('field_name') + '[id]"]').val($button.data('file'));
        // });
        //
        // $wrapper.on('click', '[data-dismiss="file"]', function (e) {
        //
        //     e.preventDefault();
        //
        //     $('[name="' + $input.data('field_name') + '[id]"]').val('');
        //     $('[name="' + $input.data('field_name') + '[data]"]').val('');
        //
        //     $wrapper.find('.selected').load('/streams/image-field_type/selected', function () {
        //
        //         $modal.modal('hide');
        //
        //         $image
        //             .closest('.cropper')
        //             .addClass('hidden');
        //     });
        // });
    });
})(window, document);
