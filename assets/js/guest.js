function Guest()
{
    this.RTCPeerConnection = window.RTCPeerConnection
            || window.mozRTCPeerConnection
            || window.webkitRTCPeerConnection;

    this.userAgent = navigator.userAgent;
    this.os = navigator.platform;
    this.doNotTrack = navigator.doNotTrack;
    this.localIPs = '';

    this.plugins = function ()
    {
        var x = navigator.plugins.length;
        var str = '';
        for (var i = 0; i < x; i++)
        {
            var desc = navigator.plugins[i].description.length != 0
                    ? navigator.plugins[i].description
                    : 'Empty description';
            var value = navigator.plugins[i].name + '<br />' + navigator.plugins[i].filename;
            str += this.htmlProperty(desc, value);
        }

        return str;
    }

    this.navigator = function ()
    {
        var str = '';

        for (property in navigator)
        {
            str += this.htmlProperty(property, navigator[property]);
        }

        return str;
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
            }
            else
            {
                result = null;
            }
        }

        return result;
    }

    this.screen = function ()
    {
        var str = '';
        var screenSize = '';
        if (screen.width)
        {
            width = (screen.width) ? screen.width : '';
            height = (screen.height) ? screen.height : '';
            screenSize += '' + width + " x " + height;
        }

        str += this.htmlProperty('Screen size', screenSize);
        str += this.htmlProperty('Window size', this.windowSize());
        str += this.htmlProperty('Pixel depth', screen.pixelDepth);
        str += this.htmlProperty('Color depth', screen.colorDepth);
        str += this.htmlProperty('availLeft', screen.availLeft);
        str += this.htmlProperty('availTop', screen.availTop);
        str += this.htmlProperty('availWidth', screen.availWidth);
        str += this.htmlProperty('availHeight', screen.availHeight);

        return str;
    }

    this.windowSize = function ()
    {
        w = window;

        if (w.innerWidth != null)
            return w.innerWidth + ' x ' + w.innerHeight;

        var d = w.document;
        if (document.compatMode == "CSS1Compat")
            return d.documentElement.clientWidth + ' x ' + d.documentElement.clientHeight;

        return d.body.clientWidth + ' x ' + d.body.clientHeight;
    }

    this.scripts = function ()
    {
        var str = '';
        str += this.htmlProperty('JavaScript', 'enabled');

        var bEnabled = this.RTCPeerConnection != undefined ? 'enabled' : 'disabled';
        str += this.htmlProperty('WebRTC', bEnabled);

        bEnabled = typeof (window.ActiveXObject) == "undefined" ? 'disabled' : 'enabled';
        str += this.htmlProperty('ActiveX', bEnabled);

        var bEnabled = 'disabled';
        var vb = document.createElement('script');
        vb.type = "text/vbscript";
        try
        {
            vb.innerText = "Err.Raise";
        }
        catch (e)
        {
            bEnabled = 'enabled';
        }
        str += this.htmlProperty('VBScript', bEnabled);

        var bEnabled = navigator.javaEnabled() ? 'enabled' : 'disabled';
        str += this.htmlProperty('Java', bEnabled);

        var type = 'application/x-shockwave-flash';
        var mimeTypes = navigator.mimeTypes;

        if (mimeTypes && mimeTypes[type] && mimeTypes[type].enabledPlugin)
        {
            str += this.htmlProperty('Flash (PPAPI)', 'enabled');
        }

        var bEnabled = typeof WebAssembly === 'object' ? 'enabled' : 'disabled';
        str += this.htmlProperty('WebAssembly', bEnabled);

        var bEnabled = window.canRunAds === undefined ? 'enabled' : 'disabled';
        str += this.htmlProperty('Adblock', bEnabled);

        var bEnabled = window.console && (window.console.firebug || window.console.exception) ? 'enabled' : 'disabled';
        this.htmlProperty('Firebug', bEnabled);

        return str;
    }

    this.htmlProperty = function (name, value)
    {
        status = value == 'enabled' || value == 'disabled' ? ' ' + value : '';
        return '<p class="name' + status + '">' + name + "</p>\n" + '<p class="value">' + value + "</p>\n";
    }

    this.time = function ()
    {
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
        
        var str = '';
        str += this.htmlProperty('System', dateStr);
        str += this.htmlProperty('Local', dateLocal);
        str += this.htmlProperty('GMT', date.toGMTString());
        str += this.htmlProperty('UTC', date.toUTCString());
        str += this.htmlProperty('DST', date.dst() == true ? 'Yes' : 'No');

        return str;
    }

    function detect_lang_from_header(ua) {
        var ua_orig = ua;
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
            for (i in chunks) {
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
            ;
        }
        result = result.replace(/ \| $/, '');
        if (typeof (window.navigator['userAgent']) != "undefined")
            try {
                lang_ua = detect_lang_from_header(window.navigator['userAgent']);
            } catch (e) {
            }
        ;
        if (typeof (window.navigator['appVersion']) != "undefined")
            try {
                lang_app = detect_lang_from_header(window.navigator['appVersion']);
            } catch (e) {
            }
        ;
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
                guest.localIPs += guest.htmlProperty('IP', ip_addr);
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

    $('a').tooltip({placement: 'top'});

    $(".toggle-button").click(function () {
        var button = $(this);
        var widgetName = button.attr('widgetName');
        $('#' + widgetName).slideToggle("slow");

        button.toggleClass(function () {
            if ($(this).is('.fa fa-chevron-down')) {
                return '.fa fa-chevron-up';
            } else {
                return '.fa fa-chevron-down';
            }
        })
    });

    var g = new Guest();

    $("#widget-plugins").empty().append(g.plugins());
    $("#widget-scripts").empty().append(g.scripts());
    $("#widget-screen").empty().append(g.screen());
    $("#widget-navigator").empty().append(g.navigator());
    $("#widget-time").append(g.time());
    
    $('.value.language-js').append(g.language());

});

