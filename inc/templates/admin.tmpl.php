<?php

use Bojaghi\Template\Template;

/**
 * @var Template $this
 *
 * Context
 * -------
 * - tabs: array{ id: string, title: string, url: string, is_active: bool, callback: callable }
 */
?>

<div class="wrap">
    <?php if ($this->get('tabs')) : ?>
        <nav class="nav-tab-wrapper">
            <?php foreach ($this->get('tabs') as $tab) : ?>
                <a class="nav-tab <?php echo $tab['is_active'] ? 'nav-tab-active' : ''; ?>"
                   href="<?php echo esc_url($tab['url']); ?>"><?php echo esc_html($tab['title']); ?></a>
            <?php endforeach; ?>
        </nav>
    <?php endif; ?>

    <?php
    $active = array_filter($this->get('tabs'), fn($tab) => $tab['is_active']);
    $active = array_shift($active);
    if ($active && is_callable($active['callback'])) {
        $active['callback']();
    }
    ?>
</div>
