<?php
include_once 'routing.php';

header('Content-type: text/xml');

$links = SelectAllLinksFromDatabase();
$posts = SelectAllPostFromDatabase();


echo '<?xml version="1.0" encoding="UTF-8" ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
echo '<url><loc>https://backlinqs.com</loc><lastmod>'.date("Y-m-d").'</lastmod><changefreq>monthly</changefreq></url>';

foreach ($links as $link)
{
   $dateTimeArray = date_parse($link['Date']);
   $dateString = $dateTimeArray['year']."-".$dateTimeArray['month']."-".$dateTimeArray['day'];
    echo '<url><loc>https://backlinqs.com/linq-browser/'.$link['permalink'].'</loc><lastmod>'.date("Y-m-d", strtotime($dateString)).'</lastmod><changefreq>monthly</changefreq></url>';
}

foreach ($posts as $post)
{
    $dateTimeArray = date_parse($post['DateCreated']);
    $dateString = $dateTimeArray['year']."-".$dateTimeArray['month']."-".$dateTimeArray['day'];

    if($post['post-type'] == "page")
    {
        echo '<url><loc>https://backlinqs.com/'.$post['permalink'].'</loc><lastmod>'.date("Y-m-d", strtotime($dateString)).'</lastmod><changefreq>monthly</changefreq></url>';

    }
    else
    {
        echo '<url><loc>https://backlinqs.com/blog/'.$post['permalink'].'</loc><lastmod>'.date("Y-m-d", strtotime($dateString)).'</lastmod><changefreq>monthly</changefreq></url>';
    }
}

echo '</urlset>';