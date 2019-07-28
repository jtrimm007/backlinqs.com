<?php

include_once 'functions.php';


if(isset($_POST["limit"], $_POST["start"])) {
    $conn = new DatabaseQuery(USER, PASS, CONNETIONSTRING);

    $query = $conn->LazyLoadQuery($_POST["start"], $_POST["limit"]);

    $search = new GoogleSearch();

  //  $query = $search->GoogleSearch( $_SESSION['linqSearch']);



    foreach ($query as $each)
    {

            $url = $search->GetUrlInformation($each);


        if($url[0] != null || $url[1] != null)
        {
            echo "<div class=\"row mb-2\">
    <div class=\"col-md-12\">
      <div class=\"row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm position-relative\">
        <div class=\"col p-4 d-flex flex-column position-static\">
          <h3 class=\"mb-0\">".$url[0]."</h3>
          <p class=\"card-text mb-auto\">".$url[1]."</p>
          <p class=\"card-text mb-auto\">".$url[2]."</p>
          <a href=\"/linq-browser/".$url[2]."\" class=\"btn btn-outline-primary col-md-3\"  >Continue reading</a>
        </div>
      </div>
    </div>
  </div>";
        }
    }
}