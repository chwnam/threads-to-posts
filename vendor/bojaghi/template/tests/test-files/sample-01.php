<div id="1"><?php echo esc_html($this->fetch('foo')); ?></div>
<div id="2"><?php echo esc_html($this->fetch('genre')); ?></div>
<div id="3"><?php echo esc_html($this->fetch('season')); ?></div>
<p>Hello! I am <?php echo esc_html($this->fetch('template', 'parent')); ?> template!</p>
<?php echo $this->fetch('footer');