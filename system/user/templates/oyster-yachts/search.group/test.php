<?php /*
$searches = array(); 
$singular = array('heritage' => false, 'press_coverage' => false);

?>

{exp:super_search:results
	status="published"
    limit="20"
    highlight_keywords="strong"
}

<?php ob_start(); ?>{page_url}<?php $pageUrl = ob_get_contents(); ob_end_clean(); ?>
<?php ob_start(); ?>{title}<?php $title = ob_get_contents(); ob_end_clean(); ?>
<?php ob_start(); ?>{excerpt}<?php $excerpt = ob_get_contents(); ob_end_clean(); ?>
<?php ob_start(); ?>{channel_short_name}<?php $channel = ob_get_contents(); ob_end_clean(); ?>

<?php
if ($pageUrl !== "") {
	$searches[] = array(
		'title' => $title,
		'excerpt' => $excerpt,
		'link' => $pageUrl
	);
} else if (array_key_exists($channel, $singular)) {
	if ($singular[$channel] === false) {
		$singular[$channel] === true;

		if ($channel === "heritage") {
			$link = "{path='about/heritage'}";
		} else if ($channel === "press_coverage") {
			$link = "{path='news/press-coverage'}";
		}

		$searches[] = array(
			'title' => $title,
			'excerpt' => $excerpt,
			'link' => $link
		);
	}
} else {
	if ($channel === "brokerage") {
		$link = "{url_title_path='brokerage/fleet'}";
	} else if ($channel === "charter") {
		$link = "{url_title_path='charter/fleet'}";
	} else if ($channel === "yacht") {
		$link = "{url_title_path='yachts/fleet'}";
	} else if ($channel === "news") {
		$link = "{url_title_path='news/article'}";
	} else if ($channel === "yacht_previous_models") {
		$link = "{url_title_path='yachts/previous-models'}";
	} else if ($channel === "yacht_special_projects") {
		$link = "{url_title_path='yachts/special-projects'}";
	} else if ($channel === "previous_rally_news") {
		$link = "{url_title_path='world-rally/world-rally-2013-14/news'}";
	} else if ($channel === "event") {
		$link = "{url_title_path='events/regattas'}";
	} else if ($channel === "event_day_report") {
		$link = "{event_day_event status="published"}{path='events/regattas/{event_day_event:url_title}'}{/event_day_event}";
	} else if ($channel === "boat_show") {
		$link = "{url_title_path='events/boat-shows'}";
	}

	$searches[] = array(
		'title' => $title,
		'excerpt' => $excerpt,
		'link' => $link
	);
}
?>
{/exp:super_search:results}
/* ?>

{embed="site/.header" snap_to="undefined" snap_offset="undefined"}

<section style="background-image: url('/assets/images/about-us-why-us.jpg'); " class="hero after-nav global-page-hero yacht-listing-photo global-page-hero--medium">
	{back_button}
</section>

<article class="why-oyster row">
	<div class="banner">
		<h1>Search</h1>
	</div>


{exp:super_search:results
	status="published"
    limit="20"
    highlight_keywords="strong"
}

    <div class="entry">
        {if channel_short_name == "brokerage"}
        	<a href="{url_title_path='brokerage/fleet'}">{title}</a>
        {if:elseif channel_short_name == "yacht"}
        	<a href="{url_title_path='yachts/fleet'}">{title}</a>
        {if:elseif channel_short_name == "charter"}
        	<a href="{url_title_path='charter/fleet'}">{title}</a>
        {if:elseif channel_short_name == "news"}
        	<a href="{url_title_path='news/article'}">{title}</a>
        {if:elseif channel_short_name == "yacht_previous_models"}
        	<a href="{url_title_path='yachts/previous-models'}">{title}</a>
        {if:elseif channel_short_name == "yacht_special_projects"}
        	<a href="{url_title_path='yachts/special-projects'}">{title}</a>
        {if:elseif channel_short_name == "previous_rally_news"}
        	<a href="{url_title_path='world-rally/world-rally-2013-14/news'}">{title}</a>
        {if:elseif channel_short_name == "event"}
        	<a href="{url_title_path='events/regattas'}">{title}</a>
        {if:elseif channel_short_name == "event_day_report"}
			{event_day_event status="published"}
			<a href="{path='events/regattas/{event_day_event:url_title}'}">{title}</a>
			{/event_day_event}
        {if:elseif channel_short_name == "boat_show"}
        	<a href="{url_title_path='events/boat-shows'}">{title}</a>
        {if:elseif page_url != ""}
        	<a href="{page_url}">{title}</a>
        {/if}

        <br>
        {channel_short_name}

        <br>

        {excerpt}

        <br>
        <br>
    </div>

    {if super_search_no_results}
        <p>No results matched your query.</p>
    {/if}
{/exp:super_search:results}


<?php /*foreach ($searches as $search): ?>
	<div class="entry">
	<a href="<?php echo $search['link']; ?>"><?php echo $search['title']; ?></a>

		<br>

        <?php echo $search['excerpt']; ?>

        <br>
        <br>
	</div>
<?php endforeach; */?>

</article>

{footer}