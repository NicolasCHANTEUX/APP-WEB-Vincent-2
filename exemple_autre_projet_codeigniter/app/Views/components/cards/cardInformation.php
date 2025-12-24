<div class="bg-secondary rounded-xl shadow-md p-6 border border-border h-full flex flex-col justify-center">

    <h3 class="text-card-foreground text-lg font-bold mb-3">
        <?= esc($title) ?>
    </h3>

    <div class="text-card-foreground text-sm leading-relaxed">
        <?php if (!empty($lines) && is_array($lines)): ?>
            <?php foreach ($lines as $line): ?>
                <div class="mb-1">
                    <?php
                    if (is_string($line) && trim($line) !== '') {
                        $parts = preg_split('/\s*:\s*/', $line, 2);
                        if (count($parts) === 2 and strlen($parts[0]) > 1) {
                            echo '<span class="font-semibold">' . esc($parts[0]) . ':</span> ' . esc($parts[1]);
                        } else {
                            echo esc($line);
                        }
                    } else {
                        echo '&nbsp;';
                    }
                    ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p><?= esc($text) ?></p>
        <?php endif; ?>
    </div>
</div>