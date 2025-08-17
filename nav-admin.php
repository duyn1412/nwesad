<?php
$base_url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/nwesadmin';

$current_page = basename($_SERVER['REQUEST_URI']);

$nav_items = array(
   
    'newsletter',
    'blog',
    'video'
);


//var_dump($_SESSION); 
if (isset($_SESSION['admin'])) {
  //$nav_items[] = 'dashboard';
  $nav_items[] = 'users';
  
  array_unshift($nav_items, 'dashboard');

 
}

if (isset($_COOKIE['username'])) {
   
  $nav_items[] = 'logout';
}



?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <!-- <a class="navbar-brand" href="#">Navbar</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button> -->
  <div class="collapse navbar-collapse show" id="navbarNav">
    <ul class="navbar-nav">
     
      <?php
        foreach ($nav_items as $nav_item) {
            $active_class = ($nav_item == $current_page) ? 'active' : '';
            $display_name = ucwords(str_replace('-', ' ', $nav_item));

            echo '<li class="nav-item '.$active_class.'">
            <a class="nav-link" href="'.$base_url.'/'.$nav_item.'">'.$display_name.'</a>
          </li>';
        }

        // if (!isset($_SESSION['admin'])) {
        //   echo '<li class="nav-item ">
        //     <a class="nav-link" href="'.$base_url.'/'.$nav_item.'">'.$nav_item.'</a>
        //   </li>';
        // }
    
     ?>

  
    </ul>
  </div>
</nav>