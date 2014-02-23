<?php
echo head(array('title' => 'Edit Front'));
?>

<h1>Front page building will go here</h1>

<?php echo common('appearance-nav'); ?>
<?php echo flash(); ?>
<p>Something similar to the navigation editing, with drag-drop blocks into order,
and into different regions, if the current public theme defines a primary and secondary
</p>
<p>
Keeping track of variations across current public theme is likely tricky.
Also tricky might be the JS/jQuery for arranging into different columns
</p>

<p>
Even more tricky would be letting plugins add parameters to their callbacks for producing HTML. For example,
selecting a Simple Page for a snippet, though maybe the filter for available blocks could make all
pages available, and admin selects one?
</p>
<pre>
<?php 


print_r($availableBlocks);
//spoofing the imagined UI that would put blocks in regions
set_option('front_page_primary', serialize($availableBlocks));

set_option('front_page_secondary', serialize($availableBlocks));


?>
</pre>


<?php echo foot(); ?>