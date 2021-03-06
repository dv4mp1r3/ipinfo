function outputData(name)
{
    this.data = '';
    this.count = 0;
    this.name = name;
    
    /**
     * 
     * @returns {String}
     */
    this.toString = function()
    {
        return "    <b>array</b> <i>"+this.name+" (size="+this.count+")</i>\n"+this.data;
    }
    
    /**
     * 
     * @param {String} name
     * @param {type} value
     * @returns {undefined}
     */
    this.addProperty = function (name, value)
    {
        var valueType = typeof (value);
        var fontColor = '#888a85';
        var addition = '';
        switch (valueType)
        {
            case 'string':
                addition = "<i>(length=" + value.length + ")</i>";
                fontColor = '#cc0000';
                value = "'" + value + "'";
                break;
            case 'number':
                if (!isNaN(parseFloat(value)))
                {
                    fontColor = '#f57900';
                } else
                {
                    fontColor = '#4e9a06';
                }
                break;
            case 'boolean':
                if (value == true)
                {
                    fontColor = '#00cc00';
                } else
                {
                    fontColor = '#cc0000';
                }
                break;
        }
        this.count++;
        this.data += "        '" + name + "' <font color='#888a85'>=&gt;</font> <small>" + valueType + "</small> <font color='" + fontColor + "'>" + value + "</font>" + addition + "\n";
    }
}

