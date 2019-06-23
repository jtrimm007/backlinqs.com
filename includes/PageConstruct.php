<?php


class PageConstruct
{

public $css;
public $javaScript;
public $pageTitle;
public $pageContent;
public $newUserPageTitle;
public $createdNewUser;
public $loginPageTitle;
public $loggedIn;
public $currentUser;
public $getLinksPageTitle;
public $getLinkUrl;
public $getLinkProviderEmail;

/**
 * PageConstruct constructor.
 * @param $page
 */
public function __construct($page)
{

    $this->pageTitle = $page[0]['Title'];
    $this->pageContent = html_entity_decode($page[0]['Content'], ENT_QUOTES);
    $this->getLinkUrl = $page[0]['LinkUrl'];
    if(isset($page[1]))
    {
        $this->getLinkProviderEmail = $page[1];
    }
    $this->Page();
}

/**
 * Description: Constructs a page
 */
public function Page()
{
    //$this->Login("Login");
    $this->HomePage();
    $this->Head();
    $this->Body();
    $this->IsNewUserPage("New User");
    //$this->Login("Login");
    $this->GetLinkPage("Get Links");

}

/**
 * Description: HTML document head
 */
public function Head()
{



?><!doctype html>
<html lang="en">
<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-142406044-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-142406044-1');
    </script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,400i,700,700i,900,900i&display=swap" rel="stylesheet">
    <?php


    ?>



    <?php echo $this->PageTitle(); echo $this->GetJavaScript(); echo $this->GetCSS(); ?>
    <?php

    //Editor scripts
    if($this->pageTitle == 'Create Post' || $this->pageTitle == 'Submit Links' || $this->pageTitle == 'Request LinqExchange' || isset($_GET['post']))
    {


    }


    $this->Dashboard();


    if($_COOKIE['status'] == "true")
    {
        $cookieEmail = $_COOKIE['user'];
        $checkStatus = CheckUserStatus($cookieEmail);

        //authenticate user via database
        if($checkStatus != 'true')
        {
            ?>
            <script>

                createCookie('status', "", -1);
                createCookie('user', "", -1);

            </script>
            <?php
        }
    }
    ?>
    <script>
        var urlParams = new URLSearchParams(window.location.search);

        var status = urlParams.get('status');
        var user = urlParams.get('user');

        console.log(readCookie('user'));
        console.log(readCookie('status'));

        if(status === 'true')
        {
            createCookie('status', status, 1);
            createCookie('user', user, 1);
        }
    </script>
<?php


}

/**
 * Description: HTML document body
 */
public function Body()
{
?><body>
<?php $this->Header(); ?>
<div class="container-fluid">

    <?php $this->Navigation(); ?>
    <?php
    if(strpos($_SERVER['REQUEST_URI'], '/linq-browser/') !== false)
    {
        echo '<h1>'.$this->pageTitle.'</h1>';
    } ?>
    <?php
    if($this->pageTitle == "Request LinqExchange")
    {

        $getUserLinqRequested = new DatabaseQuery(USER, PASS, CONNETIONSTRING);


        echo 'You are about to request a LinqShare from <a href="/get-links/'.$_COOKIE['Linq'].'">'.$_COOKIE['LinqTitle'].'</a><br>';

        $getUserLinqRequested->CloseConnection();
    }




    if($this->pageTitle == "Dashboard" || strpos($_SERVER['REQUEST_URI'], "dashboard"))
    {
        echo $this->Dashboard();
    }
    elseif ($this->pageTitle == "Submit Links")
    {
        if($_COOKIE['user'] == null || CheckUserStatusQuery($_COOKIE['user']) == 0)
        {
            echo '<p>Please <a href="/login">login</a> or <a href="/new-user">setup</a> an account to submit links.</p>';
        }
        else
        {
            echo $this->pageContent;
        }
    }
    else
    {
        echo $this->pageContent;

    }



    if($this->getLinkUrl != null)
    {
        if($_COOKIE['user'] == null || CheckUserStatusQuery($_COOKIE['user']) == 0)
        {
            echo '<p>Please login or setup and account to request links.</p>';
        }
        else{


            ?>
            <p>Link Url: <a href="<?php echo $this->getLinkUrl; ?>" target="_blank"><?php echo $this->getLinkUrl; ?></a></p>
            <p>Linq Provider Email: <a href="mailto:<?php echo $this->getLinkProviderEmail; ?>"><?php echo $this->getLinkProviderEmail; ?></a> </p>
            <iframe src="<?php echo $this->getLinkUrl; ?>" width="500px" height="500px"></iframe>
            <script>

                console.log(getUrlPathName());
                createCookie("Linq", getUrlPathName(), 1);
                createCookie("LinqTitle", "<?php echo $this->pageTitle; ?>", 1);

                //Enable code below for future use
                //document.write("<button><a href=\"/request-linqexchange\">Request LinqExchange</a></button>")
            </script>

        <?php               }
    }


    ?>

    <p><?php //echo $this->GetPostInformationFromDatabasePermalink(); ?></p>
    <?php

    if($_SERVER['REQUEST_URI'] == '/link-browser')
    {

        $this->GetLinkInformationFromDatabasePermalink();
    }

    ?>
</div>
<?php echo $this->Footer(); ?>
</body>
</html><?php
}

/**
 * Description: HTML body header
 */
public function Header()
{
    ?>
    <header class="mt-3 container-fluid">
    <div class="row d-flex ">
        <div class="col-sm-2 text-center">
            <script>
                if(readCookie('status') === 'true')
                {
                    console.log(readCookie('status'));
                    document.write('<a class="p-2 text-muted" href="/?logout=true">logout</a>');
                }
                else {
                    document.write('<a class="p-2 text-muted" href="/login">login</a>');
                }
            </script>

        </div>
        <div class="col-sm text-center">
            <h1><a class="blog-header-logo text-dark"  href="/">BackLinqs.com</a></h1>
        </div>
        <div class="col-sm-2 flex-row-reverse text-center ">
            <script>
                if(readCookie('status') === 'true')
                {
                    console.log(readCookie('status'));
                    document.write('<a class="text-muted d-flex justify-content-center justify-content-lg-end" href="/dashboard"><?php echo $_COOKIE['user']; ?></a>');
                }
                else {
                    document.write('<a class="text-muted p-2 " href="/new-user">join us</a>');
                }
            </script>
        </div>
    </div>
    </header><?php
}

/**
 * Description: HTML document body footer
 */
public function Footer()
{
    ?><footer class="blog-footer">
    <p>Website Design & Developed by: <a href="https://trimwebdesign.com/" target="_blank">Joshua Trimm</a>.</p>
    <p>
        <a href="#">Back to top</a>
    </p>
</footer>
    <?php
}

/**
 * Description: HTML document navigation
 */
public function Navigation()
{
    ?><div class="container nav-scroller py-1 mb-2 mt-lg-5 mt-sm-3">
    <nav class="nav d-flex justify-content-between">
        <a class="p-2 text-muted" href="/about">about</a>
        <a class="p-2 text-muted" href="/how-it-works">how it works</a>
        <a class="p-2 text-muted" href="/contact">contact</a>
        <a class="p-2 text-muted" href="/blog">blog</a>
        <a class="p-2 text-muted" href="/submit-links">submit links</a>
    </nav>
</div><?php
}


/**
 * Description: Head page title
 */
public function PageTitle()
{
    ?><title><?php echo $this->pageTitle; ?></title><?php
}

/**
 * Description: Gets all the JavaScript file locations and put them into an array
 * @return array
 */
public function GetJavaScriptFiles()
{

    $JAVASCRIPT = array();

    foreach (glob("js/*.js") as $file)
    {
        array_push($JAVASCRIPT, $file);
    }

    return $JAVASCRIPT;
}

/**
 * Description: Gets all the CSS file locations and put them into an array
 * @return array
 */
public function GetCSSFiles()
{
    $CSS = array();

    foreach(glob("css/*.css") as $file)
    {
        array_push($CSS, $file);
    }

    return $CSS;

}

/**
 * Description: Get and places all the JavaScript files for the HTML head
 */
public function GetJavaScript()
{

    foreach ($this->GetJavaScriptFiles() as $file)
    {
        echo '<script type="text/javascript" src="https://'. $_SERVER[HTTP_HOST]. '/' . $file .'"></script>';
    }
}

/**
 * Description: Get and places all the CSS files for the HTML head
 */
public function  GetCSS()
{
    foreach ($this->GetCSSFiles() as $file)
    {
        echo '<link rel="stylesheet" href="https://'. $_SERVER[HTTP_HOST]. '/' . $file .'">';
    }
}

/**
 * Description: Gets all the post IDs and post titles from the database and puts them into an unordered list.
 */
public function GetPostInformationFromDatabase()
{
    $query = new DatabaseQuery(USER, PASS, CONNETIONSTRING);

    $results = $query->SelectAll('backlinqs_post');


    $query->CloseConnection();

    echo '<ul>';
    foreach ($results as $result)
    {
        echo '<li><a href="?id=' . $result['PostId'] . '">' . $result['Title'] . '</a></li>';

    }
    echo '</ul>';

    //return $results;
}

/**
 *Description: Gets all the post permalink's and post titles from the database and puts them into an unordered list.
 */
public function GetPostInformationFromDatabasePermalink()
{
    $query = new DatabaseQuery(USER, PASS, CONNETIONSTRING);

    $results = $query->SelectAll('backlinqs_post');


    $query->CloseConnection();

    echo '<ul>';
    foreach ($results as $result)
    {
        echo '<li><a href="/' . $result['permalink'] . '">' . $result['Title'] . '</a></li>';

    }
    echo '</ul>';

    //return $results;
}

/**
 * Description: Gets all the linqs and puts them into an ul
 */
public function GetLinkInformationFromDatabasePermalink()
{
    $query = new DatabaseQuery(USER, PASS, CONNETIONSTRING);

    $results = array();


    if(isset($_SESSION['linqSearch']))
    {
        $results = $query->SearchForKeywordInLinks($_SESSION['linqSearch']);

    }
    else
    {
        $results = $query->SelectAll('backlinqs_links');

    }


    $query->CloseConnection();

    echo '<ul>';
    foreach ($results as $result)
    {
        //echo '<li><a href="/get-links/' . $result['permalink'] . '">' . $result['Title'] . '</a></li>';
        echo "<div class=\"row mb-2\">
    <div class=\"col-md-12\">
      <div class=\"row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm position-relative\">
        <div class=\"col p-4 d-flex flex-column position-static\">
          <h3 class=\"mb-0\">".$result['Title']."</h3>
          <div class=\"mb-1 text-muted\">".$result['Date']."</div>
          <p class=\"card-text mb-auto\">".$result['Content']."</p>
          <a href=\"/linq-browser/".$result['permalink']."\" class=\"btn btn-outline-primary col-md-3\"  >Continue reading</a>
        </div>
      </div>
    </div>
  </div>";

    }
    echo '</ul>';


}



/**
 * Description: Check to see if the current page is the new user page, if it is, it sets all the post variables for the new user form into sessions.
 * @param $newUserPage
 * @return mixed
 */
public function IsNewUserPage($newUserPage)
{

    if($this->pageTitle == $newUserPage)
    {
        $_SESSION['firstName'] = $_POST['firstName'];
        $_SESSION['lastName'] = $_POST['lastName'];
        $_SESSION['inputEmail'] = $_POST['inputEmail'];
        $_SESSION['inputPassword'] = $_POST['inputPassword'];
        $_SESSION['confirmPassword'] = $_POST['confirmPassword'];

        $this->newUserPageTitle = $newUserPage;

        return $newUserPage;
    }
}

/**
 * Description: Gets the link page title and sets the link page title property.
 * @param $getLinksPage
 * @return mixed
 */
public function GetLinkPage($getLinksPage)
{

    if($this->pageTitle == $getLinksPage)
    {

        $this->getLinksPageTitle = $getLinksPage;

        return $getLinksPage;
    }
}

/**
 * Description: Check to see if it the login in page and sets the post variables to sessions
 * @param $loginPage
 */
public function Login($loginPage)
{

    if($this->pageTitle === $loginPage)
    {

        $_SESSION['inputEmail'] = $_POST['inputEmail'];
        $_SESSION['inputPassword'] = $_POST['inputPassword'];

    }
}

/**
 * Description: Sets the home page content and title
 */
public function HomePage()
{
    $loginUri = "/?status=true&user=".$_COOKIE['user']."";
    $linqBrowser = "<form method='get' class='linq-search-form'><input name='linqSearch' class='linq-search-input col-sm-11' type='text' placeholder='search for available backlinqs....' required><button class='btn col-sm-1' type='submit'>GO</button></form><br>";

    if($_SERVER['REQUEST_URI'] == "/" || $_SERVER['REQUEST_URI'] == $loginUri)
    {
        $page = array();

        $query = new DatabaseQuery(USER, PASS, CONNETIONSTRING);
        $selectAll = $query->SelectTitleAndContentForHomePage("home");
        $query->CloseConnection();

        foreach($selectAll as $item)
        {

            $this->pageTitle = $item['Title'];


            $this->pageContent = $linqBrowser;
        }

    }
}

private function PostListForDashboard()
{
    $database = new DatabaseQuery(USER, PASS, CONNETIONSTRING);

    $results = $database->SelectAll('backlinqs_post');

    $stringAppend = '';

    foreach ($results as $result)
    {
        $author = $database->SelectAllCurrentUserWithId($result['UserId']);

        $name = '';
        foreach ($author as $each)
        {
            $name = $each['FirstName'] . ' ' . $each['LastName'];
        }

        $stringAppend .= "<div class=\"row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm position-relative\">
            <div class=\"col p-4 flex-column position-static\">
              <h3 class=\"mb-0\">".$result['Title']."</h3>
              <div class=\"mb-1 text-muted\">".$result['DateCreated']."</div>
              <p class=\"card-text \">Author ".$name." </p>
              <a href=\"/dashboard/editor?post=".$result['permalink']."\" class=\"stretched-link\"  >Edit Post</a>
            </div>
          </div>";

    }

    $database->CloseConnection();
    return $stringAppend;
}

/**
 * Description: Builds the dashboard body
 * @return string
 */
public function Dashboard()
{
    $user = new Users(USER, PASS, CONNETIONSTRING);
    if($this->pageTitle === "Dashboard" || strpos($_SERVER['REQUEST_URI'], 'dashboard' ) == true)
    {

        $dynamicMenu = '';
        $status = $user->GetUserStatus($_COOKIE['user']);
        $user->GetUserRole($_COOKIE['user']);
        $postPageList = $this->PostListForDashboard();
        $accountPage = $this->Account();

        if($user->role <= 4)
        {
            $dynamicMenu = "
            <ul class='list-unstyled'>
              <li><a href=\"/dashboard\" >dashboard</a></li>
              <li><a href=\"/dashboard/account\" >account</a></li>
              <li><a href=\"/dashboard/profile\" >profile</a></li>
              <li><a href=\"/dashboard/linqs\" >linqs</a></li>
              <li><a href=\"/dashboard/post\" >posts</a></li>
              <li><a href=\"/dashboard/create-post\" >create post</a></li>
              <li><a href=\"/dashboard/create-user\" >create user</a></li>
              <li><a href=\"/dashboard/insert-scripts\" >insert scripts</a></li>
           </ul>";
        }
        else{
            $dynamicMenu = "          
          <ul class='list-unstyled'>
              <li><a href=\"/dashboard/account\" >Account</a></li>
              <li><a href=\"/dashboard/profile\" >Profile</a></li>
              <li><a href=\"/dashboard/linqs-provided\" >Linqs Provided</a></li>
              <li><a href=\"/dashboard/linqs-requested\" >Linqs Requested</a></li>
              <li><a href=\"/dashboard/linq-exchange\" >Linq Exchange</a></li>
           </ul>";
        }


        if( $status == true)
        {

            if($_SERVER['REQUEST_URI'] == '/dashboard/post')
            {
                return "
 <div class=\"row mb-2\">
    <div class=\"col-sm-2 ml-sm-0 ml-md-5\">
      <div class=\"row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm position-relative\">
        <div class=\"col p-4 d-flex flex-column position-static\">
            ".$dynamicMenu."
        </div>
      </div>
    </div>
        <div class=\"col-md-9\">
              <div class=\"row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm  position-relative\">
        <div class=\"col p-4 d-flex flex-column position-static\">
        ".$this->pageContent."<br>".$postPageList."
        </div>
        </div>
        </div>

  </div>";
            }
            elseif ($_SERVER['REQUEST_URI'] == '/dashboard/account')
            {
                return "
 <div class=\"row mb-2\">
    <div class=\"col-sm-2 ml-sm-0 ml-md-5\">
      <div class=\"row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm position-relative\">
        <div class=\"col p-4 d-flex flex-column position-static\">
            ".$dynamicMenu."
        </div>
      </div>
    </div>
        <div class=\"col-md-9\">
              <div class=\"row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm  position-relative\">
        <div class=\"col p-4 d-flex flex-column position-static\">
        ".$this->pageContent."<br>".$accountPage."
        </div>
        </div>
        </div>

  </div>";
            }
            elseif (isset($_GET['post'])) // Checks to see if post variable is set to edit a post
            {
                if(isset($_POST['title'])) // Gets the title of the post to edit
                {
                    $database = new DatabaseQuery(USER, PASS, CONNETIONSTRING);


                    $getPostId = $database->SelectPostIdWithPermalink($_GET['post']);

                    foreach ($getPostId as $each)
                    {
                        $getPostId = (int)$each['PostId'];
                    }


                    $insert = $database->UpdatePost( $_POST['title'], (string)$_POST['content'], $_POST['permalink'], $_POST['type'], $getPostId);

                    $database->CloseConnection();
                }
                $this->pageTitle = 'Editor';
                return "
 <div class=\"row mb-2\">
    <div class=\"col-md-2 ml-sm-0 ml-md-5\">
      <div class=\"row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm position-relative\">
        <div class=\"col p-4 d-flex flex-column position-static\">
            ".$dynamicMenu."
        </div>
      </div>
    </div>
        <div class=\"col-md-9\">
              <div class=\"row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm  position-relative\">
        <div class=\"col p-4 d-flex flex-column position-static\">
        ".$this->pageContent."<br>".$this->Editor($_GET['post'])."
        </div>
        </div>
        </div>

  </div>";
            }
            else
            {
                return "
 <div class=\"row mb-2\">
    <div class=\"col-md-2 ml-sm-0 ml-md-5\">
      <div class=\"row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm position-relative\">
        <div class=\"col p-4 d-flex flex-column position-static\">
            ".$dynamicMenu."
        </div>
      </div>
    </div>
        <div class=\"col-md-9\">
              <div class=\"row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm  position-relative\">
        <div class=\"col p-4 d-flex flex-column position-static\">
        ".$this->pageContent."
        </div>
        </div>
        </div>

  </div>";

            }
        }
        else
        {
            return $this->pageContent = '<h3>Please <a href="/login">login</a> to access dashboard.</h3>';
        }
    }
}


public function Editor($permalink)
{

    $database = new DatabaseQuery(USER, PASS, CONNETIONSTRING);

    $getPost = $database->SelectContentWithPermalink($permalink);

    $form = '';

    foreach ($getPost as $item)
    {
        $form = "<form method=\"post\">
    <h3>Title</h3>
    <input class='border rounded' type=\"text\" name=\"title\" value=\"".$item['Title']."\">
    <h3>permalink</h3>
    <input class='border rounded' type=\"text\" name=\"permalink\" value=\"".$item['permalink']."\">
    <h3>Type</h3>
    <select class='border rounded' type='text' name=\"type\">
    <option value=\"page\">page</option>
    <option value=\"blog\">blog</option>
    </select>
    <h3>Content</h3>
    <textarea class='border rounded' name=\"content\" id=\"editor\" rows='10' cols=\"100\">".htmlspecialchars($item['Content'])."</textarea>
        <div class=\"row\">
      <button class=\"btn btn-primary\" type=\"submit\" >Update Post</button>
    </div>
</form>";

    }


    $database->CloseConnection();

    return $form;

}

public function Account()
{

    $database = new DatabaseQuery(USER, PASS, CONNETIONSTRING);

    $getInfo = $database->SelectUserInfo($_COOKIE['user-id']);

    $form = '';

    foreach ($getInfo as $item)
    {

        $form = "<form method=\"post\">
    <h3>Company</h3>
    <input class='border rounded' type=\"text\" name=\"company\" placeholder='Company Name' value=\"".$item['Company']."\">
    <h3>Facebook</h3>
    <input class='border rounded' type=\"text\" name=\"facebook\" placeholder='Facebook Profile URL' value=\"".$item['Facebook']."\">
    <h3>YouTube</h3>
    <input class='border rounded' type=\"text\" name=\"youtube\" placeholder='Facebook Profile URL' value=\"".$item['Youtube']."\">
    <h3>Instagram</h3>
    <input class='border rounded' type=\"text\" name=\"instagram\" placeholder='Facebook Profile URL' value=\"".$item['Youtube']."\">
    <h3>Phone</h3>
    <input class='border rounded' type=\"text\" name=\"phone\" placeholder='Facebook Profile URL' value=\"".$item['Youtube']."\">
    <h3>Bio</h3>
    <textarea class='border rounded' name=\"about\" id=\"editor\" rows='10' cols=\"100\">".htmlspecialchars($item['About'])."</textarea>


      <button class=\"btn btn-primary\" type=\"submit\" >Update Post</button>

</form>";

    }


    $database->CloseConnection();

    return $form;
}

}