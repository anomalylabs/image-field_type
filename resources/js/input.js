$(function () {

    // Initialize Croppers
    $('[data-provides="cropper"]').each(function () {

        var image = $(this);
        var wrapper = image.closest('div');

        image.cropper({
            modal: $(this).data('modal'),
            guides: $(this).data('guides'),
            movable: $(this).data('movable'),
            scalable: $(this).data('scalable'),
            zoomable: $(this).data('zoomable'),
            dragMode: $(this).data('drag-mode'),
            viewMode: $(this).data('view-mode'),
            rotatable: $(this).data('rotatable'),
            highlight: $(this).data('highlight'),
            aspectRatio: $(this).data('aspect-ratio'),
            autoCropArea: $(this).data('auto-crop-area'),
            minContainerWidth: $(this).data('min-container-width'),
            minContainerHeight: $(this).data('min-container-height'),
            crop: function (e) {
                console.log(e.x);
                console.log(e.y);
                console.log(e.width);
                console.log(e.height);
                console.log(e.rotate);
                console.log(e.scaleX);
                console.log(e.scaleY);
            }
        });

        wrapper.find('.data').click(function () {

            var data = image.cropper('getData', true);

            console.log(data.height);
        });
    });
});
