<?php 
    
include_once '../../../view_image.php';
$data = getdata();
$page = $_GET['page']?$_GET['page']:1;
$dcmPath = $data['dcmPath'];
$tmp = iconv("utf-8","gbk",$dcmPath);
$tmparr = explode('\\',$tmp);
$filename = end($tmparr);
$filename = iconv("gbk","utf-8",$filename);
$jcrq = $data['jcrq'];
$name = $data['name'];
$url = "http://localhost/dcm_store/".$jcrq."/".$name."/".$filename;

 ?>

<!DOCTYPE HTML>
<html>
<head>
    <!-- twitter bootstrap CSS stylesheet - included to make things pretty, not needed or used by cornerstone -->
    <link href="../bootstrap.min.css" rel="stylesheet">

    <link href="../cornerstone.min.css" rel="stylesheet">
    <style type="text/css">
        *{margin:0 auto;}
        table{text-align:center;background-color:#abcdef;}
        h3{float:right;}
    </style>
</head>
<body>
<div class="container">

    <div class="page-header">
        <h1 style="text-align:center">DR影像浏览</h1><h3><a href="http://localhost/dicom/view_records.php?page=<?php echo $page ?>">返回主页面</a></h3>
    <br/>
    <br/>
        
    <div class="row">
        <form class="form-horizontal">
            <div class="form-group">
                <div class="col-sm-8">
                    <input type="hidden" class="form-control" type="text" id="wadoURL" value="http://localhost/dcm_store/<?php echo $jcrq ?>/<?php echo $name ?>/<?php echo $filename ?>" />
                </div>
                <div class="col-sm-3">
                    <button style="display:none" class="form-control" type="button" id="downloadAndView" class="btn btn-primary">Download and View</button>
                </div>
            </div>
        </form>
    </div>
    <br>
    <div class="row">
        <div class="col-md-6" style="margin-left:140px">
            <div style="width:800px;height:800px;position:relative;color: white;display:inline-block;border-style:solid;border-color:black;"
                 oncontextmenu="return false"
                 class='disable-selection noIbar'
                 unselectable='on'
                 onselectstart='return false;'
                 onmousedown='return false;'>
                <div id="dicomImage"
                     style="width:800px;height:800px;top:0px;left:0px; position:absolute">
                </div>
            </div>
        </div>
       
    </div>
    
</div>
<br/>
     <br/>
    
        <table border='1' cellspacing="0" cellpadding="0"  width="60%">
            <tr>
                <td>姓名</td>
                <td>性别</td>
                <td>年龄</td>
                <td>检查日期</td>
                <td>检查部位</td>
                
            </tr>
            <tr>
                <td><?php echo $data['name'] ?></td>
                <td><?php echo strtolower(trim($data['sex']))=="m"?"男":"女" ?></td>
                <td><?php echo $data['age'] ?></td>
                <td><?php echo $data['jcrq'] ?></td>
                <td><?php echo $data['jcbw'] ?></td>
                
            </tr>
        </table>
   
     
     
     <br/>
     <br/>
     <br/>
</body>


<!-- jquery - currently a dependency and thus required for using cornerstoneWADOImageLoader -->
<script src="../jquery.min.js"></script>

<!-- bootstrap -->
<script src="../bootstrap.min.js"></script>

<!-- include the cornerstone library -->
<script src="../cornerstone.min.js"></script>
<SCRIPT src="../cornerstoneMath.js"></SCRIPT>
<SCRIPT src="../cornerstoneTools.js"></SCRIPT>

<!-- include the dicomParser library as the WADO image loader depends on it -->
<script src="../dicomParser.min.js"></script>

<!-- BEGIN Optional Codecs -->

<!-- OpenJPEG based jpeg 2000 codec -->
<script src="../../codecs/openJPEG-FixedMemory.js"></script>

<!-- PDF.js based jpeg 2000 codec -->
<!-- NOTE: do not load the OpenJPEG codec if you use this one -->
<!-- <script src="../../codecs/jpx.min.js"></script> -->

<!-- JPEG-LS codec -->
<script src="../../codecs/charLS-FixedMemory-browser.js"></script>

<!-- JPEG Lossless codec -->
<script src="../../codecs/jpegLossless.js"></script>

<!-- JPEG Baseline codec -->
<script src="../../codecs/jpeg.js"></script>

<!-- Deflate transfer syntax codec -->
<script src="../../codecs/pako.min.js"></script>

<!-- END Optional Codecs -->

<!-- include the cornerstoneWADOImageLoader library -->
<script src="../../dist/cornerstoneWADOImageLoader.js"></script>

<script src="../dicomfile/uids.js"></script>


<script>
    cornerstoneWADOImageLoader.external.cornerstone = cornerstone;

    cornerstoneWADOImageLoader.configure({
        beforeSend: function(xhr) {
            // Add custom headers here (e.g. auth tokens)
            //xhr.setRequestHeader('APIKEY', 'my auth token');
        }
    });

    var loaded = false;

    function loadAndViewImage(imageId) {
        var element = $('#dicomImage').get(0);
        try {
        var start = new Date().getTime();
            cornerstone.loadAndCacheImage(imageId).then(function(image) {
                console.log(image);
                var viewport = cornerstone.getDefaultViewportForImage(element, image);
                $('#toggleModalityLUT').attr("checked",viewport.modalityLUT !== undefined);
                $('#toggleVOILUT').attr("checked",viewport.voiLUT !== undefined);
                cornerstone.displayImage(element, image, viewport);
                if(loaded === false) {
                    cornerstoneTools.mouseInput.enable(element);
                    cornerstoneTools.mouseWheelInput.enable(element);
                    cornerstoneTools.wwwc.activate(element, 1); // ww/wc is the default tool for left mouse button
                    cornerstoneTools.pan.activate(element, 2); // pan is the default tool for middle mouse button
                    cornerstoneTools.zoom.activate(element, 4); // zoom is the default tool for right mouse button
                    cornerstoneTools.zoomWheel.activate(element); // zoom is the default tool for middle mouse wheel
                    loaded = true;
                }

                function getTransferSyntax() {
                    var value = image.data.string('x00020010');
                    return value + ' [' + uids[value] + ']';
                }

                function getSopClass() {
                    var value = image.data.string('x00080016');
                    return value + ' [' + uids[value] + ']';
                }

                function getPixelRepresentation() {
                    var value = image.data.uint16('x00280103');
                    if(value === undefined) {
                        return;
                    }
                    return value + (value === 0 ? ' (unsigned)' : ' (signed)');
                }

                function getPlanarConfiguration() {
                    var value = image.data.uint16('x00280006');
                    if(value === undefined) {
                        return;
                    }
                    return value + (value === 0 ? ' (pixel)' : ' (plane)');
                }


                $('#transferSyntax').text(getTransferSyntax());
                $('#sopClass').text(getSopClass());
                $('#samplesPerPixel').text(image.data.uint16('x00280002'));
                $('#photometricInterpretation').text(image.data.string('x00280004'));
                $('#numberOfFrames').text(image.data.string('x00280008'));
                $('#planarConfiguration').text(getPlanarConfiguration());
                $('#rows').text(image.data.uint16('x00280010'));
                $('#columns').text(image.data.uint16('x00280011'));
                $('#pixelSpacing').text(image.data.string('x00280030'));
                $('#bitsAllocated').text(image.data.uint16('x00280100'));
                $('#bitsStored').text(image.data.uint16('x00280101'));
                $('#highBit').text(image.data.uint16('x00280102'));
                $('#pixelRepresentation').text(getPixelRepresentation());
                $('#windowCenter').text(image.data.string('x00281050'));
                $('#windowWidth').text(image.data.string('x00281051'));
                $('#rescaleIntercept').text(image.data.string('x00281052'));
                $('#rescaleSlope').text(image.data.string('x00281053'));
                $('#basicOffsetTable').text(image.data.elements.x7fe00010.basicOffsetTable ? image.data.elements.x7fe00010.basicOffsetTable.length : '');
                $('#fragments').text(image.data.elements.x7fe00010.fragments ? image.data.elements.x7fe00010.fragments.length : '');
                $('#minStoredPixelValue').text(image.minPixelValue);
                $('#maxStoredPixelValue').text(image.maxPixelValue);
                var end = new Date().getTime();
                var time = end - start;
                $('#totalTime').text(time + "ms");
                $('#loadTime').text(image.loadTimeInMS + "ms");
                $('#decodeTime').text(image.decodeTimeInMS + "ms");

            }, function(err) {
                alert(err);
            });
        }
        catch(err) {
            alert(err);
        }
    }

    function downloadAndView()
    {
        var url = $('#wadoURL').val();

        // prefix the url with wadouri: so cornerstone can find the image loader
        url = "wadouri:" + url;


        // image enable the dicomImage element and activate a few tools
        loadAndViewImage(url);
    }


    function getUrlWithoutFrame() {
        var url = $('#wadoURL').val();
        var frameIndex = url.indexOf('frame=');
        if(frameIndex !== -1) {
            url = url.substr(0, frameIndex-1);
        }
        return url;
    }
    function test(){
        $("#downloadAndView").trigger("click");
    }
    $(document).ready(function() {
        window.onload = test;
        var element = $('#dicomImage').get(0);
        cornerstone.enable(element);

        $('#downloadAndView').click(function(e) {
            downloadAndView();
        });
        $('#load').click(function(e) {
            var url = getUrlWithoutFrame();
            cornerstoneWADOImageLoader.wadouri.dataSetCacheManager.load(url);
        });
        $('#unload').click(function(e) {
            var url = getUrlWithoutFrame();
            cornerstoneWADOImageLoader.wadouri.dataSetCacheManager.unload(url);
        });

        $('#purge').click(function(e) {
            cornerstone.imageCache.purgeCache();
        });

        $('form').submit(function() {
            downloadAndView();
            return false;
        });

        $('#toggleModalityLUT').on('click', function() {
            var applyModalityLUT = $('#toggleModalityLUT').is(":checked");
            console.log('applyModalityLUT=', applyModalityLUT);
            var image = cornerstone.getImage(element);
            var viewport = cornerstone.getViewport(element);
            if(applyModalityLUT) {
                viewport.modalityLUT = image.modalityLUT;
            } else {
                viewport.modalityLUT = undefined;
            }
            cornerstone.setViewport(element, viewport);
        });

        $('#toggleVOILUT').on('click', function() {
            var applyVOILUT = $('#toggleVOILUT').is(":checked");
            console.log('applyVOILUT=', applyVOILUT);
            var image = cornerstone.getImage(element);
            var viewport = cornerstone.getViewport(element);
            if(applyVOILUT) {
                viewport.voiLUT = image.voiLUT;
            } else {
                viewport.voiLUT = undefined;
            }
            cornerstone.setViewport(element, viewport);
        });


    });

    //var worker = new Worker('../webWorker.js');
    //var pixelData = new Uint8Array(512000);
    //worker.postMessage({hello: 'world', pixelData: pixelData.buffer}, [pixelData.buffer]);

</script>
</html>
