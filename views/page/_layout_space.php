<div class="container">
    <div class="row">
        <div class="col-md-2">

            <!-- show space menu widget -->
            <?php $this->widget('application.modules_core.space.widgets.SpaceMenuWidget', array()); ?>

            <!-- show space admin menu widget -->
            <?php
            // get current space
            $space = Yii::app()->getController()->getSpace();
            // display admin menu, if user has any administrative rights for this space
            if ($space->canInvite() || $space->isAdmin()) {
                $this->widget('application.modules_core.space.widgets.SpaceAdminMenuWidget', array());
            }
            ?>

        </div>

        <div class="col-md-10 wiki">
            <?php echo $content; ?>
        </div>
    </div>
</div>
