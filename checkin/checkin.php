<?php
/**
GaiaEHR (Electronic Health Records)
Copyright (C) 2013 Certun, LLC.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
if(!defined('_GaiaEXEC')) die('No direct access allowed.');
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <title>GaiaEHR :: Check In</title>
	    <script type="text/javascript" src="lib/<?php print EXTJS ?>/ext-all.js"></script>
            <link rel="stylesheet" type="text/css" href="lib/<?php print EXTJS ?>/resources/css/ext-all-neptune.css">
        <link rel="stylesheet" type="text/css" href="resources/css/style_newui.css" >
        <link rel="stylesheet" type="text/css" href="resources/css/custom_app.css" >

        <script type="text/javascript" src="lib/jsqrcode/src/grid.js"></script>
        <script type="text/javascript" src="lib/jsqrcode/src/version.js"></script>
        <script type="text/javascript" src="lib/jsqrcode/src/detector.js"></script>
        <script type="text/javascript" src="lib/jsqrcode/src/formatinf.js"></script>
        <script type="text/javascript" src="lib/jsqrcode/src/errorlevel.js"></script>
        <script type="text/javascript" src="lib/jsqrcode/src/bitmat.js"></script>
        <script type="text/javascript" src="lib/jsqrcode/src/datablock.js"></script>
        <script type="text/javascript" src="lib/jsqrcode/src/bmparser.js"></script>
        <script type="text/javascript" src="lib/jsqrcode/src/datamask.js"></script>
        <script type="text/javascript" src="lib/jsqrcode/src/rsdecoder.js"></script>
        <script type="text/javascript" src="lib/jsqrcode/src/gf256poly.js"></script>
        <script type="text/javascript" src="lib/jsqrcode/src/gf256.js"></script>
        <script type="text/javascript" src="lib/jsqrcode/src/decoder.js"></script>
        <script type="text/javascript" src="lib/jsqrcode/src/QRCode.js"></script>
        <script type="text/javascript" src="lib/jsqrcode/src/findpat.js"></script>
        <script type="text/javascript" src="lib/jsqrcode/src/alignpat.js"></script>
        <script type="text/javascript" src="lib/jsqrcode/src/databr.js"></script>
        <script type="text/javascript">
            var gCtx, gCanvas, imageData, c = 0, done = 0;

            function handleFiles(f) {
                var o = [];
                for (var i = 0; i < f.length; i++) {
                    var reader = new FileReader();

                    reader.onload = (function (theFile) {
                        return function (e) {
                            qrcode.decode(e.target.result);
                        };
                    })(f[i]);

                    // Read in the image file as a data URL.
                    reader.readAsDataURL(f[i]);
                }
            }

            function read(p) {
                done = 1;
                window.app.patientFound(p);
            }

            function load() {
                initCanvas(640, 480);
                qrcode.callback = read;
                scanning();
            }

            function initCanvas(ww, hh) {
                gCanvas = document.getElementById("qr-canvas");
                var w = ww;
                var h = hh;
                gCanvas.style.width = w + "px";
                gCanvas.style.height = h + "px";
                gCanvas.width = w;
                gCanvas.height = h;
                gCtx = gCanvas.getContext("2d");
                gCtx.clearRect(0, 0, w, h);
                imageData = gCtx.getImageData(0, 0, 320, 240);
            }

            function passLine(stringPixels) {
                //a = (intVal >> 24) & 0xff;

                var coll = stringPixels.split("-");

                for (var i = 0; i < 320; i++) {
                    var intVal = parseInt(coll[i]);
                    r = (intVal >> 16) & 0xff;
                    g = (intVal >> 8) & 0xff;
                    b = (intVal ) & 0xff;
                    imageData.data[c + 0] = r;
                    imageData.data[c + 1] = g;
                    imageData.data[c + 2] = b;
                    imageData.data[c + 3] = 255;
                    c += 4;
                }

                if (c >= 320 * 240 * 4) {
                    c = 0;
                    gCtx.putImageData(imageData, 0, 0);
                    try {
                        qrcode.decode();
                        done = 1;
                    }
                    catch (e) {
                        //console.log(e);
                        setTimeout(captureToCanvas,1000);
                    }
                }
            }

            function captureToCanvas() {
                flash = document.getElementById("embedflash");
                if(!flash) return;
                flash.ccCapture();

            }

            function scanning() {
                setTimeout(function () {
                    if (done == 0) {
                        captureToCanvas();
                        //scanning()
                    }
                }, 5000);
            }
        </script>
        <link rel="shortcut icon" href="favicon.ico" >

        <script src="data/logon_api.php"></script>
        <script type="text/javascript" src="checkin/Checkin.js"></script>
        <script type="text/javascript">
        Ext.onReady(function(){
            Ext.direct.Manager.addProvider(App.data.REMOTING_API);
            window.app = Ext.create('App.panel.checkin.Checkin');

        }); // End App
        </script>
    </head>
    <body id="login">
        <div id="copyright">GaiaEHR | <a href="javascript:void(0)" onClick="Ext.getCmp('winCopyright').show();" >Copyright Notice</a></div>
    </body>
</html>