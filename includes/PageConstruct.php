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
//        var_dump($page[1]);
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
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <link href="https://fonts.googleapis.com/css?family=Playfair+Display:700,900" rel="stylesheet">

            <?php
//            var_dump($this->pageTitle);


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
            <div class="container-fluid">
                <?php $this->Header(); ?>
                <?php $this->Navigation(); ?>
                <h1><?php echo $this->pageTitle; ?></h1>
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
                        echo '<p>Please login or setup and account to submit links.</p>';
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

                    if($_SERVER['REQUEST_URI'] == '/linq-browser')
                    {
                        echo $this->GetLinkInformationFromDatabasePermalink();
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
        echo '
      <header class="container-fluid blog-header py-3">
        <div class="row flex-nowrap justify-content-between align-items-center">
          <div class="col-4 pt-1">
            <a class="text-muted" href="/blog">Blog</a>
          </div>
          <div class="col-4 text-center">
            <a class="blog-header-logo text-dark" href="/home">BackLinqs.com</a>
          </div>
          <div class="col-4 d-flex justify-content-end align-items-center">
            <a class="text-muted" href="#">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-3"><circle cx="10.5" cy="10.5" r="7.5"></circle><line x1="21" y1="21" x2="15.8" y2="15.8"></line></svg>
            </a>
            <a class="btn btn-sm btn-outline-secondary" href="/new-user">Join us</a>
          </div>
        </div>
      </header>';
    }

    /**
     * Description: HTML document body footer
     */
    public function Footer()
    {
        ?><footer class="blog-footer">
            <p>Website Design by: <a href="https://trimwebdesign.com/" target="_blank">Trim Web Design & SEO</a> .</p>
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
        ?><div class="container nav-scroller py-1 mb-2">
        <nav class="nav d-flex justify-content-between">
            <a class="p-2 text-muted" href="/home">Home</a>
            <a class="p-2 text-muted" href="/contact">Contact</a>
            <a class="p-2 text-muted" href="/how-it-works">How it Works</a>

            <a class="p-2 text-muted" href="/about">About</a>
            <a class="p-2 text-muted" href="/submit-links">Submit Links</a>
            <a class="p-2 text-muted" href="/linq-browser">Linq Browser</a>

            <script>
                if(readCookie('status') === 'true')
                {
                    console.log(readCookie('status'));
                    document.write('<a class="p-2 text-muted" href="/dashboard"><?php echo $_COOKIE['user']; ?></a><a class="p-2 text-muted" href="/?logout=true">logout</a>');
                }
                else {
                    document.write('<a class="p-2 text-muted" href="/login">Login</a>');
                }
            </script>

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

        $results = $query->SelectAll('backlinqs_links');


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
            //var_dump($_POST['inputEmail']);

        $_SESSION['inputEmail'] = $_POST['inputEmail'];
        $_SESSION['inputPassword'] = $_POST['inputPassword'];

        }
    }

    /**
     * Description: Sets the home page content and title
     */
    public function HomePage()
    {
        //var_dump($_COOKIE['user']);
        $loginUri = "/?status=true&user=".$_COOKIE['user']."";
        $linqBrowser = "<form method='get'><input type='text' placeholder='Start your search!'><button type='submit'>Search Linqs</button></form><br>";

        if($_SERVER['REQUEST_URI'] == "/" || $_SERVER['REQUEST_URI'] == $loginUri)
        {
            $page = array();

            $query = new DatabaseQuery(USER, PASS, CONNETIONSTRING);
            $selectAll = $query->SelectTitleAndContentForHomePage("home");
            $query->CloseConnection();

            foreach($selectAll as $item)
            {
                $this->pageTitle = $item['Title'];
                $this->pageContent = $linqBrowser . $item['Content'];
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
        //var_dump($this->pageTitle);
        //var_dump($this->pageTitle);
        if($this->pageTitle === "Dashboard" || strpos($_SERVER['REQUEST_URI'], 'dashboard' ) == true)
        {
            //var_dump($this->pageTitle);

            $dynamicMenu = '';
            $status = $user->GetUserStatus($_COOKIE['user']);
            $user->GetUserRole($_COOKIE['user']);
            $postPageList = $this->PostListForDashboard();

            //var_dump($user->role);
            if($user->role <= 4)
            {
                $dynamicMenu = "
            <ul>
              <li><a href=\"/dashboard/account\" >Account</a></li>
              <li><a href=\"/dashboard/profile\" >Profile</a></li>
              <li><a href=\"/dashboard/linqs-provided\" >Linqs Provided</a></li>
              <li><a href=\"/dashboard/linqs-requested\" >Linqs Requested</a></li>
              <li><a href=\"/dashboard/linq-exchange\" >Linq Exchange</a></li>
              <li><a href=\"/dashboard/post\" >Post</a></li>
              <li><a href=\"/dashboard/create-post\" >Create Post</a></li>
              <li><a href=\"/dashboard/create-user\" >Create User</a></li>
              <li><a href=\"/dashboard/insert-scripts\" >Insert Scripts</a></li>



           </ul>";
            }
            else{
                $dynamicMenu = "          
          <ul>
              <li><a href=\"/dashboard/account\" >Account</a></li>
              <li><a href=\"/dashboard/profile\" >Profile</a></li>
              <li><a href=\"/dashboard/linqs-provided\" >Linqs Provided</a></li>
              <li><a href=\"/dashboard/linqs-requested\" >Linqs Requested</a></li>
              <li><a href=\"/dashboard/linq-exchange\" >Linq Exchange</a></li>
           </ul>";
            }

            //var_dump($status);
            if( $status == true)
            {

                if($_SERVER['REQUEST_URI'] == '/dashboard/post')
                {
                    return "
 <div class=\"row mb-2\">
    <div class=\"col-md-3\">
      <div class=\"row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative\">
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
                elseif (isset($_GET['post']))
                {
                    if(isset($_POST['title']))
                    {
                        $database = new DatabaseQuery(USER, PASS, CONNETIONSTRING);
//                        var_dump($_POST['title']);
//                        var_dump($_POST['type']);
                        $testing = "<form method=\"post\">
<input name=\"title\" type=\"text\">
<textarea id=\"editor\" name=\"content\"></textarea>
<script>
                    var editor = new Jodit('#editor', 
   iframe = true
   );
</script>
    <div class=\"row\">
      <button class=\"btn btn-primary\" type=\"submit\">Submit Linq</button>
    </div>
</form>";
                        //var_dump(filter_var( $testing, FILTER_SANITIZE_SPECIAL_CHARS));

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
    <div class=\"col-md-3\">
      <div class=\"row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative\">
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
    <div class=\"col-md-3\">
      <div class=\"row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative\">
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
    <input type=\"text\" name=\"title\" value=\"".$item['Title']."\">
    <h3>permalink</h3>
    <input type=\"text\" name=\"permalink\" value=\"".$item['permalink']."\">
    <h3>Type</h3>
    <select type='text' name=\"type\">
    <option value=\"page\">page</option>
    <option value=\"blog\">blog</option>
    </select>
    <h3>Content</h3>
    <textarea name=\"content\" id=\"editor\" rows='10' cols=\"100\">".htmlspecialchars($item['Content'])."</textarea>
        <div class=\"row\">
      <button class=\"btn btn-primary\" type=\"submit\" >Update Post</button>
    </div>
</form>";

        }


        $database->CloseConnection();

        return $form;

    }

}