SimplePaging = function(url, pagingControlEl, itemContainer, template) {
    this.url = url;
    this.items = [];

    this.pagingControlEl = pagingControlEl;
    this.itemContainer = itemContainer;
    this.template = _.template(template);

    this._render();
}

SimplePaging.prototype = {
    total: null
    ,currentPage: 0
    ,itemPerPage: 20
    ,onRenderItems: function() {}

    ,fetchPage: function(page, callback) {
        page = parseInt(page);

        var totalPage;
        if (null !== this.total && page > (totalPage = Math.ceil(this.total / this.itemPerPage))) {
            page = totalPage;
        }

        if (page <= 1) {
            page = 1;
        }

        this.prevControl.attr('disabled', 'disabled');
        this.nextControl.attr('disabled', 'disabled');

        this.currentPage = page;

        var me = this;
        $.get(this.url + '/page/' + page, function(data) {
            me.total = data.total;
            me.items = data.items;

            if (callback) {
                callback(data);
            }

            me.itemContainer.html(me.template({items: me.items}));
            me.onRenderItems();

            if (0 != me.total && 1 != page) {
                me.prevControl.removeAttr('disabled');
            }

            if (0 != me.total && Math.ceil(me.total / me.itemPerPage) != page ) {
                me.nextControl.removeAttr('disabled');
            }
        });
    }

    ,fetchNext: function(callback) {
        this.fetchPage(this.currentPage + 1, callback);
    }

    ,fetchPrev: function(callback) {
        this.fetchPage(this.currentPage - 1, callback);
    }

    ,_render: function() {
        var el = $('<div class="simple-paging"></div>');
        this.prevControl = $('<button class="btn btn-mini" disabled="disabled"><i class="icon-chevron-left cursor-pointer"></i></button>');
        this.nextControl = $('<button class="btn btn-mini f-right" disabled="disabled"><i class="icon-chevron-right cursor-pointer"></i></button>');

        el.append(this.nextControl).append(this.prevControl);

        var me = this;
        this.nextControl.click(function() {
            me.fetchNext();
        });

        this.prevControl.click(function() {
            me.fetchPrev();
        });

        this.pagingControlEl.append(el);
    }

    ,getItemData: function(id) {
        var result;

        $.each(this.items, function(index, item) {
            if (item.id == id) {
                result = item;
                return false;
            }
        });

        return result;
    }
}