/* ///////////////////////////////////////////////////////////////////////

// googlemaps.js Defines an object for managing google map instances
// Uses Google Maps API V2

/////////////////////////////////////////////////////////////////////// */
//<%=Ratchet.Ameriprise.ALWP.Framework.Config.ALWPConfig.GoogleAPIKey %>

var gMaps = {
    // http://ratchetlocal.com/ API Key
    APIKey: null,
    origin: [],
    locM: null,
    mapInfo: [],
    mapId: null,
    continent: null,
    APILoaded: false,
    afterAPILoadCallbackProps: {},
    callback: function () { },
    map: [],
    geocoder: [],
    dir: [],
    locations: [],
    initMap: function (props) {
        gMaps.mapId = props.mapId;
        var mapEl = jQuery("div#" + props.mapId).length > 0 ? jQuery("div#" + props.mapId)[0] : null;
        if (mapEl != null && GBrowserIsCompatible()) {
            gMaps.map[props.mapId] = new GMap2(mapEl);
            gMaps.map[props.mapId].setCenter(new GLatLng(gMaps.origin[0], gMaps.origin[1]), 10);
            gMaps.map[props.mapId].setUIToDefault();
            gMaps.setMarkers();
        }
        props.callback(props.callbackProps);

    },
    loadAPI: function (props) {
        if (typeof props == "object") {
            gMaps.afterAPILoadCallbackProps = props;
            if (gMaps.afterAPILoadCallbackProps.origin != null)
                gMaps.origin = gMaps.afterAPILoadCallbackProps.origin;
            if (gMaps.afterAPILoadCallbackProps.APIKey != null)
                gMaps.APIKey = gMaps.afterAPILoadCallbackProps.APIKey;
            if (gMaps.afterAPILoadCallbackProps.continent != null)
                gMaps.continent = gMaps.afterAPILoadCallbackProps.continent;
        }

        if (!gMaps.APILoaded) {
            if (typeof google == "undefined") { // Step 1: Load Google AJAX API
                jQuery("head").append('<meta name="viewport" content="initial-scale=1.0, user-scalable=yes" />');
				//jQuery.getScript("https://maps.googleapis.com/maps/api/js?key=" + gMaps.APIKey, gMaps.loadAPI);
				jQuery.getScript("https://www.google.com/jsapi?key=" + gMaps.APIKey, gMaps.loadAPI);
            }
            else if (typeof google != "undefined" && typeof GMap2 == "undefined") { // Step 2: Load Google Maps API
                google.load("maps", "2", { callback: gMaps.loadAPI });
            }
            else if (typeof google != "undefined" && typeof GMap2 != "undefined") {
                gMaps.APILoaded = true;
            }
        }

        if (gMaps.APILoaded && gMaps.afterAPILoadCallbackProps.hasOwnProperty("callback")) {
            if (gMaps.afterAPILoadCallbackProps.hasOwnProperty("callbackProps"))
                gMaps.afterAPILoadCallbackProps.callback(gMaps.afterAPILoadCallbackProps.callbackProps);
            else
                gMaps.afterAPILoadCallbackProps.callback();
        }
    },
    handleErrors: function () {
        var error;

        switch (gMaps.dir.getStatus().code) {
            case G_GEO_UNKNOWN_ADDRESS:
                error = "No corresponding geographic location could be found for one of the specified addresses. This may be due to the fact that the address is relatively new, or it may be incorrect.\nError code: " + gMaps.dir.getStatus().code;
                break;
            case G_GEO_SERVER_ERROR:
                error = "A geocoding or directions request could not be successfully processed, yet the exact reason for the failure is not known.\n Error code: " + gMaps.dir.getStatus().code;
                break;
            case G_GEO_MISSING_QUERY:
                error = "The HTTP q parameter was either missing or had no value. For geocoder requests, this means that an empty address was specified as input. For directions requests, this means that no query was specified in the input.\n Error code: " + gMaps.dir.getStatus().code;
                break;
            case G_GEO_BAD_KEY:
                error = "The given key is either invalid or does not match the domain for which it was given. \n Error code: " + gMaps.dir.getStatus().code;
                break;
            case G_GEO_BAD_REQUEST:
                error = "A directions request could not be successfully parsed.\n Error code: " + gMaps.dir.getStatus().code;
                break;
            default:
                error = "An unknown error occurred.";
                break;
        }
        logMessage('Google Maps Error: ' + error);
    },
    setMarkers: function () {
        jQuery("tr.listing").each(function (idx) { gMaps.addMarker(this); });
    },
    addMarker: function (tr) {
        var point = new GLatLng(parseFloat(jQuery(tr).attr("lat")), parseFloat(jQuery(tr).attr("long")));
        var pIcon = new GIcon(G_DEFAULT_ICON);

        if (jQuery(tr).attr("platclub") == "True") {
            pIcon.image = "/images/p-icon-large.png";
            pIcon.shadow = "/images/shadow.png";
            pIcon.iconSize = new GSize(33, 53);
            pIcon.shadowSize = new GSize(61, 53);
            pIcon.iconAnchor = new GPoint(16, 53);
        }
		else if (jQuery(tr).attr("displaygroup") == "2") {
            pIcon.image = "/images/pin-grey.png";
            pIcon.shadow = "/images/shadow.png";
            //pIcon.iconSize = new GSize(33, 53);
            //pIcon.shadowSize = new GSize(61, 53);
            //pIcon.iconAnchor = new GPoint(16, 53);
        }
        else {
            pIcon.image = "/images/pin.png";
            pIcon.shadow = "/images/pin-shadow.png";
        }
        markerOptions = { icon: pIcon };

        var marker = new GMarker(point, markerOptions);
        GEvent.addListener(marker, "click", function () { gMaps.openInfoWindow(tr, point); });
        jQuery(tr).find(".open-info").on("click", function () { gMaps.setCenterMap(point, 10); });
        jQuery(tr).find(".open-info").on("click", function () { gMaps.openInfoWindow(tr, point); });
        gMaps.map[gMaps.mapId].addOverlay(marker);
    },
    setCenterMap: function (point, zoom) {
        gMaps.map[gMaps.mapId].setCenter(point, zoom);
    },
    openInfoWindow: function (tr, point) {
        var html = '<div class="googleMapInfoWindow">';
        html = html + '<h4>' + jQuery(tr).find("h2").text() + '</h4>';
        html = html + '<p class="add">' + jQuery(tr).find("span").html() + '</p>';
        html = html + '<p>' + jQuery(tr).find("label").text() + '</p>';
        html = html + '<div class="link">';
        html = html + '<p><a href="javascript:void(0)" onclick="javascript:getDirections(event)">Get Directions</a>';
        if (jQuery(tr).attr("connectme") == "True") {
            var _cust_no = "'" + jQuery(tr).attr('rel') + "'";
            var _cust_name = "'" + jQuery(tr).attr('lang') + "'";
            html = html + '&nbsp;&nbsp;<a href="javascript:void(0)" class="google_connectme" onclick="javascript:showLeadCapture_google(' + _cust_no + ',' + _cust_name + ')">Connect Me</a>';
        }
        html = html + '</p></div>';
        html = html + '</div>';
        gMaps.map[gMaps.mapId].openInfoWindowHtml(point, html);
    },
    triggerSearch: function () {
        var newloc = gMaps.checkLocation(jQuery('#googleSearchCity'), jQuery('#googleSearchState > option:selected'));
        if (newloc != false)
            gMaps.setHash('&l=' + newloc);
    },
    search: function (sloc, latlng, zoom) {
        if (typeof sloc == 'undefined') {
            sloc = gMaps.checkLocation(jQuery('#googleSearchCity'), jQuery('#googleSearchState > option:selected'));

        }

        if (sloc != false) {

            gMaps.map[gMaps.mapId].clearOverlays();
            if (typeof zoom == 'undefined')
                zoom = 10;


            if (typeof latlng == 'undefined') {
                var url;
                var getStores;


                jQuery.post('/Services/redwingserviceJSON.svc/Store/GetLatAndLongByLocation?location=' + sloc + '&useYahooGeocoder=true', function (point) {
                    if (point.length > 0) {
                        gMaps.origin = new GLatLng(point[0].lat, point[0].lng);

                        gMaps.map[gMaps.mapId].setCenter(gMaps.origin, zoom);

                        if (gMaps.continent != null && gMaps.continent == 'US') {
                            url = '/Services/redwingserviceJSON.svc/Store/GetRedwingStoresByLatAndLong?lat=' + point[0].lat + '&lng=' + point[0].lng;
                        } else {
                            url = '/Services/redwingserviceJSON.svc/Store/GetRedwingStoresByCountryCity?location=' + escape(sloc);
                        }


                        jQuery.post(url, function (data) {
                            if (data.length > 0) {

                                gMaps.locations = data;

                                //get map markers
                                gMaps.setMarkers();

                                //get store listing
                                gMaps.setListing();
                            } else {
                                logMessage('no stores in this area');
                                mapjsp.getContentPane().find('#map-results').children('ul').html('<li class="noresults"><h4>No Stores to Display</h4></li>');
                                mapjsp.reinitialise();
                            }
                        });
                    } else {
                        logMessage('invalid location');
                    }
                });
            } else {
                jQuery.post('/Services/redwingserviceJSON.svc/Store/GetRedwingStoresByLatAndLong?lat=' + latlng[0] + '&lng=' + latlng[1], function (data) {
                    if (data.length > 0) {
                        gMaps.locations = data;
                        gMaps.setMarkers();
                        gMaps.setListing();
                    } else {
                        logMessage('no stores in this area');
                    }
                });
            }
        }
    },
    checkLocation: function (city, state) {

        if (city.val() == '')
            city.parent().addClass('error');
        else
            city.parent().removeClass('error');

        if (state.val() == '----')
            state.parent().parent().addClass('error');
        else
            state.parent().parent().removeClass('error');

        if (city.val() == '' || state.val() == '----')
            return false;
        else
            return city.val() + ', ' + state.val();
    },
    scrollTo: function (y) {
        mapjsp.scrollToY(y);
    },
    setHash: function (hash) {
        var url = self.location.href.toString().split("#")[0];
        self.location.href = url + "#" + hash;
        return false;
    },
    checkHash: function () {
        if (jQuery.getUrlHash() != '' && jQuery.getUrlHashValue('l') != '') {
            gMaps.search(jQuery.getUrlHashValue('l'));
        }
    }
};
