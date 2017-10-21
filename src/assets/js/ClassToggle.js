_context.invoke('App', function (DOM) {

    var ClassToggle = _context.extend(function(page) {
        this._ = {
            id: 0,
            page: page,
            toggles: []
        };

        DOM.addListener(document, 'click', DOM.delegate('.class-toggle', this._handleClick.bind(this)));
        this._.page.on('transaction-created', this._handleTransaction.bind(this));
    }, {
        _handleClick: function (evt, btn) {
            evt.preventDefault();

            var target = DOM.find(DOM.getData(btn, 'toggle-target')),
                classes = DOM.getData(btn, 'toggle-classes', 'toggle-active'),
                state = !DOM.getData(btn, 'toggle-active');

            DOM.setData(btn, 'toggle-active', state);
            DOM.toggleClass(target, classes, state);

            if (DOM.getData(btn, 'toggle-reset')) {
                if (state) {
                    this._.toggles.push({target: target, classes: classes});
                } else {
                    this._.toggles = this._.toggles.filter(function (toggle) {
                        toggle.target = toggle.target.filter(function(elem) {
                            return target.indexOf(elem) === -1;
                        });

                        return toggle.target.length > 0;
                    });
                }
            }
        },

        _handleTransaction: function (evt) {
            if (evt.data.transaction.isBackground() || !this._.toggles.length) {
                return;
            }

            this._.toggles.forEach(function (toggle) {
                DOM.removeClass(toggle.target, toggle.classes);
            });

            this._.toggles = [];
        }
    });

    _context.register(ClassToggle, 'ClassToggle');

}, {
    DOM: 'Utils.DOM'
});
