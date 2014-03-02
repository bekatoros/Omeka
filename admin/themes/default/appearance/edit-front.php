<?php
echo head(array('title' => 'Edit Front'));
?>

<h1>Front page building will go here</h1>

<?php echo common('appearance-nav'); ?>
<?php echo flash(); ?>

<form id="appearance-form" method="post">

<section class="seven columns alpha">
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



<div class="available-blocks">
<h2>Available Blocks</h2>
<pre>
<?php 
print_r(array_keys($available));
?>
</pre>
</div>

<div class="primary-blocks">
<h2>Primary Blocks</h2>
<?php $i = 0;?>
<?php foreach($primary as $name=>$block): ?>
<?php $i++;?>
<div class='block'>
    <label for='primary[]<?php echo $name;  ?>' ><?php echo $block['heading']; ?></label>
    <input type='text' name='primary[<?php echo $name; ?>]' value='<?php echo $i; ?>' />
</div>
<?php endforeach; ?>
<?php $i = 'no'; ?>
<?php foreach($available as $name=>$block): ?>
<?php if(!array_key_exists($name, $primary)): ?>
<div class='block'>
    <label for='primary[]<?php echo $name;  ?>' ><?php echo $block['heading']; ?></label>
    <input type='text' name='primary[<?php echo $name; ?>]' value='<?php echo $i; ?>' />
</div>
<?php endif; ?>
<?php endforeach;?>
</div>
<div class="secondary-blocks">
<h2>Secondary Blocks</h2>
<?php $i=0;?>
<?php foreach($secondary as $name=>$block): ?>
<?php $i++; ?>
<div class='block'>
    <label for='secondary[<?php echo $name; ?>]' ><?php echo $block['heading']; ?></label>
    <input type='text' name='secondary[<?php echo $name; ?>]' value='<?php echo $i; ?>' />
</div>
<?php endforeach; ?>
<?php $i = 'no'; ?>
<?php foreach($available as $name=>$block): ?>
<?php if(!array_key_exists($name, $secondary)): ?>
<div class='block'>
    <label for='secondary[]<?php echo $name;  ?>' ><?php echo $block['heading']; ?></label>
    <input type='text' name='secondary[<?php echo $name; ?>]' value='<?php echo $i; ?>' />
</div>
<?php endif; ?>
<?php endforeach;?>

</div>

<pre>
<?php print_r($primary); print_r($secondary); ?>
</pre>
</section>

<section class='three columns omega'>
    <div id="save" class="panel">
        <button  name='submit' class='submit big green button'>Save Changes</button>
    </div>

</section>

<?php echo foot(); ?>