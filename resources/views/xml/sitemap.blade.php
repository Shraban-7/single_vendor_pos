<?php
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach($urls as $url)
    <url>
        <loc>{{ e($url['loc']) }}</loc>
        <lastmod>{{ e($url['lastmod']) }}</lastmod>
        <changefreq>{{ e($url['changefreq']) }}</changefreq>
        <priority>{{ e($url['priority']) }}</priority>
    </url>
@endforeach
</urlset>