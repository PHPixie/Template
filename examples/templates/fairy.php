<?php $this->layout('layout'); ?>
<?php $this->startBlock('title'); ?>
Fairy page
<?php $this->endBlock(); ?>

<h2>Hello <?php $_($name); ?></h2>
<?php include $this->resolve('fairy/greeting'); ?>