var LocaleSwitcherApp = {
    select: null,

    init: function () {
        this.select = $('.locale-switcher').find('select');
        this.bindEvents();
    },

    bindEvents: function () {
        var that = this;
        this.select.on('change', function () {
            return document.location.href = that.updateQueryStringParameter(
                window.location.pathname, 'locale', that.select.val());
        });
    },

    updateQueryStringParameter: function (uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        if (uri.match(re)) {
            return uri.replace(re, '$1' + key + "=" + value + '$2');
        }
        else {
            return uri + separator + key + "=" + value;
        }
    }
};

var InvestorProfileApp = {
    init: function () {
        var that = this;
        $('.loading-avatar').each(function () {
            that.refresh($(this));
        })
    },

    refresh: function (cont) {
        var that = this, api = cont.data('api'), img = new Image();
        setTimeout(function () {
            $.getJSON(api, function (response) {
                if (typeof response['result'] !== 'undefined') {
                    if (response.result.avatar.ready) {
                        img.src = response.result.avatar.url;
                        img.onload = function () {
                            img = $(img);
                            img.hide();
                            cont.before(img).remove();
                            img.fadeIn('fast');
                        };
                    }
                    else {
                        that.refresh(cont);
                    }
                }
            });
        }, 1000);
    }
};

var LoanListApp = {
    searchForm: null,
    searchFormAction: null,
    searchFormQuery: null,
    investmentForm: null,
    investmentFormPlaceholder: null,
    investmentFormNothing: null,
    investmentFormLoading: null,
    investmentFormQuery: null,
    timeout: null,
    lastQuery: null,

    init: function () {
        this.searchForm = $('#platform_loan_search');
        this.searchFormAction = this.searchForm.attr('action');
        this.searchFormQuery = this.searchForm.find('input[name*=query]');

        this.investmentForm = $('#platform_investment');
        this.investmentFormPlaceholder = this.investmentForm.find('.placeholder');
        this.investmentFormNothing = this.investmentFormPlaceholder.find('.nothing');
        this.investmentFormLoading = this.investmentFormPlaceholder.find('.loading');
        this.investmentFormQuery = this.investmentForm.find('input[name*=loanSearchQuery]');

        this.bindEvents();
        this.search();
    },

    bindEvents: function () {
        var that = this;

        this.investmentFormQuery.on('keyup', function () {
            that.onKeyUp();
        });

        this.investmentFormQuery.on('keypress', function (e) {
            that.onKeyPress(e);
        });
    },

    onKeyUp: function () {
        var that = this;

        if (this.timeout) {
            clearTimeout(this.timeout);
        }

        this.timeout = setTimeout(function () {
            that.search();
        }, 1000);
    },

    onKeyPress: function (e) {
        if ((e.keyCode ? e.keyCode : e.which) == 13) {
            e.preventDefault();
        }
    },

    search: function () {
        var that = this, query = $.trim(this.investmentFormQuery.val());

        if (this.lastQuery === query) {
            return false;
        }

        this.investmentFormPlaceholder.show().parent().children().not('.placeholder').remove();

        this.investmentFormNothing.hide();
        this.investmentFormLoading.show();

        this.searchFormQuery.val(query);
        this.investmentFormQuery.val(query);
        this.lastQuery = query;

        $.post(this.searchFormAction, this.searchForm.serialize()).always(function (response) {
            that.investmentFormLoading.hide();
            that.render(response);
        }, 'json');
    },

    render: function (response) {
        var result, len = 0, i, item, nodes;

        if (typeof response['result'] !== 'undefined') {
            result = response['result'];
            len = result.length;
        }

        this.investmentFormPlaceholder[len ? 'hide' : 'show']();
        this.investmentFormNothing[len ? 'hide' : 'show']();

        for (i = 0; i < len; i++) {
            item = result[i];
            nodes = [];
            nodes.push('<tr>');
            nodes.push('<td>' + item.id + '</td>');
            nodes.push('<td>' + parseFloat(item.amount).toFixed(2) + '</td>');
            nodes.push('<td>' + parseFloat(item.available_for_investments).toFixed(2) + '</td>');
            nodes.push('<td>' + item.description + '</td>');
            nodes.push('<td>');
            nodes.push('<input type="radio" name="platform_investment[plainLoanId]" ' + (!i ? 'checked="checked"' : '') + 'value="' + item.id + '"/>');
            nodes.push('</td>');
            nodes.push('</tr>');
            this.investmentFormPlaceholder.parent().append($(nodes.join('')));
        }
    }
};