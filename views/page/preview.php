<link rel="stylesheet" href="<?php echo $this->getModule()->getAssetsUrl(); ?>/highlight.js/styles/github.css">
<link rel="stylesheet" type="text/css" href="<?php echo $this->getModule()->getAssetsUrl(); ?>/bootstrap-markdown-override.css">
<script src="<?php echo $this->getModule()->getAssetsUrl(); ?>/highlight.js//highlight.pack.js"></script>
    
<div class="markdown-render">
    <?php echo $content; ?>
</div>

<script>

    $('pre code').each(function(i, e) {
        hljs.highlightBlock(e);
    });

</script>
