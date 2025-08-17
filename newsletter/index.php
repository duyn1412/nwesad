<?php
// Debug code
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// 

session_start();

include '../connect-sql.php';
include __DIR__ . '/get-newsletter-settings.php';
include '../get-settings.php';

$message = '';

if (!isset($_COOKIE['username'])) {
  header('Location: /nwesadmin/');
  exit();
}

?>



<!DOCTYPE html>
<html lang="en" dir="ltr" itemscope itemtype="http://schema.org/WebPage">
  <head>
    
  <title>NWES Admin | Northwest Engineering Solutions (Portland, OR)</title>
    <link rel="icon" type="image/png" href="https://www.nwengineeringllc.com/images/favicon.png" />
    <link rel="shortcut icon" type="image/png" href="https://www.nwengineeringllc.com/images/favicon.png"/>
    <meta name="theme-color" content="#f05250"/>
    <link rel="canonical" href="https://www.nwengineeringllc.com/nwesadmin/"/>
    <meta charset="utf-8">
    
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="NWES Admin | Northwest Engineering Solutions">
    <meta name="robots" content="noindex">
    <meta property="og:title" content="NWES Admin | Northwest Engineering Solutions" />
    <meta property="og:description" content="Northwest Engineering Solutions NWES Admin" />
    <meta property="og:image" content="https://www.nwengineeringllc.com/images/ogsplash.jpg" />

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito+Sans:200,300,400,700,900|Roboto+Mono:300,400,500">
    <link rel="stylesheet" href="https://www.nwengineeringllc.com/fonts/icomoon/style.css">

    <link rel="stylesheet" href="https://www.nwengineeringllc.com/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://www.nwengineeringllc.com/css/magnific-popup.css">
    <link rel="stylesheet" href="https://www.nwengineeringllc.com/css/jquery-ui.css">
    <link rel="stylesheet" href="https://www.nwengineeringllc.com/css/owl.carousel.min.css">
    <link rel="stylesheet" href="https://www.nwengineeringllc.com/css/owl.theme.default.min.css">
    <link rel="stylesheet" href="https://www.nwengineeringllc.com/css/bootstrap-datepicker.css">
    <link rel="stylesheet" type="text/css" href="https://www.nwengineeringllc.com/css/carousel.css" rel="stylesheet" type="text/css" >


    <link rel="stylesheet" href="https://www.nwengineeringllc.com/fonts/flaticon/font/flaticon.css">

    <link rel="stylesheet" href="https://www.nwengineeringllc.com/css/aos.css">

    <link rel="stylesheet" href="https://www.nwengineeringllc.com/pcb-material-search/style.css">

    <link rel="stylesheet" href="https://www.nwengineeringllc.com/nwesadmin/styles.css">

    <link rel="stylesheet" href="https://www.nwengineeringllc.com/css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

  <link href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" rel="stylesheet" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>


<script src="https://www.nwengineeringllc.com/js/jquery-3.3.1.min.js"></script>

<script async src="https://www.googletagmanager.com/gtag/js?id=G-LEHRRGFREB"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', 'G-LEHRRGFREB');
</script>

