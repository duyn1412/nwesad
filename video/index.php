<?php
// Debug code
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// 

session_start();
include '../connect-sql.php';

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
        "dateModified":"2020-01-25T13:13:26+00:00",
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

      <!-- Include navigation -->
      <?php include __DIR__ . '/../nav-admin.php'; ?>

    

      <!-- Tab Content -->
      <div class="tab-content" id="videoTabContent">
        <!-- AI Assistant Tab -->
        <div class="tab-pane fade show active" id="ai-assistant" role="tabpanel" aria-labelledby="ai-assistant-tab">
          <div class="row">
            <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h5 class="mb-0">YouTube Transcript Fetcher</h5>
                  </div>
                  <div class="card-body">
                    <div class="mb-3">
                      <label for="youtubeUrl" class="form-label">YouTube Video URL</label>
                      <div class="input-group">
                        <input type="text" id="youtubeUrl" class="form-control" placeholder="Enter YouTube URL (e.g., https://www.youtube.com/watch?v=VIDEO_ID)" />
                        <button type="button" id="fetchBtn" class="btn btn-primary">
                          <span class="btn-text">Fetch Transcript</span>
                          <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>
                      </div>
                      <small class="form-text text-muted">Supports YouTube watch URLs, short URLs (youtu.be), and embed URLs</small>
                    </div>

                    <div class="mb-3">
                      <label for="transcript" class="form-label">Transcript</label>
                      <textarea id="transcript" class="form-control" rows="8" placeholder="Enter your transcript here manually or paste from another source..."></textarea>
                      <div class="form-text">
                        <span id="transcriptStatus" class="text-muted">Ready to enter transcript manually</span>
                      </div>
                    </div>

                    <div class="mb-3">
                      <button type="button" id="sendBtn" class="btn btn-success" disabled>
                        <span class="btn-text">Send to Assistant</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                      </button>
                      <small class="form-text text-muted d-block mt-1">Send transcript to OpenAI Assistant for analysis</small>
                    </div>

                    <div class="mb-3">
                      <label for="assistantReply" class="form-label">Assistant Reply</label>
                      <textarea id="assistantReply" class="form-control" rows="6" placeholder="Assistant response will appear here..." readonly></textarea>
                      <div class="form-text">
                        <span id="assistantStatus" class="text-muted">Ready to process</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </div>

        <!-- Batch Processing Tab -->
        <div class="tab-pane fade" id="batch-processing" role="tabpanel" aria-labelledby="batch-processing-tab">
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h5 class="mb-0">Batch Processing</h5>
                </div>
                <div class="card-body">
                  <p>Batch processing functionality will be implemented here.</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Settings Tab -->
        <div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="settings-tab">
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h5 class="mb-0">Settings</h5>
                </div>
                <div class="card-body">
                  <p>Settings configuration will be implemented here.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    
    <script src="transcript-app.js"></script>
    
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
      "dateModified":"2020-01-25T13:13:26+00:00",
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

  </body>
</html>

