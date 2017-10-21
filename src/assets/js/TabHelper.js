_context.invoke('App', function (DOM) {

    var TabHelper = _context.extend(function(page) {
        this._ = {
            page: page
        };

        this._handleResponse = this._handleResponse.bind(this);
        this._.page.on('transaction-created', this._handleTransaction.bind(this));
    }, {
        _handleTransaction: function (evt) {
            if (!evt.data.transaction.isBackground() && DOM.getByClassName('tab-container').length) {
                evt.data.transaction.on('ajax-response', this._handleResponse);
            }
        },

        _handleResponse: function (evt) {
            var payload = evt.data.response.getPayload();

            DOM.getByClassName('tab-container').forEach(function (elem) {
                var src = DOM.getData(elem, 'source'),
                    items = DOM.getChildren(elem);

                if (src && src in payload) {
                    items.forEach(function (item) {
                        var value = DOM.getData(item, 'value');
                        DOM.toggleClass(item, 'active', value === payload[src]);
                    });
                }
            });
        }
    });

    _context.register(TabHelper, 'TabHelper');

}, {
    DOM: 'Utils.DOM'
});