<script type="application/ld+json">
  {
    "@context" : "http://schema.org",
    "@graph" : [{
    "@type" : "Corporation",
    "@id" : "https://www.nwengineeringllc.com/",
    "legalName" : "Northwest Engineering Solutions",
    "description" : "NWES provides R&D, electronics design services, and embedded systems to technology companies.",
    "email" : "contact@nwengineeringllc.com",
    "address" : {
      "@type" : "PostalAddress",
      "streetAddress" : "111 SW 5th Ave., Suite 3150",
      "addressLocality" : "Portland",
      "addressRegion" : "OR",
      "addressCountry" : "United States",
      "postalCode" : "97204"
    },
    "url" : "https://www.nwengineeringllc.com/",
    "image": {"@id":"https://nwengineeringllc.com#site-logo","url":"https://nwengineeringllc.com/images/nwestop2.png"},
    "naics": "541710",
    "logo": {"@type":"ImageObject","@id":"https://nwengineeringllc.com#site-logo","url":"https://nwengineeringllc.com/images/nwestop2.png","caption":""},
    "sameAs" : ["https://www.linkedin.com/company/18624901/", "https://www.facebook.com/NWESPDX/", "https://www.pcbdirectory.com/manufacturer/profile/northwest-engineering-solutions-llc", "https://www.upwork.com/o/companies/~011e10f9c0480b0563/"]
    },
    {
        "@type":"WebSite",
        "@id":"https://www.nwengineeringllc.com#website",
        "url":"https://www.nwengineeringllc.com",
        "name":"Northwest Engineering Solutions",
        "publisher":{"@id":"https://www.nwengineeringllc.com#corporation"}
    },
        {
        "@type":"WebPage","@id":"https://www.nwengineeringllc.com/pcb-material-search#webpage",
        "url":"https://www.nwengineeringllc.com",
        "inLanguage":"en-US",
        "name":"PCB Material Search | Northwest Engineering Solutions",
        "isPartOf":{"@id":"https://www.nwengineeringllc.com#website"},
        "breadcrumb":{"@id":"https://www.nwengineeringllc.com/pcb-material-search#breadcrumblist"},
        "description":"Northwest Engineering Solutions PCB Material Search",
        "datePublished":"2017-06-12T04:57:41+00:00",
        "dateModified":"2020-01-25T13:11:26+00:00",
        "about":{"@id":"https://www.nwengineeringllc.com#organization"}
        },
        {
        "@type":"BreadcrumbList",
        "@id":"https://www.nwengineeringllc.com/pcb-material-search#breadcrumblist",
        "itemListElement":[{
            "@type":"ListItem",
            "position":1,
            "item":{"@type":"WebPage","@id":"https://www.nwengineeringllc.com/","url":"https://www.nwengineeringllc.com/","name":"Home"}
            },
          {
              "@type":"ListItem",
              "position":2,
              "item":{"@type":"WebPage","@id":"https://www.nwengineeringllc.com/pcb-material-search","url":"https://www.nwengineeringllc.com/pcb-material-search","name":"PCB Material Search"}
          }]
        }
    ]
  }
</script>

<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-KV3PXBH');</script>
<!-- End Google Tag Manager -->
  </head>

<body itemscope itemtype="http://schema.org/WebPage">
<div itemprop="isPartOf" itemscope itemtype="https://schema.org/WebSite">
        <link itemprop="url" href="https://www.nwengineeringllc.com/pcb-material-search" />
