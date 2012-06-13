$(function() {
    function updateNewsletterList() {
        $.get('/newsletters/list', function(data) {
            var template = _.template($('#newsletter-template').html());
            var html = template({items: data});
            $('#newsletter-table').html(html);
        });
    }

    updateNewsletterList();
});