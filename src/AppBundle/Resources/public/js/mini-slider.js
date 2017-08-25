$('#myCarousel').carousel({
    interval: 5000
});

$('#carousel-text').html($('#slide-content-0').html());

// When the carousel slides, auto update the text
$('#myCarousel').on('slid.bs.carousel', function (e) {
    var id = $('.item.active').data('slide-number');
    $('#carousel-text').html($('#slide-content-'+id).html());
});
