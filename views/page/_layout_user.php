<div class="container">

    <div class="row">
        <div class="col-md-9">
            <?php $this->widget('application.modules_core.user.widgets.ProfileHeaderWidget'); ?>

        </div>
    </div>

    <div class="row">

        <div class="profile-nav-container col-md-2">
            <?php $this->widget('application.modules_core.user.widgets.ProfileMenuWidget', array()); ?>
        </div>
        
        <?php echo $content; ?>
    </div>

</div>
