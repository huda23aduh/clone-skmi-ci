<?php
$pager->setSurroundCount(2);
?>
<ul class="ci-pagination">
    <?php if ($pager->hasPrevious()): ?>
        <li><a href="<?= $pager->getFirst() ?>">&laquo;</a></li>
        <li><a href="<?= $pager->getPrevious() ?>">&lsaquo;</a></li>
    <?php else: ?>
        <li class="disabled"><span>&laquo;</span></li>
        <li class="disabled"><span>&lsaquo;</span></li>
    <?php endif ?>

    <?php foreach ($pager->links() as $link): ?>
        <?php if ($link['active']): ?>
            <li class="active"><span><?= $link['title'] ?></span></li>
        <?php else: ?>
            <li><a href="<?= $link['uri'] ?>"><?= $link['title'] ?></a></li>
        <?php endif ?>
    <?php endforeach ?>

    <?php if ($pager->hasNext()): ?>
        <li><a href="<?= $pager->getNext() ?>">&rsaquo;</a></li>
        <li><a href="<?= $pager->getLast() ?>">&raquo;</a></li>
    <?php else: ?>
        <li class="disabled"><span>&rsaquo;</span></li>
        <li class="disabled"><span>&raquo;</span></li>
    <?php endif ?>
</ul>
