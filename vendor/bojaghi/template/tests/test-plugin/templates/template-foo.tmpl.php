<?php
/**
 * Context
 *
 * @var string $foo
 * @var string $bar
 */

use ShoplicKr\WpTemplate\Template;

Template::fragment('header');
?>
    <p>Template - <?php echo $foo; ?>, <?php echo $bar; ?></p>
<?php
Template::fragment('footer', false);