</div>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KV3PXBH"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

  <div id="webpage" class="site-wrap">
    <!-- start site-mobile-menu  donot edit this-->
    <div class="site-mobile-menu">
      <div class="site-mobile-menu-header">
        <div class="site-mobile-menu-close mt-3">
          <span class="icon-close2 js-menu-toggle"></span>
        </div>
      </div>
      <div class="site-mobile-menu-body"></div>
    </div> <!-- start site-mobile-menu  donot edit this -->

    <!-- header start -->
    <div class="site-navbar-wrap bg-white">
      <div class="site-navbar-top">
        <div class="container py-2">
          <div class="row align-items-center">
            <div class="col-6">
            </div>
            <div class="col-6">
              <div class="d-flex ml-auto">
                <a href="mailto:contact@nwengineeringllc.com" class="d-flex align-items-center ml-auto mr-4">
                  <span class="icon-envelope mr-2"></span>
                  <span class="d-none d-md-inline-block">contact@nwengineeringllc.com</span>
                </a>
                <a class="d-flex align-items-center">
                  <span class="icon-phone mr-2"></span>
                  <span class="d-none d-md-inline-block" style="color: black;">833-330-NWES</span>
                </a>
                <div class="d-flex align-items-center">
                <a class="d-flex align-items-center ml-auto mr-4">
                  <span class="mr-2"></span>
                  <span class="d-none d-md-inline-block"></span>
                </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="site-navbar bg-light">
        <div class="container py-1">
          <div class="row align-items-center">
            <div class="col-2" itemscope itemtype="http://schema.org/Corporation">
              <a href="https://www.nwengineeringllc.com/"><img src="https://www.nwengineeringllc.com/images/nwestop2.png" alt="Northwest Engineering Solutions LLC" width="157" height="46" border="0" style="margin-bottom:6px;"/></a>
              <!--<h2 class="mb-0 site-logo"><a href="https://www.nwengineeringllc.com/">NWES</a></h2> -->
              <br />
            </div>
            <div class="col-10">
              <nav class="site-navigation text-right" role="navigation" itemscope="itemscope" itemtype="https://schema.org/SiteNavigationElement">
                <div class="container">
                  <div class="d-inline-block d-lg-none ml-md-0 mr-auto py-3"><a href="#" class="site-menu-toggle js-menu-toggle text-black"><span class="icon-menu h3"></span></a></div>

                  <div id="header"></div>
                </div>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>

     <div class="site-section">
      <div class="container">
      <!-- <h2 style="text-align: center; margin-top:25px;">Add New User </h2> -->

      <?php include('../nav-admin.php'); ?>
      <?php //include('../newsletter-settings.php'); ?>

    
        
      <div class="row">
     
        <div class="col-md-6">
          <!-- Content for the first column -->
          <form action="newsletter-settings.php" method="post">
            <div class="form-group row">
              <label for="topHeading" class="col-sm-3 col-form-label">Top Heading:</label>
              <div class="col-sm-9">
                <input type="text" value="<?php echo $TOP_HEADER_TXT; ?>" class="form-control" id="topHeading" name="TOP_HEADER_TXT">
              </div>
            </div>
            <div class="form-group row">
              <label for="topText" class="col-sm-3 col-form-label">Top Text:</label>
              <div class="col-sm-9">
                <input type="text" value="<?php echo $TOP_TEXT_TXT; ?>" class="form-control" id="topText" name="TOP_TEXT_TXT">
              </div>
            </div>
            <div class="form-group row">
              <label for="link1" class="col-sm-3 col-form-label">Link 1:</label>
              <div class="col-sm-9">
                <input type="text" value="<?php echo $LINK_URL_1; ?>" class="form-control" id="link1" name="LINK_URL_1">
              </div>
            </div>
            <div class="form-group row">
              <label for="desc1" class="col-sm-3 col-form-label">Description 1:</label>
              <div class="col-sm-9">
                <textarea class="form-control" id="desc1" rows="5" name="desc1" readonly><?php echo $LINK_TEXT_1; ?></textarea>
              </div>
            </div>
            <div class="form-group row">
              <label for="link2" class="col-sm-3 col-form-label">Link 2:</label>
              <div class="col-sm-9">
                <input type="text" value="<?php echo $LINK_URL_2; ?>" class="form-control" id="link2" name="LINK_URL_2">
              </div>
            </div>
            <div class="form-group row">
              <label for="desc2" class="col-sm-3 col-form-label">Description 2:</label>
              <div class="col-sm-9">
                <textarea class="form-control" id="desc2" rows="5" name="desc2" readonly><?php echo $LINK_TEXT_2; ?></textarea>
              </div>
            </div>
            <div class="form-group row">
              <label for="link3" class="col-sm-3 col-form-label">Link 3:</label>
              <div class="col-sm-9">
                <input type="text" value="<?php echo $LINK_URL_3; ?>" class="form-control" id="link3" name="LINK_URL_3">
              </div>
            </div>
            <div class="form-group row">
              <label for="desc3" class="col-sm-3 col-form-label">Description 3:</label>
              <div class="col-sm-9">
                <textarea class="form-control" id="desc3" rows="5" name="desc3" readonly><?php echo $LINK_TEXT_3; ?></textarea>
              </div>
            </div>

            <div class="form-group row">
              <label for="video_id" class="col-sm-3 col-form-label">Video ID:</label>
              <div class="col-sm-9">
                <div class="input-group">
                  <input type="text" value="<?php echo $VIDEO_ID; ?>" class="form-control" id="video_id" name="VIDEO_ID" placeholder="Enter YouTube Video ID or URL">
                  <div class="input-group-append">
                    <button type="button" class="btn btn-info" id="fetch_video_info">Fetch Info</button>
                  </div>
                </div>
                <small class="form-text text-muted">Enter YouTube Video ID (e.g., dQw4w9WgXcQ) or paste YouTube URL</small>
              </div>
            </div>
            
            <!-- Video Preview Section -->
            <div class="form-group row">
              <div class="col-sm-12">
                <div id="video_preview" style="display: none;"></div>
                <div id="video_loading" style="display: none; text-align: center; padding: 20px;">
                  <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                  </div>
                  <p>Fetching video information...</p>
                </div>
                <div id="video_error" style="display: none; color: red; padding: 10px;"></div>
              </div>
            </div>


            <button type="submit" class="btn btn-primary">Submit</button>
            <?php
                  // Check if the message session variable is set
                  if (isset($_SESSION['message'])) {
                      // Display the message and unset the session variable
                      echo "<p class='d-sub-mess'>" . $_SESSION['message'] . "</p>";
                      unset($_SESSION['message']);
                  }
                  ?>
          </form>
        </div>
        <div class="col-md-6">
          <!-- Content for the second column -->
         
        <h4>Newsletter HTML</h4>
        <!-- <button id="copyButton" style="display: none;">Copy HTML</button> -->

       <div class="d-new-template">
      
       <?php
          if (isset($TOP_HEADER_TXT)) {
            $html = str_replace('TOP_HEADER_TXT', $TOP_HEADER_TXT, $html);
        }
        if (isset($TOP_TEXT_TXT)) {
            $html = str_replace('TOP_TEXT_TXT', $TOP_TEXT_TXT, $html);
        }
        if (isset($LINK_OG_IMG_1)) {
            $html = str_replace('LINK_OG_IMG_1', $LINK_OG_IMG_1, $html);
        }
        if (isset($LINK_URL_1)) {
            $html = str_replace('LINK_URL_1', $LINK_URL_1, $html);
        }
        if (isset($LINK_OG_TITLE_1)) {
            $html = str_replace('LINK_OG_TITLE_1', $LINK_OG_TITLE_1, $html);
        }
        if (isset($LINK_TEXT_1)) {
            $html = str_replace('LINK_TEXT_1', $LINK_TEXT_1, $html);
        }
        if (isset($LINK_OG_IMG_2)) {
            $html = str_replace('LINK_OG_IMG_2', $LINK_OG_IMG_2, $html);
        }
        if (isset($LINK_URL_2)) {
            $html = str_replace('LINK_URL_2', $LINK_URL_2, $html);
        }
        if (isset($LINK_OG_TITLE_2)) {
            $html = str_replace('LINK_OG_TITLE_2', $LINK_OG_TITLE_2, $html);
        }
        if (isset($LINK_TEXT_2)) {
            $html = str_replace('LINK_TEXT_2', $LINK_TEXT_2, $html);
        }
        if (isset($LINK_OG_IMG_3)) {
            $html = str_replace('LINK_OG_IMG_3', $LINK_OG_IMG_3, $html);
        }
        if (isset($LINK_URL_3)) {
            $html = str_replace('LINK_URL_3', $LINK_URL_3, $html);
        }
        if (isset($LINK_OG_TITLE_3)) {
            $html = str_replace('LINK_OG_TITLE_3', $LINK_OG_TITLE_3, $html);
        }
        if (isset($LINK_TEXT_3)) {
            $html = str_replace('LINK_TEXT_3', $LINK_TEXT_3, $html);
        }

        if (isset($VIDEO_ID)) {
            $html = str_replace('VideoID', $VIDEO_ID, $html);
        }

          // $html = str_replace('TOP_HEADER_TXT', $TOP_HEADER_TXT, $html);
          // $html = str_replace('TOP_TEXT_TXT', $TOP_TEXT_TXT, $html);
          // $html = str_replace('LINK_OG_IMG_1', $LINK_OG_IMG_1, $html);
          // $html = str_replace('LINK_URL_1', $LINK_URL_1, $html);
          // $html = str_replace('LINK_OG_TITLE_1', $LINK_OG_TITLE_1, $html);
          // $html = str_replace('LINK_TEXT_1', $LINK_TEXT_1, $html);
          // $html = str_replace('LINK_OG_IMG_2', $LINK_OG_IMG_2, $html);
          // $html = str_replace('LINK_URL_2', $LINK_URL_2, $html);
          // $html = str_replace('LINK_OG_TITLE_2', $LINK_OG_TITLE_2, $html);
          // $html = str_replace('LINK_TEXT_2', $LINK_TEXT_2, $html);
          // $html = str_replace('LINK_OG_IMG_3', $LINK_OG_IMG_3, $html);
          // $html = str_replace('LINK_URL_3', $LINK_URL_3, $html);
          // $html = str_replace('LINK_OG_TITLE_3', $LINK_OG_TITLE_3, $html);
          // $html = str_replace('LINK_TEXT_3', $LINK_TEXT_3, $html);

                  
                  
                  echo htmlspecialchars_decode($html); 
          ?>
       </div>
       

          
         
          
         </div>
       
        </div>
      
        
    <footer class="site-footer" itemscope="itemscope" itemtype="https://schema.org/WPFooter">
      <!-- SCHEMA BEGIN --><script type="application/ld+json">
      [{
      "@context":"https:\/\/schema.org",
      "@type":"WebPage",
      "mainEntityOfPage":{
      "@type":"WebPage",
      "@id":"https:\/\/www.nwengineeringllc.com\/pcb-material-search"},
      "headline":"PCB Material Search",
      "datePublished":"2017-06-12T04:57:41+00:00",
      "dateModified":"2020-01-25T13:11:26+00:00",
      "description":"NWES provides R&D, electronics and PCB design services, and embedded systems to technology companies."}]
      </script><!-- /SCHEMA END -->
        <div class="container">


            <!-- <div id="footer"></div> -->
            <hr />
            <div class="row text-center">
              <div class="col-md-12">
                <p>
                Copyright &copy; <script>document.write(new Date().getFullYear());</script> Northwest Engineering Solutions LLC
                </p>
                <p style="font-size: 70%; color: #d3d3d3"><a href="https://www.nwengineeringllc.com/compliance.php">Compliance</a>&nbsp;|&nbsp;<a href="https://www.nwengineeringllc.com/terms-of-service.php">Terms of Service</a>&nbsp;|&nbsp;<a href="https://www.nwengineeringllc.com/privacy-policy.php">Privacy Policy</a>&nbsp;|&nbsp;<a href="https://www.nwengineeringllc.com/disclaimer.php">Disclaimer</a></p>
        
            </div>
          </div>
    </footer>
    <!-- footer ends -->
    <meta itemprop="url" content="https://www.nwengineeringllc.com/pcb-material-search">
    <span itemprop="publisher" itemscope itemtype="http://schema.org/Organization">
    <meta itemprop="name" content="Northwest Engineering Solutions"></span>
  </div>

  <script src="https://www.nwengineeringllc.com/js/jquery-migrate-3.0.1.min.js"></script>
  <script src="https://www.nwengineeringllc.com/js/jquery-ui.js"></script>
  <script src="https://www.nwengineeringllc.com/js/popper.min.js"></script>
  <script src="https://www.nwengineeringllc.com/js/bootstrap.min.js"></script>
  <script src="https://www.nwengineeringllc.com/js/owl.carousel.min.js"></script>
  <script src="https://www.nwengineeringllc.com/js/jquery.stellar.min.js"></script>
  <script src="https://www.nwengineeringllc.com/js/jquery.countdown.min.js"></script>
  <script src="https://www.nwengineeringllc.com/js/jquery.magnific-popup.min.js"></script>
  <script src="https://www.nwengineeringllc.com/js/bootstrap-datepicker.min.js"></script>
  <script src="https://www.nwengineeringllc.com/js/aos.js"></script>
  <script src="https://www.nwengineeringllc.com/js/slick.js" type="text/javascript" charset="utf-8"></script>
  <script src="https://www.nwengineeringllc.com/js/main.js"></script>
