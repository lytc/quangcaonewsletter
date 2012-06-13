Crawler = function() {
    this.tmpEl = $('<div id="tmp" style="height: 0; overflow: hidden;"></div>');
};

Crawler.prototype = {
    fetch: function(callback) {
        var me = this;
        $.get('/crawler/fetch', function(data) {
            me.tmpEl.html(data.content);
            var items = Crawler.adapter[data.adapter].parseList(me.tmpEl);

            $.post('/crawler/fetch', {items: items}, function(data) {
                var length = data.items.length
                    ,count = 0
                    ,items = [];

                $(data.items).each(function(index, item) {
                    count ++;

                    me.tmpEl.html(item.content);
                    item.content = Crawler.adapter[data.adapter].parseDetail(me.tmpEl);
                    items.push(item);

                    if (count == length) {
                        $.post('/crawler/save', {items: items}, function(data) {
                            if (callback) {
                                callback();
                            }
                        });
                    }
                });
            });
        });
    }
};

Crawler.adapter = {
    phapluattp: {
        parseList: function(el) {
            var items = [], itemData;
            el.find('div[class="fl w380 box1"]').each(function(index, item) {
                item = $(item);
                itemData = {};

                var a = item.find('div[class="mt1 mr1"] > a');
                itemData.original_url = a.attr('href');
                itemData.name = a.html();

                itemData.thumbnail = item.find('img').attr('src');
                itemData.summary = item.find('div[class="mt1 fon5"]').html();

                if (itemData.name) {
                    items.push(itemData);
                }
            });

            return items;
        }

        ,parseDetail: function(el) {
            return el.find('#contentdetail').html();
        }
    }
}