function Guest()
{
    this.RTCPeerConnection = window.RTCPeerConnection
            || window.mozRTCPeerConnection
            || window.webkitRTCPeerConnection;

    this.userAgent = navigator.userAgent;
    this.os = navigator.platform;
    this.doNotTrack = navigator.doNotTrack;
    this.localIPs = new outputData("WebRTC local IP's");

    this.plugins = function ()
    {
        var x = navigator.plugins.length;
        var output = new outputData('plugins');
        for (var i = 0; i < x; i++)
        {
            var desc = navigator.plugins[i].description.length != 0
                    ? navigator.plugins[i].description
                    : '';
            var value = navigator.plugins[i].name + ' (' + navigator.plugins[i].filename+')';
            output.addProperty(desc, value);
        }

        output.toString();
    }

    this.navigator = function ()
    {
        var output = new outputData('navigator');
        for (var property in navigator)
        {
            output.addProperty(property, navigator[property]);
        }

        return output.toString();
    }

    this.flashVersion = function ()
    {
        var result = null;
        if (typeof swfobject != 'undefined')
        {
            var fv = swfobject.getFlashPlayerVersion();
            if (fv.major > 0)
            {
                result = fv.major + '.' + fv.minor + ' r' + fv.release;
            } else
            {
                result = null;
            }
        }

        return result;
    }

    this.screen = function ()
    {
        var output = new outputData('screen');
        var screenSize = '';
        var height;
        var width;
        if (screen.width) {
            width = (screen.width) ? screen.width : '';
            height = (screen.height) ? screen.height : '';
            screenSize += '' + width + " x " + height;
        }

        output.addProperty('screenSize', screenSize);
        output.addProperty('windowSize', this.windowSize());
        output.addProperty('pixelDepth', screen.pixelDepth);
        output.addProperty('colorDepth', screen.colorDepth);
        output.addProperty('availLeft', screen.availLeft);
        output.addProperty('availTop', screen.availTop);
        output.addProperty('availWidth', screen.availWidth);
        output.addProperty('availHeight', screen.availHeight);

        return output.toString();
    }

    this.windowSize = function ()
    {
        var w = window;

        if (w.innerWidth != null)
            return w.innerWidth + ' x ' + w.innerHeight;

        var d = w.document;
        if (document.compatMode == "CSS1Compat")
            return d.documentElement.clientWidth + ' x ' + d.documentElement.clientHeight;

        return d.body.clientWidth + ' x ' + d.body.clientHeight;
    }

    this.scripts = function ()
    {
        var output = new outputData('scripts');
        output.addProperty('JavaScript', true);
        output.addProperty('WebRTC', this.RTCPeerConnection != undefined);
        output.addProperty('ActiveX', typeof (window.ActiveXObject) == "undefined");

        var bEnabled = false;
        var vb = document.createElement('script');
        vb.type = "text/vbscript";
        try
        {
            vb.innerText = "Err.Raise";
        } catch (e)
        {
            bEnabled = true;
        }
        output.addProperty('VBScript', bEnabled);
        output.addProperty('Java', navigator.javaEnabled());

        var type = 'application/x-shockwave-flash';
        var mimeTypes = navigator.mimeTypes;

        if (mimeTypes && mimeTypes[type] && mimeTypes[type].enabledPlugin)
        {
            output.addProperty('Flash (PPAPI)', true);
        }

        output.addProperty('WebAssembly', typeof WebAssembly === 'object');
        output.addProperty('Adblock', window.canRunAds === undefined);

        bEnabled = window.console && (window.console.firebug || window.console.exception);
        if (bEnabled == undefined)
        {
            bEnabled = false;
        } 
        output.addProperty('Firebug', bEnabled);

        return output.toString();
    }

    this.time = function ()
    {
        var output = new outputData('time');
        var date = new Date();
        var dateStr = date.toString();

        var usertime = date.toLocaleString();

        var tzsregex = /\b(ACDT|ACST|ACT|ADT|AEDT|AEST|AFT|AKDT|AKST|AMST|AMT|ART|AST|AWDT|AWST|AZOST|AZT|BDT|BIOT|BIT|BOT|BRT|BST|BTT|CAT|CCT|CDT|CEDT|CEST|CET|CHADT|CHAST|CIST|CKT|CLST|CLT|COST|COT|CST|CT|CVT|CXT|CHST|DFT|EAST|EAT|ECT|EDT|EEDT|EEST|EET|EST|FJT|FKST|FKT|GALT|GET|GFT|GILT|GIT|GMT|GST|GYT|HADT|HAEC|HAST|HKT|HMT|HST|ICT|IDT|IRKT|IRST|IST|JST|KRAT|KST|LHST|LINT|MART|MAGT|MDT|MET|MEST|MIT|MSD|MSK|MST|MUT|MYT|NDT|NFT|NPT|NST|NT|NZDT|NZST|OMST|PDT|PETT|PHOT|PKT|PST|RET|SAMT|SAST|SBT|SCT|SGT|SLT|SST|TAHT|THA|UYST|UYT|VET|VLAT|WAT|WEDT|WEST|WET|WST|YAKT|YEKT)\b/gi;
        var timezonenames =
                {
                    "UTC+0": "GMT", "UTC+1": "CET", "UTC+2": "EET", "UTC+3": "MSK",
                    "UTC+3.5": "IRST", "UTC+4": "MSD", "UTC+4.5": "AFT", "UTC+5": "PKT", "UTC+5.5": "IST",
                    "UTC+6": "BST", "UTC+6.5": "MST", "UTC+7": "THA", "UTC+8": "AWST", "UTC+9": "AWDT",
                    "UTC+9.5": "ACST", "UTC+10": "AEST", "UTC+10.5": "ACDT", "UTC+11": "AEDT", "UTC+11.5": "NFT",
                    "UTC+12": "NZST", "UTC-1": "AZOST", "UTC-2": "GST", "UTC-3": "BRT", "UTC-3.5": "NST", "UTC-4": "CLT",
                    "UTC-4.5": "VET", "UTC-5": "EST", "UTC-6": "CST", "UTC-7": "MST", "UTC-8": "PST", "UTC-9": "AKST",
                    "UTC-9.5": "MIT", "UTC-10": "HST", "UTC-11": "SST", "UTC-12": "BIT"
                };

        var timezone = usertime.match(tzsregex);
        if (timezone) {
            timezone = timezone[timezone.length - 1];
        } else {
            var offset = -1 * date.getTimezoneOffset() / 60;
            offset = "UTC" + (offset >= 0 ? "+" + offset : offset);
            timezone = timezonenames[offset];
        }

        var dateLocal = dateStr.substring(0, dateStr.indexOf('(')) + '(' + timezone + ')';

        output.addProperty('System', dateStr);
        output.addProperty('Local', dateLocal);
        output.addProperty('GMT', date.toGMTString());
        output.addProperty('UTC', date.toUTCString());
        output.addProperty('DST', date.dst() == true ? 'Yes' : 'No');
        
        var lastVisit = this.getCookie('lastVisit');
        if (lastVisit !== undefined)
        {
            output.addProperty('lastVisit', lastVisit + ' seconds ago');
        }

        return output.toString();
    }

    function detect_lang_from_header(ua) {
        var result = '';
        if (/\[(\w\w|\w\w-\w\w)\]/.test(ua.toLowerCase())) {
            if (ua_lang[RegExp.$1]) {
                result = RegExp.$1;
            }
        } else if (/esperanto/.test(ua.toLowerCase())) {
            result = 'esperanto';
        } else if (/; en[;)]/.test(ua.toLowerCase())) {
            result = 'en';
        } else {
            var chunks = ua.toLowerCase().split(/;|\)/);
            for (var i in chunks) {
                chunks[i] = chunks[i].replace('_', '-');
                if (/^[ \w\-,]{0,} (\w\w|\w\w-\w\w)$/.test(chunks[i].toLowerCase())) {
                    if (ua_lang[RegExp.$1]) {
                        if (chunks[i] == 'wv')
                            continue;
                        result = chunks[i];
                        result = result.replace(/\s+/, '');
                    }
                }
            }
        }
        return result;
    }

    this.language = function ()
    {
        var result = '',
                lang_app = '',
                lang_ua = '';
        var lang_names = ["language", "browserLanguage", "userLanguage", "systemLanguage"];
        for (var i in lang_names) {
            try {
                if (typeof (window.navigator[lang_names[i]]) == "undefined")
                    continue;
                result += window.navigator[lang_names[i]] + " | ";
                pub["lang_js"].push(window.navigator[lang_names[i]]);
            } catch (e) {
            }
        }
        result = result.replace(/ \| $/, '');
        if (typeof (window.navigator['userAgent']) != "undefined")
            try {
                lang_ua = detect_lang_from_header(window.navigator['userAgent']);
            } catch (e) {
            }
        if (typeof (window.navigator['appVersion']) != "undefined")
            try {
                lang_app = detect_lang_from_header(window.navigator['appVersion']);
            } catch (e) {
            }
        if ((lang_ua == '' && lang_app == '') || (lang_ua != '' && lang_app != '')) {
            result = result ? result + " | " + lang_ua : lang_ua;
        } else {
            var separator = result ? " | " : '';
            if (lang_ua && lang_app) {
                result += separator + lang_ua + separator + lang_app;
            } else {
                result += separator + (lang_ua ? lang_ua : lang_app);
            }
        }
        result = result.replace(/ \| $/, '');
        return result;
    }
    
    this.timeInfo = function ()
    {
        var fromServer = document.cookie;
        console.log(fromServer);
    }
    
    this.getCookie = function(name)
    {
        var matches = document.cookie.match(new RegExp(
          "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }
    
    this.setCookie = function (name, value, options)
    {
        options = options || {};
        var expires = options.expires;

        if (typeof expires == "number" && expires) {
            var d = new Date();
            d.setTime(d.getTime() + expires * 1000);
            expires = options.expires = d;
        }
        if (expires && expires.toUTCString) {
            options.expires = expires.toUTCString();
        }

        value = encodeURIComponent(value);

        var updatedCookie = name + "=" + value;

        for (var propName in options) {
            updatedCookie += "; " + propName;
            var propValue = options[propName];
            if (propValue !== true) {
                updatedCookie += "=" + propValue;
            }
        }

        document.cookie = updatedCookie;
    }

    this.getRTCIPs = function ()
    {
        var ip_dups = {};
        var guest = this;
        //compatibility for firefox and chrome
        var RTCPeerConnection = guest.RTCPeerConnection;
        var useWebKit = !!window.webkitRTCPeerConnection;

        //bypass naive webrtc blocking using an iframe
        if (!RTCPeerConnection) {
            var win = iframe.contentWindow;
            RTCPeerConnection = win.RTCPeerConnection
                    || win.mozRTCPeerConnection
                    || win.webkitRTCPeerConnection;
            useWebKit = !!win.webkitRTCPeerConnection;
        }

        //minimal requirements for data connection
        var mediaConstraints = {
            optional: [{RtpDataChannels: true}]
        };

        var servers = {iceServers: [{urls: "stun:stun.services.mozilla.com"}]};

        //construct a new RTCPeerConnection
        var pc = new RTCPeerConnection(servers, mediaConstraints);

        function handleCandidate(candidate) {
            //match just the IP address
            var ip_regex = /([0-9]{1,3}(\.[0-9]{1,3}){3}|[a-f0-9]{1,4}(:[a-f0-9]{1,4}){7})/
            var ip_addr = ip_regex.exec(candidate)[1];

            //remove duplicates
            if (ip_dups[ip_addr] === undefined)
            {
                guest.localIPs.addProperty('IP', ip_addr);
                console.log(ip_addr);
            }

            ip_dups[ip_addr] = true;
        }

        //listen for candidate events
        pc.onicecandidate = function (ice) {

            //skip non-candidate events
            if (ice.candidate)
                handleCandidate(ice.candidate.candidate);
        };

        //create a bogus data channel
        pc.createDataChannel("");

        //create an offer sdp
        pc.createOffer(function (result) {

            //trigger the stun server request
            pc.setLocalDescription(result, function () {
            }, function () {
            });

        }, function () {
        });

        //wait for a while to let everything done
        setTimeout(function () {
            //read candidate info from local description
            var lines = pc.localDescription.sdp.split('\n');

            lines.forEach(function (line) {
                if (line.indexOf('a=candidate:') === 0)
                {
                    handleCandidate(line);
                    return guest.localIPs;
                }
            });
        }, 1000);

    }


}

$(document).ready(function () {

    Date.prototype.stdTimezoneOffset = function () {
        var jan = new Date(this.getFullYear(), 0, 1);
        var jul = new Date(this.getFullYear(), 6, 1);
        return Math.max(jan.getTimezoneOffset(), jul.getTimezoneOffset());
    }

    Date.prototype.dst = function () {
        return this.getTimezoneOffset() < this.stdTimezoneOffset();
    }

    var g = new Guest();   
    var pre = $('pre.ipinfo-vardumper');
    if (pre == null)
        return;
    
    var methods = ['plugins', 'scripts', 'screen', 'navigator', 'time'];   
    pre.append("\n<b>array</b> <i>frontent_data (size="+(methods.length+1)+")</i>\n");
    for(var i in methods)
    {
        pre.append(eval("g."+methods[i]+"()"));
    }
    
    new Fingerprint2().get(function(result, components) {
        var output = new outputData('fingerprintJS');
        g.setCookie('fingerprint', result);      
        output.addProperty('hash', result);   
        output.addProperty('fingerPrints', g.getCookie('fingerPrints')); 
        pre.append(output.toString());
    });
    //pre.append(g.language());

});