<script src="https://www.nwengineeringllc.com/js/pageutils.js"></script>
<script src="https://www.nwengineeringllc.com/js/pageutils2.js"></script>
  <script type="text/javascript">
		$(document).ready(function(){
			$('.customer-logos').slick({
				slidesToShow: 6,
				slidesToScroll: 1,
				autoplay: true,
				autoplaySpeed: 1000,
				arrows: false,
				dots: false,
					pauseOnHover: false,
					responsive: [{
					breakpoint: 768,
					settings: {
						slidesToShow: 4
					}
				}, {
					breakpoint: 520,
					settings: {
						slidesToShow: 3
					}
				}]
			});
		});
	</script>
<!-- <script src="https://files.getvirtualbrain.com/scripts/main.embedded.js" type="module"></script>
<script>
	window.virtualBrainId="5ba2279e-6d83-43ee-8a6a-3e543ecd1dba"
</script> -->
<script>
// window.onload = function() {
//   var div = document.querySelector('.d-new-template');
//   var button = document.createElement('button');
//   button.textContent = 'Copy HTML';
//   button.style.display = 'none';
//   div.appendChild(button);

//   div.addEventListener('mouseover', function() {
//     button.style.display = 'block';
//   });

//   div.addEventListener('mouseout', function() {
//     button.style.display = 'none';
//   });

