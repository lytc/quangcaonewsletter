$(function() {
    var crawler = new Crawler();
    $('#fetch-newsletter-bt').click(function() {
        $('#fetch-newsletter-bt').attr('disabled', 'disabled');
        $('#fetch-loading').removeClass('hide');

        crawler.fetch(function() {
            $('#fetch-newsletter-bt').removeAttr('disabled');
            $('#fetch-loading').addClass('hide');
            window.location.href = '/news';
        });
    });
});