(function($){
    $(document).ready(function() {
        $('p:empty').remove();

        // modal window for gallery lightbox
        /*$('.tk-gallery').each(function(){
            $lightbox = $('.tk-lightbox', this);
            $('[data-target^="#tk_lightbox"]', this).on('click', function(event) {
                var imgsrc = $(this).data('imgsrc'),
                imgalt = $('img', this).attr('alt'),
                imgcaption = $(this).data('caption');
                $lightbox.find('.close').addClass('hidden');
                $lightbox.find('img').attr('src', imgsrc);
                $lightbox.find('img').attr('alt', imgalt);
                $lightbox.find('.caption').remove();
                if (imgcaption){
                    $lightbox.find('.modal-footer').prepend($('<p class="caption"/>').html(imgcaption));
                }
            });
        });*/
    });
})(jQuery);