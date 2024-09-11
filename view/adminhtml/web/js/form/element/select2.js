define([
    'ko',
    'jquery',
    'Magento_Ui/js/form/element/select',
    '../../lib/select2'
], function (ko, $, Abstract) {
    'use strict';

    ko.bindingHandlers.select2 = {
        init: function(element, valueAccessor) {
            let $element = $(element);
            let options = ko.unwrap(valueAccessor());

            if (options) {
                let baseUrl = window.BASE_URL.split(options.frontName)[0];

                let ajaxOptions = {
                    ajax: {
                        url: baseUrl + 'formselect2/ajax/search',
                        dataType: 'json',
                        delay: 250,
                        type: 'POST',
                        data: function (params) {
                            return {
                                q: params.term,
                                page: params.page,
                                model: options.model,
                                form_key: window.FORM_KEY
                            };
                        },
                        processResults: function (data, params) {
                            return {
                                results: data.items,
                                pagination: {
                                    more: (params.page * 30) < data.size
                                }
                            };
                        },
                        cache: false
                    },
                    minimumInputLength: options.minimumInputLength
                };

                options = $.extend(options, ajaxOptions);
            }

            $element.select2(options);

            $element.on("select2:select", function (e) {});
            $element.on("select2:unselect", function (e) {});
        }
    };

    return Abstract.extend({
        defaults: {
            select2: {}
        },

        initObservable: function () {
            this._super();

            this.observe('select2');

            return this;
        },

        normalizeData: function (value) {
            this.getCurrentValue(value);

            return value;
        },

        getCurrentValue: function (value) {
            if (value) {
                let self = this;
                let baseUrl = window.BASE_URL.split(this.select2().frontName)[0];

                $.post(
                    baseUrl + 'formselect2/ajax/search',
                    {
                        id: value,
                        form_key: window.FORM_KEY,
                        model: this.select2().model
                    },
                    function (data) {
                        self.addCurrentValueToOptions(data.items, value);
                    }
                );
            }
        },

        addCurrentValueToOptions: function (items, value) {
            let options = [];

            $.each(items, function(key,item) {
                options.push({'label': item.text, 'value': item.id});
            });

            this.setOptions(options);

            if (value) {
                this.value(value);
            }
        },

        change: function () {}
    });
});
