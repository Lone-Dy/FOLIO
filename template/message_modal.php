<dialog open class="<?php if ($success) echo "success_dialog"; else echo "error_dialog"; ?>">
    <form action="<?= $url ?>" method="POST">
        <input type="submit" value="X">
    </form>
    <p>
        <?= $message ?>
    </p>
</dialog>