//   button.addEventListener('click', function() {
//     var tempElement = document.createElement('textarea');
//     tempElement.value = div.innerHTML;
//     document.body.appendChild(tempElement);
//     tempElement.select();
//     document.execCommand('copy');
//     document.body.removeChild(tempElement);
//     alert('HTML copied to clipboard');
//   });
// };
window.onload = function() {
  // Copy HTML functionality
  var div = document.querySelector('.d-new-template');
  var button = document.createElement('button');
  button.textContent = 'Copy HTML';
  button.style.display = 'none';
  div.appendChild(button);

  div.addEventListener('mouseover', function() {
    button.style.display = 'block';
  });

  div.addEventListener('mouseout', function() {
    button.style.display = 'none';
  });

  button.addEventListener('click', function() {
    var tempElement = document.createElement('textarea');
    // Clone the div and remove the button from the clone before copying its HTML
    var clone = div.cloneNode(true);
    clone.removeChild(clone.querySelector('button'));
    tempElement.value = clone.innerHTML;
    document.body.appendChild(tempElement);
    tempElement.select();
    document.execCommand('copy');
    document.body.removeChild(tempElement);
    alert('HTML copied to clipboard');
  });

  // YouTube Video Info functionality
  var fetchButton = document.getElementById('fetch_video_info');
  var videoIdInput = document.getElementById('video_id');
  var videoPreview = document.getElementById('video_preview');
  var videoLoading = document.getElementById('video_loading');
  var videoError = document.getElementById('video_error');

  fetchButton.addEventListener('click', function() {
    var videoId = videoIdInput.value.trim();
    
    if (!videoId) {
      alert('Please enter a Video ID or URL');
      return;
    }

    // Extract video ID if URL is provided
    if (videoId.includes('youtube.com') || videoId.includes('youtu.be')) {
      // Simple URL parsing - you can enhance this
      var urlMatch = videoId.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/);
      if (urlMatch) {
        videoId = urlMatch[1];
        videoIdInput.value = videoId;
      }
    }

    // Show loading
    videoLoading.style.display = 'block';
    videoPreview.style.display = 'none';
    videoError.style.display = 'none';

    // Fetch video info via AJAX
    fetch('youtube-integration.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: 'action=get_video_info&video_id=' + encodeURIComponent(videoId)
    })
    .then(response => response.json())
    .then(data => {
      videoLoading.style.display = 'none';
      
      if (data.success) {
        // Display video preview
        videoPreview.innerHTML = generateVideoPreviewHTML(data.data);
        videoPreview.style.display = 'block';
      } else {
        // Show error
        videoError.textContent = data.message;
        videoError.style.display = 'block';
      }
    })
    .catch(error => {
      videoLoading.style.display = 'none';
      videoError.textContent = 'Error fetching video information: ' + error.message;
      videoError.style.display = 'block';
    });
  });

  // Function to generate video preview HTML
  function generateVideoPreviewHTML(videoInfo) {
    var html = '<div class="video-preview" style="border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 5px;">';
    html += '<h4>Video Preview</h4>';
    
    // Thumbnail
    if (videoInfo.thumbnail_medium) {
      html += '<div style="text-align: center; margin-bottom: 15px;">';
      html += '<img src="' + videoInfo.thumbnail_medium + '" alt="Video Thumbnail" style="max-width: 100%; height: auto; border-radius: 5px;">';
      html += '</div>';
    }
    
    // Video information
    html += '<div class="video-info">';
    html += '<p><strong>Title:</strong> ' + videoInfo.title + '</p>';
    html += '<p><strong>Channel:</strong> ' + videoInfo.channel_title + '</p>';
    html += '<p><strong>Published:</strong> ' + videoInfo.published_at + '</p>';
    html += '<p><strong>Views:</strong> ' + parseInt(videoInfo.view_count).toLocaleString() + '</p>';
    
    // Links
    html += '<div style="margin-top: 15px;">';
    html += '<a href="https://www.youtube.com/watch?v=' + videoInfo.video_id + '" target="_blank" class="btn btn-primary btn-sm">Watch on YouTube</a> ';
    html += '<a href="https://www.youtube.com/embed/' + videoInfo.video_id + '" target="_blank" class="btn btn-secondary btn-sm">Embed URL</a>';
    html += '</div>';
    
    html += '</div>';
    html += '</div>';
    
    return html;
  }
};
</script>
  </body>
