define(['ko'], function (ko) {
    'use strict';
    return {
        isReviewRequired: ko.observable(false),
        customerEmail: ko.observable(null),
        active: ko.observable(false)
    }
});