var App = (function ($) {
    let siteUrl = '';
    return {
        init: function (config) {
            siteUrl = config.siteUrl;
        },getSiteurl: function() {
            return siteUrl
        }
    }
})(jQuery);