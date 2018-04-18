(function (window, document) {

    let fields = Array.prototype.slice.call(
        document.querySelectorAll('[data-provides="anomaly.field_type.image"]')
    );

    fields.forEach(function (field) {

        let name = field.getAttribute('data-field_name');

        let modal = field.parentElement.querySelector('.modal');
        let container = field.parentElement.querySelector('.cropper__container');
        let image = field.parentElement.querySelector('[data-provides="cropper"]');
        let removeToggle = field.parentElement.querySelector('[data-dismiss="file"]');
        let cropperToggle = field.parentElement.querySelector('[data-toggle="cropper"]');
        let dataInput = field.parentElement.querySelector('input[name="' + name + '[data]"]');
        let idInput = field.parentElement.querySelector('input[name="' + name + '[id]"]');

        let value = JSON.parse(image.getAttribute('data-data'));

        let options = {
            startSize: [80, 80, '%'],
            aspectRatio: Number(image.getAttribute('data-aspect-ratio')),
            onInitialize: function (instance) {
                instance.box.x1 = value.x;
                instance.box.y1 = value.y;
                instance.box.x2 = value.x + value.width;
                instance.box.y2 = value.y + value.height;
            },
            onCropEnd: function (data) {

                /**
                 * This prevents trashy data from
                 * being parsed into the field value.
                 */
                // if (!isFinite(data.x) || isNaN(data.x) || typeof data.x == 'undefined' || data.x == null) {
                //     return;
                // }

                dataInput.value = JSON.stringify({
                    'x': data.x,
                    'y': data.y,
                    'width': data.width,
                    'height': data.height,
                });
            }
        };

        cropperToggle.addEventListener('click', function (event) {

            event.preventDefault();

            container.classList.remove('hidden');

            new Croppr(image, options);

            return false;
        });

        removeToggle.addEventListener('click', function (event) {

            event.preventDefault();

            dataInput.value = '';
            idInput.value = '';

            modal.modal('hide');

            container.classList.add('remove');

            return false;
        });

        document.addEventListener('click', function (event) {
            if (event.target.hasAttribute('data-file')) {

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
            }
        });

    });

})(window, document);
