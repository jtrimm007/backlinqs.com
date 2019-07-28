<?php

class GoogleSearch
{

    public function __construct()
    {

    }

    public function GoogleSearch($query)
    {
        $useragent = "Opera/9.80 (J2ME/MIDP; Opera Mini/4.2.14912/870; U; id) Presto/2.4.15";
        $ch = curl_init("");
        curl_setopt($ch, CURLOPT_URL, "http://www.google.com/search?hl=en&tbo=d&site=&source=hp&q=" . $query . "&num=10");
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent); // set user agent
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        libxml_use_internal_errors(true);
        //echo
        $output = curl_exec($ch);
        //var_dump($output);
        curl_close($ch);

        $dom = new DOMDocument();

        $dom->loadHTML($output);
        $out = array();
        foreach ($dom->getElementsByTagName('a') as $item) {
            $out[] = array(
                'str' => $dom->saveHTML($item),
                'href' => $item->getAttribute('href'),
                'anchorText' => $item->nodeValue
            );
        }

        $newArray = array();
        foreach ($out as $each) {

            $getHttp = str_replace('/url?q=', '', $each['href']);

            //var_dump(substr( $getHttp, 0, 4 ));
            if (substr($getHttp, 0, 4) === "http") {

                if (substr($getHttp, 11, 6) !== "google") {
                    if (substr($getHttp, 12, 6) !== "google") {


                        if (substr($getHttp, 8, 8) !== "accounts") {
                           // var_dump(substr($getHttp, 8, 8));
                            array_push($newArray, $getHttp);

                        }
                    }

                }


            }

        }

        return $newArray;
    }


    public function GetUrlInformation($urlArray)
    {
        $useragent = "Opera/9.80 (J2ME/MIDP; Opera Mini/4.2.14912/870; U; id) Presto/2.4.15";

        $ch = curl_init($urlArray["url"]);

        curl_setopt($ch, CURLOPT_USERAGENT, $useragent); // set user agent
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


        libxml_use_internal_errors(true);




        $output = curl_exec($ch);

        curl_close($ch);

        $out = array();

        $dom = new DOMDocument();
        $title = '';

        $url = "http://" . $urlArray["url"];

        if(@file_get_contents($url) !== FALSE)
        {

            if ($dom->loadHTML(@file_get_contents($url)) == true)
            {


                $description = '';

                $list = $dom->getElementsByTagName("title");
                $meta = $dom->getElementsByTagName("meta");


                foreach ($meta as $item)
                {
                    //var_dump($item->getAttribute('name'));
                    if (strtolower($item->getAttribute('name')) == 'description')
                    {
                        //var_dump($item->getAttribute('content'));
                        $description = $item->getAttribute('content');
                    }
                }
               foreach ($list as $each)
                {

                    $title = $each->nodeValue;

                }

            }
        }

        array_push($out, $title, $description, $urlArray["url"]);

        return $out;
    }

    public function GetUrlInformationNoneArray($url)
    {
        $useragent = "Opera/9.80 (J2ME/MIDP; Opera Mini/4.2.14912/870; U; id) Presto/2.4.15";

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent); // set user agent
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


        libxml_use_internal_errors(true);




        $output = curl_exec($ch);

        curl_close($ch);

        $out = array();

        $dom = new DOMDocument();
        $title = '';

        $url = "http://" . $url;

        if(@file_get_contents($url) !== FALSE)
        {

            if ($dom->loadHTML(@file_get_contents($url)) == true)
            {


                $description = '';

                $list = $dom->getElementsByTagName("title");
                $meta = $dom->getElementsByTagName("meta");


                foreach ($meta as $item)
                {
                    //var_dump($item->getAttribute('name'));
                    if (strtolower($item->getAttribute('name')) == 'description')
                    {
                        //var_dump($item->getAttribute('content'));
                        $description = $item->getAttribute('content');
                    }
                }
                foreach ($list as $each)
                {

                    $title = $each->nodeValue;

                }

            }
        }

        array_push($out, $title, $description, $url);

        return $out;
    }

    public function CheckHttpStatus($url)
    {
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);

        /* Get the HTML or whatever is linked in $url. */
        $response = curl_exec($handle);

        /* Check for 404 (file not found). */
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);
        //var_dump($httpCode);
        return $httpCode;
    }


}

