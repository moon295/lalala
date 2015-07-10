function lpo_replace(key) {
    if (navigator.cookieEnabled) {
        var cookie = document.cookie + ";";
        var referrer = document.referrer;
        var url = location.href;
        var regexp = new RegExp("^https?:\/\/[^\/]+");
        var param_referrer;
        if (referrer.length > 0 && url.indexOf("ltadwrs=true") != -1) {
            referrer += "&ltadwrs=true";
        } else if (referrer.length > 0 && url.indexOf("ltovtre=true") != -1) {
            referrer += "&ltovtre=true";
        } else if (referrer.length > 0 && url.indexOf("telsiru_") != -1) {
            var param = url.replace(/^.+[?|&]*telsiru_/, 'telsiru_').replace(/&.+/, '');
            var mark = referrer.indexOf('?') == -1 ? '?' : '&';
            referrer += mark + param;
        }
        if (referrer.length != 0 && url.match(regexp)[0] == referrer.match(regexp)[0]) {
            param_referrer = cookie.replace(/^.*referrer=/, '').replace(/;.+$/, '');
        } else {
            referrer = referrer.replace(/;/g, '@');
            document.cookie = "referrer=" + referrer + "; path=/";
            param_referrer = referrer;
        }
        if (document.getElementById(key)) {
            var enc = document.charset ? document.charset : document.characterSet;
            var script = '<script type="text/javascript" src="//ts.marketing.io/?referrer=' + encodeURIComponent(param_referrer) + '&url=' + encodeURIComponent(url) + '&key=' + key + '&enc=' + enc + '"></script>';
            document.write(script);
        }
    }
}
