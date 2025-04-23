<?php
$this->extends('sample-01');
$this->assign('foo', $this->get('foo'));
$this->assign('genre', $this->get('genre'));
$this->assign('season', $this->get('season'));
$this->assign('template', 'child');

$this->start('footer');
?>
<footer></footer>
<?php
$this->end();
