$(function() {
    var isDirty = false
        ,newsItemsAdded = {}
        ,adsItemsAdded = {};

    window.onbeforeunload = function() {
        if (isDirty) {
            return 'Bạn có chắc chắn muốn thoát khỏi trang này !';
        }
    }

    function updateIframeHeight() {
        $('#iframe-editor').height($(document.getElementById('iframe-editor').contentWindow.document.body).height());
    }
    setInterval(updateIframeHeight, 1000);

    function updateItemState(type, id) {
        var state = 'add';
        if ('news' == type && newsItemsAdded[id]) {
            state = 'delete';
        }

        if ('ads' == type && adsItemsAdded[id]) {
            state = 'delete';
        }

        var bt = $('.btn-add-to-newsletter[data-id=' + id + '][data-type=' + type + ']');

        if ('add' == state) {
            bt.removeClass('btn-danger')
                .addClass('btn-primary')
                .attr('data-state', 'add')
                .html('Thêm');
        } else {
            bt.removeClass('btn-primary')
                .addClass('btn-danger')
                .attr('data-state', 'delete')
                .html('Xoá');
        }

    }

    function updateItemsState(type, id) {
        if (!type || 'news' == type) {
            if (id) {
                return updateItemsState('news', id);
            }

            $('#news-list .btn-add-to-newsletter')
                .removeClass('btn-danger')
                .addClass('btn-primary')
                .attr('data-state', 'add')
                .html('Thêm');

            $.each(newsItemsAdded, function(id, data) {
                updateItemState('news', id);
            });
        }

        if (!type || 'ads' == type) {
            if (id) {
                return updateItemsState('ads', id);
            }

            $('#ads-list .btn-add-to-newsletter')
                .removeClass('btn-danger')
                .addClass('btn-primary')
                .attr('data-state', 'add')
                .html('Thêm');

            $.each(adsItemsAdded, function(id, data) {
                updateItemState('ads', id);

            });
        }
    }

    var newsSimplePaging = new SimplePaging('/news/list', $('#news-list-pagination-control'), $('#news-list'), $('#news-template').html());
    newsSimplePaging.onRenderItems = function() {
        updateItemsState('news');
    }
    newsSimplePaging.fetchPage(1);
    //$('#news-list-pagination-control').append(newsSimplePaging.render());

    var adsSimplePaging = new SimplePaging('/ads/list', $('#ads-list-pagination-control'), $('#ads-list'), $('#ads-template').html());
    adsSimplePaging.onRenderItems = function() {
        updateItemsState('ads');
    }
    adsSimplePaging.fetchPage(1);
    //$('#ads-list-pagination-control').append(adsSimplePaging.render());

    var crawler = new Crawler();
    $('#fetch-newsletter-bt').click(function() {
        $('#fetch-newsletter-bt').attr('disabled', 'disabled');
        $('#fetch-loading').removeClass('hide');

        crawler.fetch(function() {
            $('#fetch-newsletter-bt').removeAttr('disabled');
            $('#fetch-loading').addClass('hide');
            newsSimplePaging.fetchPage(1);
        });
    });

    $('#right-panel').on('click', '.btn-add-to-newsletter', function() {
        isDirty = true;

        var bt = $(this)
            ,id = bt.attr('data-id')
            ,type = bt.attr('data-type')
            ,state = bt.attr('data-state')
            ,data = 'news' == type? newsSimplePaging.getItemData(id) : adsSimplePaging.getItemData(id)
            ,newsletterList = $(document.getElementById('iframe-editor').contentWindow.document).find('#list');

        if (!state || 'add' == state) {
            var template = _.template($('news' == type? '#newsletter-news-item-template' : '#newsletter-ads-item-template').html());
            newsletterList.append(template({item: data}));

            'news' == type? newsItemsAdded[id] = data : adsItemsAdded[id] = data;
        } else {
            newsletterList.find('li[data-type=' + type + '][data-id=' + id + ']').remove();

            'news' == type? delete newsItemsAdded[id] : delete adsItemsAdded[id];
        }

        updateItemState(type, id);
    });

    $('#save-newsletter').click(function() {
        var d = new Date()
            ,year = d.getFullYear()
            ,month = d.getMonth() + 1
            ,day = d.getDay()
            ,hour = d.getHours()
            ,minute = d.getMinutes()
            ,defaultName = 'Newsletter-' + ([year, month, day, hour, minute].join('-'))
            ,name;

        function inputName() {
            var name;
            if (name = prompt('Vui lòng đặt tên cho newsletter !', defaultName)) {
                // get iframe content
                var content = $(document.getElementById('iframe-editor').contentWindow.document).find('html').html();

                $('#save-newsletter').attr('disabled', 'disabled');
                $('#save-newsletter-processing').removeClass('hide');

                $.post('/newsletters/add', {name: name, content: content}, function() {
                    isDirty = false;
                    window.location.href = '/newsletters';
                });
            } else if ('' === name) {
                inputName();
            }
        }

        inputName();
    });
});